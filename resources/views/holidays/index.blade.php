@extends('layouts.app')

@section('title', 'Clinic Holidays & Doctor Emergency Off')
@section('breadcrumb', 'Appointments / Clinic Holidays')
@section('page_title', 'Clinic Holidays & Emergency Off Management')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER BANNER -->
    <div class="card-custom p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                <i data-lucide="calendar-x" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Clinic Holidays & Doctor Off</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Auto-Skip Enabled
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 max-w-3xl">
                    Dates listed here are <strong class="text-slate-700">automatically skipped</strong> during appointment package booking (along with Sundays).
                    If a doctor takes an <strong>Emergency Off</strong>, all appointments for that date will automatically be moved to the next available working day after each patient's last package session.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="openModal('emergencyOffModal')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm shadow-rose-600/20">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span>Doctor Emergency Off Today</span>
            </button>
            <button type="button" onclick="openModal('addHolidayModal')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Holiday / Off Date</span>
            </button>
        </div>
    </div>

    <!-- STATS SNAPSHOT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-custom p-4 bg-white">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1">
                <span>Today's Status</span>
                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
            </div>
            @if($isTodayHoliday)
                <div class="text-xl font-black text-rose-600 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>CLINIC OFF TODAY</span>
                </div>
                <div class="text-[11px] text-rose-700 font-medium mt-1">
                    {{ $todayHolidayTitle ?? 'Emergency Off / Holiday' }}
                </div>
            @else
                <div class="text-xl font-black text-emerald-600 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>CLINIC OPEN TODAY</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Appointments are proceeding normally</div>
            @endif
        </div>

        <div class="card-custom p-4 bg-white">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1">
                <span>Upcoming Scheduled Holidays</span>
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">
                {{ $upcomingHolidaysCount }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Auto-skipped in upcoming booking cycles</div>
        </div>

        <div class="card-custom p-4 bg-white">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1">
                <span>Today's Scheduled Appointments</span>
                <i data-lucide="users" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">
                {{ $todayAppointmentsCount }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Patients booked for today ({{ now()->format('d M Y') }})</div>
        </div>
    </div>

    <!-- HOLIDAYS TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i>
                    <span>List of Declared Holidays & Off Days</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Holidays apply to appointment packages and auto-skip dates</p>
            </div>
            <button type="button" onclick="openModal('addHolidayModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add Date</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5 pl-5">Holiday Date</th>
                        <th class="p-3.5">Title / Reason</th>
                        <th class="p-3.5">Applies To Clinic</th>
                        <th class="p-3.5">Description</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($holidays as $h)
                        @php
                            $hDate = \Carbon\Carbon::parse($h->holiday_date);
                            $isToday = $hDate->isToday();
                            $isPast = $hDate->isPast() && !$isToday;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isToday ? 'bg-amber-50/40' : '' }}">
                            <td class="p-3.5 pl-5">
                                <div class="font-extrabold text-slate-900 flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-500"></i>
                                    <span>{{ $hDate->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">({{ $hDate->format('l') }})</span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800">{{ $h->title }}</div>
                            </td>
                            <td class="p-3.5">
                                @if($h->clinic)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">
                                        <i data-lucide="building-2" class="w-3 h-3"></i>
                                        <span>{{ $h->clinic->name }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-bold border border-purple-200">
                                        <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                        <span>All My Clinics</span>
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-500">
                                {{ $h->description ?: '—' }}
                            </td>
                            <td class="p-3.5">
                                @if($isToday)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                        TODAY (OFF)
                                    </span>
                                @elseif($isPast)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">
                                        Past
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Upcoming
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <form action="{{ route('holidays.destroy', $h->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this holiday ({{ $hDate->format('d M Y') }})?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition" title="Delete Holiday">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <i data-lucide="calendar-check-2" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                <div class="font-bold text-sm text-slate-700">No Holidays Declared Yet</div>
                                <p class="text-xs text-slate-400 mt-1">Sundays are automatically skipped. Add any festival, maintenance, or off dates here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($holidays->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL: ADD SCHEDULED HOLIDAY -->
<div id="addHolidayModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Add Clinic Holiday / Planned Off</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">This date will automatically be skipped in patient appointment booking</p>
            </div>
            <button onclick="closeModal('addHolidayModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('holidays.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Holiday Title / Occasion *</label>
                <input type="text" name="title" required placeholder="e.g. Diwali, Clinic Renovation, Independence Day"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 font-bold text-slate-900 text-sm outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Holiday Date *</label>
                    <input type="date" name="holiday_date" required min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Applicable Clinic</label>
                    <select name="clinic_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
                        <option value="">All My Clinics</option>
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 space-y-2">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="reschedule_existing" value="1" checked class="mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="font-bold text-amber-900 block">Auto-Reschedule existing appointments on this date</span>
                        <span class="text-[11px] text-amber-700 block mt-0.5">
                            Any existing appointments scheduled on this date will automatically be shifted to the end of each patient's package session so they do not miss any treatment.
                        </span>
                    </div>
                </label>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Description / Notes (Optional)</label>
                <textarea name="description" rows="2" placeholder="Additional details or patient notification note..."
                          class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addHolidayModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-bold rounded-xl shadow-sm">Save Holiday</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: DOCTOR EMERGENCY OFF -->
<div id="emergencyOffModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-rose-200 overflow-hidden my-auto">
        <div class="p-5 border-b border-rose-100 flex items-center justify-between bg-rose-50/60">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-xs shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-rose-950 text-sm">Doctor Emergency Off Declaration</h3>
                    <p class="text-[11px] text-rose-700 mt-0.5">Auto-shifts today's scheduled patients to end of treatment</p>
                </div>
            </div>
            <button onclick="closeModal('emergencyOffModal')" class="text-rose-400 hover:text-rose-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('holidays.emergency-off') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf

            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-4 h-4 text-rose-600"></i>
                    <span>How Emergency Off Works:</span>
                </div>
                <p class="text-[11px] text-rose-800 leading-relaxed">
                    1. The selected date will be declared as an off day for the clinic.<br>
                    2. All active appointments booked on this date will be <strong>automatically moved to after the last appointment of each patient's package</strong>.<br>
                    3. No treatment session is lost, and Sundays and other holidays are strictly respected!
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Off Date *</label>
                    <input type="date" name="off_date" required value="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 font-bold text-slate-900 outline-none focus:border-rose-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Applicable Clinic</label>
                    <select name="clinic_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-rose-500">
                        <option value="">All My Clinics</option>
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Reason / Title *</label>
                <input type="text" name="reason" required value="Doctor Emergency Off"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 font-bold text-slate-900 outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Additional Notes</label>
                <textarea name="description" rows="2" placeholder="e.g. Doctor is unwell / personal emergency. Appointments auto-rescheduled."
                          class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-rose-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('emergencyOffModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-sm shadow-rose-600/30">
                    Confirm Emergency Off &amp; Auto-Reschedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
