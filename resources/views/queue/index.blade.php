@extends('layouts.app')

@section('title', "Today's Patient Queue")
@section('breadcrumb', 'Queue')
@section('page_title', "Today's Patient Queue & OPD Board")

@section('content')
<div class="space-y-6">

    <!-- TOP SUMMARY BAR -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <div class="card-custom p-4 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total in Queue</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $allToday->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
        </div>

        <div class="card-custom p-4 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Currently in Room</span>
                <div class="text-2xl font-extrabold text-indigo-700 mt-0.5">{{ $inConsultation ? '1' : '0' }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="stethoscope" class="w-4 h-4"></i>
            </div>
        </div>

        <div class="card-custom p-4 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waiting in Lobby</span>
                <div class="text-2xl font-extrabold text-amber-700 mt-0.5">{{ $waitingList->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
        </div>

        <div class="card-custom p-4 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Completed Consultations</span>
                <div class="text-2xl font-extrabold text-emerald-700 mt-0.5">{{ $completedList->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            </div>
        </div>

    </div>

    <!-- WHO IS NEXT CALLOUT BANNER -->
    @if($nextPatient)
        <div class="card-custom p-5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/10 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-black text-xl text-white border border-white/30">
                    #{{ $nextPatient->token_number }}
                </div>
                <div>
                    <div class="inline-block px-2.5 py-0.5 rounded-full bg-white text-blue-900 font-bold text-[10px] uppercase tracking-wider mb-1">
                        🎯 Next Patient Ready
                    </div>
                    <h3 class="text-lg font-bold text-white">
                        {{ $nextPatient->patient->full_name }}
                        <span class="text-xs font-normal text-blue-100">({{ $nextPatient->patient->age }}y / {{ $nextPatient->patient->gender }} • {{ $nextPatient->patient->patient_id }})</span>
                    </h3>
                    <p class="text-xs text-blue-100 mt-0.5">
                        Chief complaint: <strong>{{ $nextPatient->reason ?? 'General Consultation' }}</strong> • Waiting: {{ $nextPatient->waiting_since ? $nextPatient->waiting_since->diffForHumans(null, true) : '5m' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('queue.start', $nextPatient->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                        <i data-lucide="stethoscope" class="w-4 h-4 text-blue-700"></i>
                        <span>Start Consultation</span>
                    </button>
                </form>
                <button onclick="playChime('{{ $nextPatient->patient->full_name }}', {{ $nextPatient->token_number }})" 
                        class="px-3.5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 border border-white/20">
                    <i data-lucide="volume-2" class="w-4 h-4"></i>
                    <span>Call In</span>
                </button>
            </div>
        </div>
    @endif

    <!-- QUEUE FILTER BAR (CLINIC & CATEGORY WISE) -->
    <div class="card-custom p-4 bg-white border border-slate-100 shadow-xs">
        <form action="{{ route('queue.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="flex-1 min-w-[200px]">
                <label class="block font-semibold text-slate-600 mb-1">Filter by Clinic</label>
                <select name="clinic_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Practice Clinics</option>
                    @foreach($clinics as $cl)
                        <option value="{{ $cl->id }}" {{ (string) request('clinic_id', $clinicId ?? '') === (string) $cl->id ? 'selected' : '' }}>
                            {{ $cl->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block font-semibold text-slate-600 mb-1">Filter by Category</label>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Treatment Categories</option>
                    @foreach($categories as $cg)
                        <option value="{{ $cg->id }}" {{ (string) request('category_id', $categoryId ?? '') === (string) $cg->id ? 'selected' : '' }}>
                            {{ $cg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-5">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm">
                    Filter Queue
                </button>
                @if(request()->hasAny(['clinic_id', 'category_id']))
                    <a href="{{ route('queue.index') }}" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ACTIVE QUEUE BOARD -->
    <div class="card-custom bg-white border border-slate-100 shadow-xs overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Today's Active OPD Waiting List</h3>
                <p class="text-xs text-slate-400">Tokens ordered by arrival and doctor room status for {{ now()->format('d M Y') }}</p>
            </div>
            <button onclick="openModal('quickAppointmentModal')" class="flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add Walk-in Patient</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5 pl-5">Token</th>
                        <th class="p-3.5">Patient Details</th>
                        <th class="p-3.5">Time</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Waiting Time</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Payment</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allToday as $apt)
                        <tr class="hover:bg-slate-50/80 transition {{ $apt->status === 'in_consultation' ? 'bg-blue-50/40' : '' }}">
                            <td class="p-3.5 pl-5">
                                <div class="w-9 h-9 rounded-xl {{ $apt->status === 'in_consultation' ? 'bg-blue-600 text-white' : ($apt->status === 'waiting' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700') }} flex items-center justify-center font-black text-sm">
                                    #{{ $apt->token_number }}
                                </div>
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700 block">
                                    {{ $apt->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $apt->patient->patient_id }} • {{ $apt->patient->age }}y / {{ $apt->patient->gender }} • Ph: {{ $apt->patient->mobile }}</span>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-700">
                                {{ $apt->appointment_time }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $apt->appointment_type }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600">
                                @if($apt->status === 'waiting')
                                    <span class="font-bold text-amber-700">
                                        {{ $apt->waiting_since ? $apt->waiting_since->diffForHumans(null, true) : 'Just now' }}
                                    </span>
                                @elseif($apt->status === 'in_consultation')
                                    <span class="font-bold text-blue-700">In Room</span>
                                @elseif($apt->status === 'completed')
                                    <span class="text-slate-400">Done</span>
                                @else
                                    <span class="text-slate-400">Not arrived</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($apt->status === 'in_consultation')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span> In Consultation
                                    </span>
                                @elseif($apt->status === 'waiting')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Waiting
                                    </span>
                                @elseif($apt->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i data-lucide="check" class="w-3 h-3"></i> Completed
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">
                                        {{ $apt->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($apt->payment_status === 'paid')
                                    <span class="font-bold text-emerald-700">₹{{ number_format($apt->consultation_fee, 0) }} (Paid)</span>
                                @elseif($apt->payment_status === 'partially_paid')
                                    <span class="font-bold text-amber-700">Partial Due</span>
                                @else
                                    <span class="font-bold text-rose-600">Unpaid</span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Call In button -->
                                    <button onclick="playChime('{{ $apt->patient->full_name }}', {{ $apt->token_number }})" 
                                            title="Call Token" class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                                        <i data-lucide="volume-2" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Consultation / Action -->
                                    @if($apt->status === 'waiting' || $apt->status === 'in_consultation')
                                        <a href="{{ route('consultations.create', ['appointment_id' => $apt->id]) }}" 
                                           class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1">
                                            <i data-lucide="stethoscope" class="w-3 h-3"></i>
                                            <span>Consult</span>
                                        </a>
                                    @elseif($apt->status === 'scheduled' || $apt->status === 'confirmed')
                                        <form action="{{ route('queue.call', $apt->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold transition">
                                                Check In
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Patient Profile -->
                                    <a href="{{ route('patients.show', $apt->patient_id) }}" title="View Patient" class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <i data-lucide="clock" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                No patients waiting in the queue today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- AUDIO CHIME NOTIFIER SCRIPT -->
<script>
    function playChime(patientName, tokenNo) {
        try {
            // Synthesize voice announcement
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(`Token number ${tokenNo}, ${patientName}, please proceed to the doctor consultation room.`);
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            }
        } catch(e) {
            console.log(e);
        }
        alert(`🔔 Calling Token #${tokenNo} (${patientName}) to Doctor Room!`);
    }
</script>
@endsection
