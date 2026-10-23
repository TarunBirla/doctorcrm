<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ClinicHoliday extends Model
{
    protected $fillable = [
        'title',
        'holiday_date',
        'description',
        'clinic_id',
        'doctor_id',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Check if a specific date is a clinic/doctor holiday
     */
    public static function isHolidayDate($date, $clinicId = null, $doctorId = null): bool
    {
        self::ensureTableExists();

        try {
            $dateStr = ($date instanceof Carbon) ? $date->format('Y-m-d') : Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return false;
        }

        try {
            return self::whereDate('holiday_date', $dateStr)
                ->where(function ($q) use ($clinicId, $doctorId) {
                    // Global holiday (applies to all clinics and doctors)
                    $q->where(function ($g) {
                        $g->whereNull('clinic_id')->whereNull('doctor_id');
                    });

                    // Clinic-specific holiday
                    if ($clinicId) {
                        $q->orWhere('clinic_id', $clinicId);
                    }

                    // Doctor-specific off/leave
                    if ($doctorId) {
                        $q->orWhere('doctor_id', $doctorId);
                    }
                })
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Find the next available working day (skipping Sundays and Holidays)
     */
    public static function nextWorkingDay($from, $clinicId = null, $doctorId = null): Carbon
    {
        $current = ($from instanceof Carbon) ? $from->copy() : Carbon::parse($from);
        
        // Safety cap to avoid infinite loop
        for ($i = 0; $i < 365; $i++) {
            $current->addDay();
            if (!$current->isSunday() && !self::isHolidayDate($current, $clinicId, $doctorId)) {
                return $current;
            }
        }

        return $current;
    }

    /**
     * Ensure database table & columns exist safely on live server
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('clinic_holidays')) {
                Schema::create('clinic_holidays', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->date('holiday_date');
                    $table->string('description')->nullable();
                    $table->foreignId('doctor_id')->nullable();
                    $table->foreignId('clinic_id')->nullable();
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('clinic_holidays', 'clinic_id')) {
                    Schema::table('clinic_holidays', function (Blueprint $table) {
                        $table->foreignId('clinic_id')->nullable()->after('description');
                    });
                }
                if (!Schema::hasColumn('clinic_holidays', 'doctor_id')) {
                    Schema::table('clinic_holidays', function (Blueprint $table) {
                        $table->foreignId('doctor_id')->nullable()->after('clinic_id');
                    });
                }
            }
        } catch (\Throwable $e) {
            // Ignore if unable to modify dynamically
        }
    }
}
