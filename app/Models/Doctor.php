<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'specialization',
        'qualification',
        'registration_no',
        'phone',
        'email',
        'consultation_fee',
        'bio',
        'signature_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function ownedClinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'doctor_clinics')->withPivot('is_primary')->withTimestamps();
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function slotOverrides()
    {
        return $this->hasMany(DoctorSlotOverride::class);
    }
}
