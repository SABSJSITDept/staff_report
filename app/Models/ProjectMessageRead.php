<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMessageRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_message_id',
        'user_id',
        'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(ProjectMessage::class, 'project_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

