<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #4F46E5; }
        .header h1 { color: #4F46E5; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .report-section { margin-bottom: 25px; page-break-inside: avoid; }
        .staff-info { background: #F3F4F6; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .staff-info table { width: 100%; border-collapse: collapse; }
        .staff-info td { padding: 5px; }
        .label { font-weight: bold; color: #4B5563; }
        table.tasks { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.tasks th, table.tasks td { border: 1px solid #E5E7EB; padding: 8px; text-align: left; }
        table.tasks th { background: #F9FAFB; font-weight: bold; color: #374151; }
        .status-badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-completed { background: #D1FAE5; color: #065F46; }
        .status-progress { background: #DBEAFE; color: #1E40AF; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; }
        .meta-box { margin-top: 10px; padding: 8px; border: 1px solid #E5E7EB; border-radius: 5px; background: #fff; }
        .meta-box h4 { margin: 0 0 5px 0; font-size: 11px; color: #4F46E5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>STAFF DAILY ACTIVITY REPORT</h1>
        @if($start && $end)
            <p>From: {{ date('d M Y', strtotime($start)) }} To: {{ date('d M Y', strtotime($end)) }}</p>
        @elseif($start)
            <p>From: {{ date('d M Y', strtotime($start)) }} onwards</p>
        @else
            <p>Generated on {{ date('d M Y, h:i A') }}</p>
        @endif
    </div>

    @foreach($reports as $report)
        <div class="report-section">
            <div class="staff-info">
                @php
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
                @endphp
                <table>
                    <tr>
                        <td width="40%"><span class="label">Staff:</span> {{ $report->staff->name ?? '—' }}</td>
                        <td width="35%"><span class="label">Date:</span> {{ $report->report_date->format('d M Y (l)') }}</td>
                        <td width="25%"><span class="label">Total Time:</span> {{ $totalDayStr }}</td>
                    </tr>
                </table>
            </div>

            @if($report->tasks->count() > 0)
                <table class="tasks">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">Task Title</th>
                            <th width="40%">Description</th>
                            <th width="10%">Time</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->tasks as $index => $task)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $task->task_title }}</td>
                                <td>{{ $task->description ?? '—' }}</td>
                                <td>{{ $task->time_spend ?? '—' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($task->status) {
                                            'completed' => 'status-completed',
                                            'in_progress' => 'status-progress',
                                            default => 'status-pending',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $task->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #999; font-style: italic;">No tasks reported for this day.</p>
            @endif

            <div class="grid" style="display: flex; margin-top: 10px;">
                @if($report->pending_task)
                <div class="meta-box">
                    <h4>Yesterday's Pending Work</h4>
                    <p>{{ $report->pending_task }}</p>
                </div>
                @endif
                
                @if($report->planned_task)
                <div class="meta-box">
                    <h4>Tomorrow's Planned Work</h4>
                    <p>{{ $report->planned_task }}</p>
                </div>
                @endif

                @if($report->comments)
                <div class="meta-box">
                    <h4>Comments</h4>
                    <p>{{ $report->comments }}</p>
                </div>
                @endif
            </div>
            
            <hr style="border: 0.5px dashed #E5E7EB; margin: 20px 0;">
        </div>
    @endforeach

    <div class="footer">
        Generated by Staff Portal | Page {PAGE_NUM}
    </div>
</body>
</html>
