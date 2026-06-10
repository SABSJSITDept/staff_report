<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyReportTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'assigned_by',
        'source_task_id',
        'task_title',
        'description',
        'is_carry',
        'previous_time',
        'status',
        'time_spend',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'daily_report_task_id')->orderBy('created_at', 'asc');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function sessions()
    {
        return $this->hasMany(DailyReportTaskSession::class, 'daily_report_task_id')->orderBy('start_time', 'asc');
    }
}
