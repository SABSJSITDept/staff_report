<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'due_date',
        'priority',
        'status',
        'last_completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'last_completed_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isDaily(): bool
    {
        return $this->type === 'daily';
    }

    public function isScheduled(): bool
    {
        return $this->type === 'scheduled';
    }

    public function isCompletedForToday(): bool
    {
        if ($this->isDaily()) {
            return $this->last_completed_at && $this->last_completed_at->isToday();
        }
        return $this->status === 'completed';
    }

    public function scopeDaily($query)
    {
        return $query->where('type', 'daily');
    }

    public function scopeScheduled($query)
    {
        return $query->where('type', 'scheduled');
    }
}
