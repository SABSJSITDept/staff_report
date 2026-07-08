<?php

namespace App\Models\Staff;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Office\OfficeModel;
use App\Models\Office\DepartmentModel;

class StaffModel extends Model
{
    protected $table = 'staff_details';

    protected $fillable = [
        'user_id',
        'name',
        'f_name',
        'dob',
        'mobile',
        'email',
        'doj',
        'dept_id',
        'designation',
        'address',
        'office_id',
        'photo',
        'status',
        'left_date',
        'backup_sequence',
    ];

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'dept_id');
    }

    public function office()
    {
        return $this->belongsTo(OfficeModel::class, 'office_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function systemAllotment()
    {
        return $this->hasOne(\App\Models\SystemAllotment::class, 'staff_id');
    }

    public function systemBackups()
    {
        return $this->hasMany(\App\Models\SystemBackup::class, 'staff_id');
    }

    public function stockAllotments()
    {
        return $this->hasMany(\App\Models\StockAllotment::class, 'staff_id');
    }

    public function managedDepartment()
    {
        return $this->hasOne(DepartmentModel::class, 'hod_id');
    }

    public function isHod(): bool
    {
        return $this->managedDepartment()->exists();
    }
}
