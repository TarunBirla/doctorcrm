@extends('layouts.app')

@section('title', 'Staff & Role Management')
@section('breadcrumb', 'Doctor Panel / Staff & Role Manage')
@section('page_title', 'Clinic Staff & Role Management')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER CARD -->
    <div class="card-custom p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                <i data-lucide="user-cog" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Staff &amp; Role Management</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Create clinic staff accounts (Email &amp; Password login), manage <strong class="text-emerald-700">Active</strong> / <strong class="text-amber-700">Inactive</strong> / <strong class="text-rose-600">Suspend</strong> status, and <strong class="text-blue-700">Show / Hide</strong> sidebar menus for Staff.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="switchStaffTab('permissions')" id="btnSwitchPermTop"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-indigo-200 bg-indigo-50/70 hover:bg-indigo-100 text-indigo-800 text-xs font-bold transition">
                <i data-lucide="sliders" class="w-4 h-4 text-indigo-600"></i>
                <span>Staff Sidebar Show / Hide</span>
            </button>
            <button type="button" onclick="openModal('addStaffModal')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold transition shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Create New Staff</span>
            </button>
        </div>
    </div>

    <!-- KPI SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Staff Members</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
                <span class="text-[11px] font-semibold text-blue-600 mt-1 block">Linked to {{ $loggedInDoctor->name ?? 'Clinic Doctor' }}</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Active Staff</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['active'] }}</div>
                <span class="text-[11px] font-semibold text-emerald-600 mt-1 block">Can login with Email &amp; Password</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Inactive Staff</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['inactive'] }}</div>
                <span class="text-[11px] font-semibold text-amber-700 mt-1 block">Temporarily disabled</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="pause-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Suspended Staff</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['suspended'] }}</div>
                <span class="text-[11px] font-semibold text-rose-600 mt-1 block">Login strictly blocked</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="ban" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- NAVIGATION TABS -->
    <div class="border-b border-slate-200 bg-white rounded-2xl p-1.5 shadow-2xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max text-xs">
            <button type="button" id="tabBtnStaff" onclick="switchStaffTab('staff')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition {{ $activeTab !== 'permissions' ? 'bg-navy-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Staff Directory &amp; Status ({{ $stats['total'] }})</span>
            </button>

            <button type="button" id="tabBtnPermissions" onclick="switchStaffTab('permissions')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition {{ $activeTab === 'permissions' ? 'bg-navy-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Staff Role Manage (Sidebar Show / Hide)</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: STAFF DIRECTORY & CREATE / EDIT / STATUS -->
    <div id="staffTabSection" class="{{ $activeTab !== 'permissions' ? 'block' : 'hidden' }} space-y-6">
        <!-- Filter Bar -->
        <div class="card-custom p-4 sm:p-5 bg-white">
            <form method="GET" action="{{ route('doctor.staff.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="staff">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search staff by name, email, phone or position..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                </div>
                <select name="status" onchange="this.form.submit()"
                        class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold sm:w-52">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Status ({{ $stats['total'] }})</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only ({{ $stats['active'] }})</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive Only ({{ $stats['inactive'] }})</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only ({{ $stats['suspended'] }})</option>
                </select>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-xs">
                        Filter
                    </button>
                    @if($search !== '' || $statusFilter !== 'all')
                        <a href="{{ route('doctor.staff.index', ['tab' => 'staff']) }}" class="px-3.5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-semibold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Staff Table -->
        <div class="card-custom bg-white overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="badge-check" class="w-4 h-4 text-blue-600"></i>
                        <span>Clinic Staff Accounts</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Staff members log in at <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">{{ url('/login') }}</code> using their Email &amp; Password. Payment data is automatically hidden for all Staff.
                    </p>
                </div>
                <button type="button" onclick="openModal('addStaffModal')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Staff</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-3.5 pl-5">Staff Member</th>
                            <th class="p-3.5">Email (Login ID) &amp; Phone</th>
                            <th class="p-3.5">Position / Designation</th>
                            <th class="p-3.5">Access Level</th>
                            <th class="p-3.5">Current Status</th>
                            <th class="p-3.5">Quick Status Control</th>
                            <th class="p-3.5 pr-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($staffMembers as $staff)
                            @php
                                $effStatus = $staff->status ?? ($staff->is_active ? 'active' : 'inactive');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 pl-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                #STF-{{ str_pad($staff->id, 3, '0', STR_PAD_LEFT) }} • Joined {{ $staff->created_at ? $staff->created_at->format('d M Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-blue-700 font-mono text-[11px]">{{ $staff->email }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $staff->phone ?: 'No phone' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 inline-block">
                                        {{ $staff->position ?: 'Clinic Staff / Receptionist' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                        <span>Doctor Clinical Modules</span>
                                    </div>
                                    <div class="text-[10px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                                        <i data-lucide="eye-off" class="w-3 h-3"></i>
                                        <span>Payments &amp; Revenue Hidden</span>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    @if($effStatus === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @elseif($effStatus === 'suspended')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($effStatus !== 'active')
                                            <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif

                                        @if($effStatus !== 'inactive')
                                            <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="inactive">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[10px] font-bold transition">
                                                    Inactivate
                                                </button>
                                            </form>
                                        @endif

                                        @if($effStatus !== 'suspended')
                                            <form method="POST" action="{{ route('doctor.staff.status', $staff->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-bold transition">
                                                    Suspend
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button type="button" title="View Staff Profile"
                                                onclick="openViewStaffModal({{ json_encode([
                                                    'id' => $staff->id,
                                                    'name' => $staff->name,
                                                    'email' => $staff->email,
                                                    'phone' => $staff->phone ?: 'N/A',
                                                    'position' => $staff->position ?: 'Clinic Staff / Receptionist',
                                                    'status' => $effStatus,
                                                    'joined' => $staff->created_at ? $staff->created_at->format('d M Y, h:i A') : 'N/A',
                                                    'doctor' => $staff->parentDoctor->name ?? ($loggedInDoctor->name ?? 'All Clinics'),
                                                ]) }})"
                                                class="p-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <button type="button" title="Edit Staff / Change Password"
                                                onclick="openEditStaffModal({{ json_encode([
                                                    'id' => $staff->id,
                                                    'name' => $staff->name,
                                                    'email' => $staff->email,
                                                    'phone' => $staff->phone ?: '',
                                                    'position' => $staff->position ?: 'Receptionist',
                                                    'status' => $effStatus,
                                                    'update_url' => route('doctor.staff.update', $staff->id),
                                                ]) }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-semibold transition">
                                            <i data-lucide="edit-3" class="w-3 h-3 text-blue-600"></i>
                                            <span>Edit</span>
                                        </button>

                                        <form method="POST" action="{{ route('doctor.staff.destroy', $staff->id) }}" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete staff account {{ addslashes($staff->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete Staff"
                                                    class="p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                                        <i data-lucide="user-plus" class="w-6 h-6"></i>
                                    </div>
                                    <div class="font-bold text-sm text-slate-900">No Staff Accounts Found</div>
                                    <p class="text-xs text-slate-400 max-w-md mx-auto mt-1 mb-4">
                                        Create a staff account with Name, Email, Phone, Position, and Password so your receptionist or assistant can log in and manage clinic operations.
                                    </p>
                                    <button type="button" onclick="openModal('addStaffModal')"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold transition shadow-sm">
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                        <span>Create First Staff Member</span>
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
    <div id="permissionsTabSection" class="{{ $activeTab === 'permissions' ? 'block' : 'hidden' }} space-y-6">
        <div class="card-custom p-5 bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-white border-l-4 border-l-blue-600 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                    <span>Doctor Control: Show / Hide Sidebar Menus for Staff</span>
                </h3>
                <p class="text-xs text-slate-600 mt-1">
                    Turn any module <strong>ON (Show)</strong> or <strong>OFF (Hide)</strong> for your Staff. Whatever you enable here will appear in the Staff sidebar.
                    <strong class="text-rose-600">Payment, Billing &amp; Revenue modules are permanently hidden for Staff everywhere.</strong>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="toggleAllStaffMenus(true)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    <span>Show All Clinical</span>
                </button>
                <button type="button" onclick="toggleAllStaffMenus(false)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    <i data-lucide="eye-off" class="w-3.5 h-3.5"></i>
                    <span>Hide All Optional</span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('doctor.staff.permissions.update') }}" class="space-y-6">
            @csrf
            @foreach($menuGroups as $groupName => $items)
                <div class="card-custom bg-white overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center gap-2">
                        <i data-lucide="folder-kanban" class="w-4 h-4 text-blue-600"></i>
                        <h4 class="text-sm font-bold text-slate-900">{{ $groupName }}</h4>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5">
                        @foreach($items as $menuKey => $info)
                            @php
                                $isChecked = $staffPermissions[$menuKey] ?? true;
                            @endphp
                            <label for="menu_toggle_{{ $menuKey }}"
                                   class="staff-perm-card flex items-center justify-between gap-3 p-4 rounded-xl border cursor-pointer transition {{ $isChecked ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50/60 border-slate-200' }}">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
                                        <i data-lucide="{{ $info['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-900">{{ $info['label'] }}</div>
                                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $info['desc'] }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    <input type="checkbox" id="menu_toggle_{{ $menuKey }}" name="menus[{{ $menuKey }}]" value="1"
                                           class="staff-menu-checkbox w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                           {{ $isChecked ? 'checked' : '' }}
                                           onchange="updatePermBadge(this, '{{ $menuKey }}')">
                                    <span id="perm_badge_{{ $menuKey }}"
                                          class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isChecked ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $isChecked ? 'SHOW' : 'HIDE' }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Locked Payment Modules Notice -->
            <div class="card-custom bg-rose-50/40 border-rose-200 overflow-hidden">
                <div class="p-4 border-b border-rose-200/70 flex items-center gap-2 text-rose-800">
                    <i data-lucide="lock" class="w-4 h-4 text-rose-600"></i>
                    <h4 class="text-xs font-extrabold uppercase tracking-wider">Payment &amp; Financial Modules (Strictly Hidden for Staff)</h4>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach([
                        ['label' => 'Payment Manage & Reports', 'icon' => 'indian-rupee'],
                        ['label' => 'Invoices & Billing', 'icon' => 'receipt'],
                        ['label' => 'Pending Dues & Collections', 'icon' => 'credit-card'],
                        ['label' => 'Dashboard Revenue & Fee KPIs', 'icon' => 'bar-chart-3'],
                    ] as $lockedItem)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-dashed border-rose-300">
                            <div class="flex items-center gap-2 text-xs font-bold text-rose-900">
                                <i data-lucide="{{ $lockedItem['icon'] }}" class="w-4 h-4 text-rose-600 shrink-0"></i>
                                <span>{{ $lockedItem['label'] }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800 uppercase shrink-0">
                                HIDDEN
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold transition shadow-md">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Staff Show / Hide Settings</span>
                </button>
            </div>
        </form>
    </div>

</div>

<!-- MODAL: CREATE NEW STAFF -->
<div id="addStaffModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Create New Staff Account</h3>
                    <p class="text-[11px] text-slate-500">Email &amp; Password login for clinic staff</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('addStaffModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('doctor.staff.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="p-3 rounded-xl bg-blue-50/80 border border-blue-200 text-blue-900 font-semibold leading-relaxed">
                Staff will log in with the <strong>Email &amp; Password</strong> you set below and will see your clinic data (without any Payment/Revenue info).
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Staff Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Priya Sharma"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address (Login ID) <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="staff@physiopii.in"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Phone / Mobile No. <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" required placeholder="e.g. 9876543210"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Position / Role Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" list="staffPositionPresets" required value="Receptionist" placeholder="e.g. Receptionist"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                    <datalist id="staffPositionPresets">
                        <option value="Receptionist">
                        <option value="Clinic Assistant">
                        <option value="Physiotherapy Assistant">
                        <option value="Front Desk Executive">
                        <option value="Patient Coordinator">
                        <option value="Nurse / Attendant">
                    </datalist>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Status <span class="text-rose-500">*</span></label>
                    <select name="status" required
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="active" selected>Active (Allow Login)</option>
                        <option value="inactive">Inactive (Disable Login)</option>
                        <option value="suspended">Suspended (Block Access)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Login Password <span class="text-rose-500">*</span></label>
                <input type="text" name="password" required minlength="6" placeholder="Enter password (min 6 characters)"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                <p class="text-[11px] text-slate-400 mt-1">Share this email and password with your staff member so they can sign in.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('addStaffModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Create Staff Account</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: VIEW STAFF DETAILS -->
<div id="viewStaffModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="id-card" class="w-4 h-4 text-blue-600"></i>
                <span>Staff Profile Details</span>
            </h3>
            <button type="button" onclick="closeModal('viewStaffModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 text-xs">
            <div class="flex items-center gap-3.5 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                <div id="viewStaffAvatar" class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-base shrink-0">
                    ST
                </div>
                <div>
                    <div id="viewStaffName" class="text-sm font-extrabold text-slate-900">-</div>
                    <div id="viewStaffPosition" class="text-xs font-bold text-blue-600 mt-0.5">-</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Login Email</span>
                    <div id="viewStaffEmail" class="font-bold text-slate-800 mt-0.5 break-all">-</div>
                </div>
                <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Phone Number</span>
                    <div id="viewStaffPhone" class="font-bold text-slate-800 mt-0.5">-</div>
                </div>
                <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Account Status</span>
                    <div id="viewStaffStatus" class="font-bold text-slate-800 mt-0.5 capitalize">-</div>
                </div>
                <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Assigned Doctor</span>
                    <div id="viewStaffDoctor" class="font-bold text-slate-800 mt-0.5">-</div>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 leading-relaxed">
                <strong>Privacy &amp; Revenue Protection:</strong> This staff member can manage appointments, patients, queue, and clinical records, but <strong>cannot view any payments, invoices, fees, or revenue reports</strong>.
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" onclick="closeModal('viewStaffModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50 font-bold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDIT STAFF -->
<div id="editStaffModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="user-Check" class="w-4 h-4 text-blue-600"></i>
                <span>Edit Staff Member</span>
            </h3>
            <button type="button" onclick="closeModal('editStaffModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" id="editStaffForm" action="" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 mb-1">Staff Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="editStaffName" required
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address (Login ID) <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="editStaffEmail" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Phone / Mobile No. <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="editStaffPhone" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Position / Designation <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" id="editStaffPosition" list="staffPositionPresets" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="editStaffStatus" required
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="active">Active (Allow Login)</option>
                        <option value="inactive">Inactive (Disable Login)</option>
                        <option value="suspended">Suspended (Block Access)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Reset Password <span class="text-slate-400 font-normal">(Leave blank to keep existing password)</span></label>
                <input type="text" name="password" minlength="6" placeholder="Enter new password only if you want to change it"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('editStaffModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Staff</span>
                </button>
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

    const activeCls = 'flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition bg-navy-900 text-white shadow-xs';
    const inactiveCls = 'flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition text-slate-600 hover:text-slate-900 hover:bg-slate-50';

    if (tab === 'permissions') {
        staffSec.classList.add('hidden');
        staffSec.classList.remove('block');
        permSec.classList.remove('hidden');
        permSec.classList.add('block');
        btnPerm.className = activeCls;
        btnStaff.className = inactiveCls;
    } else {
        staffSec.classList.remove('hidden');
        staffSec.classList.add('block');
        permSec.classList.add('hidden');
        permSec.classList.remove('block');
        btnStaff.className = activeCls;
        btnPerm.className = inactiveCls;
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
        badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800';
        if (card) {
            card.classList.remove('bg-slate-50/60', 'border-slate-200');
            card.classList.add('bg-emerald-50/40', 'border-emerald-200');
        }
    } else {
        badge.textContent = 'HIDE';
        badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-200 text-slate-600';
        if (card) {
            card.classList.remove('bg-emerald-50/40', 'border-emerald-200');
            card.classList.add('bg-slate-50/60', 'border-slate-200');
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
