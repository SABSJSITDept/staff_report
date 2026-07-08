<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RatingReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $groupedData;

    public function __construct($groupedData)
    {
        $this->groupedData = $groupedData;
    }

    public function view(): View
    {
        return view('ratings.export', [
            'groupedData' => $this->groupedData
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
