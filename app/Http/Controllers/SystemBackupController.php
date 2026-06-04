<?php

namespace App\Http\Controllers;

use App\Models\SystemBackup;
use Illuminate\Http\Request;

class SystemBackupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $staff = auth()->user()->staff;
        if (!$staff) {
            return back()->with('error', 'Staff profile not found.');
        }

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $backups = SystemBackup::where('staff_id', $staff->id)
            ->whereYear('backup_date', $year)
            ->whereMonth('backup_date', $month)
            ->orderBy('backup_date', 'desc')
            ->get();

        return view('Staff.StaffBackupView', compact('backups', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = auth()->user()->staff;
        if (!$staff) {
            return back()->with('error', 'Staff profile not found.');
        }

        $locations = \App\Models\BackupLocation::all();
        $todayBackup = SystemBackup::where('staff_id', $staff->id)
            ->whereDate('backup_date', today())
            ->first();

        return view('Staff.StaffBackupCreate', compact('locations', 'todayBackup'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $staff = auth()->user()->staff;
        if (!$staff) {
            return back()->with('error', 'Staff profile not found.');
        }

        $data = $request->validate([
            'status' => 'required|in:YES,NO',
            'location' => 'nullable|required_if:status,YES|array',
            'location.*' => 'string',
            'remark' => 'nullable|string',
        ]);

        if (isset($data['location']) && is_array($data['location'])) {
            $data['location'] = implode(', ', $data['location']);
        }

        $data['staff_id'] = $staff->id;
        $data['backup_date'] = today();

        SystemBackup::updateOrCreate(
            ['staff_id' => $staff->id, 'backup_date' => today()],
            $data
        );

        return redirect()->route('staff.daily-backup.index')->with('success', 'Daily backup status submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemBackup $systemBackup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemBackup $systemBackup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemBackup $systemBackup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemBackup $systemBackup)
    {
        //
    }
}
