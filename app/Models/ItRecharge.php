<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItRecharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'purpose',
        'duration_months',
        'last_date',
        'amount',
        'payment_type',
        'mode',
        'details',
    ];

    protected $casts = [
        'last_date' => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(ItRechargePayment::class)->orderBy('paid_at', 'desc');
    }
}
