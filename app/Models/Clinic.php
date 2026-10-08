<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'tagline',
        'doctor_name',
        'doctor_reg_no',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'website',
        'gst_number',
        'consultation_fee',
        'appointment_duration',
        'working_days',
        'working_hours',
        'break_hours',
        'prescription_header',
        'invoice_footer',
    ];
}
