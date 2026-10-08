<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_id',
        'follow_up_date',
        'follow_up_time',
        'reason',
        'notes',
        'status',
        'reminder_sent',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
