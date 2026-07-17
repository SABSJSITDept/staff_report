<?php

namespace App\Http\Controllers;

use App\Models\Staff\StaffModel;
use App\Models\DailyReport;
use App\Models\DailyReportTask;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function index()
    {
        \App\Http\Controllers\DailyReportController::autoPauseMidnightTasks(Auth::id());
        \App\Http\Controllers\DailyReportController::autoCarryForwardPaused(Auth::id());

        $staffDetail = StaffModel::with(['department', 'office'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$staffDetail) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Staff details not found.');
        }

        $totalReports = DailyReport::where('staff_id', Auth::id())->count();
        $todayReport  = DailyReport::where('staff_id', Auth::id())
            ->whereDate('report_date', today())
            ->first();
        $recentReports = DailyReport::with('tasks')
            ->where('staff_id', Auth::id())
            ->orderByDesc('report_date')
            ->limit(5)
            ->get();

        // Fetch Recent Profile Requests
        $profileRequests = \App\Models\Staff\ProfileUpdateRequest::where('staff_id', $staffDetail->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Calculate Global Special Occasions (All Staff)
        $today = now()->format('m-d');
        
        $todaysBirthdays = StaffModel::where('status', 'Active')
            ->whereNotNull('dob')
            ->get()
            ->filter(function($staff) use ($today) {
                return \Carbon\Carbon::parse($staff->dob)->format('m-d') === $today;
            });

        $todaysAnniversaries = StaffModel::where('status', 'Active')
            ->whereNotNull('doj')
            ->get()
            ->filter(function($staff) use ($today) {
                return \Carbon\Carbon::parse($staff->doj)->format('m-d') === $today;
            })->map(function($staff) {
                $staff->yearsOfService = \Carbon\Carbon::parse($staff->doj)->diffInYears(now());
                return $staff;
            });

        // Fetch all Employee of the Month records for this office in the current year
        $employeesOfTheMonth = \App\Models\EmployeeOfTheMonth::with('staff')
            ->where('office_id', $staffDetail->office_id)
            ->where('year', now()->year)
            ->orderByDesc('month')
            ->get();
        
        $featuredEmployee = $employeesOfTheMonth->first();
        $otherEmployees = $employeesOfTheMonth->slice(1);

        // IT Pending Recharges Notification
        $pendingRecharges = collect();
        if (Auth::user()->canAccessIT()) {
            $pendingRecharges = \App\Models\ItRecharge::where('last_date', '<=', now()->addDays(7))
                                    ->orderBy('last_date')
                                    ->get();
        }

        // Keep variables for backward compatibility if needed, though we will use the new collections
        $isBirthday = $todaysBirthdays->contains('id', $staffDetail->id);
        $isAnniversary = $todaysAnniversaries->contains('id', $staffDetail->id);
        $yearsOfService = $isAnniversary ? $todaysAnniversaries->firstWhere('id', $staffDetail->id)->yearsOfService : 0;

        return view('Staff.dashboard', [
            'staffDetail' => $staffDetail,
            'totalReports' => $totalReports,
            'todayReport' => $todayReport,
            'recentReports' => $recentReports,
            'profileRequests' => $profileRequests,
            'isBirthday' => $isBirthday,
            'isAnniversary' => $isAnniversary,
            'yearsOfService' => $yearsOfService,
            'todaysBirthdays' => $todaysBirthdays,
            'todaysAnniversaries' => $todaysAnniversaries,
            'featuredEmployee' => $featuredEmployee,
            'otherEmployees' => $otherEmployees,
            'pendingRecharges' => $pendingRecharges
        ]);
    }

    public function trackTask()
    {
        \App\Http\Controllers\DailyReportController::autoPauseMidnightTasks(Auth::id());
        \App\Http\Controllers\DailyReportController::autoCarryForwardPaused(Auth::id());

        $staffDetail = StaffModel::with(['department', 'office'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$staffDetail) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Staff details not found.');
        }

        $todayReport  = DailyReport::where('staff_id', Auth::id())
            ->whereDate('report_date', today())
            ->first();

        $userId = Auth::id();
        $activeTask = DailyReportTask::whereHas('dailyReport', function($q) use ($userId) {
            $q->where('staff_id', $userId)
              ->whereDate('report_date', today());
        })->where('status', 'in_progress')->first();

        $todayTasks = $todayReport ? $todayReport->tasks()->withCount('comments')->orderBy('created_at', 'asc')->get() : collect();

        return view('Staff.track-task', [
            'staffDetail' => $staffDetail,
            'todayReport' => $todayReport,
            'activeTask' => $activeTask,
            'todayTasks' => $todayTasks
        ]);
    }

    public function guide()
    {
        return view('Staff.guide');
    }
}
