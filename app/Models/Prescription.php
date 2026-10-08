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
        'prescription_date',
        'diagnosis_summary',
        'advice',
        'follow_up_date',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public static function generatePrescriptionNo(): string
    {
        $dateStr = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'RX-' . $dateStr . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
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
