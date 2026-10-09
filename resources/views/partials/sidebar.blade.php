@php
    $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
    $roleLabels = [
        'super_admin' => 'SUPER ADMIN',
        'doctor' => 'DOCTOR',
        'receptionist' => 'RECEPTIONIST / STAFF',
    ];
    $todayQueueCount = \App\Models\Appointment::where('appointment_date', now()->toDateString())->whereIn('status', ['waiting', 'in_consultation'])->count();
    $dueCount = \App\Models\Invoice::whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->count();

    $doctorClinics = collect();
    if ($currentRole === 'doctor' && auth()->check()) {
        $loggedInDoctorObj = \App\Models\Doctor::where('user_id', auth()->id())->first();
        if ($loggedInDoctorObj) {
            $clinicIds = \Illuminate\Support\Facades\DB::table('doctor_clinics')
                ->where('doctor_id', $loggedInDoctorObj->id)
                ->pluck('clinic_id')
                ->merge(\App\Models\Clinic::where('doctor_id', $loggedInDoctorObj->id)->pluck('id'))
                ->unique();
            $doctorClinics = \App\Models\Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get();
        }
    }
@endphp

<aside class="w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0 min-h-screen select-none no-print">
    <!-- CLINIC BRANDING HEADER -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white shadow-sm shadow-blue-200">
                <i data-lucide="cross" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold tracking-tight text-slate-900 leading-tight">CAREPOINT</h1>
                <p class="text-[10px] text-blue-600 font-semibold uppercase tracking-wider">Super Clinic</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-700 uppercase">
            <span>{{ $roleLabels[$currentRole] ?? 'USER' }}</span>
        </div>
    </div>

    <!-- SEARCH MENU INPUT -->
    <div class="p-4 border-b border-slate-100">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="sidebarSearch" placeholder="Search menu..." 
                   class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none transition placeholder:text-slate-400 text-slate-700">
        </div>
    </div>

    <!-- NAVIGATION LINKS -->
    <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto text-xs">

        <!-- OVERVIEW -->
        @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'dashboard'))
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Overview
            </div>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
        @endif

        <!-- PATIENTS & QUEUE -->
        @php
            $hasPatientSection = \App\Models\RoleMenuPermission::canAccess($currentRole, 'patients') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'queue') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'appointments') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'calendar') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'clinics') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'slots');
        @endphp
        @if($hasPatientSection)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Patients & Appointments
            </div>
            <div class="space-y-1">
                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'patients'))
                <a href="{{ route('patients.index') }}" 
                   class="sidebar-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('patients.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('patients.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span>Patients Master</span>
                    </div>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'queue'))
                <a href="{{ route('queue.index') }}" 
                   class="sidebar-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('queue.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clock" class="w-4 h-4 {{ request()->routeIs('queue.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span>Today's Queue</span>
                    </div>
                    @if($todayQueueCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                            {{ $todayQueueCount }} waiting
                        </span>
                    @endif
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'appointments'))
                <a href="{{ route('appointments.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('appointments.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="calendar" class="w-4 h-4 {{ request()->routeIs('appointments.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Appointments</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'calendar'))
                <a href="{{ route('calendar.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('calendar.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="calendar-days" class="w-4 h-4 {{ request()->routeIs('calendar.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Appointment Calendar</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'clinics'))
                <a href="{{ route('clinics.index') }}" 
                   class="sidebar-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('clinics.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="building-2" class="w-4 h-4 {{ request()->routeIs('clinics.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span>{{ $currentRole === 'doctor' ? 'My Practice Clinics' : 'Clinics & Branches' }}</span>
                    </div>
                    @if($doctorClinics->isNotEmpty())
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">{{ $doctorClinics->count() }}</span>
                    @endif
                </a>

                @if($currentRole === 'doctor' && $doctorClinics->isNotEmpty())
                <div class="pl-7 pr-2 py-1 space-y-1">
                    @foreach($doctorClinics as $docClinic)
                    <a href="{{ route('appointments.index', ['clinic_id' => $docClinic->id]) }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-blue-50/70 hover:text-blue-800 transition group"
                       title="{{ $docClinic->name }} ({{ $docClinic->city ?? 'Clinic' }})">
                        <div class="flex items-center gap-2 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate">{{ $docClinic->name }}</span>
                        </div>
                        <span class="text-[9px] px-1 py-0.5 rounded bg-slate-100 text-slate-500 font-bold shrink-0 group-hover:bg-blue-100 group-hover:text-blue-700">{{ $docClinic->city ?? 'Clinic' }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'slots'))
                <a href="{{ route('slots.manage') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('slots.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="calendar-clock" class="w-4 h-4 {{ request()->routeIs('slots.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Slot & Schedule Manager</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- CLINICAL CARE / PHYSIOTHERAPY -->
        @php
            $hasClinicalSection = \App\Models\RoleMenuPermission::canAccess($currentRole, 'consultations') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'prescriptions') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'exercises') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'categories') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'medical_reports') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'progress') ||
                                  \App\Models\RoleMenuPermission::canAccess($currentRole, 'followups');
        @endphp
        @if($hasClinicalSection)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Clinical Care & Therapy
            </div>
            <div class="space-y-1">
                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'consultations'))
                <a href="{{ route('consultations.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('consultations.*') || request()->routeIs('visits.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="stethoscope" class="w-4 h-4 {{ request()->routeIs('consultations.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Consultations & Visits</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'exercises'))
                <a href="{{ route('exercises.index') }}" 
                   class="sidebar-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('exercises.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="dumbbell" class="w-4 h-4 {{ request()->routeIs('exercises.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span>Exercises Library</span>
                    </div>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Rehab</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'categories'))
                <a href="{{ route('categories.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="layers" class="w-4 h-4 {{ request()->routeIs('categories.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Manage Categories</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'prescriptions'))
                <a href="{{ route('prescriptions.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('prescriptions.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="file-text" class="w-4 h-4 {{ request()->routeIs('prescriptions.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Prescriptions</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'medical_reports'))
                <a href="{{ route('medical-reports.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('medical-reports.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="clipboard-list" class="w-4 h-4 {{ request()->routeIs('medical-reports.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Medical Reports</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'progress'))
                <a href="{{ route('progress.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('progress.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="trending-up" class="w-4 h-4 {{ request()->routeIs('progress.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Patient Progress Tracker</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'followups'))
                <a href="{{ route('followups.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('followups.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="alarm-clock" class="w-4 h-4 {{ request()->routeIs('followups.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Follow-ups</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'availability'))
                <a href="{{ route('settings.availability') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('settings.availability') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="clock-4" class="w-4 h-4 {{ request()->routeIs('settings.availability') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>{{ $currentRole === 'doctor' ? 'My Availability & Slots' : 'Doctor Availability' }}</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- BILLING & FINANCE -->
        @php
            $hasBillingSection = \App\Models\RoleMenuPermission::canAccess($currentRole, 'billing') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'dues') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'payments') ||
                                 \App\Models\RoleMenuPermission::canAccess($currentRole, 'expenses');
        @endphp
        @if($hasBillingSection)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Billing & Finance
            </div>
            <div class="space-y-1">
                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'billing'))
                <a href="{{ route('invoices.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('invoices.index') || request()->routeIs('invoices.show') || request()->routeIs('invoices.create') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="receipt" class="w-4 h-4 {{ request()->routeIs('invoices.*') && !request()->routeIs('due-payments.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Invoices & Billing</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'dues'))
                <a href="{{ route('due-payments.index') }}" 
                   class="sidebar-nav-link flex items-center justify-between px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('due-payments.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="credit-card" class="w-4 h-4 {{ request()->routeIs('due-payments.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span>Due Payments Collection</span>
                    </div>
                    @if($dueCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                            {{ $dueCount }} dues
                        </span>
                    @endif
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'payments'))
                <a href="{{ route('payments.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('payments.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="wallet" class="w-4 h-4 {{ request()->routeIs('payments.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Payment History</span>
                </a>
                @endif

                @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'expenses'))
                <a href="{{ route('expenses.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="pie-chart" class="w-4 h-4 {{ request()->routeIs('expenses.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Clinic Expenses</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- REPORTS & ANALYTICS -->
        @if(\App\Models\RoleMenuPermission::canAccess($currentRole, 'reports'))
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Reports & Analytics
            </div>
            <div class="space-y-1">
                <a href="{{ route('reports.financial') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('reports.financial') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 {{ request()->routeIs('reports.financial') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Financial Reports</span>
                </a>

                <a href="{{ route('reports.patients') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('reports.patients') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="users-2" class="w-4 h-4 {{ request()->routeIs('reports.patients') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Patient Analytics</span>
                </a>

                <a href="{{ route('reports.appointments') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('reports.appointments') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4 {{ request()->routeIs('reports.appointments') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Appointment Reports</span>
                </a>
            </div>
        </div>
        @endif

        <!-- CLINIC ADMINISTRATION (SUPER ADMIN ONLY) -->
        @if($currentRole === 'super_admin')
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Clinic Administration
            </div>
            <div class="space-y-1">
                <a href="{{ route('admin.doctors.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.doctors.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="user-cog" class="w-4 h-4 {{ request()->routeIs('admin.doctors.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Manage Doctors</span>
                </a>

                <a href="{{ route('admin.roles.permissions') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('admin.roles.permissions*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="shield-alert" class="w-4 h-4 {{ request()->routeIs('admin.roles.permissions*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Role Permissions</span>
                </a>

                <a href="{{ route('settings.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('settings.index') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="settings" class="w-4 h-4 {{ request()->routeIs('settings.index') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Clinic Settings</span>
                </a>

                <a href="{{ route('settings.availability') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('settings.availability') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="clock-4" class="w-4 h-4 {{ request()->routeIs('settings.availability') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Doctor Availability</span>
                </a>

                <a href="{{ route('audit-logs.index') }}" 
                   class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition font-semibold {{ request()->routeIs('audit-logs.index') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i data-lucide="shield-check" class="w-4 h-4 {{ request()->routeIs('audit-logs.index') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Audit Trail Logs</span>
                </a>
            </div>
        </div>
        @endif

    </nav>

    <!-- FOOTER PROFILE & ACTIONS -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center justify-between px-2 py-1">
            <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:opacity-80 transition" title="View Profile">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] text-slate-700 font-bold">My Profile</span>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('profile.show') }}" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="User Profile">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Log Out">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
