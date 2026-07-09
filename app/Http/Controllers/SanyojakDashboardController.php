<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Staff\StaffModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SanyojakDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sanyojak = $user->sanyojak;

        if (!$sanyojak) {
            abort(403, 'Unauthorized access.');
        }

        $staffAssignedIds = $sanyojak->staff_assigned ?? [];
        
        // Get user_ids for the assigned staff
        $userIds = StaffModel::whereIn('id', $staffAssignedIds)->whereNotNull('user_id')->pluck('user_id')->filter()->toArray();

        // Optional date filter, default to today
        $date = $request->input('date', Carbon::today()->toDateString());

        // Fetch reports for these users on the specific date
        $reports = DailyReport::with(['staff.staff', 'tasks'])
            ->where(function($q) use ($userIds, $staffAssignedIds) {
                $q->whereIn('staff_id', $userIds ?: [0])
                  ->orWhereHas('staff.staff', function($sq) use ($staffAssignedIds) {
                      $sq->whereIn('id', $staffAssignedIds ?: [0]);
                  });
            })
            ->whereDate('report_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Sanyojak.Dashboard', [
            'sanyojak' => $sanyojak,
            'reports' => $reports,
            'date' => $date
        ]);
    }
}
