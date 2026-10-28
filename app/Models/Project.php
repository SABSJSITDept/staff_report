<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Office\OfficeModel;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'office_id',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function office()
    {
        return $this->belongsTo(OfficeModel::class, 'office_id');
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->latest();
    }

    public function messages()
    {
        return $this->hasMany(ProjectMessage::class)->oldest();
    }

    public function isOwner($userId): bool
    {
        return $this->created_by == $userId;
    }

    public function isMember($userId): bool
    {
        if ($this->created_by == $userId) {
            return true;
        }
        return $this->projectMembers()->where('user_id', $userId)->exists();
    }

    public function progressPercentage(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->tasks()->where('status', 'completed')->count();
        return (int) round(($completed / $total) * 100);
    }
}
