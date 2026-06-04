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

        // Calculate Special Occasions
        $isBirthday = false;
        $isAnniversary = false;
        $yearsOfService = 0;

        if ($staffDetail->dob) {
            $dob = \Carbon\Carbon::parse($staffDetail->dob);
            if ($dob->isBirthday()) {
                $isBirthday = true;
            }
        }

        if ($staffDetail->doj) {
            $doj = \Carbon\Carbon::parse($staffDetail->doj);
            if ($doj->format('m-d') === now()->format('m-d')) {
                $isAnniversary = true;
                $yearsOfService = $doj->diffInYears(now());
            }
        }

        return view('Staff.dashboard', [
            'staffDetail' => $staffDetail,
            'totalReports' => $totalReports,
            'todayReport' => $todayReport,
            'recentReports' => $recentReports,
            'profileRequests' => $profileRequests,
            'isBirthday' => $isBirthday,
            'isAnniversary' => $isAnniversary,
            'yearsOfService' => $yearsOfService
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
