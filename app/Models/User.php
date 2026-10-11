<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
        'phone',
        'position',
        'avatar',
        'is_active',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor' || $this->role === 'super_admin';
    }

    public function isReceptionist(): bool
    {
        return in_array($this->role, ['receptionist', 'staff']);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['receptionist', 'staff']);
    }

    public static function isStaffRole(?string $role): bool
    {
        return in_array($role, ['receptionist', 'staff']);
    }

    public function getEffectiveStatusAttribute(): string
    {
        if (!empty($this->status)) {
            return $this->status;
        }
        return $this->is_active ? 'active' : 'inactive';
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function parentDoctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
