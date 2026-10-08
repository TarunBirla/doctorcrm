<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientCommunication extends Model
{
    protected $fillable = [
        'patient_id',
        'channel',
        'type',
        'subject',
        'message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
