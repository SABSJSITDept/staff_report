<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ITTicketReply extends Model
{
    use HasFactory;

    protected $table = 'it_ticket_replies';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachment',
    ];

    public function ticket()
    {
        return $this->belongsTo(ITTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
