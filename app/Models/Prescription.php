<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'prescription_no',
        'visit_id',
        'patient_id',
        'doctor_id',
        'clinic_id',
        'prescription_date',
        'assessment_type',
        'assessment_data',
        'prescribed_exercises',
        'modalities',
        'treatment_days',
        'diagnosis_summary',
        'advice',
        'follow_up_date',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'follow_up_date' => 'date',
        'assessment_data' => 'array',
        'prescribed_exercises' => 'array',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public static function generatePrescriptionNo(): string
    {
        $prefix = 'RX-' . now()->format('Ymd') . '-';
        
        $latest = self::where('prescription_no', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(prescription_no) DESC, prescription_no DESC')
            ->first();

        if ($latest && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $latest->prescription_no, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        $prescriptionNo = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

        while (self::where('prescription_no', $prescriptionNo)->exists()) {
            $nextSeq++;
            $prescriptionNo = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        }

        return $prescriptionNo;
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
