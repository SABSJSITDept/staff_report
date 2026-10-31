<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\StaffModel;

class DailyReportTaskOutput extends Model
{
    use HasFactory;

    protected $table = 'daily_report_task_outputs';

    protected $fillable = [
        'daily_report_task_id',
        'user_id',
        'staff_id',
        'action_type',
        'comment',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $appends = [
        'file_url',
    ];

    public function task()
    {
        return $this->belongsTo(DailyReportTask::class, 'daily_report_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'staff_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }
}

