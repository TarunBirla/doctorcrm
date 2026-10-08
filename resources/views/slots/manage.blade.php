@extends('layouts.app')

@section('title', 'Slot & Schedule Manager')
@section('page_title', 'Appointment Slot & Schedule Management')
@section('breadcrumb', 'Slot Manager')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Clinic Slot & Schedule Manager</h1>
            <p class="text-sm text-slate-500">
                Manage appointment availability, block/unblock time slots, and configure working hours per clinic
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddSlotModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold shadow-sm hover:bg-blue-700 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Add Extra Slot
            </button>
            <a href="{{ route('clinics.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                <i data-lucide="building-2" class="w-4 h-4"></i> Manage Clinics
            </a>
        </div>
    </div>

    <!-- Clinic & Date Control Panel -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('slots.manage') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- Clinic Selection -->
            <div class="md:col-span-4 space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                    <i data-lucide="building" class="w-3.5 h-3.5 text-blue-600"></i> Select Practice Clinic
                </label>
                <select name="clinic_id" onchange="document.getElementById('filterForm').submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ $selectedClinic && $selectedClinic->id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Doctor Selection (Super Admin only) -->
            @if($currentRole === 'super_admin')
            <div class="md:col-span-3 space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-600"></i> Practicing Doctor
                </label>
                <select name="doctor_id" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                    @foreach($allDoctors as $doc)
                        <option value="{{ $doc->id }}" {{ $doctor && $doctor->id == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }} ({{ $doc->specialization }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Date Picker & Controls -->
            <div class="{{ $currentRole === 'super_admin' ? 'md:col-span-5' : 'md:col-span-8' }} space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600"></i> Schedule Date
                    </span>
                    <span class="text-blue-600 font-bold">{{ $dayOfWeek }}, {{ $carbonDate->format('d M Y') }}</span>
                </label>
                <div class="flex items-center gap-2">
                    <a href="{{ route('slots.manage', array_merge(request()->query(), ['date' => $carbonDate->copy()->subDay()->toDateString()])) }}" 
                       class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-600" title="Previous Day">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </a>

                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="document.getElementById('filterForm').submit()"
                           class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">

                    <a href="{{ route('slots.manage', array_merge(request()->query(), ['date' => $carbonDate->copy()->addDay()->toDateString()])) }}" 
                       class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-600" title="Next Day">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>

                    <a href="{{ route('slots.manage', array_merge(request()->query(), ['date' => now()->toDateString()])) }}" 
                       class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold hover:bg-slate-50 transition text-slate-700">
                        Today
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Slots</p>
                <h4 class="text-2xl font-bold text-slate-800">{{ $totalSlotsCount }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Available Slots</p>
                <h4 class="text-2xl font-bold text-emerald-700">{{ $availableSlotsCount }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Booked Appointments</p>
                <h4 class="text-2xl font-bold text-rose-700">{{ $bookedSlotsCount }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Blocked / Off Slots</p>
                <h4 class="text-2xl font-bold text-amber-700">{{ $blockedSlotsCount }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i data-lucide="ban" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ activeTab: 'slots' }">
        <!-- Tabs Nav -->
        <div class="flex border-b border-slate-200 bg-slate-50/75 px-4 pt-3 gap-2">
            <button @click="activeTab = 'slots'" 
                    :class="activeTab === 'slots' ? 'bg-white text-blue-700 border-slate-200 border-b-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 border-transparent font-medium'"
                    class="px-5 py-2.5 rounded-t-xl border border-b-0 text-sm transition flex items-center gap-2">
                <i data-lucide="calendar-check-2" class="w-4 h-4"></i>
                Daily Live Slots Grid ({{ $carbonDate->format('d M') }})
            </button>

            <button @click="activeTab = 'schedule'" 
                    :class="activeTab === 'schedule' ? 'bg-white text-blue-700 border-slate-200 border-b-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 border-transparent font-medium'"
                    class="px-5 py-2.5 rounded-t-xl border border-b-0 text-sm transition flex items-center gap-2">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                Clinic Working Hours & Weekly Schedule
            </button>
        </div>

        <!-- TAB 1: DAILY SLOTS GRID -->
        <div x-show="activeTab === 'slots'" class="p-6 space-y-6">
            <!-- Day Status Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold shrink-0">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">
                            {{ $selectedClinic ? $selectedClinic->name : 'Clinic' }} — {{ $dayOfWeek }} Working Schedule
                        </h4>
                        <p class="text-xs text-slate-500">
                            Operating Hours: {{ $currentDayAvailability ? $currentDayAvailability->start_time . ' - ' . $currentDayAvailability->end_time : '09:00 - 17:00' }} 
                            • Slot Duration: {{ $currentDayAvailability ? $currentDayAvailability->slot_duration : '15' }} mins
                            @if($currentDayAvailability && $currentDayAvailability->break_start)
                                • Lunch Break: {{ $currentDayAvailability->break_start }} - {{ $currentDayAvailability->break_end }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Available
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 font-bold border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Booked
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Blocked
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Break
                    </span>
                </div>
            </div>

            <!-- Slots Grid -->
            @if(count($slots) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @foreach($slots as $slot)
                    @if($slot['status'] === 'booked')
                        <!-- BOOKED SLOT CARD -->
                        <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/70 space-y-2 flex flex-col justify-between shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-rose-900 text-sm flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-4 h-4 text-rose-600"></i>
                                    {{ $slot['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-200 text-rose-800 uppercase">
                                    Booked
                                </span>
                            </div>

                            @if($slot['appointment'])
                            <div class="text-xs text-rose-800 space-y-0.5 pt-1 border-t border-rose-200/60">
                                <p class="font-bold text-slate-900 flex items-center justify-between">
                                    <span>{{ $slot['appointment']->patient?->full_name ?? 'Patient' }}</span>
                                    <span class="text-[10px] bg-white px-1.5 py-0.2 rounded border border-rose-200">
                                        Token #{{ $slot['appointment']->token_number }}
                                    </span>
                                </p>
                                <p class="text-[11px] text-slate-600">
                                    {{ $slot['appointment']->appointment_no }} • {{ ucfirst($slot['appointment']->appointment_type) }}
                                </p>
                            </div>
                            @endif

                            <div class="pt-2 text-[10px] text-rose-600 font-semibold flex items-center justify-between">
                                <span>Double booking blocked</span>
                                <span class="text-emerald-700 font-bold">Confirmed</span>
                            </div>
                        </div>

                    @elseif($slot['status'] === 'blocked')
                        <!-- BLOCKED SLOT CARD -->
                        <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/70 space-y-2 flex flex-col justify-between shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-900 text-sm flex items-center gap-1.5">
                                    <i data-lucide="ban" class="w-4 h-4 text-amber-600"></i>
                                    {{ $slot['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-800 uppercase">
                                    Blocked
                                </span>
                            </div>

                            <p class="text-xs text-amber-800 italic">
                                {{ $slot['status_reason'] ?? 'Unavailable' }}
                            </p>

                            <div class="pt-2 border-t border-amber-200/60 flex items-center justify-end">
                                <form action="{{ route('slots.override.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="clinic_id" value="{{ $selectedClinic->id }}">
                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                    <input type="hidden" name="slot_date" value="{{ $selectedDate }}">
                                    <input type="hidden" name="slot_time" value="{{ $slot['time'] }}">
                                    <input type="hidden" name="block" value="0">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-200/80 hover:bg-amber-300 text-amber-900 text-xs font-bold transition flex items-center gap-1">
                                        <i data-lucide="unlock" class="w-3.5 h-3.5"></i> Unblock Slot
                                    </button>
                                </form>
                            </div>
                        </div>

                    @elseif($slot['status'] === 'break')
                        <!-- BREAK TIME SLOT CARD -->
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-100/80 space-y-2 flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-600 text-sm flex items-center gap-1.5">
                                    <i data-lucide="coffee" class="w-4 h-4 text-slate-500"></i>
                                    {{ $slot['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 uppercase">
                                    Lunch Break
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Standard Scheduled Break</p>
                            <div class="pt-1 text-[10px] text-slate-400">Non-consulting window</div>
                        </div>

                    @else
                        <!-- AVAILABLE SLOT CARD -->
                        <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 space-y-2 flex flex-col justify-between shadow-xs transition hover:border-emerald-300">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-900 text-sm flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                                    {{ $slot['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                                    Available
                                </span>
                            </div>

                            <p class="text-xs text-emerald-700">Open for immediate booking</p>

                            <div class="pt-2 border-t border-emerald-200/60 flex items-center justify-between">
                                <button type="button" onclick="openBlockModal('{{ $slot['time'] }}', '{{ $slot['label'] }}')" 
                                        class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition flex items-center gap-1">
                                    <i data-lucide="lock" class="w-3.5 h-3.5"></i> Block Slot
                                </button>

                                <a href="{{ route('appointments.create', ['clinic_id' => $selectedClinic->id, 'date' => $selectedDate, 'time' => $slot['time']]) }}" 
                                   class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Book
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @else
            <div class="p-12 text-center rounded-2xl border border-dashed border-slate-200">
                <i data-lucide="calendar-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h4 class="font-bold text-slate-700 mb-1">No slots configured for this date</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Switch to the "Clinic Working Hours" tab to set up operating hours for {{ $selectedClinic->name }} on {{ $dayOfWeek }}s.
                </p>
                <button @click="activeTab = 'schedule'" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">
                    Configure Schedule
                </button>
            </div>
            @endif
        </div>

        <!-- TAB 2: CLINIC WORKING HOURS & WEEKLY SCHEDULE -->
        <div x-show="activeTab === 'schedule'" class="p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        Operational Schedule — {{ $selectedClinic ? $selectedClinic->name : '' }}
                    </h3>
                    <p class="text-xs text-slate-500">
                        Define practicing working hours, consultation duration, and lunch breaks for each day of the week
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('slots.schedule.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="clinic_id" value="{{ $selectedClinic->id }}">
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4">Day of Week</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Working Start</th>
                                <th class="py-3 px-4">Working End</th>
                                <th class="py-3 px-4">Lunch Break Start</th>
                                <th class="py-3 px-4">Lunch Break End</th>
                                <th class="py-3 px-4">Slot Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $daysList = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp
                            @foreach($daysList as $dayName)
                                @php
                                    $av = $weeklyAvailabilities->get($dayName);
                                    $isAv = $av ? $av->is_available : ($dayName !== 'Sunday');
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3.5 px-4 font-bold text-slate-800 text-sm">
                                        {{ $dayName }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="days[{{ $dayName }}][is_available]" value="1" {{ $isAv ? 'checked' : '' }}
                                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                            <span class="font-semibold {{ $isAv ? 'text-emerald-700' : 'text-slate-400' }}">
                                                {{ $isAv ? 'Working' : 'Day Off' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <input type="time" name="days[{{ $dayName }}][start_time]" value="{{ $av ? substr($av->start_time, 0, 5) : '09:00' }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <input type="time" name="days[{{ $dayName }}][end_time]" value="{{ $av ? substr($av->end_time, 0, 5) : '17:00' }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <input type="time" name="days[{{ $dayName }}][break_start]" value="{{ $av && $av->break_start ? substr($av->break_start, 0, 5) : '13:00' }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <input type="time" name="days[{{ $dayName }}][break_end]" value="{{ $av && $av->break_end ? substr($av->break_end, 0, 5) : '14:00' }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <select name="days[{{ $dayName }}][slot_duration]" class="px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500 font-semibold">
                                            <option value="10" {{ ($av && $av->slot_duration == 10) ? 'selected' : '' }}>10 min</option>
                                            <option value="15" {{ (!$av || $av->slot_duration == 15) ? 'selected' : '' }}>15 min</option>
                                            <option value="20" {{ ($av && $av->slot_duration == 20) ? 'selected' : '' }}>20 min</option>
                                            <option value="30" {{ ($av && $av->slot_duration == 30) ? 'selected' : '' }}>30 min</option>
                                            <option value="45" {{ ($av && $av->slot_duration == 45) ? 'selected' : '' }}>45 min</option>
                                            <option value="60" {{ ($av && $av->slot_duration == 60) ? 'selected' : '' }}>60 min</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm shadow-sm hover:bg-blue-700 transition flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Save Weekly Schedule for {{ $selectedClinic->name }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- BLOCK SLOT MODAL -->
<div id="blockSlotModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 flex items-center gap-2 text-base">
                <i data-lucide="lock" class="w-4 h-4 text-rose-600"></i> Block Appointment Slot
            </h3>
            <button onclick="closeBlockModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('slots.override.toggle') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="clinic_id" value="{{ $selectedClinic->id }}">
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            <input type="hidden" name="slot_date" value="{{ $selectedDate }}">
            <input type="hidden" name="slot_time" id="modalBlockSlotTime" value="">
            <input type="hidden" name="block" value="1">

            <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                <p><strong>Clinic:</strong> {{ $selectedClinic->name }}</p>
                <p><strong>Date:</strong> {{ $carbonDate->format('l, d M Y') }}</p>
                <p><strong>Slot Time:</strong> <span id="modalBlockSlotLabel" class="font-bold text-rose-700"></span></p>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Reason for Blocking Slot</label>
                <input type="text" name="reason" placeholder="e.g. Doctor personal emergency, surgical procedure..." required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeBlockModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition">
                    Confirm Block
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ADD EXTRA CUSTOM SLOT MODAL -->
<div id="addSlotModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 flex items-center gap-2 text-base">
                <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i> Add Extra / Emergency Slot
            </h3>
            <button onclick="closeAddSlotModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('slots.custom.create') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="clinic_id" value="{{ $selectedClinic->id }}">
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            <input type="hidden" name="slot_date" value="{{ $selectedDate }}">

            <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                <p><strong>Clinic:</strong> {{ $selectedClinic->name }}</p>
                <p><strong>Date:</strong> {{ $carbonDate->format('l, d M Y') }}</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Slot Time (24h or AM/PM) <span class="text-rose-500">*</span></label>
                <input type="time" name="slot_time" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Note / Reason</label>
                <input type="text" name="reason" placeholder="e.g. Evening Emergency Extra Slot"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddSlotModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition">
                    Add Slot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBlockModal(time, label) {
        document.getElementById('modalBlockSlotTime').value = time;
        document.getElementById('modalBlockSlotLabel').innerText = label;
        document.getElementById('blockSlotModal').classList.remove('hidden');
    }
    function closeBlockModal() {
        document.getElementById('blockSlotModal').classList.add('hidden');
    }
    function openAddSlotModal() {
        document.getElementById('addSlotModal').classList.remove('hidden');
    }
    function closeAddSlotModal() {
        document.getElementById('addSlotModal').classList.add('hidden');
    }
</script>
@endsection
