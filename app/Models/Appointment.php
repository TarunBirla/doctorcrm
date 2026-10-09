<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'appointment_no',
        'patient_id',
        'doctor_id',
        'clinic_id',
        'category_id',
        'appointment_date',
        'treatment_days',
        'daily_fee',
        'recovery_percentage',
        'extended_days',
        'recovery_status',
        'recovery_notes',
        'appointment_time',
        'end_time',
        'appointment_type',
        'token_number',
        'reason',
        'notes',
        'consultation_fee',
        'payment_status',
        'status',
        'waiting_since',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'treatment_days' => 'integer',
        'daily_fee' => 'decimal:2',
        'consultation_fee' => 'decimal:2',
        'recovery_percentage' => 'integer',
        'extended_days' => 'integer',
        'waiting_since' => 'datetime',
    ];

    public function getTotalDaysAttribute(): int
    {
        return (int) ($this->treatment_days + ($this->extended_days ?? 0));
    }

    public function getRecoveryLevelLabel(): string
    {
        $pct = (int) ($this->recovery_percentage ?? 0);
        if ($pct >= 100) return 'Fully Recovered (100%)';
        if ($pct >= 75) return 'Significant Improvement (' . $pct . '%)';
        if ($pct >= 50) return 'Moderate Improvement (' . $pct . '%)';
        if ($pct >= 25) return 'Mild Recovery (' . $pct . '%)';
        if ($pct > 0) return 'Slight Progress (' . $pct . '%)';
        return 'Initial Assessment (0%)';
    }

    public static function generateAppointmentNo(): string
    {
        $dateStr = now()->format('Ymd');
        $countToday = self::withTrashed()->whereDate('created_at', now()->toDateString())->count() + 1;
        return 'APT-' . $dateStr . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
    }

    public static function nextTokenForDate($date, $doctorId = null): int
    {
        $query = self::where('appointment_date', $date);
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        return ($query->max('token_number') ?? 0) + 1;
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function category()
    {
        return $this->belongsTo(TreatmentCategory::class, 'category_id');
    }

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function scopeToday($query)
    {
        return $query->where('appointment_date', now()->toDateString());
    }

    public function scopeQueue($query)
    {
        return $query->where('appointment_date', now()->toDateString())
                     ->whereIn('status', ['waiting', 'in_consultation', 'scheduled', 'confirmed'])
                     ->orderByRaw("CASE WHEN status = 'in_consultation' THEN 1 WHEN status = 'waiting' THEN 2 ELSE 3 END")
                     ->orderBy('token_number', 'asc');
    }
}
