<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RatingSubmittedMail;
use App\Models\User;
use App\Models\RatingCategory;
use App\Models\RatingQuestion;
use App\Models\RatingReportCard;
use App\Models\OverallRemark;
use App\Models\Office\OfficeModel;
use App\Models\Staff\StaffModel;
use App\Models\RatingSetting;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $setting = RatingSetting::first();
        if ((!$setting || !$setting->is_active) && !$user->isAdmin()) {
            return redirect()->route('staff.dashboard')->with('error', 'Rating link is currently closed.');
        }

        $isHod = $user->role === 'staff' && $user->staff && $user->staff->isHod();
        
        $query = User::where('role', 'staff');

        if ($user->isAdmin() || $user->isKaryalaySanyojak()) {
            // Admin and Karyalay Sanyojak can see all staff
        } elseif ($user->isSanyojak()) {
            $sanyojak = $user->sanyojak;
            $staffAssignedIds = $sanyojak ? ($sanyojak->staff_assigned ?? []) : [];
            if (is_string($staffAssignedIds)) {
                $staffAssignedIds = json_decode($staffAssignedIds, true);
            }
            $userIds = StaffModel::whereIn('id', $staffAssignedIds)->whereNotNull('user_id')->pluck('user_id')->filter()->toArray();
            $query->where(function($q) use ($userIds, $staffAssignedIds) {
                $q->whereIn('id', $userIds ?: [0])
                  ->orWhereHas('staff', function($sq) use ($staffAssignedIds) {
                      $sq->whereIn('id', $staffAssignedIds ?: [0]);
                  });
            });
        } elseif ($isHod) {
            $deptId = $user->staff->managedDepartment->id;
            $deptStaffUserIds = StaffModel::where('dept_id', $deptId)->where('user_id', '!=', $user->id)->pluck('user_id')->filter()->toArray();
            if (empty($deptStaffUserIds)) {
                $query->where('id', 0); // No staff to rate
            } else {
                $query->whereIn('id', $deptStaffUserIds);
            }
        } else {
            abort(403, 'Unauthorized action.');
        }

        // Apply Office Filter first so it affects the Staff dropdown
        if ($request->filled('office_id')) {
            $officeId = $request->office_id;
            $query->whereHas('staff', function($q) use ($officeId) {
                $q->where('office_id', $officeId);
            });
        }

        // Clone the base query to get eligible staff for the dropdown (filtered by office if selected)
        $allStaff = (clone $query)->with('staff')->get();

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply Staff Name Dropdown Filter
        if ($request->filled('staff_id')) {
            $query->where('id', $request->staff_id);
        }

        $staff = $query->get();
        $offices = OfficeModel::all();

        return view('ratings.index', compact('staff', 'allStaff', 'offices'));
    }

    public function create($staff_id)
    {
        $user = auth()->user();
        
        $setting = RatingSetting::first();
        if ((!$setting || !$setting->is_active) && !$user->isAdmin()) {
            return redirect()->route('staff.dashboard')->with('error', 'Rating link is currently closed.');
        }

        // Check if already rated
        $alreadyRated = RatingReportCard::where('staff_id', $staff_id)
                            ->where('rating_given_by_id', $user->id)
                            ->exists();
        if ($alreadyRated) {
            return redirect()->route('ratings.index')->with('error', 'You have already rated this staff member.');
        }

        $isHod = $user->role === 'staff' && $user->staff && $user->staff->isHod();

        // Permission check
        if ($user->isSanyojak()) {
            $sanyojak = $user->sanyojak;
            $staffAssignedIds = $sanyojak ? ($sanyojak->staff_assigned ?? []) : [];
            if (is_string($staffAssignedIds)) {
                $staffAssignedIds = json_decode($staffAssignedIds, true);
            }
            $userIds = StaffModel::whereIn('id', $staffAssignedIds)->whereNotNull('user_id')->pluck('user_id')->filter()->toArray();
            $staffLinked = StaffModel::where('user_id', $staff_id)->whereIn('id', $staffAssignedIds)->exists();
            if (!in_array($staff_id, $userIds) && !$staffLinked) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($isHod) {
            $deptId = $user->staff->managedDepartment->id;
            $deptStaffUserIds = StaffModel::where('dept_id', $deptId)->pluck('user_id')->toArray();
            if (!in_array($staff_id, $deptStaffUserIds) || $staff_id == $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (!$user->isAdmin() && !$user->isKaryalaySanyojak()) {
            abort(403, 'Unauthorized action.');
        }

        $staffMember = User::findOrFail($staff_id);
        $categories = RatingCategory::with('questions')->get();
        
        return view('ratings.create', compact('staffMember', 'categories'));
    }

    public function store(Request $request, $staff_id)
    {
        $user = auth()->user();

        $setting = RatingSetting::first();
        if ((!$setting || !$setting->is_active) && !$user->isAdmin()) {
            return redirect()->route('staff.dashboard')->with('error', 'Rating link is currently closed.');
        }

        // Check if already rated
        $alreadyRated = RatingReportCard::where('staff_id', $staff_id)
                            ->where('rating_given_by_id', $user->id)
                            ->exists();
        if ($alreadyRated) {
            return redirect()->route('ratings.index')->with('error', 'You have already rated this staff member.');
        }

        $isHod = $user->role === 'staff' && $user->staff && $user->staff->isHod();

        // Permission check
        if ($user->isSanyojak()) {
            $sanyojak = $user->sanyojak;
            $staffAssignedIds = $sanyojak ? ($sanyojak->staff_assigned ?? []) : [];
            if (is_string($staffAssignedIds)) {
                $staffAssignedIds = json_decode($staffAssignedIds, true);
            }
            $userIds = StaffModel::whereIn('id', $staffAssignedIds)->whereNotNull('user_id')->pluck('user_id')->filter()->toArray();
            $staffLinked = StaffModel::where('user_id', $staff_id)->whereIn('id', $staffAssignedIds)->exists();
            if (!in_array($staff_id, $userIds) && !$staffLinked) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($isHod) {
            $deptId = $user->staff->managedDepartment->id;
            $deptStaffUserIds = StaffModel::where('dept_id', $deptId)->pluck('user_id')->toArray();
            if (!in_array($staff_id, $deptStaffUserIds) || $staff_id == $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (!$user->isAdmin() && !$user->isKaryalaySanyojak()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*.rating' => 'required|integer|min:1|max:5',
            'ratings.*.remark' => 'nullable|string',
            'overall_remark' => 'nullable|string'
        ]);

        $ratingDetails = [];
        $totalRating = 0;
        $ratingCount = 0;

        foreach ($request->ratings as $question_id => $data) {
            $question = RatingQuestion::find($question_id);
            if ($question) {
                RatingReportCard::create([
                    'staff_id' => $staff_id,
                    'category_id' => $question->category_id,
                    'question_id' => $question_id,
                    'rating' => $data['rating'],
                    'rating_given_by_id' => $user->id,
                    'financial_year' => $setting->financial_year ?? null,
                    'remark' => $data['remark'] ?? null,
                ]);
                $ratingDetails[] = [
                    'question' => $question->question,
                    'rating' => $data['rating'],
                    'remark' => $data['remark'] ?? ''
                ];
                $totalRating += (int)$data['rating'];
                $ratingCount++;
            }
        }

        $averageRating = $ratingCount > 0 ? round($totalRating / $ratingCount, 2) : 0;
        $raterName = $user->name;

        if ($request->filled('overall_remark')) {
            OverallRemark::create([
                'staff_id' => $staff_id,
                'remark' => $request->overall_remark,
                'remark_given_by_id' => $user->id,
                'financial_year' => $setting->financial_year ?? null,
            ]);
        }
        try {
            \Log::info('Attempting to send email for rating for staff id: ' . $staff_id);
            $staffMember = User::find($staff_id);
            if ($staffMember) {
                $overallRemark = $request->overall_remark ?? '';
                Mail::mailer('godaddy')
                    ->to('pst@sadhumargi.com')
                    ->send(new RatingSubmittedMail($staffMember, $ratingDetails, $overallRemark, $raterName, $averageRating));
                \Log::info('Rating email sent successfully to pst@sadhumargi.com');
            } else {
                \Log::warning('Staff member not found for id: ' . $staff_id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send rating email: ' . $e->getMessage());
        }

        return redirect()->route('ratings.index')->with('success', 'Rating submitted successfully for staff member.');
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        if (!$user->isPst()) {
            abort(403, 'Unauthorized access.');
        }

        $groupedData = $this->getReportData($request);
        $allStaff = User::where('role', 'staff')->get();

        return view('ratings.report', compact('groupedData', 'allStaff'));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        if (!$user->isPst()) {
            abort(403, 'Unauthorized access.');
        }

        $groupedData = $this->getReportData($request);
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RatingReportExport($groupedData), 'Rating_Reports.xlsx');
    }

    private function getReportData(Request $request)
    {
        $query = RatingReportCard::with(['staff', 'category', 'question', 'ratingGivenBy']);
        
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $ratings = $query->orderBy('created_at', 'asc')->get();

        $remarksQuery = OverallRemark::with(['staff', 'remarkGivenBy']);
        if ($request->filled('staff_id')) {
            $remarksQuery->where('staff_id', $request->staff_id);
        }
        $overallRemarks = $remarksQuery->orderBy('created_at', 'desc')->get()->groupBy('staff_id')->map(function($items) {
            return $items->map(function($item) {
                return [
                    'rater' => $item->remarkGivenBy->name ?? 'Unknown',
                    'remark' => $item->remark
                ];
            })->toArray();
        });

        $groupedData = [];
        foreach ($ratings as $rating) {
            $staffId = $rating->staff_id;
            $categoryId = $rating->category_id;
            $questionId = $rating->question_id;
            $raterName = $rating->ratingGivenBy->name ?? 'Unknown';
            $categoryName = $rating->category->name ?? 'Uncategorized';

            if (!isset($groupedData[$staffId])) {
                $groupedData[$staffId] = [
                    'staff_name' => $rating->staff->name ?? 'Unknown',
                    'raters' => [],
                    'categories' => [],
                    'overall_remarks' => $overallRemarks->get($staffId, []),
                    'financial_years' => []
                ];
            }

            if ($rating->financial_year && !in_array($rating->financial_year, $groupedData[$staffId]['financial_years'])) {
                $groupedData[$staffId]['financial_years'][] = $rating->financial_year;
            }

            if (!in_array($raterName, $groupedData[$staffId]['raters'])) {
                $groupedData[$staffId]['raters'][] = $raterName;
            }

            if (!isset($groupedData[$staffId]['categories'][$categoryId])) {
                $groupedData[$staffId]['categories'][$categoryId] = [
                    'category_name' => $categoryName,
                    'questions' => []
                ];
            }

            if (!isset($groupedData[$staffId]['categories'][$categoryId]['questions'][$questionId])) {
                $groupedData[$staffId]['categories'][$categoryId]['questions'][$questionId] = [
                    'question_text' => $rating->question->question ?? 'Unknown',
                    'ratings_by_rater' => [],
                ];
            }

            $groupedData[$staffId]['categories'][$categoryId]['questions'][$questionId]['ratings_by_rater'][$raterName] = [
                'rating' => $rating->rating,
                'remark' => $rating->remark
            ];
        }

        return $groupedData;
    }
}
