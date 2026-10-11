@extends('layouts.app')

@section('title', 'Staff & Role Management')
@section('breadcrumb', 'Doctor Panel / Staff & Role Management')

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-users-gear" style="color:var(--primary);"></i>
            Staff & Role Management
        </h1>
        <p class="page-subtitle">Create clinic staff accounts (Email & Password login), manage Active / Inactive / Suspend status, and Show / Hide sidebar menus for Staff.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button type="button" class="btn btn-outline" onclick="switchStaffTab('permissions')" id="btnSwitchPermTop">
            <i class="fa-solid fa-sliders"></i> Staff Sidebar Show / Hide
        </button>
        <button type="button" class="btn btn-primary" onclick="openModal('addStaffModal')">
            <i class="fa-solid fa-user-plus"></i> + Create New Staff
        </button>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-bottom: 24px;">
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-user-group"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Total Staff Members</div>
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-change up"><i class="fa-solid fa-user-doctor"></i> Linked to {{ $loggedInDoctor->name ?? 'Clinic Doctor' }}</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Active Staff</div>
            <div class="kpi-value" style="color:#16a34a;">{{ $stats['active'] }}</div>
            <div class="kpi-change up"><i class="fa-solid fa-key"></i> Can login with Email & Password</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon orange"><i class="fa-solid fa-circle-pause"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Inactive Staff</div>
            <div class="kpi-value" style="color:#d97706;">{{ $stats['inactive'] }}</div>
            <div class="kpi-change" style="color:#b45309;"><i class="fa-solid fa-clock"></i> Temporarily disabled</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon red" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-ban"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Suspended Staff</div>
            <div class="kpi-value" style="color:#dc2626;">{{ $stats['suspended'] }}</div>
            <div class="kpi-change down"><i class="fa-solid fa-lock"></i> Login strictly blocked</div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div style="display:flex; gap:10px; margin-bottom:20px; border-bottom:2px solid var(--border); padding-bottom:12px; flex-wrap:wrap;">
    <button type="button" id="tabBtnStaff" onclick="switchStaffTab('staff')"
        class="btn {{ $activeTab !== 'permissions' ? 'btn-primary' : 'btn-outline' }}"
        style="border-radius:10px; font-weight:700; padding:10px 20px;">
        <i class="fa-solid fa-users"></i> Staff Directory & Status ({{ $stats['total'] }})
    </button>
    <button type="button" id="tabBtnPermissions" onclick="switchStaffTab('permissions')"
        class="btn {{ $activeTab === 'permissions' ? 'btn-primary' : 'btn-outline' }}"
        style="border-radius:10px; font-weight:700; padding:10px 20px;">
        <i class="fa-solid fa-eye-slash"></i> Staff Role Manage (Sidebar Show / Hide)
    </button>
</div>

<!-- TAB 1: STAFF DIRECTORY & CREATE / EDIT / STATUS -->
<div id="staffTabSection" style="display: {{ $activeTab !== 'permissions' ? 'block' : 'none' }};">
    <!-- Filter Bar -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('doctor.staff.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <input type="hidden" name="tab" value="staff">
                <div style="flex:1; min-width:240px; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" style="padding-left:38px;"
                        placeholder="Search staff by name, email, phone or position...">
                </div>
                <select name="status" class="form-control" style="width:190px;" onchange="this.form.submit()">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Status ({{ $stats['total'] }})</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only ({{ $stats['active'] }})</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive Only ({{ $stats['inactive'] }})</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only ({{ $stats['suspended'] }})</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                @if($search !== '' || $statusFilter !== 'all')
                    <a href="{{ route('doctor.staff.index', ['tab' => 'staff']) }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span class="card-title"><i class="fa-solid fa-id-badge" style="color:var(--primary); margin-right:8px;"></i>Clinic Staff Accounts</span>
                <p style="margin:4px 0 0; font-size:12.5px; color:var(--text-muted);">
                    Staff members can log in at <code>{{ url('/login') }}</code> using their Email & Password. Payment data is automatically hidden for all Staff.
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addStaffModal')">
                <i class="fa-solid fa-plus"></i> Add Staff
            </button>
        </div>

        <div class="table-Container" style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Email (Login ID) & Phone</th>
                        <th>Position / Designation</th>
                        <th>Access Level</th>
                        <th>Current Status</th>
                        <th>Quick Status Control</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                        @php
                            $effStatus = $staff->status ?? ($staff->is_active ? 'active' : 'inactive');
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#0d9488,#0284c7); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px;">
                                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:var(--text-primary); font-size:14px;">{{ $staff->name }}</div>
                                        <div style="font-size:11.5px; color:var(--text-muted);">
                                            ID: #STF-{{ str_pad($staff->id, 3, '0', STR_PAD_LEFT) }} • Joined {{ $staff->created_at ? $staff->created_at->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600; color:var(--primary); font-size:13px;">
                                    <i class="fa-regular fa-envelope" style="margin-right:4px;"></i>{{ $staff->email }}
                                </div>
                                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                                    <i class="fa-solid fa-phone" style="font-size:11px; margin-right:4px;"></i>{{ $staff->phone ?: 'No phone' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-confirmed" style="font-size:12px; padding:5px 10px;">
                                    <i class="fa-solid fa-briefcase-medical" style="margin-right:4px;"></i>{{ $staff->position ?: 'Clinic Staff / Receptionist' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:12px; font-weight:600; color:#0f766e;">
                                    <i class="fa-solid fa-check-double"></i> Doctor Clinical Modules
                                </div>
                                <div style="font-size:11px; color:#dc2626; font-weight:600; margin-top:2px;">
                                    <i class="fa-solid fa-eye-slash"></i> Payments & Revenue Hidden
                                </div>
                            </td>
                            <td>
                                @if($effStatus === 'active')
                                    <span class="badge badge-completed" style="background:#dcfce7; color:#15803d; font-weight:700; padding:6px 12px;">
                                        <i class="fa-solid fa-circle-check"></i> Active
                                    </span>
                                @elseif($effStatus === 'suspended')
                                    <span class="badge badge-cancelled" style="background:#fee2e2; color:#b91c1c; font-weight:700; padding:6px 12px;">
                                        <i class="fa-solid fa-ban"></i> Suspended
                                    </span>
                                @else
                                    <span class="badge badge-waiting" style="background:#fef3c7; color:#b45309; font-weight:700; padding:6px 12px;">
                                        <i class="fa-solid fa-circle-pause"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:5px; flex-wrap:wrap;">
                                    @if($effStatus !== 'active')
                                        <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm" style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:4px 9px; font-size:11.5px; font-weight:700;" title="Activate Staff">
                                                <i class="fa-solid fa-check"></i> Active
                                            </button>
                                        </form>
                                    @endif

                                    @if($effStatus !== 'inactive')
                                        <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="inactive">
                                            <button type="submit" class="btn btn-sm" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:4px 9px; font-size:11.5px; font-weight:700;" title="Mark Staff Inactive">
                                                <i class="fa-solid fa-pause"></i> Inactive
                                            </button>
                                        </form>
                                    @endif

                                    @if($effStatus !== 'suspended')
                                        <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn btn-sm" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; padding:4px 9px; font-size:11.5px; font-weight:700;" title="Suspend Staff Account">
                                                <i class="fa-solid fa-ban"></i> Suspend
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <button type="button" class="btn btn-outline btn-sm" title="View Staff Profile"
                                        onclick="openViewStaffModal({{ json_encode([
                                            'id' => $staff->id,
                                            'name' => $staff->name,
                                            'email' => $staff->email,
                                            'phone' => $staff->phone ?: 'N/A',
                                            'position' => $staff->position ?: 'Clinic Staff / Receptionist',
                                            'status' => $effStatus,
                                            'joined' => $staff->created_at ? $staff->created_at->format('d M Y, h:i A') : 'N/A',
                                            'doctor' => $staff->parentDoctor->name ?? ($loggedInDoctor->name ?? 'All Clinics'),
                                        ]) }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline btn-sm" title="Edit Staff / Change Password"
                                        onclick="openEditStaffModal({{ json_encode([
                                            'id' => $staff->id,
                                            'name' => $staff->name,
                                            'email' => $staff->email,
                                            'phone' => $staff->phone ?: '',
                                            'position' => $staff->position ?: 'Receptionist',
                                            'status' => $effStatus,
                                            'update_url' => route('doctor.staff.update', $staff->id),
                                        ]) }})">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('doctor.staff.destroy', $staff->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete staff account {{ addslashes($staff->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626; border-color:#fecaca;" title="Delete Staff">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:45px 20px;">
                                <div style="width:60px; height:60px; border-radius:50%; background:#f1f5f9; color:var(--text-muted); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:24px;">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <div style="font-weight:700; font-size:16px; color:var(--text-primary); margin-bottom:4px;">No Staff Accounts Found</div>
                                <p style="color:var(--text-muted); font-size:13px; max-width:440px; margin:0 auto 16px;">
                                    Create a staff account with Name, Email, Phone, Position, and Password so your receptionist or assistant can log in and manage clinic operations.
                                </p>
                                <button type="button" class="btn btn-primary" onclick="openModal('addStaffModal')">
                                    <i class="fa-solid fa-user-plus"></i> + Create First Staff Member
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 2: STAFF ROLE MANAGE (SHOW / HIDE SIDEBAR MENUS) -->
<div id="permissionsTabSection" style="display: {{ $activeTab === 'permissions' ? 'block' : 'none' }};">
    <div class="card" style="margin-bottom:20px; border-left:4px solid var(--primary); background:linear-gradient(90deg, #f0fdfa 0%, #ffffff 100%);">
        <div class="card-body" style="padding:18px 22px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <h3 style="margin:0 0 6px; font-size:16px; font-weight:700; color:#0f766e;">
                    <i class="fa-solid fa-shield-halved" style="margin-right:6px;"></i>
                    Doctor Control: Show / Hide Sidebar Menus for Staff
                </h3>
                <p style="margin:0; font-size:13px; color:#334155;">
                    Turn any module <strong>ON (Show)</strong> or <strong>OFF (Hide)</strong> for your Staff. Whatever you enable here will be visible in the Staff sidebar.
                    <strong style="color:#dc2626;">Payment, Billing & Revenue modules are permanently hidden for Staff everywhere.</strong>
                </p>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllStaffMenus(true)">
                    <i class="fa-solid fa-eye"></i> Show All Clinical Menus
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllStaffMenus(false)">
                    <i class="fa-solid fa-eye-slash"></i> Hide All Optional
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('doctor.staff.permissions.update') }}">
        @csrf
        @foreach($menuGroups as $groupName => $items)
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-folder-tree" style="color:var(--primary); margin-right:8px;"></i>{{ $groupName }}</span>
                </div>
                <div class="card-body" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:14px;">
                    @foreach($items as $menuKey => $info)
                        @php
                            $isChecked = $staffPermissions[$menuKey] ?? true;
                        @endphp
                        <label for="menu_toggle_{{ $menuKey }}" style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border:1px solid {{ $isChecked ? '#99f6e4' : 'var(--border)' }}; background:{{ $isChecked ? '#f0fdfa' : '#f8fafc' }}; border-radius:12px; cursor:pointer; transition:all 0.2s;" class="staff-perm-card">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <div style="width:38px; height:38px; border-radius:10px; background:#fff; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:15px; flex-shrink:0;">
                                    <i class="fa-solid {{ $info['icon'] }}"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:13.5px; color:var(--text-primary);">{{ $info['label'] }}</div>
                                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">{{ $info['desc'] }}</div>
                                </div>
                            </div>
                            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0;">
                                <input type="checkbox" id="menu_toggle_{{ $menuKey }}" name="menus[{{ $menuKey }}]" value="1"
                                    class="staff-menu-checkbox"
                                    {{ $isChecked ? 'checked' : '' }}
                                    onchange="updatePermBadge(this, '{{ $menuKey }}')"
                                    style="width:20px; height:20px; accent-color:#0d9488; cursor:pointer;">
                                <span id="perm_badge_{{ $menuKey }}" style="font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:6px; background:{{ $isChecked ? '#dcfce7' : '#e2e8f0' }}; color:{{ $isChecked ? '#15803d' : '#64748b' }};">
                                    {{ $isChecked ? 'SHOW' : 'HIDE' }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Locked Payment Modules Notice -->
        <div class="card" style="margin-bottom:24px; border:1px solid #fecaca; background:#fff5f5;">
            <div class="card-header" style="border-bottom-color:#fecaca;">
                <span class="card-title" style="color:#b91c1c;">
                    <i class="fa-solid fa-lock" style="margin-right:8px;"></i>
                    Payment & Financial Modules (Strictly Hidden for Staff)
                </span>
            </div>
            <div class="card-body" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">
                @foreach([
                    ['label' => 'Payment Manage & Reports', 'icon' => 'fa-indian-rupee-sign'],
                    ['label' => 'Invoices & Billing', 'icon' => 'fa-file-invoice-dollar'],
                    ['label' => 'Pending Dues & Collections', 'icon' => 'fa-hand-holding-dollar'],
                    ['label' => 'Dashboard Revenue & Fee KPIs', 'icon' => 'fa-chart-column'],
                ] as $lockedItem)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 14px; background:#ffffff; border:1px dashed #fca5a5; border-radius:10px; opacity:0.85;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <i class="fa-solid {{ $lockedItem['icon'] }}" style="color:#dc2626;"></i>
                            <span style="font-size:13px; font-weight:600; color:#7f1d1d;">{{ $lockedItem['label'] }}</span>
                        </div>
                        <span style="font-size:10.5px; font-weight:700; background:#fee2e2; color:#b91c1c; padding:3px 8px; border-radius:6px;">
                            <i class="fa-solid fa-eye-slash"></i> HIDDEN
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:30px;">
            <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-size:14px; font-weight:700;">
                <i class="fa-solid fa-floppy-disk"></i> Save Staff Show / Hide Settings
            </button>
        </div>
    </form>
</div>

<!-- MODAL: CREATE NEW STAFF -->
<div class="modal-backdrop" id="addStaffModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-plus" style="color:var(--primary); margin-right:8px;"></i>Create New Staff Account</span>
            <button type="button" class="modal-close" onclick="closeModal('addStaffModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('doctor.staff.store') }}">
            @csrf
            <div class="modal-body" style="display:grid; gap:14px;">
                <div style="padding:10px 14px; background:#f0fdfa; border:1px solid #99f6e4; border-radius:10px; font-size:12.5px; color:#0f766e;">
                    <i class="fa-solid fa-circle-info"></i> Staff will log in with the <strong>Email & Password</strong> you set below and will see your clinic data (without any Payment/Revenue info).
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Staff Full Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Priya Sharma">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Email Address (Login ID) <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="staff@physiopii.in">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Phone / Mobile No. <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="e.g. 9876543210">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Position / Role Title <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="position" list="staffPositionPresets" class="form-control" required placeholder="e.g. Receptionist, Assistant" value="Receptionist">
                        <datalist id="staffPositionPresets">
                            <option value="Receptionist">
                            <option value="Clinic Assistant">
                            <option value="Physiotherapy Assistant">
                            <option value="Front Desk Executive">
                            <option value="Patient Coordinator">
                            <option value="Nurse / Attendant">
                        </datalist>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Account Status <span style="color:#dc2626;">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="active" selected>Active (Allow Login)</option>
                            <option value="inactive">Inactive (Disable Login)</option>
                            <option value="suspended">Suspended (Block Access)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Login Password <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="password" class="form-control" required minlength="6" placeholder="Enter password (min 6 characters)">
                    <small style="font-size:11.5px; color:var(--text-muted);">Share this email and password with your staff member so they can sign in.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addStaffModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Create Staff Account</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: VIEW STAFF DETAILS -->
<div class="modal-backdrop" id="viewStaffModal">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-id-card" style="color:var(--primary); margin-right:8px;"></i>Staff Profile Details</span>
            <button type="button" class="modal-close" onclick="closeModal('viewStaffModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="display:grid; gap:12px;">
            <div style="display:flex; align-items:center; gap:14px; padding:12px; background:#f8fafc; border-radius:12px;">
                <div style="width:52px; height:52px; border-radius:12px; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700;" id="viewStaffAvatar">ST</div>
                <div>
                    <div style="font-size:16px; font-weight:700; color:var(--text-primary);" id="viewStaffName">-</div>
                    <div style="font-size:12.5px; color:var(--primary); font-weight:600;" id="viewStaffPosition">-</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
                <div style="padding:10px; border:1px solid var(--border); border-radius:8px;">
                    <div style="font-size:11px; color:var(--text-muted);">Login Email</div>
                    <div style="font-weight:700; margin-top:2px;" id="viewStaffEmail">-</div>
                </div>
                <div style="padding:10px; border:1px solid var(--border); border-radius:8px;">
                    <div style="font-size:11px; color:var(--text-muted);">Phone Number</div>
                    <div style="font-weight:700; margin-top:2px;" id="viewStaffPhone">-</div>
                </div>
                <div style="padding:10px; border:1px solid var(--border); border-radius:8px;">
                    <div style="font-size:11px; color:var(--text-muted);">Account Status</div>
                    <div style="font-weight:700; margin-top:2px; text-transform:capitalize;" id="viewStaffStatus">-</div>
                </div>
                <div style="padding:10px; border:1px solid var(--border); border-radius:8px;">
                    <div style="font-size:11px; color:var(--text-muted);">Assigned Doctor</div>
                    <div style="font-weight:700; margin-top:2px;" id="viewStaffDoctor">-</div>
                </div>
            </div>
            <div style="padding:12px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; font-size:12px; color:#991b1b;">
                <i class="fa-solid fa-shield-halved"></i> <strong>Privacy & Revenue Protection:</strong> This staff member can manage appointments, patients, queue, and clinical records, but <strong>cannot view any payments, invoices, fees, or revenue reports</strong>.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('viewStaffModal')">Close</button>
        </div>
    </div>
</div>

<!-- MODAL: EDIT STAFF -->
<div class="modal-backdrop" id="editStaffModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-pen" style="color:var(--primary); margin-right:8px;"></i>Edit Staff Member</span>
            <button type="button" class="modal-close" onclick="closeModal('editStaffModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editStaffForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-body" style="display:grid; gap:14px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Staff Full Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" id="editStaffName" class="form-control" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Email Address (Login ID) <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" id="editStaffEmail" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Phone / Mobile No. <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="phone" id="editStaffPhone" class="form-control" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Position / Designation <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="position" id="editStaffPosition" list="staffPositionPresets" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Account Status <span style="color:#dc2626;">*</span></label>
                        <select name="status" id="editStaffStatus" class="form-control" required>
                            <option value="active">Active (Allow Login)</option>
                            <option value="inactive">Inactive (Disable Login)</option>
                            <option value="suspended">Suspended (Block Access)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Reset Password <span style="color:var(--text-muted); font-weight:400;">(Leave blank to keep existing password)</span></label>
                    <input type="text" name="password" class="form-control" minlength="6" placeholder="Enter new password only if you want to change it">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editStaffModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Staff</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function switchStaffTab(tab) {
    const staffSec = document.getElementById('staffTabSection');
    const permSec = document.getElementById('permissionsTabSection');
    const btnStaff = document.getElementById('tabBtnStaff');
    const btnPerm = document.getElementById('tabBtnPermissions');

    if (tab === 'permissions') {
        staffSec.style.display = 'none';
        permSec.style.display = 'block';
        btnPerm.className = 'btn btn-primary';
        btnStaff.className = 'btn btn-outline';
    } else {
        staffSec.style.display = 'block';
        permSec.style.display = 'none';
        btnStaff.className = 'btn btn-primary';
        btnPerm.className = 'btn btn-outline';
    }
}

function openViewStaffModal(data) {
    document.getElementById('viewStaffAvatar').textContent = (data.name || 'ST').substring(0, 2).toUpperCase();
    document.getElementById('viewStaffName').textContent = data.name;
    document.getElementById('viewStaffPosition').textContent = data.position;
    document.getElementById('viewStaffEmail').textContent = data.email;
    document.getElementById('viewStaffPhone').textContent = data.phone;
    document.getElementById('viewStaffStatus').textContent = data.status;
    document.getElementById('viewStaffDoctor').textContent = data.doctor;
    openModal('viewStaffModal');
}

function openEditStaffModal(data) {
    document.getElementById('editStaffForm').action = data.update_url;
    document.getElementById('editStaffName').value = data.name;
    document.getElementById('editStaffEmail').value = data.email;
    document.getElementById('editStaffPhone').value = data.phone;
    document.getElementById('editStaffPosition').value = data.position;
    document.getElementById('editStaffStatus').value = data.status;
    openModal('editStaffModal');
}

function updatePermBadge(checkbox, key) {
    const badge = document.getElementById('perm_badge_' + key);
    const card = checkbox.closest('.staff-perm-card');
    if (checkbox.checked) {
        badge.textContent = 'SHOW';
        badge.style.background = '#dcfce7';
        badge.style.color = '#15803d';
        if (card) {
            card.style.background = '#f0fdfa';
            card.style.borderColor = '#99f6e4';
        }
    } else {
        badge.textContent = 'HIDE';
        badge.style.background = '#e2e8f0';
        badge.style.color = '#64748b';
        if (card) {
            card.style.background = '#f8fafc';
            card.style.borderColor = 'var(--border)';
        }
    }
}

function toggleAllStaffMenus(checkState) {
    document.querySelectorAll('.staff-menu-checkbox').forEach(cb => {
        cb.checked = checkState;
        const idParts = cb.id.replace('menu_toggle_', '');
        updatePermBadge(cb, idParts);
    });
}
</script>
@endpush
@endsection
