@extends('layouts.app')

@section('title', 'Appointment Calendar')
@section('breadcrumb', 'Calendar')
@section('page_title', 'Appointment Schedule & Calendar')

@section('content')
<div class="space-y-6">

    <!-- TOP CONTROLS BAR -->
    <div class="card-custom p-4 bg-white flex flex-wrap items-center justify-between gap-4">
        
        <!-- View Toggle & Date Navigation -->
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                <a href="?view=day&date={{ $selectedDate }}" class="px-3 py-1.5 rounded-lg transition {{ $view === 'day' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Day</a>
                <a href="?view=week&date={{ $selectedDate }}" class="px-3 py-1.5 rounded-lg transition {{ $view === 'week' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Week</a>
                <a href="?view=month&date={{ $selectedDate }}" class="px-3 py-1.5 rounded-lg transition {{ $view === 'month' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Month</a>
            </div>

            <div class="flex items-center gap-2">
                <a href="?view={{ $view }}&date={{ $carbonDate->copy()->subMonth()->toDateString() }}" class="p-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
                <span class="text-sm font-bold text-slate-900 min-w-[140px] text-center">
                    {{ $carbonDate->format('F Y') }}
                </span>
                <a href="?view={{ $view }}&date={{ $carbonDate->copy()->addMonth()->toDateString() }}" class="p-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
                <a href="?view={{ $view }}&date={{ now()->toDateString() }}" class="px-2.5 py-1 text-xs border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 font-semibold">
                    Today
                </a>
            </div>
        </div>

        <button onclick="openModal('quickAppointmentModal')" class="flex items-center gap-1.5 px-4 py-2 bg-navy-900 text-white rounded-xl text-xs font-bold hover:bg-navy-800 transition shadow-sm">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Appointment</span>
        </button>
    </div>

    <!-- CALENDAR GRID CARD -->
    <div class="card-custom bg-white p-6">
        
        <!-- MONTH VIEW -->
        @if($view === 'month')
            @php
                $startOfMonth = $carbonDate->copy()->startOfMonth();
                $endOfMonth = $carbonDate->copy()->endOfMonth();
                $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 for Sunday
                $daysInMonth = $carbonDate->daysInMonth;
            @endphp

            <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-bold uppercase text-slate-400 tracking-wider">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>

            <div class="grid grid-cols-7 gap-2">
                <!-- Padding for days before start of month -->
                @for($p = 0; $p < $startDayOfWeek; $p++)
                    <div class="min-h-[110px] bg-slate-50/50 rounded-xl border border-slate-100 p-2 text-slate-300"></div>
                @endfor

                <!-- Days of month -->
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $curDate = $carbonDate->copy()->day($d)->toDateString();
                        $dayAppts = $appointments->where('appointment_date', $curDate);
                        $isToday = $curDate === now()->toDateString();
                    @endphp
                    <div class="min-h-[110px] rounded-xl border {{ $isToday ? 'border-blue-400 bg-blue-50/20 shadow-xs' : 'border-slate-200 bg-white' }} p-2 flex flex-col justify-between hover:border-slate-300 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold {{ $isToday ? 'w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center' : 'text-slate-700' }}">
                                {{ $d }}
                            </span>
                            @if($dayAppts->count() > 0)
                                <span class="text-[10px] font-bold text-slate-400">{{ $dayAppts->count() }} Appts</span>
                            @endif
                        </div>

                        <!-- Appt Pills -->
                        <div class="space-y-1 mt-1 overflow-y-auto max-h-16 text-[10px]">
                            @foreach($dayAppts->take(3) as $a)
                                <a href="{{ route('patients.show', $a->patient_id) }}" 
                                   class="block px-1.5 py-0.5 rounded truncate font-semibold 
                                    @if($a->status === 'completed') bg-emerald-50 text-emerald-800 border border-emerald-100
                                    @elseif($a->status === 'waiting') bg-amber-50 text-amber-800 border border-amber-100
                                    @elseif($a->status === 'in_consultation') bg-blue-100 text-blue-900 border border-blue-200
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $a->appointment_time }} - {{ $a->patient->first_name }}
                                </a>
                            @endforeach
                            @if($dayAppts->count() > 3)
                                <span class="text-[9px] text-slate-400 font-bold block">+{{ $dayAppts->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        @else
            <!-- DAY OR WEEK VIEW TABLE -->
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($appointments as $a)
                    <div class="py-3.5 flex items-center justify-between hover:bg-slate-50 px-3 rounded-xl transition">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-bold flex items-center justify-center">
                                #{{ $a->token_number }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $a->patient->full_name }}</h4>
                                <span class="text-slate-500">{{ $a->appointment_date->format('d M Y') }} at <strong>{{ $a->appointment_time }}</strong> • Type: {{ ucfirst($a->appointment_type) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold capitalize 
                                {{ $a->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($a->status === 'waiting' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $a->status }}
                            </span>
                            <a href="{{ route('patients.show', $a->patient_id) }}" class="px-3 py-1.5 border border-slate-200 rounded-xl font-semibold hover:bg-slate-100">
                                View Patient
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="py-12 text-center text-slate-400">No appointments scheduled for this period.</p>
                @endforelse
            </div>
        @endif

    </div>

</div>
@endsection
