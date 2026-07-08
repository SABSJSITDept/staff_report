<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rating Reports</title>
    <style>
        body { font-family: notosansdevanagari, sans-serif; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; word-wrap: break-word; white-space: normal; }
        .table th { background-color: #ffffff; font-weight: bold; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold; }
        .rater-name { font-weight: bold; font-size: 13px; color: #000; }
        .bg-header { background-color: #ffffff; color: #000000; }
        .bg-light { background-color: #ffffff; }
    </style>
</head>
<body>
    @forelse($groupedData as $staffId => $staffData)
    @php
        $cols = 3 + (count($staffData['raters']) * 2);
    @endphp
    <div style="page-break-inside: avoid;">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="{{ $cols }}" class="text-center bg-header" style="font-size: 16px; padding: 10px;">
                        Staff: {{ $staffData['staff_name'] }}
                        @if(!empty($staffData['financial_years']))
                            <br><span style="font-size: 12px; font-weight: normal; color: #555;">(Session: {{ implode(', ', $staffData['financial_years']) }})</span>
                        @endif
                    </th>
                </tr>
                <tr>
                    <th class="bg-light">Category</th>
                    <th class="bg-light">Question</th>
                    @foreach($staffData['raters'] as $rater)
                        <th class="bg-light"><span class="rater-name">{{ $rater }}</span><br>(Rating)</th>
                        <th class="bg-light"><span class="rater-name">{{ $rater }}</span><br>(Remark)</th>
                    @endforeach
                    <th class="bg-light">Average</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $sr = 1; 
                    $totalStaffSum = 0;
                    $totalStaffCount = 0;
                @endphp
                @foreach($staffData['categories'] as $categoryId => $catData)
                    @php 
                        $isFirstQuestionInCategory = true; 
                        $rowspan = count($catData['questions']);
                    @endphp
                    @foreach($catData['questions'] as $questionId => $qData)
                        <tr>
                            @if($isFirstQuestionInCategory)
                                <td rowspan="{{ $rowspan }}" style="vertical-align: middle; background-color: #f9fafb;">
                                    <b>{{ $catData['category_name'] }}</b>
                                </td>
                            @endif
                            
                            <td class="text-center">{{ $sr++ }}. {{ $qData['question_text'] }}</td>

                            @php
                                $sum = 0;
                                $count = 0;
                            @endphp
                            @foreach($staffData['raters'] as $rater)
                                @php
                                    $r = $qData['ratings_by_rater'][$rater] ?? null;
                                    if ($r) {
                                        $sum += $r['rating'];
                                        $count++;
                                        $totalStaffSum += $r['rating'];
                                        $totalStaffCount++;
                                    }
                                @endphp
                                <td class="text-center font-bold">{{ $r ? $r['rating'] : '-' }}</td>
                                <td>{{ $r && $r['remark'] ? $r['remark'] : '-' }}</td>
                            @endforeach

                            <td class="text-center font-bold">
                                {{ $count > 0 ? round($sum / $count, 1) : '-' }}
                            </td>
                        </tr>
                        @php $isFirstQuestionInCategory = false; @endphp
                    @endforeach
                @endforeach
                
                {{-- Overall Average Row --}}
                <tr>
                    <td colspan="{{ $cols - 1 }}" class="text-center font-bold bg-light" style="font-size: 14px;">
                        Overall Average Rating (out of 5):
                    </td>
                    <td class="text-center font-bold bg-light" style="font-size: 15px; color: #16a34a;">
                        {{ $totalStaffCount > 0 ? round($totalStaffSum / $totalStaffCount, 1) : '-' }}
                    </td>
                </tr>

                {{-- Overall Remarks Rows --}}
                <tr>
                    <td colspan="{{ $cols }}" class="text-center font-bold" style="background-color: #e5e7eb; font-size: 14px; padding: 10px;">
                        Overall Remarks:
                    </td>
                </tr>
                @forelse($staffData['overall_remarks'] as $rmk)
                    <tr>
                        <td colspan="2" class="text-center font-bold bg-light" style="vertical-align: middle;">
                            <span class="rater-name">{{ $rmk['rater'] }}</span>
                        </td>
                        <td colspan="{{ $cols - 2 }}" class="text-center" style="vertical-align: middle;">
                            {{ $rmk['remark'] ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $cols }}" class="text-center">None</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <br>
    </div>
    @empty
        <p>No ratings found.</p>
    @endforelse
</body>
</html>
