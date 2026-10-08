@extends('layouts.app')

@section('title', 'Follow-up Management')
@section('breadcrumb', 'Clinical / Follow-ups')
@section('page_title', 'Follow-up Scheduling & Tracking')

@section('content')
<div class="space-y-6">

    <!-- KPI STATS PILLS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="?status=today" class="card-custom p-5 bg-white hover:border-blue-400 transition {{ $status === 'today' ? 'border-blue-600 bg-blue-50/20' : '' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Today's Follow-ups</span>
            <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">{{ $todayCount }}</div>
        </a>

        <a href="?status=upcoming" class="card-custom p-5 bg-white hover:border-indigo-400 transition {{ $status === 'upcoming' ? 'border-indigo-600 bg-indigo-50/20' : '' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Upcoming Scheduled</span>
            <div class="text-3xl font-extrabold text-indigo-700 tracking-tight mt-1">{{ $upcomingCount }}</div>
        </a>

        <a href="?status=missed" class="card-custom p-5 bg-white hover:border-rose-400 transition {{ $status === 'missed' ? 'border-rose-600 bg-rose-50/20' : '' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Missed Follow-ups</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">{{ $missedCount }}</div>
        </a>

        <a href="?status=completed" class="card-custom p-5 bg-white hover:border-emerald-400 transition {{ $status === 'completed' ? 'border-emerald-600 bg-emerald-50/20' : '' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Completed Reviews</span>
            <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">{{ $completedCount }}</div>
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Follow-up Patient Registry</h3>
                <p class="text-xs text-slate-400">Scheduled post-consultation reviews and patient checks</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('followups.index') }}" class="px-3 py-1.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    View All
                </a>
                <button onclick="openModal('addFollowUpGlobalModal')" class="flex items-center gap-1.5 px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold hover:bg-navy-800 transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Schedule Follow-up</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3.5 pl-5">Follow-up Date & Time</th>
                        <th class="p-3.5">Patient Details</th>
                        <th class="p-3.5">Doctor</th>
                        <th class="p-3.5">Reason for Review</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($followUps as $fu)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5 pl-5">
                                <span class="font-bold text-slate-900 block">{{ $fu->follow_up_date->format('d M Y') }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $fu->follow_up_time ?? '10:00 AM' }}</span>
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('patients.show', $fu->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                                    {{ $fu->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 block">{{ $fu->patient->patient_id }} • {{ $fu->patient->mobile }}</span>
                            </td>
                            <td class="p-3.5 text-slate-700 font-semibold">{{ $fu->doctor->name ?? 'Doctor' }}</td>
                            <td class="p-3.5 text-slate-800 max-w-xs">{{ $fu->reason }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize 
                                    @if($fu->status === 'completed') bg-emerald-100 text-emerald-800
                                    @elseif($fu->status === 'missed') bg-rose-100 text-rose-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $fu->status }}
                                </span>
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($fu->status === 'scheduled')
                                        <form action="{{ route('followups.status', $fu->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg font-bold text-[11px] transition">
                                                Mark Done
                                            </button>
                                        </form>
                                        <form action="{{ route('followups.status', $fu->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="missed">
                                            <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white rounded-lg font-bold text-[11px] transition">
                                                Missed
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('patients.show', $fu->patient_id) }}" class="p-1.5 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center text-slate-400">No follow-ups found for this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($followUps->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $followUps->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL -->
<div id="addFollowUpGlobalModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Schedule Follow-up Review</h3>
            <button onclick="closeModal('addFollowUpGlobalModal')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form action="{{ route('followups.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Select Patient *</label>
                <select name="patient_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Doctor *</label>
                <select name="doctor_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="follow_up_date" value="{{ now()->addDays(7)->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Time</label>
                    <input type="time" name="follow_up_time" value="10:30" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Review Purpose / Reason *</label>
                <input type="text" name="reason" value="Follow-up review of symptoms and drug response" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addFollowUpGlobalModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 text-white font-bold rounded-xl">Schedule Follow-up</button>
            </div>
        </form>
    </div>
</div>
@endsection
