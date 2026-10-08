<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'age',
        'mobile',
        'alt_mobile',
        'email',
        'address',
        'city',
        'state',
        'country',
        'emergency_contact',
        'emergency_phone',
        'blood_group',
        'occupation',
        'marital_status',
        'profile_photo',
        'referral_source',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'age' => 'integer',
    ];

    public static function generatePatientId(): string
    {
        $lastPatient = self::withTrashed()->orderBy('id', 'desc')->first();
        $nextNumber = $lastPatient ? ($lastPatient->id + 1) : 1;
        return 'PAT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function medicalHistory()
    {
        return $this->hasOne(PatientMedicalHistory::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->orderBy('appointment_date', 'desc')->orderBy('appointment_time', 'desc');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class)->orderBy('visit_date', 'desc');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class)->orderBy('prescription_date', 'desc');
    }

    public function reports()
    {
        return $this->hasMany(MedicalReport::class)->orderBy('report_date', 'desc');
    }

    public function progressRecords()
    {
        return $this->hasMany(PatientProgress::class)->orderBy('recorded_date', 'desc');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class)->orderBy('follow_up_date', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class)->orderBy('invoice_date', 'desc');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class)->orderBy('payment_date', 'desc');
    }

    public function communications()
    {
        return $this->hasMany(PatientCommunication::class)->orderBy('sent_at', 'desc');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->invoices()->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->sum('due_amount');
    }

    public function getLastVisitAttribute()
    {
        return $this->visits()->first();
    }

    public function getNextAppointmentAttribute()
    {
        return $this->appointments()
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['scheduled', 'confirmed', 'waiting'])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->first();
    }
}
