<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Defaulters List Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #dc2626; /* Red color */
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .missed-days {
            color: #dc2626;
            font-weight: bold;
        }
        .recent-dates {
            color: #16a34a;
        }
        .never {
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Action Required: Continuous Missing Backups</h2>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Office</th>
                <th>Department</th>
                <th>Consecutive Days Missed</th>
                <th>Recent Backup Dates</th>
            </tr>
        </thead>
        <tbody>
            @forelse($defaulters as $defaulter)
                <tr>
                    <td>
                        <strong>{{ $defaulter['staff']->name }}</strong><br>
                        <span style="font-size: 10px; color: #666;">{{ $defaulter['staff']->email }}</span>
                    </td>
                    <td>{{ $defaulter['staff']->office->name ?? 'N/A' }}</td>
                    <td>{{ $defaulter['staff']->department->name ?? 'N/A' }}</td>
                    <td class="missed-days">{{ $defaulter['consecutive_missed'] }} Days</td>
                    <td>
                        @if($defaulter['recent_backups']->isEmpty())
                            <span class="never">Never taken a backup</span>
                        @else
                            <span class="recent-dates">
                                @php
                                    $dates = [];
                                    foreach ($defaulter['recent_backups'] as $date) {
                                        $dates[] = \Carbon\Carbon::parse($date)->format('d M');
                                    }
                                    echo implode(', ', $dates);
                                @endphp
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">No staff members have missed their backup for 3 or more consecutive days.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
