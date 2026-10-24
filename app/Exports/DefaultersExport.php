<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DefaultersExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $defaulters;

    public function __construct($defaulters)
    {
        $this->defaulters = $defaulters;
    }

    public function collection()
    {
        return $this->defaulters;
    }

    public function headings(): array
    {
        return [
            'Staff Name',
            'Email',
            'Office',
            'Department',
            'Consecutive Days Missed',
            'Recent Backup Dates'
        ];
    }

    public function map($defaulter): array
    {
        $recentBackups = 'Never taken a backup';
        if ($defaulter['recent_backups']->isNotEmpty()) {
            $dates = [];
            foreach ($defaulter['recent_backups'] as $date) {
                $dates[] = \Carbon\Carbon::parse($date)->format('d M Y');
            }
            $recentBackups = implode(', ', $dates);
        }

        return [
            $defaulter['staff']->name,
            $defaulter['staff']->email,
            $defaulter['staff']->office->name ?? 'N/A',
            $defaulter['staff']->department->name ?? 'N/A',
            $defaulter['consecutive_missed'] . ' Days',
            $recentBackups,
        ];
    }
}
