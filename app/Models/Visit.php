<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'visit_no',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'visit_date',
        'visit_type',
        'chief_complaint',
        'symptoms',
        'diagnosis_summary',
        'vitals_json',
        'clinical_notes',
        'treatment_plan',
        'follow_up_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'follow_up_date' => 'date',
        'vitals_json' => 'array',
    ];

    public static function generateVisitNo(): string
    {
        $dateStr = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'VST-' . $dateStr . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(VisitDiagnosis::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function progress()
    {
        return $this->hasOne(PatientProgress::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function reports()
    {
        return $this->hasMany(MedicalReport::class);
    }
}
