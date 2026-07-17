<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\StaffModel;
use App\Models\Office\OfficeModel;

class EmployeeOfTheMonth extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'office_id',
        'month',
        'year',
        'description',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'staff_id');
    }

    public function office()
    {
        return $this->belongsTo(OfficeModel::class, 'office_id');
    }
}
