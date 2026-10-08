<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{
    protected $fillable = [
        'report_no',
        'patient_id',
        'visit_id',
        'report_name',
        'report_type',
        'report_date',
        'laboratory',
        'description',
        'file_path',
        'file_size',
        'doctor_notes',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public static function generateReportNo(): string
    {
        $count = self::count() + 1;
        return 'REP-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
