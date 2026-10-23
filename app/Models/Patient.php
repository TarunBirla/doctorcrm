<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'category_id',
        'recovery_percentage',
        'created_by_user_id',
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
        'description',
    ];

    protected $casts = [
        'dob' => 'date',
        'age' => 'integer',
        'recovery_percentage' => 'integer',
    ];

    public static function generatePatientId(): string
    {
        $lastPatient = self::withTrashed()->orderBy('id', 'desc')->first();
        $nextNumber = $lastPatient ? ($lastPatient->id + 1) : 1;
        $patientId = 'PAT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        while (self::withTrashed()->where('patient_id', $patientId)->exists()) {
            $nextNumber++;
            $patientId = 'PAT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }
        return $patientId;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function medicalHistory()
    {
        return $this->hasOne(PatientMedicalHistory::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->orderBy('appointment_date', 'asc')->orderBy('id', 'asc');
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

    /**
     * Extend patient's physiotherapy treatment by +N days (creating N new daily appointments skipping Sundays),
     * update invoice total & due amount, and sync recovery percentage & progress logs.
     */
    public function extendTreatmentAndSyncRecovery(array $options = []): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($options) {
            $addDays = max(0, (int) ($options['additional_days'] ?? 0));
            $recoveryPct = isset($options['recovery_percentage']) && $options['recovery_percentage'] !== ''
                ? max(0, min(100, (int) $options['recovery_percentage']))
                : (int) ($this->recovery_percentage ?? 0);
            $recoveryNotes = $options['recovery_notes'] ?? null;
            $markPreviousCompleted = !empty($options['mark_previous_completed']);

            $activeAppointments = $this->appointments()
                ->whereNotIn('status', ['cancelled'])
                ->orderBy('appointment_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($markPreviousCompleted && $activeAppointments->isNotEmpty()) {
                foreach ($activeAppointments as $prevApt) {
                    if ($prevApt->status !== 'completed') {
                        $prevApt->update(['status' => 'completed']);
                    }
                }
            }

            $lastApt = $activeAppointments->last();
            $doctorId = $options['doctor_id'] ?? $lastApt?->doctor_id ?? $this->doctor_id ?? \App\Models\Doctor::active()->first()?->id;
            $clinicId = $options['clinic_id'] ?? $lastApt?->clinic_id ?? $this->clinic_id ?? \App\Models\Clinic::where('is_active', true)->first()?->id;
            $categoryId = $options['category_id'] ?? $lastApt?->category_id ?? $this->category_id ?? \App\Models\TreatmentCategory::active()->first()?->id;

            $clinicObj = $clinicId ? \App\Models\Clinic::find($clinicId) : null;
            $dailyFee = isset($options['daily_fee']) && $options['daily_fee'] !== '' && (float) $options['daily_fee'] > 0
                ? (float) $options['daily_fee']
                : (float) ($lastApt?->daily_fee ?? $lastApt?->consultation_fee ?? $clinicObj?->consultation_fee ?? 500.00);

            // Sync recovery percentage on Patient
            $this->recovery_percentage = $recoveryPct;
            if ($clinicId) $this->clinic_id = $clinicId;
            if ($categoryId) $this->category_id = $categoryId;
            $this->save();

            // Sync recovery percentage on existing appointments of this patient
            if ($lastApt) {
                $lastApt->recovery_percentage = $recoveryPct;
                $lastApt->recovery_status = $lastApt->getRecoveryLevelLabel();
                if ($recoveryNotes !== null) {
                    $lastApt->recovery_notes = $recoveryNotes;
                }
                $lastApt->save();

                $this->appointments()
                    ->whereNotIn('status', ['cancelled'])
                    ->update([
                        'recovery_percentage' => $recoveryPct,
                        'recovery_status' => $lastApt->getRecoveryLevelLabel(),
                    ]);
            }

            $createdAppointments = [];
            $existingCount = $activeAppointments->count();

            // Create +N new daily appointments skipping Sundays
            if ($addDays > 0) {
                if (!empty($options['start_date'])) {
                    $currentDate = \Carbon\Carbon::parse($options['start_date'])->startOfDay();
                } elseif ($lastApt && $lastApt->appointment_date) {
                    $currentDate = \Carbon\Carbon::parse($lastApt->appointment_date)->addDay()->startOfDay();
                    if ($currentDate->lt(now()->startOfDay())) {
                        // Keep sequential after last appointment date unless user specified start_date
                    }
                } else {
                    $currentDate = now()->startOfDay();
                }

                // Resolve active invoice to link to
                $activeInvoice = ($lastApt && $lastApt->resolved_invoice)
                    ? $lastApt->resolved_invoice
                    : $this->invoices()->orderBy('id', 'desc')->first();

                $sessionNum = $existingCount + 1;
                $reasonText = $this->description ?: ($lastApt?->reason ?: 'Physiotherapy Follow-up Session');

                while (count($createdAppointments) < $addDays) {
                    if (!$currentDate->isSunday() && !\App\Models\ClinicHoliday::isHolidayDate($currentDate, $clinicId, $doctorId)) {
                        $dateStr = $currentDate->format('Y-m-d');
                        $appointmentNo = \App\Models\Appointment::generateAppointmentNo();
                        $tokenNumber = \App\Models\Appointment::nextTokenForDate($dateStr, $doctorId);

                        $aptData = [
                            'appointment_no' => $appointmentNo,
                            'patient_id' => $this->id,
                            'doctor_id' => $doctorId,
                            'clinic_id' => $clinicId,
                            'category_id' => $categoryId,
                            'appointment_date' => $dateStr,
                            'treatment_days' => 1,
                            'extended_days' => 0,
                            'daily_fee' => $dailyFee,
                            'consultation_fee' => $dailyFee,
                            'recovery_percentage' => $recoveryPct,
                            'recovery_status' => (new \App\Models\Appointment(['recovery_percentage' => $recoveryPct]))->getRecoveryLevelLabel(),
                            'recovery_notes' => $recoveryNotes,
                            'appointment_time' => "Session {$sessionNum}",
                            'appointment_type' => ($sessionNum === 1) ? 'new' : 'follow_up',
                            'token_number' => $tokenNumber,
                            'reason' => "Session {$sessionNum} (Extended) - {$reasonText}",
                            'payment_status' => 'unpaid',
                            'status' => 'scheduled',
                        ];

                        if ($activeInvoice && \Illuminate\Support\Facades\Schema::hasColumn('appointments', 'invoice_id')) {
                            $aptData['invoice_id'] = $activeInvoice->id;
                        }

                        $newApt = \App\Models\Appointment::create($aptData);
                        $createdAppointments[] = $newApt;
                        $sessionNum++;
                    }
                    $currentDate->addDay();
                }

                // Update or Create Invoice for the extended days (+addDays * dailyFee)
                $addedFee = $addDays * $dailyFee;
                $totalSessionsNow = $existingCount + count($createdAppointments);
                $categoryObj = $categoryId ? \App\Models\TreatmentCategory::find($categoryId) : null;
                $categoryName = $categoryObj ? $categoryObj->name : 'Physiotherapy Treatment';

                if ($activeInvoice) {
                    $newSubtotal = (float) $activeInvoice->subtotal + $addedFee;
                    $newTotal = max(0, $newSubtotal - (float) $activeInvoice->discount + (float) $activeInvoice->additional_charges);
                    $newDue = max(0, $newTotal - (float) $activeInvoice->paid_amount);
                    $newPaymentStatus = ($newDue <= 0 && $newTotal > 0)
                        ? 'paid'
                        : ((float) $activeInvoice->paid_amount > 0 ? 'partially_paid' : 'unpaid');

                    $activeInvoice->update([
                        'subtotal' => $newSubtotal,
                        'total_amount' => $newTotal,
                        'due_amount' => $newDue,
                        'payment_status' => $newPaymentStatus,
                        'notes' => "Physiotherapy package: {$categoryName} — Total {$totalSessionsNow} Days (Extended +{$addDays} days @ ₹" . number_format($dailyFee, 0) . "/day, excluding Sundays)",
                    ]);

                    $firstItem = $activeInvoice->items()->first();
                    if ($firstItem && $activeInvoice->items()->count() === 1) {
                        $firstItem->update([
                            'item_description' => "{$categoryName} ({$totalSessionsNow} Days Treatment Package)",
                            'quantity' => $totalSessionsNow,
                            'unit_price' => $dailyFee,
                            'total' => $newSubtotal,
                        ]);
                    } else {
                        \App\Models\InvoiceItem::create([
                            'invoice_id' => $activeInvoice->id,
                            'item_description' => "{$categoryName} (+{$addDays} Extended Days: Session " . ($existingCount + 1) . " to {$totalSessionsNow})",
                            'quantity' => $addDays,
                            'unit_price' => $dailyFee,
                            'total' => $addedFee,
                        ]);
                    }

                    $activeInvoice->syncAppointmentsPaymentStatus();
                } else {
                    $firstCreated = $createdAppointments[0] ?? null;
                    $invoiceNo = \App\Models\Invoice::generateInvoiceNo();
                    $activeInvoice = \App\Models\Invoice::create([
                        'invoice_no' => $invoiceNo,
                        'patient_id' => $this->id,
                        'appointment_id' => $firstCreated ? $firstCreated->id : null,
                        'doctor_id' => $doctorId,
                        'invoice_date' => $firstCreated ? $firstCreated->appointment_date : now()->toDateString(),
                        'subtotal' => $addedFee,
                        'discount' => 0.00,
                        'additional_charges' => 0.00,
                        'total_amount' => $addedFee,
                        'paid_amount' => 0.00,
                        'due_amount' => $addedFee,
                        'payment_status' => 'unpaid',
                        'notes' => "Physiotherapy package: {$categoryName} for {$addDays} days (excluding Sundays)",
                    ]);

                    \App\Models\InvoiceItem::create([
                        'invoice_id' => $activeInvoice->id,
                        'item_description' => "{$categoryName} ({$addDays} Days Treatment Package)",
                        'quantity' => $addDays,
                        'unit_price' => $dailyFee,
                        'total' => $addedFee,
                    ]);

                    if (\Illuminate\Support\Facades\Schema::hasColumn('appointments', 'invoice_id')) {
                        foreach ($createdAppointments as $cApt) {
                            $cApt->update(['invoice_id' => $activeInvoice->id]);
                        }
                    }
                }
            }

            // Record Progress Entry if recovery % or extension was provided
            if (isset($options['recovery_percentage']) || $addDays > 0) {
                $totalSessionsNow = $existingCount + count($createdAppointments);
                $levelLabel = (new \App\Models\Appointment(['recovery_percentage' => $recoveryPct]))->getRecoveryLevelLabel();
                \App\Models\PatientProgress::create([
                    'patient_id' => $this->id,
                    'recorded_date' => now()->toDateString(),
                    'pain_level' => max(0, min(10, (int) round((100 - $recoveryPct) / 10))),
                    'symptoms_assessment' => "Recovery: {$recoveryPct}% - {$levelLabel}",
                    'treatment_response' => $addDays > 0
                        ? "Extended treatment by +{$addDays} days (New Sessions: " . ($existingCount + 1) . " to {$totalSessionsNow}, skipping Sunday). Current Recovery: {$recoveryPct}%."
                        : "Recovery assessment updated to {$recoveryPct}% ({$levelLabel}).",
                    'doctor_notes' => $recoveryNotes ?: ($addDays > 0 ? "Added +{$addDays} sessions @ ₹{$dailyFee}/day (+₹" . ($addDays * $dailyFee) . ")." : "Patient recovery evaluated at {$recoveryPct}%."),
                ]);
            }

            return [
                'created_appointments' => $createdAppointments,
                'added_days' => count($createdAppointments),
                'added_fee' => count($createdAppointments) * $dailyFee,
                'total_sessions' => $existingCount + count($createdAppointments),
                'recovery_percentage' => $recoveryPct,
            ];
        });
    }
}
