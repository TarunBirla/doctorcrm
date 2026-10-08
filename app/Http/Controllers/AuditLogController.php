<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $role = $request->get('role');
        $search = $request->get('search');

        $query = AuditLog::query();

        if (!empty($action)) {
            $query->where('action', 'like', "%{$action}%");
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('entity_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('settings.audit-logs', compact('logs', 'action', 'role', 'search'));
    }
}
