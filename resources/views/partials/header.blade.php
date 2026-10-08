@php
    $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
    $roleDisplayNames = [
        'super_admin' => 'Super Admin',
        'doctor' => 'Doctor',
        'receptionist' => 'Receptionist / Staff',
    ];

    $authUser = auth()->user();
    if ($authUser) {
        $userName = strtoupper($authUser->name);
        $nameParts = explode(' ', trim($authUser->name));
        if (count($nameParts) >= 2) {
            $userInitials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
        } else {
            $userInitials = strtoupper(substr($authUser->name, 0, 2));
        }
    } else {
        $userName = match($currentRole) {
            'doctor' => 'DOCTOR',
            'receptionist' => 'STAFF',
            default => 'SYSTEM SUPERADMIN',
        };
        $userInitials = 'DR';
    }

    $notifications = \App\Models\Notification::latest()->take(4)->get();
@endphp

<header class="bg-white border-b border-slate-200/90 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-[0_1px_2px_rgba(0,0,0,0.02)] no-print">
    <!-- LEFT: BREADCRUMBS & PAGE TITLE -->
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-600 transition">Dashboard</a>
            <span>/</span>
            <span class="text-slate-600 font-semibold">@yield('breadcrumb', 'Overview')</span>
        </div>
        <h2 class="text-lg font-extrabold text-slate-900 tracking-tight mt-0.5">
            @yield('page_title', 'Clinic Operations Dashboard')
        </h2>
    </div>

    <!-- RIGHT: CONTROLS & USER ACTIONS -->
    <div class="flex items-center gap-3">

        <!-- GLOBAL SEARCH TRIGGER BUTTON -->
        <!-- <button onclick="openGlobalSearch()" class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-500 transition shadow-2xs">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
            <span class="hidden md:inline">Quick Search...</span>
            <kbd class="hidden md:inline-block px-1.5 py-0.5 text-[10px] font-mono bg-white border border-slate-200 rounded text-slate-400">⌘K</kbd>
        </button> -->

        <!-- ROLE SWITCH DROPDOWN PILL -->
        <!-- <div class="relative" x-data="{ open: false }">
            <button onclick="document.getElementById('roleSwitchMenu').classList.toggle('hidden')" 
                    class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 transition shadow-2xs">
                <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Role Switch: <strong class="text-blue-700">{{ $roleDisplayNames[$currentRole] ?? 'Admin' }}</strong></span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
            </button>

            <div id="roleSwitchMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 animate-fade-in text-xs">
                <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    Select Role Preview
                </div>
                <a href="{{ route('role.switch', 'super_admin') }}" 
                   class="flex items-center justify-between px-3 py-2 hover:bg-blue-50 text-slate-700 transition {{ $currentRole === 'super_admin' ? 'font-bold text-blue-700 bg-blue-50/60' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $currentRole === 'super_admin' ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                        <span>Super Admin</span>
                    </div>
                    @if($currentRole === 'super_admin')
                        <i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i>
                    @endif
                </a>
                <a href="{{ route('role.switch', 'doctor') }}" 
                   class="flex items-center justify-between px-3 py-2 hover:bg-blue-50 text-slate-700 transition {{ $currentRole === 'doctor' ? 'font-bold text-blue-700 bg-blue-50/60' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $currentRole === 'doctor' ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                        <span>Doctor Role</span>
                    </div>
                    @if($currentRole === 'doctor')
                        <i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i>
                    @endif
                </a>
                <a href="{{ route('role.switch', 'receptionist') }}" 
                   class="flex items-center justify-between px-3 py-2 hover:bg-blue-50 text-slate-700 transition {{ $currentRole === 'receptionist' ? 'font-bold text-blue-700 bg-blue-50/60' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $currentRole === 'receptionist' ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                        <span>Receptionist (Front Desk)</span>
                    </div>
                    @if($currentRole === 'receptionist')
                        <i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i>
                    @endif
                </a>
            </div>
        </div> -->

        <!-- NOTIFICATION BELL WITH COUNTER -->
        <div class="relative">
            <button onclick="document.getElementById('notificationMenu').classList.toggle('hidden')" 
                    class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-xl border border-transparent hover:border-slate-200 transition">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white font-bold text-[9px] px-1 py-0.2 rounded-full shadow-xs">
                    99+
                </span>
            </button>

            <!-- Notifications Dropdown -->
            <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 animate-fade-in text-xs">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                    <span class="font-bold text-slate-800">Clinic Alerts & Notices</span>
                    <span class="text-[10px] text-blue-600 font-semibold cursor-pointer">Mark all read</span>
                </div>
                <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                    @forelse($notifications as $notif)
                        <div class="p-3 hover:bg-slate-50 transition flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            </div>
                            <div>
                                <h5 class="font-semibold text-slate-800 text-xs">{{ $notif->title }}</h5>
                                <p class="text-[11px] text-slate-500 leading-snug mt-0.5">{{ $notif->message }}</p>
                                <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-slate-400 text-xs">No pending alerts</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- USER PROFILE BADGE & DROPDOWN -->
        <div class="relative">
            <button onclick="document.getElementById('userProfileMenu').classList.toggle('hidden')" 
                    class="flex items-center gap-2 pl-2 border-l border-slate-200 hover:opacity-80 transition cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    {{ $userInitials }}
                </div>
                <div class="hidden xl:block text-left">
                    <span class="font-bold text-xs text-slate-900 block leading-none">{{ $userName }}</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $roleDisplayNames[$currentRole] ?? 'Admin' }}</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
            </button>

            <!-- User Menu Dropdown -->
            <div id="userProfileMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 text-xs animate-fade-in">
                <div class="px-3.5 py-2 border-b border-slate-100">
                    <span class="font-bold text-slate-900 block">{{ $userName }}</span>
                    <span class="text-[11px] text-slate-400 block truncate">{{ auth()->user()->email ?? ($currentRole . '@carepoint.com') }}</span>
                </div>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-slate-50 transition font-semibold">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>My Profile</span>
                </a>
                @if($currentRole === 'super_admin')
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Clinic Settings</span>
                </a>
                <a href="{{ route('admin.doctors.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="user-cog" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Manage Doctors</span>
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100 mt-1 pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-rose-600 hover:bg-rose-50 transition font-semibold text-left">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- PRIMARY HEADER CTA BUTTON (DEEP NAVY BLUE AS IN THEME) -->
        <button onclick="openModal('quickAppointmentModal')" 
                class="flex items-center gap-2 px-3.5 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition shadow-sm hover:shadow">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>New Appointment</span>
        </button>

    </div>
</header>
