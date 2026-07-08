<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverallRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'remark',
        'remark_given_by_id',
        'financial_year',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function remarkGivenBy()
    {
        return $this->belongsTo(User::class, 'remark_given_by_id');
    }
}
