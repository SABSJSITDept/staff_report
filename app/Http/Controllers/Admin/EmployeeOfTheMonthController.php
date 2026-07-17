<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeOfTheMonth;
use App\Models\Staff\StaffModel;
use App\Models\Office\OfficeModel;
use Illuminate\Http\Request;

class EmployeeOfTheMonthController extends Controller
{
    public function index()
    {
        $records = EmployeeOfTheMonth::with(['staff', 'office'])->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        $offices = OfficeModel::all();
        $staffMembers = StaffModel::all();

        return view('admin.employee_of_month.index', compact('records', 'offices', 'staffMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_details,id',
            'office_id' => 'required|exists:office_details,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'description' => 'required|string|max:1000'
        ]);

        // Check if an entry already exists for this office, month, and year
        $existing = EmployeeOfTheMonth::where('office_id', $request->office_id)
                                      ->where('month', $request->month)
                                      ->where('year', $request->year)
                                      ->first();

        if ($existing) {
            return back()->with('error', 'An Employee of the Month already exists for this office and month. Please remove it first to re-assign.');
        }

        EmployeeOfTheMonth::create($request->all());

        return back()->with('success', 'Employee of the Month assigned successfully!');
    }

    public function destroy($id)
    {
        $record = EmployeeOfTheMonth::findOrFail($id);
        $record->delete();

        return back()->with('success', 'Employee of the Month record removed.');
    }
}
