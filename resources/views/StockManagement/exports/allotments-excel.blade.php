<table>
    <thead>
        <tr>
            <th>Staff Name</th>
            <th>Designation</th>
            <th>Office</th>
            <th>Allotment Date</th>
            <th>Category</th>
            <th>Item Type</th>
            <th>Brand/Model</th>
            <th>Quantity</th>
            <th>Allotment Type</th>
            <th>Expected Return Date</th>
            <th>Status</th>
            <th>Return Date</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        @foreach($staffs as $staff)
            @foreach($staff->stockAllotments as $allot)
            <tr>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->designation ?? 'N/A' }}</td>
                <td>{{ $staff->office->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($allot->allotment_date)->format('d M, Y') }}</td>
                <td>{{ $allot->brand->item->category->name ?? 'N/A' }}</td>
                <td>{{ $allot->brand->item->name ?? 'N/A' }}</td>
                <td>{{ $allot->brand->name ?? 'N/A' }}</td>
                <td>{{ $allot->quantity }}</td>
                <td>{{ $allot->allotment_type }}</td>
                <td>{{ $allot->return_date ? \Carbon\Carbon::parse($allot->return_date)->format('d M, Y') : 'N/A' }}</td>
                <td>{{ $allot->status }}</td>
                <td>{{ $allot->returned_date ? \Carbon\Carbon::parse($allot->returned_date)->format('d M, Y') : 'N/A' }}</td>
                <td>{{ $allot->remark }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
