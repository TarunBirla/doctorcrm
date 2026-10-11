<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'specialization',
        'qualification',
        'registration_no',
        'phone',
        'email',
        'consultation_fee',
        'bio',
        'signature_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function ownedClinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'doctor_clinics')->withPivot('is_primary')->withTimestamps();
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function slotOverrides()
    {
        return $this->hasMany(DoctorSlotOverride::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'doctor_id')->whereIn('role', ['receptionist', 'staff']);
    }

    /**
     * Get all clinic IDs belonging to or associated with this doctor
     */
    public function allClinicIds(): array
    {
        try {
            $owned = $this->ownedClinics()->pluck('clinics.id')->toArray();
            $assigned = $this->clinics()->pluck('clinics.id')->toArray();
            return array_values(array_unique(array_merge($owned, $assigned)));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Resolve the active Doctor profile for the current logged-in user (works for both Doctor and their Staff)
     */
    public static function resolveForCurrentUser(?string $currentRole = null): ?self
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();
        $role = $currentRole ?? session('current_role', $user->role ?? 'super_admin');

        if ($role === 'doctor') {
            return self::where('user_id', $user->id)->first();
        }

        if (in_array($role, ['receptionist', 'staff'])) {
            if (!empty($user->doctor_id)) {
                $doc = self::find($user->doctor_id);
                if ($doc) {
                    return $doc;
                }
            }
            // Fallback if staff user wasn't explicitly linked or in role switch preview
            $ownDoc = self::where('user_id', $user->id)->first();
            if ($ownDoc) {
                return $ownDoc;
            }
            return self::where('is_active', true)->first() ?? self::first();
        }

        return null;
    }
}
