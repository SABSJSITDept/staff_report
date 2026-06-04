<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_task_id',
        'user_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(DailyReportTask::class, 'daily_report_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
