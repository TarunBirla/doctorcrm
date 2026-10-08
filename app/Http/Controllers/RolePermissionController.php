<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoleMenuPermission;
use App\Models\AuditLog;

class RolePermissionController extends Controller
{
    /**
     * Show Role & Sidebar Menu Permissions Manager.
     */
    public function index()
    {
        $menuDefinitions = [
            'dashboard' => ['title' => 'Dashboard Overview', 'description' => 'Daily KPIs, operational summary and metrics', 'icon' => 'layout-dashboard'],
            'queue' => ['title' => 'Today\'s OPD Queue', 'description' => 'Live token calling, waiting list & consultation launch', 'icon' => 'users-round'],
            'appointments' => ['title' => 'Appointments Management', 'description' => 'Appointment list, booking, status transitions & filters', 'icon' => 'calendar'],
            'calendar' => ['title' => 'Appointment Calendar', 'description' => 'Month, week and day interactive slot calendar', 'icon' => 'calendar-days'],
            'patients' => ['title' => 'Patients Master Directory', 'description' => 'Patient registration, search, and 11-tab medical profile', 'icon' => 'user-plus'],
            'consultations' => ['title' => 'Consultations & Visits', 'description' => 'Clinical examinations, vitals recording & doctor notes', 'icon' => 'stethoscope'],
            'prescriptions' => ['title' => 'Prescription Management', 'description' => 'Digital Rx generation, medicine builder & printing', 'icon' => 'pill'],
            'medical_reports' => ['title' => 'Medical Reports Catalog', 'description' => 'Diagnostic lab attachments, previews & downloads', 'icon' => 'clipboard-list'],
            'progress' => ['title' => 'Patient Progress Tracker', 'description' => 'Vitals trend charts (Weight, BP, Pulse) over time', 'icon' => 'trending-up'],
            'followups' => ['title' => 'Follow-up Scheduler', 'description' => 'Upcoming, missed and scheduled patient reviews', 'icon' => 'alarm-clock'],
            'billing' => ['title' => 'Invoices & Billing', 'description' => 'Invoice generation, itemized charges & discounts', 'icon' => 'receipt'],
            'dues' => ['title' => 'Due Payments Collection', 'description' => 'Outstanding dues tracker & partial payment collector', 'icon' => 'wallet'],
            'payments' => ['title' => 'Payment Ledger', 'description' => 'Completed transactions log & printable receipts', 'icon' => 'circle-dollar-sign'],
            'expenses' => ['title' => 'Clinic Disbursements/Expenses', 'description' => 'Staff salary, clinic utilities and rent tracking', 'icon' => 'credit-card'],
            'reports' => ['title' => 'Financial & Clinical Reports', 'description' => 'Analytical revenue, demographic & appointment reports', 'icon' => 'bar-chart-3'],
        ];

        $roles = [
            'doctor' => 'Doctor Role',
            'receptionist' => 'Receptionist / Staff Role',
        ];

        // Gather existing permissions
        $permissions = [];
        foreach ($roles as $roleKey => $roleLabel) {
            foreach ($menuDefinitions as $menuKey => $meta) {
                $permissions[$roleKey][$menuKey] = RoleMenuPermission::canAccess($roleKey, $menuKey);
            }
        }

        return view('admin.roles.permissions', compact('menuDefinitions', 'roles', 'permissions'));
    }

    /**
     * Update Role Menu Permissions.
     */
    public function update(Request $request)
    {
        $submittedPermissions = $request->input('permissions', []); // [role => [menu_key => 1]]

        $menuKeys = [
            'dashboard', 'queue', 'appointments', 'calendar', 'patients',
            'consultations', 'prescriptions', 'medical_reports', 'progress',
            'followups', 'billing', 'dues', 'payments', 'expenses', 'reports'
        ];

        $roles = ['doctor', 'receptionist'];

        foreach ($roles as $role) {
            foreach ($menuKeys as $key) {
                $isVisible = isset($submittedPermissions[$role][$key]) && $submittedPermissions[$role][$key] == '1';

                RoleMenuPermission::updateOrCreate(
                    ['role' => $role, 'menu_key' => $key],
                    ['is_visible' => $isVisible]
                );
            }
        }

        AuditLog::record('Role Permissions Updated', 'RolePermission', 'System', "Super Admin updated role sidebar navigation permissions.");

        return back()->with('success', 'Sidebar role menu permissions updated successfully! Changes will reflect immediately on the corresponding accounts.');
    }
}
