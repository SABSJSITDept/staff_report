<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\StaffModel;
use App\Models\User;

class BackupDefaulterMailLog extends Model
{
    protected $fillable = [
        'staff_id',
        'sent_by'
    ];

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'staff_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
