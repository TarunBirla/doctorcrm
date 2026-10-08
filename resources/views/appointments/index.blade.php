@extends('layouts.app')

@section('title', 'Appointments & Queue Management')
@section('breadcrumb', 'Appointments')
@section('page_title', 'Appointment Operations & Queue')

@section('content')
<div class="space-y-6">

    <!-- 4 TOP KPI CARDS (MATCHING MEDIA 2) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Appointments</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalAppointments }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Revenue Collected</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Dues Pending</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">₹{{ number_format($totalDues, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Completed Consultations</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $completedAppointments }}</div>
        </div>

    </div>

    <!-- FILTER BAR (MATCHING MEDIA 2 SCREENSHOT) -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('appointments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 text-xs items-end">
            
            <div class="md:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">Search Patient</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Code, name, phone..." 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Appointment Date</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="waiting" {{ $status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                    <option value="in_consultation" {{ $status === 'in_consultation' ? 'selected' : '' }}>In Consultation</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="no_show" {{ $status === 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="new" {{ $type === 'new' ? 'selected' : '' }}>New Consultation</option>
                    <option value="follow_up" {{ $type === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                    <option value="revisit" {{ $type === 'revisit' ? 'selected' : '' }}>Revisit</option>
                    <option value="emergency" {{ $type === 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm text-center">
                    Filter
                </button>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-medium text-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- DATA TABLE CONTAINER (MATCHING MEDIA 2) -->
    <div class="card-custom bg-white overflow-hidden">
        
        <!-- HEADER WITH ADD APPOINTMENT CTA -->
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Appointments / Patient Queue</h3>
                <p class="text-xs text-slate-400">Recorded clinical appointments with real-time status and billing parameters</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index') }}" class="px-3 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Calendar View</span>
                </a>
                <button onclick="openModal('quickAppointmentModal')" class="flex items-center gap-1.5 px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Appointment</span>
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3 pl-5">Token / No</th>
                        <th class="p-3">Date & Time</th>
                        <th class="p-3">Patient / Code</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Reason / Complaint</th>
                        <th class="p-3">Fee (₹)</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 pl-5">
                                <span class="font-bold text-slate-900 block">Token #{{ $apt->token_number }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $apt->appointment_no }}</span>
                            </td>
                            <td class="p-3">
                                <span class="font-semibold text-slate-800 block">{{ $apt->appointment_date->format('d M Y') }}</span>
                                <span class="text-xs text-slate-500">{{ $apt->appointment_time }}</span>
                            </td>
                            <td class="p-3">
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700 block">
                                    {{ $apt->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $apt->patient->patient_id }} • {{ $apt->patient->mobile }}</span>
                            </td>
                            <td class="p-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold uppercase 
                                    @if($apt->appointment_type === 'new') bg-blue-50 text-blue-700
                                    @elseif($apt->appointment_type === 'follow_up') bg-indigo-50 text-indigo-700
                                    @elseif($apt->appointment_type === 'revisit') bg-purple-50 text-purple-700
                                    @else bg-rose-50 text-rose-700 @endif">
                                    {{ str_replace('_', ' ', $apt->appointment_type) }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600 max-w-xs truncate">
                                {{ $apt->reason ?? 'General Consultation' }}
                            </td>
                            <td class="p-3 font-bold text-slate-800">
                                ₹{{ number_format($apt->consultation_fee, 2) }}
                            </td>
                            <td class="p-3">
                                @if($apt->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i data-lucide="check" class="w-3 h-3"></i> Paid
                                    </span>
                                @elseif($apt->payment_status === 'partially_paid')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Partial Due
                                    </span>
                                @elseif($apt->payment_status === 'due')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Due Pending
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($apt->status === 'in_consultation')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span> In Consultation
                                    </span>
                                @elseif($apt->status === 'waiting')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Waiting ({{ $apt->waiting_since ? $apt->waiting_since->diffForHumans(null, true) : 'ready' }})
                                    </span>
                                @elseif($apt->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Completed
                                    </span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">
                                        {{ $apt->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- One-Click Consultation Action -->
                                    @if($apt->status === 'waiting' || $apt->status === 'in_consultation')
                                        <a href="{{ route('consultations.create', ['appointment_id' => $apt->id]) }}" 
                                           title="Doctor Consultation Room" 
                                           class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1">
                                            <i data-lucide="stethoscope" class="w-3 h-3"></i>
                                            <span>Consult</span>
                                        </a>
                                    @elseif($apt->status === 'scheduled' || $apt->status === 'confirmed')
                                        <form action="{{ route('appointments.status', $apt->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="waiting">
                                            <button type="submit" title="Mark Patient Arrived & Waiting" 
                                                    class="px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-lg text-[11px] font-bold transition">
                                                Check In
                                            </button>
                                        </form>
                                    @endif

                                    <!-- View Patient Profile -->
                                    <a href="{{ route('patients.show', $apt->patient_id) }}" title="Patient Profile" 
                                       class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <!-- Delete / Cancel -->
                                    @if($apt->status !== 'completed')
                                        <form action="{{ route('appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Cancel Appointment" class="p-1.5 bg-slate-50 text-rose-600 hover:bg-rose-100 rounded-lg transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="calendar-x" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-slate-700 text-sm">No Appointments Found</h4>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting the date or status filters above, or book a new appointment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($appointments->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $appointments->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
