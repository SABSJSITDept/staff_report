<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff\StaffModel;
use App\Models\SystemAllotment;
use App\Models\SystemBackup;

class ITManagementController extends Controller
{
    public function backupLocationsIndex()
    {
        $locations = \App\Models\BackupLocation::all();
        return view('ITManagement.backup-locations', compact('locations'));
    }

    public function backupLocationsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:backup_locations,name|max:255',
        ]);

        \App\Models\BackupLocation::create([
            'name' => strtoupper($request->name),
        ]);

        return back()->with('success', 'Backup Location added successfully.');
    }

    public function backupLocationsDestroy($id)
    {
        $location = \App\Models\BackupLocation::findOrFail($id);
        $location->delete();

        return back()->with('success', 'Backup Location deleted successfully.');
    }

    public function backupReportsIndex(Request $request)
    {
        $data = $this->getBackupReportMatrixData($request);
        return view('ITManagement.backup-reports', $data);
    }

    public function backupReportsExportPdf(Request $request)
    {
        $data = $this->getBackupReportMatrixData($request);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.backup-report-pdf', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('backup-reports-' . $data['month'] . '-' . $data['year'] . '.pdf');
    }

    public function backupReportsExportExcel(Request $request)
    {
        $data = $this->getBackupReportMatrixData($request);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BackupReportExport($data['reportData'], $data['dates']),
            'backup-reports-' . $data['month'] . '-' . $data['year'] . '.xlsx'
        );
    }

    private function getBackupReportMatrixData(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $officeId = $request->input('office_id');
        $staffId = $request->input('staff_id');
        $showFullMonth = $request->boolean('full_month', false);

        $offices = \App\Models\Office\OfficeModel::orderBy('name')->get();
        $allStaffList = StaffModel::where('status', 'Active')->orderBy('name')->get();

        $staffQuery = StaffModel::where('status', 'Active')->with(['office', 'department'])->orderBy('name');
        if ($officeId) {
            $staffQuery->where('office_id', $officeId);
        }
        if ($staffId) {
            $staffQuery->where('id', $staffId);
        }
        $staffs = $staffQuery->get();

        // Calculate days in the selected month
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        $totalDaysToShow = ($year == date('Y') && $month == date('m') && !$showFullMonth) 
            ? (int)date('d') 
            : $daysInMonth;

        $dates = [];
        for ($d = 1; $d <= $totalDaysToShow; $d++) {
            $dateCarbon = \Carbon\Carbon::createFromDate($year, $month, $d);
            $dates[] = [
                'day' => str_pad($d, 2, '0', STR_PAD_LEFT),
                'date_str' => $dateCarbon->toDateString(),
                'display' => $dateCarbon->format('d-m-Y'),
                'day_name' => $dateCarbon->format('D'),
                'is_sunday' => $dateCarbon->isSunday(),
                'is_future' => $dateCarbon->isFuture(),
            ];
        }

        // Fetch backups for this month and for these staff in a single query
        $backups = SystemBackup::whereIn('staff_id', $staffs->pluck('id'))
            ->whereYear('backup_date', $year)
            ->whereMonth('backup_date', $month)
            ->get()
            ->groupBy('staff_id');

        $reportData = [];
        foreach ($staffs as $index => $staff) {
            $staffBackups = $backups->get($staff->id, collect())->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->backup_date)->toDateString();
            });

            $row = [
                'sr' => $index + 1,
                'staff' => $staff,
                'days' => []
            ];

            foreach ($dates as $dateObj) {
                $dStr = $dateObj['date_str'];
                $backup = $staffBackups->get($dStr);

                if ($backup) {
                    if ($backup->status === 'YES') {
                        $val = 'YES';
                    } elseif ($backup->remark === 'NO-FILL') {
                        $val = 'NO-FILL';
                    } else {
                        $val = 'NO';
                    }
                } else {
                    if ($dateObj['is_future']) {
                        $val = '-';
                    } else {
                        $val = 'NO-FILL';
                    }
                }

                $row['days'][$dStr] = $val;
            }

            $reportData[] = $row;
        }

        return [
            'reportData' => $reportData,
            'dates' => $dates,
            'month' => $month,
            'year' => $year,
            'officeId' => $officeId,
            'staffId' => $staffId,
            'offices' => $offices,
            'allStaffList' => $allStaffList,
            'showFullMonth' => $showFullMonth,
            'daysInMonth' => $daysInMonth
        ];
    }

    public function defaultersIndex(Request $request)
    {
        $minDays = (int) $request->input('min_days', 3);
        if ($minDays < 1) $minDays = 3;
        $officeId = $request->input('office_id');
        $excludeMailed = $request->input('exclude_mailed', '1') === '1';

        $defaulters = $this->getDefaultersData($minDays, $officeId, $excludeMailed);
        $offices = \App\Models\Office\OfficeModel::orderBy('name')->get();

        // Count how many staff are currently in the 7-day grace period
        $sevenDaysAgo = now()->subDays(7);
        $recentlyMailedCount = \App\Models\BackupDefaulterMailLog::where('created_at', '>=', $sevenDaysAgo)
            ->distinct('staff_id')
            ->count('staff_id');

        return view('ITManagement.backup-defaulters', compact('defaulters', 'offices', 'minDays', 'officeId', 'excludeMailed', 'recentlyMailedCount'));
    }

    public function defaultersExportPdf(Request $request)
    {
        $minDays = (int) $request->input('min_days', 3);
        if ($minDays < 1) $minDays = 3;
        $officeId = $request->input('office_id');
        $excludeMailed = $request->input('exclude_mailed', '1') === '1';

        $defaulters = $this->getDefaultersData($minDays, $officeId, $excludeMailed);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.defaulters-pdf', compact('defaulters'));
        return $pdf->download('backup-defaulters-' . date('Y-m-d') . '.pdf');
    }

    public function defaultersExportExcel(Request $request)
    {
        $minDays = (int) $request->input('min_days', 3);
        if ($minDays < 1) $minDays = 3;
        $officeId = $request->input('office_id');
        $excludeMailed = $request->input('exclude_mailed', '1') === '1';

        $defaulters = $this->getDefaultersData($minDays, $officeId, $excludeMailed);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DefaultersExport($defaulters), 'backup-defaulters-' . date('Y-m-d') . '.xlsx');
    }

    public function getDefaultersData($minDays = 3, $officeId = null, $excludeRecentlyMailed = true)
    {
        $query = StaffModel::where('status', 'Active')->with(['department', 'office'])->orderBy('name');
        if ($officeId) {
            $query->where('office_id', $officeId);
        }

        // If excluding staff who were mailed within the last 7 days
        if ($excludeRecentlyMailed) {
            $sevenDaysAgo = now()->subDays(7);
            $recentlyMailedStaffIds = \App\Models\BackupDefaulterMailLog::where('created_at', '>=', $sevenDaysAgo)
                ->pluck('staff_id')
                ->unique()
                ->toArray();

            if (!empty($recentlyMailedStaffIds)) {
                $query->whereNotIn('id', $recentlyMailedStaffIds);
            }
        }

        $activeStaffs = $query->get();
        $defaulters = collect();

        // Optimized single query for all backups in the past 60 days
        $lookbackDate = today()->subDays(60)->toDateString();
        $allBackups = SystemBackup::whereIn('staff_id', $activeStaffs->pluck('id'))
            ->whereDate('backup_date', '>=', $lookbackDate)
            ->get()
            ->groupBy('staff_id');

        // Fetch latest mail logs for each staff member to display last mailed date/grace info
        $latestMailLogs = \App\Models\BackupDefaulterMailLog::whereIn('staff_id', $activeStaffs->pluck('id'))
            ->latest()
            ->get()
            ->groupBy('staff_id');

        foreach ($activeStaffs as $staff) {
            $staffBackups = $allBackups->get($staff->id, collect())->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->backup_date)->toDateString();
            });

            $consecutiveMissed = 0;
            $dateCursor = today();

            for ($i = 0; $i < 60; $i++) {
                if ($dateCursor->isSunday()) {
                    $dateCursor->subDay();
                    continue;
                }

                $dateStr = $dateCursor->toDateString();
                $backup = $staffBackups->get($dateStr);

                if ($backup && $backup->status === 'YES') {
                    break;
                }

                $consecutiveMissed++;
                $dateCursor->subDay();
            }

            if ($consecutiveMissed >= $minDays) {
                $recentBackups = $staffBackups->filter(function($b) {
                    return $b->status === 'YES';
                })->sortByDesc('backup_date')->take(3)->pluck('backup_date');

                $lastMail = $latestMailLogs->get($staff->id)?->first();

                $defaulters->push([
                    'staff' => $staff,
                    'consecutive_missed' => $consecutiveMissed,
                    'recent_backups' => $recentBackups,
                    'last_mail' => $lastMail
                ]);
            }
        }

        return $defaulters->sortByDesc('consecutive_missed')->values();
    }

    public function sendDefaulterMail($id)
    {
        $staff = StaffModel::findOrFail($id);

        if ($staff->email) {
            try {
                $missedDays = $staff->getConsecutiveMissedBackupDays();
                \Illuminate\Support\Facades\Mail::to($staff->email)->send(new \App\Mail\BackupDefaulterMail($staff, $missedDays));
                
                \App\Models\BackupDefaulterMailLog::create([
                    'staff_id' => $staff->id,
                    'sent_by' => auth()->id()
                ]);

                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Mail sent successfully to ' . $staff->name
                    ]);
                }

                return back()->with('success', 'Mail sent successfully to ' . $staff->name);
            } catch (\Exception $e) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send mail: ' . $e->getMessage()
                    ], 500);
                }
                return back()->with('error', 'Failed to send mail: ' . $e->getMessage());
            }
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'No email address found for ' . $staff->name
            ], 422);
        }

        return back()->with('error', 'No email address found for ' . $staff->name);
    }

    public function sendBulkDefaulterMail(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff_details,id'
        ]);

        $staffs = StaffModel::whereIn('id', $request->staff_ids)->get();
        $sentCount = 0;

        foreach ($staffs as $staff) {
            if ($staff->email) {
                \Illuminate\Support\Facades\Mail::to($staff->email)->send(new \App\Mail\BackupDefaulterMail($staff));
                
                \App\Models\BackupDefaulterMailLog::create([
                    'staff_id' => $staff->id,
                    'sent_by' => auth()->id()
                ]);
                $sentCount++;
            }
        }

        return back()->with('success', "Mail sent successfully to $sentCount staff members.");
    }

    public function defaulterMailLogsIndex(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\BackupDefaulterMailLog::with(['staff', 'sender'])->latest();

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $totalCount = $query->count();
        $logs = $query->paginate(20)->withQueryString();
        $staffList = \App\Models\Staff\StaffModel::orderBy('name')->get();

        return view('ITManagement.backup-mail-logs', compact('logs', 'totalCount', 'staffList'));
    }
}
