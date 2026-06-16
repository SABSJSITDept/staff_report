<?php

namespace App\Exports;

use App\Models\Staff\StaffModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffAllotmentExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        $user = auth()->user();
        $query = StaffModel::whereHas('stockAllotments')
            ->with(['stockAllotments.brand.item.category', 'office']);
            
        if (!$user->isAdmin() && $user->staff) {
            $query->where('office_id', $user->staff->office_id);
        }
        
        $staffs = $query->get();

        return view('StockManagement.exports.allotments-excel', [
            'staffs' => $staffs
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
