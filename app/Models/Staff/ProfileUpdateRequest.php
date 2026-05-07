<?php

namespace App\Models\Staff;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'requested_data',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'staff_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}
