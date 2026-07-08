<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'financial_year',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
