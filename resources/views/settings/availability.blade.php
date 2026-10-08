@extends('layouts.app')

@section('title', 'Doctor Availability & Working Hours')
@section('breadcrumb', 'Settings / Doctor Availability')
@section('page_title', 'Doctor Consultation Schedule & Slots')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="font-bold text-slate-900 text-base">Weekly Practice Schedule: {{ $doctor->name ?? 'Dr. Rajiv Sharma' }}</h3>
            <p class="text-xs text-slate-400">Configure daily working hours, break periods, and maximum OPD token capacity</p>
        </div>

        <form action="{{ route('settings.availability.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="space-y-3">
                @php
                    $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                @endphp

                @foreach($allDays as $day)
                    @php
                        $avail = $availabilities->firstWhere('day_of_week', $day);
                        $isAvail = $avail ? $avail->is_available : ($day !== 'Sunday');
                        $startTime = $avail->start_time ?? '09:30';
                        $endTime = $avail->end_time ?? '19:30';
                        $breakStart = $avail->break_start ?? '13:30';
                        $breakEnd = $avail->break_end ?? '16:00';
                        $slotDuration = $avail->slot_duration ?? 15;
                        $maxPatients = $avail->max_patients ?? 35;
                    @endphp

                    <div class="p-4 rounded-xl border {{ $isAvail ? 'border-slate-200 bg-white' : 'border-slate-100 bg-slate-50 opacity-70' }} grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                        <div class="md:col-span-2 flex items-center gap-2">
                            <input type="checkbox" name="days[{{ $day }}][is_available]" value="1" {{ $isAvail ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600">
                            <span class="font-bold text-slate-800 text-xs">{{ $day }}</span>
                        </div>

                        <div class="md:col-span-3 flex items-center gap-1.5">
                            <input type="time" name="days[{{ $day }}][start_time]" value="{{ $startTime }}" class="w-1/2 px-2 py-1 border border-slate-200 rounded-lg bg-slate-50 text-center">
                            <span class="text-slate-400">to</span>
                            <input type="time" name="days[{{ $day }}][end_time]" value="{{ $endTime }}" class="w-1/2 px-2 py-1 border border-slate-200 rounded-lg bg-slate-50 text-center">
                        </div>

                        <div class="md:col-span-3 flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Break:</span>
                            <input type="time" name="days[{{ $day }}][break_start]" value="{{ $breakStart }}" class="w-1/2 px-2 py-1 border border-slate-200 rounded-lg bg-slate-50 text-center text-[11px]">
                            <span class="text-slate-400">-</span>
                            <input type="time" name="days[{{ $day }}][break_end]" value="{{ $breakEnd }}" class="w-1/2 px-2 py-1 border border-slate-200 rounded-lg bg-slate-50 text-center text-[11px]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-[9px] text-slate-400 block font-semibold">Slot: {{ $slotDuration }}m</label>
                            <input type="hidden" name="days[{{ $day }}][slot_duration]" value="{{ $slotDuration }}">
                        </div>

                        <div class="md:col-span-2 text-right">
                            <label class="text-[9px] text-slate-400 block font-semibold">Max OPD Limit</label>
                            <input type="number" name="days[{{ $day }}][max_patients]" value="{{ $maxPatients }}" class="w-16 px-2 py-1 border border-slate-200 rounded-lg bg-slate-50 text-center font-bold">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm">
                    Save Schedule & Slots
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
