<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\RoleMenuPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorStaffController extends Controller
{
    /**
     * Resolve the current doctor or ensure doctor/super_admin access
     */
    protected function authorizeAndResolveDoctor(): ?Doctor
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (!in_array($currentRole, ['doctor', 'super_admin'], true)) {
            abort(403, 'Unauthorized access. Only Doctors can manage clinic staff.');
        }

        if ($currentRole === 'doctor' && auth()->check()) {
            return Doctor::where('user_id', auth()->id())->first() ?? Doctor::first();
        }

        return Doctor::where('is_active', true)->first() ?? Doctor::first();
    }

    /**
     * Display Staff Management & Staff Role Sidebar Show/Hide Permissions
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = $this->authorizeAndResolveDoctor();
        $activeTab = $request->get('tab', 'staff'); // 'staff' or 'permissions'

        $search = trim((string) $request->get('search', ''));
        $statusFilter = $request->get('status', 'all');

        $query = User::whereIn('role', ['receptionist', 'staff'])->with('parentDoctor');

        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $query->where(function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id)
                  ->orWhereNull('doctor_id');
            });
        } elseif ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            if ($statusFilter === 'active') {
                $query->where('is_active', true)->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                });
            } elseif ($statusFilter === 'inactive') {
                $query->where(function ($q) {
                    $q->where('status', 'inactive')
                      ->orWhere(function ($sub) {
                          $sub->where('is_active', false)->where(function ($s2) {
                              $s2->whereNull('status')->orWhere('status', '!=', 'suspended');
                          });
                      });
                });
            } elseif ($statusFilter === 'suspended') {
                $query->where('status', 'suspended');
            }
        }

        $staffMembers = $query->orderByDesc('created_at')->get();

        // Compute KPI counts across all staff for this doctor
        $baseCountQuery = User::whereIn('role', ['receptionist', 'staff']);
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $baseCountQuery->where(function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id)->orWhereNull('doctor_id');
            });
        }
        $allStaffForStats = $baseCountQuery->get();
        $stats = [
            'total' => $allStaffForStats->count(),
            'active' => $allStaffForStats->filter(fn ($u) => $u->is_active && ($u->status ?? 'active') === 'active')->count(),
            'inactive' => $allStaffForStats->filter(fn ($u) => ($u->status ?? ($u->is_active ? 'active' : 'inactive')) === 'inactive')->count(),
            'suspended' => $allStaffForStats->filter(fn ($u) => ($u->status ?? '') === 'suspended')->count(),
        ];

        // Load Staff Sidebar Menu Permissions for this Doctor
        $menuGroups = RoleMenuPermission::staffControllableMenus();
        $doctorId = $loggedInDoctor ? $loggedInDoctor->id : null;
        $staffPermissions = [];
        foreach ($menuGroups as $group => $items) {
            foreach ($items as $key => $info) {
                $staffPermissions[$key] = RoleMenuPermission::canAccess('receptionist', $key, $doctorId);
            }
        }

        $doctors = Doctor::orderBy('name')->get();

        return view('doctor.staff.index', compact(
            'staffMembers',
            'stats',
            'search',
            'statusFilter',
            'activeTab',
            'menuGroups',
            'staffPermissions',
            'loggedInDoctor',
            'doctors',
            'currentRole'
        ));
    }

    /**
     * Store a newly created Staff account
     */
    public function store(Request $request)
    {
        $loggedInDoctor = $this->authorizeAndResolveDoctor();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:25',
            'position' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'status' => 'nullable|in:active,inactive,suspended',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $status = $validated['status'] ?? 'active';
        $isActive = ($status === 'active');
        $doctorId = $loggedInDoctor ? $loggedInDoctor->id : ($validated['doctor_id'] ?? null);

        $staff = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'password' => Hash::make($validated['password']),
            'role' => 'receptionist',
            'doctor_id' => $doctorId,
            'is_active' => $isActive,
            'status' => $status,
        ]);

        AuditLog::record(
            'Staff Created',
            'User',
            (string) $staff->id,
            "Staff member {$staff->name} ({$staff->position} - {$staff->email}) created with status: {$status}."
        );

        return redirect()->route('doctor.staff.index')
            ->with('success', "Staff account for {$staff->name} ({$staff->position}) created! They can now log in using {$staff->email}.");
    }

    /**
     * Update an existing Staff member's details & password
     */
    public function update(Request $request, $id)
    {
        $loggedInDoctor = $this->authorizeAndResolveDoctor();
        $staff = User::whereIn('role', ['receptionist', 'staff'])->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'phone' => 'required|string|max:25',
            'position' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $status = $validated['status'];
        $isActive = ($status === 'active');

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'is_active' => $isActive,
            'status' => $status,
        ];

        if ($loggedInDoctor && empty($staff->doctor_id)) {
            $updateData['doctor_id'] = $loggedInDoctor->id;
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $staff->update($updateData);

        AuditLog::record(
            'Staff Updated',
            'User',
            (string) $staff->id,
            "Staff member {$staff->name} ({$staff->email}) updated. Status: {$status}."
        );

        return redirect()->route('doctor.staff.index')
            ->with('success', "Staff profile for {$staff->name} updated successfully!");
    }

    /**
     * Quick Status Change: Active / Inactive / Suspend
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAndResolveDoctor();
        $staff = User::whereIn('role', ['receptionist', 'staff'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $status = $validated['status'];
        $staff->update([
            'status' => $status,
            'is_active' => ($status === 'active'),
        ]);

        $statusLabels = [
            'active' => 'Activated',
            'inactive' => 'Marked Inactive',
            'suspended' => 'Suspended',
        ];
        $label = $statusLabels[$status] ?? ucfirst($status);

        AuditLog::record(
            'Staff Status Changed',
            'User',
            (string) $staff->id,
            "Staff {$staff->name} ({$staff->email}) status changed to {$status}."
        );

        return back()->with('success', "Staff member {$staff->name} has been {$label}.");
    }

    /**
     * Remove a Staff account
     */
    public function destroy($id)
    {
        $this->authorizeAndResolveDoctor();
        $staff = User::whereIn('role', ['receptionist', 'staff'])->findOrFail($id);
        $name = $staff->name;
        $staff->delete();

        AuditLog::record('Staff Deleted', 'User', (string) $id, "Staff member {$name} deleted.");

        return redirect()->route('doctor.staff.index')
            ->with('success', "Staff account '{$name}' has been permanently removed.");
    }

    /**
     * Update Sidebar Menu Show / Hide Permissions for Staff
     */
    public function updatePermissions(Request $request)
    {
        $loggedInDoctor = $this->authorizeAndResolveDoctor();
        $submittedMenus = $request->input('menus', []);

        $menuGroups = RoleMenuPermission::staffControllableMenus();
        $docRoleKey = $loggedInDoctor ? ('receptionist_doc_' . $loggedInDoctor->id) : 'receptionist';

        foreach ($menuGroups as $group => $items) {
            foreach ($items as $menuKey => $info) {
                $isVisible = isset($submittedMenus[$menuKey]) && $submittedMenus[$menuKey] == '1';

                // Save for this doctor's staff
                RoleMenuPermission::updateOrCreate(
                    ['role' => $docRoleKey, 'menu_key' => $menuKey],
                    ['is_visible' => $isVisible]
                );

                // Also keep global receptionist permission in sync so role switcher reflects it immediately
                RoleMenuPermission::updateOrCreate(
                    ['role' => 'receptionist', 'menu_key' => $menuKey],
                    ['is_visible' => $isVisible]
                );
            }
        }

        // Ensure payment menus are ALWAYS hidden for staff
        foreach (RoleMenuPermission::PAYMENT_MENUS as $payKey) {
            RoleMenuPermission::updateOrCreate(
                ['role' => $docRoleKey, 'menu_key' => $payKey],
                ['is_visible' => false]
            );
            RoleMenuPermission::updateOrCreate(
                ['role' => 'receptionist', 'menu_key' => $payKey],
                ['is_visible' => false]
            );
        }

        AuditLog::record(
            'Staff Sidebar Permissions Updated',
            'RoleMenuPermission',
            (string) ($loggedInDoctor->id ?? 0),
            'Doctor updated sidebar menu Show/Hide visibility for Staff role.'
        );

        return redirect()->route('doctor.staff.index', ['tab' => 'permissions'])
            ->with('success', 'Staff sidebar menu visibility (Show / Hide) updated successfully! Payment sections remain strictly hidden for Staff.');
    }
}
