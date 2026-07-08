<?php

namespace App\Models\Office;

use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'status',
        'hod_id',
    ];

    public function hod()
    {
        return $this->belongsTo(\App\Models\Staff\StaffModel::class, 'hod_id');
    }
}
