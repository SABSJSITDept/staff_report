<!DOCTYPE html>
<html>
<head>
    <title>New Employee Rating Submitted</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .overall { margin-top: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <h2>Employee Rating</h2>
    <p>जय जिनेन्द्र,</p>
    <p>A new rating has been submitted for <strong>{{ $staff->name }}</strong> by <strong>{{ $raterName }}</strong>.</p>
    
    <div style="background-color: #eef2ff; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <strong style="color: #4f46e5;">Average Rating:</strong> 
        <span style="font-size: 1.2em; font-weight: bold;">{{ $averageRating }} / 5</span>
    </div>

    <p>Here are the details:</p>

    @if(!empty($ratingDetails))
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Rating (Out of 5)</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ratingDetails as $detail)
            <tr>
                <td>{{ $detail['question'] }}</td>
                <td><strong>{{ $detail['rating'] }}</strong></td>
                <td>{{ $detail['remark'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(!empty($overallRemark))
    <div class="overall">
        <strong>Overall Remark:</strong><br>
        {{ $overallRemark }}
    </div>
    @endif

    <br>
    <p>साधुवाद, जय जिनेन्द्र,</p>
    <p>श्री अखिल भारतवर्षीय साधुमार्गी जैन संघ</p>
</body>
</html>
