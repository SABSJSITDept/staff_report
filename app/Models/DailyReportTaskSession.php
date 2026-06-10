<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportTaskSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_task_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function dailyReportTask()
    {
        return $this->belongsTo(DailyReportTask::class);
    }
}
