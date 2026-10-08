<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
    ];

    public static function record(string $action, ?string $entityType = null, ?string $entityId = null, string $description = ''): void
    {
        $user = auth()->user() ?? session('mock_user');
        self::create([
            'user_id' => $user->id ?? 1,
            'user_name' => $user->name ?? 'System Admin',
            'role' => session('current_role', $user->role ?? 'super_admin'),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
