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

    public function outputs()
    {
        return $this->hasMany(DailyReportTaskOutput::class, 'daily_report_task_id')->orderBy('created_at', 'desc');
    }

    /**
     * Calculate the earliest auto-stop deadline for a session starting at $sessionStart.
     * Rules:
     * 1. 2 hours after start.
     * 2. 1:00 PM (13:00) midday cutoff.
     * 3. 6:30 PM (18:30) evening cutoff.
     */
    public static function calculateAutoStopDeadline(\Carbon\Carbon $sessionStart): array
    {
        // 1. Two hours continuous limit
        $c2h = $sessionStart->copy()->addHours(2);

        // 2. 1:00 PM (13:00) midday cutoff
        $cLunch = $sessionStart->copy()->setTime(13, 0, 0);

        // 3. 6:30 PM (18:30) evening cutoff
        $cEvening = $sessionStart->copy()->setTime(18, 30, 0);

        $candidates = [
            [
                'deadline' => $c2h,
                'reason' => '2-hour continuous limit reached',
                'type' => '2_hours',
                'label' => '2-Hour Limit'
            ]
        ];

        if ($cLunch->greaterThan($sessionStart)) {
            $candidates[] = [
                'deadline' => $cLunch,
                'reason' => '1:00 PM midday cutoff reached',
                'type' => 'lunch_cutoff',
                'label' => '1:00 PM Cutoff'
            ];
        }

        if ($cEvening->greaterThan($sessionStart)) {
            $candidates[] = [
                'deadline' => $cEvening,
                'reason' => '6:30 PM evening cutoff reached',
                'type' => 'evening_cutoff',
                'label' => '6:30 PM Cutoff'
            ];
        }

        // Sort by deadline ascending
        usort($candidates, function ($a, $b) {
            return $a['deadline']->timestamp <=> $b['deadline']->timestamp;
        });

        return $candidates[0];
    }

    /**
     * Get auto-stop info for this task if currently in-progress.
     */
    public function getAutoStopDeadlineInfo(): ?array
    {
        if ($this->status !== 'in_progress') {
            return null;
        }

        $session = $this->sessions()->whereNull('end_time')->latest('start_time')->first();
        $sessionStart = $session && $session->start_time ? $session->start_time : ($this->start_time ?: $this->created_at);

        if (!$sessionStart) {
            return null;
        }

        return static::calculateAutoStopDeadline($sessionStart);
    }

    /**
     * Check if this task was stopped (completed or auto-stopped) without submitting work outcome.
     */
    public function needsStopOutput(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        // Only enforce for tasks tracked today
        if ($this->dailyReport && $this->dailyReport->report_date && !\Carbon\Carbon::parse($this->dailyReport->report_date)->isToday()) {
            return false;
        }

        // Check if there is an output of action_type = 'stop'
        return !$this->outputs()->where('action_type', 'stop')->exists();
    }

    /**
     * Deprecated: Pauses no longer require output.
     */
    public function needsPauseOutput(): bool
    {
        return false;
    }
}
