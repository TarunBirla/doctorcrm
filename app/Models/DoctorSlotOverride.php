<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSlotOverride extends Model
{
    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'slot_date',
        'slot_time',
        'is_blocked',
        'reason',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'is_blocked' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
