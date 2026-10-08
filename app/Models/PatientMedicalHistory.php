<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMedicalHistory extends Model
{
    protected $fillable = [
        'patient_id',
        'conditions',
        'allergies',
        'surgeries',
        'family_history',
        'current_medications',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
