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

        // Also update the linked appointment's payment status if present
        if ($this->appointment) {
            $this->appointment->payment_status = $this->payment_status;
            $this->appointment->save();
        }
    }
}
