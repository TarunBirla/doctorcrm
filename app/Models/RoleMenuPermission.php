<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenuPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'menu_key',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Menus that are strictly payment/financial and NEVER visible to Staff
     */
    public const PAYMENT_MENUS = [
        'billing',
        'dues',
        'payments',
        'expenses',
        'reports',
    ];

    /**
     * Controllable non-payment menus that a Doctor can show or hide for their Staff
     */
    public static function staffControllableMenus(): array
    {
        return [
            'Main & Patient Flow' => [
                'dashboard' => ['label' => 'Dashboard', 'icon' => 'layout-grid', 'desc' => 'Clinic overview & daily patient statistics (without revenue)'],
                'queue' => ['label' => 'Today\'s Queue', 'icon' => 'clock', 'desc' => 'Real-time waiting room & token management'],
                'appointments' => ['label' => 'Appointments', 'icon' => 'calendar', 'desc' => 'Book, reschedule & manage patient appointments'],
                'calendar' => ['label' => 'Appointment Calendar', 'icon' => 'calendar-days', 'desc' => 'Interactive slot schedule & calendar view'],
                'clinics' => ['label' => 'My Practice Clinics', 'icon' => 'building-2', 'desc' => 'View clinic locations & timings'],
                // 'availability' => ['label' => 'Doctor Availability', 'icon' => 'clock-4', 'desc' => 'Manage doctor weekly schedule & slot overrides'],
            ],
            'Clinical & Patient Care' => [
                'patients' => ['label' => 'Patients Master', 'icon' => 'users', 'desc' => 'Register, edit & view patient profiles'],
                'consultations' => ['label' => 'Consultations & Visits', 'icon' => 'stethoscope', 'desc' => 'OPD consultation notes & assessment records'],
                'prescriptions' => ['label' => 'Prescriptions (Rx)', 'icon' => 'file-text', 'desc' => 'View & print patient prescriptions'],
                'medical_reports' => ['label' => 'Medical Reports', 'icon' => 'clipboard-list', 'desc' => 'Upload & manage X-Ray, MRI & lab reports'],
                // 'progress' => ['label' => 'Recovery Tracker', 'icon' => 'trending-up', 'desc' => 'Track patient physiotherapy recovery sessions'],
                // 'followups' => ['label' => 'Follow-ups', 'icon' => 'alarm-clock', 'desc' => 'Upcoming patient follow-ups & reminders'],
            ],
            'Physiotherapy Library' => [
                'exercises' => ['label' => 'Exercises Library', 'icon' => 'dumbbell', 'desc' => 'Physiotherapy exercises & home routines'],
                'categories' => ['label' => 'Manage Categories', 'icon' => 'layers', 'desc' => 'Conditions & physiotherapy treatment categories'],
            ],
        ];
    }

    /**
     * Default menus for each role if not customized in database
     */
    public static function defaultPermissions(): array
    {
        return [
            'doctor' => [
                'dashboard' => true,
                'queue' => true,
                'appointments' => true,
                'calendar' => true,
                'clinics' => true,
                'slots' => true,
                'patients' => true,
                'consultations' => true,
                'prescriptions' => true,
                'medical_reports' => true,
                'progress' => true,
                'followups' => true,
                'exercises' => true,
                'videos' => true,
                'categories' => true,
                'availability' => true,
                'billing' => true,
                'dues' => true,
                'payments' => true,
                'expenses' => false,
                'reports' => true,
                'staff_manage' => true,
            ],
            'receptionist' => [
                'dashboard' => true,
                'patients' => true,
                'appointments' => true,
                'queue' => true,
                'calendar' => true,
                'clinics' => true,
                'slots' => true,
                'availability' => true,
                'consultations' => true,
                'prescriptions' => true,
                'medical_reports' => true,
                'progress' => true,
                'followups' => true,
                'exercises' => true,
                'videos' => true,
                'categories' => true,
                'billing' => false,
                'dues' => false,
                'payments' => false,
                'expenses' => false,
                'reports' => false,
            ],
        ];
    }

    /**
     * Check if a specific menu item is allowed for a given role
     */
    public static function canAccess(string $role, string $menuKey, ?int $doctorId = null): bool
    {
        if ($role === 'super_admin') {
            return true;
        }

        // Normalize 'staff' role to 'receptionist'
        $normalizedRole = ($role === 'staff') ? 'receptionist' : $role;

        // Staff is NEVER allowed to access payment/billing/financial menus
        if ($normalizedRole === 'receptionist' && in_array($menuKey, self::PAYMENT_MENUS, true)) {
            return false;
        }

        // Doctor always has access to Payment Management and Staff Management
        if ($normalizedRole === 'doctor' && in_array($menuKey, ['payments', 'billing', 'dues', 'staff_manage'], true)) {
            return true;
        }

        try {
            // If checking for staff, first check doctor-specific staff permission override
            if ($normalizedRole === 'receptionist') {
                if ($doctorId === null && auth()->check()) {
                    $doc = Doctor::resolveForCurrentUser($role);
                    $doctorId = $doc ? $doc->id : null;
                }
                if ($doctorId) {
                    $docRoleKey = 'receptionist_doc_' . $doctorId;
                    $docRecord = self::where('role', $docRoleKey)->where('menu_key', $menuKey)->first();
                    if ($docRecord !== null) {
                        return (bool) $docRecord->is_visible;
                    }
                }
            }

            $record = self::where('role', $normalizedRole)->where('menu_key', $menuKey)->first();

            if ($record !== null) {
                return (bool) $record->is_visible;
            }
        } catch (\Throwable $e) {
            // Fallback to defaults if table doesn't exist yet
        }

        $defaults = self::defaultPermissions();
        return $defaults[$normalizedRole][$menuKey] ?? false;
    }
}
