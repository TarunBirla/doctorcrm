@extends('layouts.app')

@section('title', 'Appointment Performance Reports')
@section('breadcrumb', 'Analytics / Appointments')
@section('page_title', 'Appointment Volume & Status Distribution')

@section('content')
<div class="space-y-6">

    <!-- DATE RANGE FILTER -->
    <div class="card-custom p-5 bg-white flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('reports.appointments') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <span class="font-bold text-slate-700">Period Filter:</span>
            <select name="range" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-semibold outline-none">
                <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $range === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $range === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $range === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $range === 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>

        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
            Reporting: {{ $label }}
        </span>
    </div>

    <!-- STATS ROW -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Appointments</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalAppointments }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Completed</span>
            <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">{{ $completedCount }}</div>
            <span class="text-xs text-slate-400 mt-1 block">{{ $totalAppointments > 0 ? round(($completedCount/$totalAppointments)*100, 1) : 0 }}% completion rate</span>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Cancelled</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">{{ $cancelledCount }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500 block">No Show</span>
            <div class="text-3xl font-extrabold text-amber-700 tracking-tight mt-1">{{ $noShowCount }}</div>
        </div>
    </div>

    <!-- SPLIT: TYPES & STATUSES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- APPOINTMENT TYPES -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Consultation Classification Types</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700">New Patient Consultations</span>
                    <strong class="text-blue-700">{{ $newTypeCount }} ({{ $totalAppointments > 0 ? round(($newTypeCount/$totalAppointments)*100) : 0 }}%)</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700">Follow-up Visits</span>
                    <strong class="text-indigo-700">{{ $followUpTypeCount }}</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700">Revisits</span>
                    <strong class="text-purple-700">{{ $revisitTypeCount }}</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700">Emergency OPD Walk-ins</span>
                    <strong class="text-rose-700">{{ $emergencyTypeCount }}</strong>
                </div>
            </div>
        </div>

        <!-- STATUS BREAKDOWN CHART -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Appointment Status Distribution</h3>
            <div class="h-60">
                <canvas id="statusDonutChart"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const donutEl = document.getElementById('statusDonutChart');
    if (donutEl) {
        new Chart(donutEl, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Waiting / Pending', 'Cancelled', 'No Show'],
                datasets: [{
                    data: [{{ $completedCount }}, {{ $waitingCount }}, {{ $cancelledCount }}, {{ $noShowCount }}],
                    backgroundColor: ['#10b981', '#3b82f6', '#f43f5e', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } }
                }
            }
        });
    }
</script>
@endpush
