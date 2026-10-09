<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TreatmentCategory extends Model
{
    use HasFactory;

    protected $table = 'treatment_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'doctor_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'category_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'category_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
