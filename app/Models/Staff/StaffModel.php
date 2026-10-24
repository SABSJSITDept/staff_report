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

    /**
     * Calculate how many consecutive working days the staff has missed backup.
     * Missed means: either NO ENTRY was made, OR status was 'NO'.
     * Returns 0 if backup has been taken today with status 'YES'.
     */
    public function getConsecutiveMissedBackupDays(): int
    {
        $consecutiveMissed = 0;
        $dateCursor = today();

        // Calculate consecutive missed days backwards (up to 60 working days)
        for ($i = 0; $i < 60; $i++) {
            // Skip Sundays
            if ($dateCursor->isSunday()) {
                $dateCursor->subDay();
                continue;
            }

            $backup = \App\Models\SystemBackup::where('staff_id', $this->id)
                ->whereDate('backup_date', $dateCursor->toDateString())
                ->first();

            // If backup was taken with YES on this working day, stop counting
            if ($backup && $backup->status === 'YES') {
                break;
            }

            // Either no record exists (no entry) OR status is NO -> both count as no backup taken!
            $consecutiveMissed++;
            $dateCursor->subDay();
        }

        return $consecutiveMissed;
    }
}
