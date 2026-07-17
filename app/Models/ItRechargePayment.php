<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItRechargePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'it_recharge_id',
        'amount_paid',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
    ];

    public function recharge()
    {
        return $this->belongsTo(ItRecharge::class, 'it_recharge_id');
    }
}
