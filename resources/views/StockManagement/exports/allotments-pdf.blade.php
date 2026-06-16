<!DOCTYPE html>
<html>
<head>
    <title>Staff Allotment Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .staff-header { background-color: #eef2ff; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-bottom: none; font-size: 13px;}
        .staff-name { font-weight: bold; color: #4338ca; }
        .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 9px; font-weight: bold;}
        .badge-active { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .badge-returned { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-temp { background-color: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .badge-perm { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .small-text { font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; color: #1f2937;">Staff-wise IT Asset Allotment Report</h2>
        <p style="margin: 5px 0 0 0; color: #6b7280;">Generated on: {{ date('d M, Y H:i') }}</p>
    </div>

    @foreach($staffs as $staff)
        <div class="staff-header">
            <span class="staff-name">{{ $staff->name }}</span> 
            <span style="color:#666;">({{ $staff->designation ?? 'Staff' }}) - {{ $staff->office->name ?? 'N/A' }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="15%">Date</th>
                    <th width="35%">Item Details</th>
                    <th width="10%">Qty</th>
                    <th width="20%">Allotment Type</th>
                    <th width="20%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff->stockAllotments as $allot)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($allot->allotment_date)->format('d M, Y') }}
                    </td>
                    <td>
                        <strong>{{ $allot->brand->item->name ?? 'Deleted Item' }}</strong><br>
                        {{ $allot->brand->name ?? 'N/A' }}<br>
                        <span class="small-text">{{ $allot->brand->item->category->name ?? 'N/A' }}</span>
                        @if($allot->remark)
                            <br><span class="small-text" style="font-style: italic;">"{{ $allot->remark }}"</span>
                        @endif
                    </td>
                    <td>{{ $allot->quantity }}</td>
                    <td>
                        <span class="badge {{ $allot->allotment_type == 'Temporary' ? 'badge-temp' : 'badge-perm' }}">{{ $allot->allotment_type }}</span>
                        @if($allot->return_date)
                            <br><span class="small-text">Expected: {{ \Carbon\Carbon::parse($allot->return_date)->format('d M, Y') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($allot->status === 'Returned')
                            <span class="badge badge-returned">Returned</span>
                            @if($allot->returned_date)
                                <br><span class="small-text">On: {{ \Carbon\Carbon::parse($allot->returned_date)->format('d M, Y') }}</span>
                            @endif
                        @else
                            <span class="badge badge-active">Active</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">No allotments found for this staff member.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
