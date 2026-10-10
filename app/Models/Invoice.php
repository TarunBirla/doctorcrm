<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'patient_id',
        'appointment_id',
        'visit_id',
        'doctor_id',
        'invoice_date',
        'subtotal',
        'discount',
        'additional_charges',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public static function generateInvoiceNo(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        
        $latest = self::where('invoice_no', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(invoice_no) DESC, invoice_no DESC')
            ->first();

        if ($latest && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $latest->invoice_no, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        $invoiceNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        while (self::where('invoice_no', $invoiceNo)->exists()) {
            $nextSeq++;
            $invoiceNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
        }

        return $invoiceNo;
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

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class)->orderBy('payment_date', 'asc');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'invoice_id');
    }

    public function recalculate(): void
    {
        // 1. Calculate subtotal from items if present
        if ($this->items()->count() > 0) {
            $this->subtotal = (float) $this->items()->sum('total');
        }

        // 2. Total = subtotal + additional_charges - discount
        $total = max(0, ((float) $this->subtotal + (float) $this->additional_charges) - (float) $this->discount);
        $this->total_amount = $total;

        // 3. Paid = sum of transactions
        $paid = (float) $this->transactions()->sum('amount');
        $this->paid_amount = $paid;

        // 4. Due = Total - Paid
        $due = max(0, $total - $paid);
        $this->due_amount = $due;

        // 5. Determine payment status strictly according to business logic
        if ($paid <= 0) {
            $this->payment_status = ($total > 0) ? 'unpaid' : 'paid';
        } elseif ($due <= 0.001) {
            $this->payment_status = 'paid';
            $this->due_amount = 0.00;
        } else {
            $this->payment_status = 'partially_paid';
        }

        // Also set primary payment method from latest transaction if available
        $latestTxn = $this->transactions()->latest()->first();
        if ($latestTxn) {
            $this->payment_method = $latestTxn->payment_method;
        }

        $this->save();

        // Sync all appointments covered by this invoice
        $this->syncAppointmentsPaymentStatus();
    }

    public function syncAppointmentsPaymentStatus(): void
    {
        // 1. If appointments are explicitly linked via invoice_id
        $appointments = $this->appointments()->get();

        // 2. Fallback: If not linked via invoice_id, check primary appointment and package siblings
        if ($appointments->isEmpty() && $this->appointment_id) {
            $primaryAppt = Appointment::find($this->appointment_id);
            if ($primaryAppt) {
                // Link this invoice to the primary appointment if column exists
                if (\Illuminate\Support\Facades\Schema::hasColumn('appointments', 'invoice_id') && !$primaryAppt->invoice_id) {
                    $primaryAppt->update(['invoice_id' => $this->id]);
                }

                // Find package sibling appointments created around the same time for this patient & category
                $siblings = Appointment::where('patient_id', $primaryAppt->patient_id)
                    ->where('category_id', $primaryAppt->category_id)
                    ->whereBetween('created_at', [
                        $primaryAppt->created_at->copy()->subMinutes(15),
                        $primaryAppt->created_at->copy()->addMinutes(15)
                    ])
                    ->get();

                if (\Illuminate\Support\Facades\Schema::hasColumn('appointments', 'invoice_id')) {
                    foreach ($siblings as $sib) {
                        if (!$sib->invoice_id) {
                            $sib->update(['invoice_id' => $this->id]);
                        }
                    }
                }
                $appointments = $siblings;
            }
        }

        if ($appointments->isEmpty()) {
            if ($this->appointment) {
                $this->appointment->update(['payment_status' => $this->payment_status]);
            }
            return;
        }

        // Apply payment status across the appointments
        if ($this->payment_status === 'paid' || $this->due_amount <= 0.001) {
            foreach ($appointments as $apt) {
                $apt->update(['payment_status' => 'paid']);
            }
        } elseif ($this->payment_status === 'unpaid' || $this->paid_amount <= 0.001) {
            foreach ($appointments as $apt) {
                $apt->update(['payment_status' => 'unpaid']);
            }
        } else {
            // Partially paid: allocate paid sessions
            $dailyFee = (float) ($appointments->first()?->daily_fee ?? $appointments->first()?->consultation_fee ?? 500);
            $paidCount = ($dailyFee > 0) ? (int) floor($this->paid_amount / $dailyFee) : 0;
            
            $sorted = $appointments->sortBy('appointment_date')->values();
            foreach ($sorted as $idx => $apt) {
                if ($idx < $paidCount) {
                    $apt->update(['payment_status' => 'paid']);
                } else {
                    $apt->update(['payment_status' => 'unpaid']);
                }
            }
        }
    }
}
