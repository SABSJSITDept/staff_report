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
        $staffs = StaffModel::where('status', 'Active')->orderBy('name')->get();
        $query = $this->buildBackupReportQuery($request);
        $backups = $query->paginate(20);

        return view('ITManagement.backup-reports', compact('staffs', 'backups'));
    }

    public function backupReportsExportPdf(Request $request)
    {
        $query = $this->buildBackupReportQuery($request);
        $backups = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.backup-report-pdf', compact('backups'));
        return $pdf->download('backup-reports-' . date('Y-m-d') . '.pdf');
    }

    public function backupReportsExportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BackupReportExport($request), 'backup-reports-' . date('Y-m-d') . '.xlsx');
    }

    private function buildBackupReportQuery(Request $request)
    {
        $query = SystemBackup::with('staff')
            ->join('staff_details', 'system_backups.staff_id', '=', 'staff_details.id')
            ->select('system_backups.*')
            ->orderBy('system_backups.backup_date', 'desc')
            ->orderBy('staff_details.name', 'asc');

        if ($request->filled('staff_id')) {
            $query->where('system_backups.staff_id', $request->staff_id);
        }

        if ($request->filter_type == 'month') {
            if ($request->filled('month')) {
                $query->whereMonth('system_backups.backup_date', $request->month);
            }
            if ($request->filled('year')) {
                $query->whereYear('system_backups.backup_date', $request->year);
            }
        } elseif ($request->filter_type == 'date_range') {
            if ($request->filled('start_date')) {
                $query->where('system_backups.backup_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->where('system_backups.backup_date', '<=', $request->end_date);
            }
        }

        return $query;
    }

    public function defaultersIndex()
    {
        $defaulters = $this->getDefaultersData();
        return view('ITManagement.backup-defaulters', compact('defaulters'));
    }

    public function defaultersExportPdf()
    {
        $defaulters = $this->getDefaultersData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.defaulters-pdf', compact('defaulters'));
        return $pdf->download('backup-defaulters-' . date('Y-m-d') . '.pdf');
    }

    public function defaultersExportExcel()
    {
        $defaulters = $this->getDefaultersData();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DefaultersExport($defaulters), 'backup-defaulters-' . date('Y-m-d') . '.xlsx');
    }

    private function getDefaultersData()
    {
        $activeStaffs = StaffModel::where('status', 'Active')->get();
        $defaulters = collect();

        foreach ($activeStaffs as $staff) {
            $consecutiveMissed = 0;
            $dateCursor = today();
            
            // Calculate consecutive missed days backwards
            while (true) {
                // If it's Sunday, skip it
                if ($dateCursor->isSunday()) {
                    $dateCursor->subDay();
                    continue;
                }

                $backup = SystemBackup::where('staff_id', $staff->id)
                                      ->whereDate('backup_date', $dateCursor->toDateString())
                                      ->first();

                // Stop counting consecutive missed days if we hit a YES
                if ($backup && $backup->status == 'YES') {
                    break;
                }

                $consecutiveMissed++;
                $dateCursor->subDay();
                
                // Prevent infinite loops (limit lookback to max 30 days)
                if ($consecutiveMissed > 30) {
                    break;
                }
            }

            // If missed 3 or more consecutive working days
            if ($consecutiveMissed >= 3) {
                // Find recent YES backups
                $recentBackups = SystemBackup::where('staff_id', $staff->id)
                                             ->where('status', 'YES')
                                             ->orderBy('backup_date', 'desc')
                                             ->take(3)
                                             ->pluck('backup_date');
                                             
                $defaulters->push([
                    'staff' => $staff,
                    'consecutive_missed' => $consecutiveMissed,
                    'recent_backups' => $recentBackups
                ]);
            }
        }

        // Sort by most missed days first
        return $defaulters->sortByDesc('consecutive_missed')->values();
    }

    public function sendDefaulterMail($id)
    {
        $staff = StaffModel::findOrFail($id);

        if ($staff->email) {
            \Illuminate\Support\Facades\Mail::to($staff->email)->send(new \App\Mail\BackupDefaulterMail($staff));
            
            \App\Models\BackupDefaulterMailLog::create([
                'staff_id' => $staff->id,
                'sent_by' => auth()->id()
            ]);

            return back()->with('success', 'Mail sent successfully to ' . $staff->name);
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

    public function defaulterMailLogsIndex()
    {
        $logs = \App\Models\BackupDefaulterMailLog::with(['staff', 'sender'])->latest()->paginate(20);
        return view('ITManagement.backup-mail-logs', compact('logs'));
    }
}
