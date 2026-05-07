<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Staff Name',
            'Report Date',
            'Task Title',
            'Task Description',
            'Status',
            'Time Spent',
            'Total Day Time',
            'Pending Work (Next Day)',
            'Planned Work (Next Day)',
            'Comments'
        ];
    }

    public function map($report): array
    {
        $rows = [];
        $first = true;

        $totalMinutes = 0;
        foreach($report->tasks as $task) {
            $ts = strtolower($task->time_spend);
            if (preg_match('/(\d+)\s*h/', $ts, $m)) $totalMinutes += $m[1] * 60;
            if (preg_match('/(\d+)\s*m/', $ts, $m)) $totalMinutes += $m[1];
            if (preg_match('/(\d+):(\d+)/', $ts, $m)) $totalMinutes += $m[1] * 60 + $m[2];
        }
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;
        $totalDayStr = ($h > 0 ? $h.'h ' : '') . ($m > 0 ? $m.'m' : '') ?: '—';

        if ($report->tasks->isEmpty()) {
            return [[
                $report->id,
                $report->staff->name ?? '—',
                $report->report_date->format('d-m-Y'),
                '—',
                '—',
                '—',
                '—',
                $totalDayStr,
                $report->pending_task,
                $report->planned_task,
                $report->comments
            ]];
        }

        foreach ($report->tasks as $task) {
            $rows[] = [
                $first ? $report->id : '',
                $first ? ($report->staff->name ?? '—') : '',
                $first ? $report->report_date->format('d-m-Y') : '',
                $task->task_title,
                $task->description,
                ucfirst($task->status),
                $task->time_spend,
                $first ? $totalDayStr : '',
                $first ? $report->pending_task : '',
                $first ? $report->planned_task : '',
                $first ? $report->comments : '',
            ];
            $first = false;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }
}
