<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isSanyojak(): bool
    {
        return $this->role === 'sanyojak';
    }

    public function isKaryalaySanyojak(): bool
    {
        return $this->role === 'karyalay_sanyojak';
    }

    public function isPst(): bool
    {
        return $this->role === 'pst';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function staff()
    {
        return $this->hasOne(\App\Models\Staff\StaffModel::class, 'user_id');
    }

    public function sanyojak()
    {
        return $this->hasOne(\App\Models\Sanyojak::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class)->latest();
    }

    public function canAccessIT(): bool
    {
        if ($this->role === 'IT DEPARTMENT') {
            return true;
        }
        
        if ($this->staff && $this->staff->department && $this->staff->department->name === 'IT DEPARTMENT') {
            return true;
        }
        
        return false;
    }
}
