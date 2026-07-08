<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanyojak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'pravarti',
        'email',
        'password',
        'staff_assigned'
    ];

    protected $casts = [
        'staff_assigned' => 'array',
        'password' => 'hashed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
