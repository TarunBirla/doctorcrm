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
                'availability' => true,
                'billing' => false,
                'dues' => false,
                'payments' => false,
                'expenses' => false,
                'reports' => false,
            ],
            'receptionist' => [
                'dashboard' => true,
                'patients' => true,
                'appointments' => true,
                'queue' => true,
                'calendar' => true,
                'clinics' => true,
                'slots' => true,
                'billing' => true,
                'dues' => true,
                'payments' => true,
                'followups' => true,
                'availability' => false,
                'consultations' => false,
                'prescriptions' => false,
                'medical_reports' => false,
                'progress' => false,
                'expenses' => false,
                'reports' => false,
            ],
        ];
    }

    /**
     * Check if a specific menu item is allowed for a given role
     */
    public static function canAccess(string $role, string $menuKey): bool
    {
        if ($role === 'super_admin') {
            return true;
        }

        $record = self::where('role', $role)->where('menu_key', $menuKey)->first();

        if ($record !== null) {
            return (bool) $record->is_visible;
        }

        $defaults = self::defaultPermissions();
        return $defaults[$role][$menuKey] ?? false;
    }
}
