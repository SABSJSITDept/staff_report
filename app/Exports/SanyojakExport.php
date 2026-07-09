<?php

namespace App\Exports;

use App\Models\Sanyojak;
use App\Models\Staff\StaffModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SanyojakExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Sanyojak::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Pravarti',
            'Email',
            'Password',
            'Staff Allotted',
        ];
    }

    public function map($sanyojak): array
    {
        $staffNames = [];
        $type = $sanyojak->user ? $sanyojak->user->role : 'sanyojak';
        
        if ($type === 'karyalay_sanyojak') {
            $staffNames = ['All Staff (Global)'];
        } elseif (!empty($sanyojak->staff_assigned) && is_array($sanyojak->staff_assigned)) {
            $staffs = StaffModel::whereIn('id', $sanyojak->staff_assigned)->pluck('name')->toArray();
            $staffNames = $staffs;
        }

        return [
            $sanyojak->name,
            $sanyojak->pravarti,
            $sanyojak->email,
            '********', // Hashed in database
            implode(', ', $staffNames)
        ];
    }
}
