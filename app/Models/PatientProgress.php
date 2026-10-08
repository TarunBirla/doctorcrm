<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProgress extends Model
{
    protected $table = 'patient_progress';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'recorded_date',
        'weight',
        'bp_systolic',
        'bp_diastolic',
        'pulse',
        'temperature',
        'spo2',
        'bmi',
        'pain_level',
        'symptoms_assessment',
        'treatment_response',
        'doctor_notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'weight' => 'float',
        'temperature' => 'float',
        'bmi' => 'float',
        'bp_systolic' => 'integer',
        'bp_diastolic' => 'integer',
        'pulse' => 'integer',
        'spo2' => 'integer',
        'pain_level' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
