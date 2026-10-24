<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BackupReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $reportData;
    protected $dates;

    public function __construct(array $reportData, array $dates)
    {
        $this->reportData = $reportData;
        $this->dates = $dates;
    }

    public function collection()
    {
        return collect($this->reportData)->map(function ($row) {
            $rowValues = [
                $row['sr'],
                $row['staff']->name ?? 'Unknown',
                $row['staff']->office->name ?? '-',
            ];

            foreach ($this->dates as $dateObj) {
                $dStr = $dateObj['date_str'];
                $rowValues[] = $row['days'][$dStr] ?? '-';
            }

            return $rowValues;
        });
    }

    public function headings(): array
    {
        $headings = ['SR', 'STAFF NAME', 'OFFICE'];
        foreach ($this->dates as $dateObj) {
            $headings[] = $dateObj['display'];
        }
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}
