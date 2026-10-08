<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitDiagnosis extends Model
{
    protected $fillable = [
        'visit_id',
        'diagnosis_name',
        'diagnosis_type',
        'notes',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
