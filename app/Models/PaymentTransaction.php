<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'invoice_id',
        'patient_id',
        'transaction_no',
        'amount',
        'payment_method',
        'payment_date',
        'transaction_reference',
        'collected_by',
        'notes',
        'receipt_no',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public static function generateTransactionNo(): string
    {
        $count = self::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() + 1;
        return 'TXN-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNo(): string
    {
        $count = self::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() + 1;
        return 'REC-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
