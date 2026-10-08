<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'doctor_id',
        'name',
        'tagline',
        'doctor_name',
        'doctor_reg_no',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'website',
        'gst_number',
        'consultation_fee',
        'appointment_duration',
        'working_days',
        'working_hours',
        'break_hours',
        'prescription_header',
        'invoice_footer',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_clinics')->withPivot('is_primary')->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    public function slotOverrides()
    {
        return $this->hasMany(DoctorSlotOverride::class);
    }
}
