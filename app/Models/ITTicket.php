<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ITTicket extends Model
{
    use HasFactory;

    protected $table = 'it_tickets';

    protected $fillable = [
        'staff_id',
        'it_staff_id',
        'category',
        'subject',
        'issue_description',
        'photos',
        'status',
        'expected_arrival_time',
        'remarks',
    ];

    protected $casts = [
        'expected_arrival_time' => 'datetime',
        'photos'                => 'array',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function itStaff()
    {
        return $this->belongsTo(User::class, 'it_staff_id');
    }

    public function replies()
    {
        return $this->hasMany(ITTicketReply::class, 'ticket_id');
    }
}
