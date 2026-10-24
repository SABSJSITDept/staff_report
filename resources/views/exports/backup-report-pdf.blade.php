<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Backup Report - {{ date("F", mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 8px;
        }
        .header h2 {
            margin: 0;
            color: #4f46e5;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #64748b;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 3px;
            text-align: center;
            font-size: 7.5px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            white-space: nowrap;
        }
        .staff-name-th, .staff-name-td {
            text-align: left;
            padding-left: 6px;
            font-weight: bold;
            white-space: nowrap;
        }
        .sr-col {
            width: 24px;
        }
        .sun-col {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-yes {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: bold;
        }
        .status-no {
            background-color: #ffe4e6;
            color: #be123c;
            font-weight: bold;
        }
        .status-nofill {
            background-color: #fef3c7;
            color: #b45309;
            font-weight: bold;
        }
        .status-dash {
            color: #94a3b8;
        }
        .legend {
            margin-top: 10px;
            display: inline-block;
            font-size: 8px;
        }
        .legend span {
            display: inline-block;
            padding: 2px 6px;
            margin-right: 8px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Backup Report - {{ date("F", mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}</h2>
        <p>Generated on: {{ date('d M Y, h:i A') }} | ABSJS IT Department</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="sr-col">SR</th>
                <th class="staff-name-th">STAFF NAME</th>
                @foreach($dates as $dateObj)
                    <th class="{{ $dateObj['is_sunday'] ? 'sun-col' : '' }}">
                        {{ $dateObj['display'] }}<br>
                        <small>({{ $dateObj['day_name'] }})</small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
                <tr>
                    <td class="sr-col">{{ $row['sr'] }}</td>
                    <td class="staff-name-td">
                        {{ $row['staff']->name }}
                        @if($row['staff']->office)
                            <br><span style="font-size: 6.5px; color: #64748b; font-weight: normal;">{{ $row['staff']->office->name }}</span>
                        @endif
                    </td>
                    @foreach($dates as $dateObj)
                        @php
                            $dStr = $dateObj['date_str'];
                            $val = $row['days'][$dStr] ?? '-';
                        @endphp
                        @if($val === 'YES')
                            <td class="status-yes">YES</td>
                        @elseif($val === 'NO')
                            <td class="status-no">NO</td>
                        @elseif($val === 'NO-FILL')
                            <td class="status-nofill">NO-FILL</td>
                        @else
                            <td class="status-dash">-</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 2 }}" style="text-align: center; padding: 20px;">
                        No records found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        <strong>Legend:</strong>
        <span class="status-yes">YES : Backup Taken</span>
        <span class="status-no">NO : Backup Failed / Not Taken</span>
        <span class="status-nofill">NO-FILL : No Entry Submitted</span>
    </div>
</body>
</html>
