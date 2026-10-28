<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_task_id',
        'user_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
