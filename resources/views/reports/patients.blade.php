@extends('layouts.app')

@section('title', 'Patient Demographics & Analytics')
@section('breadcrumb', 'Analytics / Patients')
@section('page_title', 'Patient Demographics & Clinical Analytics')

@section('content')
<div class="space-y-6">

    <!-- KPI ROW -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Registry</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalPatients }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Male Patients</span>
            <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">{{ $maleCount }}</div>
            <span class="text-xs text-slate-400 mt-1 block">{{ $totalPatients > 0 ? round(($maleCount/$totalPatients)*100, 1) : 0 }}% of total</span>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Female Patients</span>
            <div class="text-3xl font-extrabold text-purple-700 tracking-tight mt-1">{{ $femaleCount }}</div>
            <span class="text-xs text-slate-400 mt-1 block">{{ $totalPatients > 0 ? round(($femaleCount/$totalPatients)*100, 1) : 0 }}% of total</span>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Other / Non-Binary</span>
            <div class="text-3xl font-extrabold text-slate-700 tracking-tight mt-1">{{ $otherCount }}</div>
        </div>
    </div>

    <!-- AGE GROUPS & BLOOD GROUPS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- AGE GROUPS -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Age Group Demographics</h3>
            <div class="space-y-3 text-xs">
                @foreach($ageGroups as $grpName => $grpCount)
                    @php $pct = $totalPatients > 0 ? round(($grpCount/$totalPatients)*100, 1) : 0; @endphp
                    <div>
                        <div class="flex justify-between font-semibold text-slate-700 mb-1">
                            <span>{{ $grpName }}</span>
                            <span>{{ $grpCount }} patients ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- BLOOD GROUP DISTRIBUTION -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Blood Group Frequency</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-center">
                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                    @php $c = $bloodGroups[$bg] ?? 0; @endphp
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-black text-rose-700 text-base block">{{ $bg }}</span>
                        <strong class="text-slate-800 text-xs block mt-0.5">{{ $c }}</strong>
                        <span class="text-[10px] text-slate-400">patients</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- REGISTRATION TREND -->
    <div class="card-custom p-6 bg-white space-y-4">
        <h3 class="font-bold text-slate-900 text-sm">New Patient Intake History (Past 6 Months)</h3>
        <div class="h-60">
            <canvas id="patientRegistrationChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const regCtx = document.getElementById('patientRegistrationChart');
    if (regCtx) {
        new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($registrationMonths) !!},
                datasets: [{
                    label: 'New Patients Registered',
                    data: {!! json_encode($registrationCounts) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
</script>
@endpush
