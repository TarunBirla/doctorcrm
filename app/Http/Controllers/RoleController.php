<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

class RoleController extends Controller
{
    public function switchRole(Request $request, string $role)
    {
        $allowed = ['super_admin', 'doctor', 'receptionist'];
        if (!in_array($role, $allowed)) {
            $role = 'super_admin';
        }

        session(['current_role' => $role]);

        // Auto-login corresponding user if exists for authentic experience
        $user = User::where('role', $role)->first();
        if ($user) {
            auth()->login($user);
            session(['mock_user' => $user]);
        }

        $roleLabels = [
            'super_admin' => 'Super Admin',
            'doctor' => 'Doctor Role',
            'receptionist' => 'Receptionist / Front Desk Role',
        ];

        AuditLog::record('Role Switched', 'User', (string) ($user->id ?? 1), "Active role changed to {$roleLabels[$role]}");

        return back()->with('success', "Active role switched to {$roleLabels[$role]} successfully.");
    }
}
