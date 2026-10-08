@extends('layouts.app')

@section('title', 'Patient Progress Tracker')
@section('breadcrumb', 'Clinical / Progress')
@section('page_title', 'Longitudinal Patient Progress & Vitals Tracker')

@section('content')
<div class="space-y-6">

    <!-- SELECT PATIENT HEADER CARD -->
    <div class="card-custom p-5 bg-white flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('progress.index') }}" method="GET" class="flex items-center gap-3 text-xs">
            <span class="font-bold text-slate-700">Select Patient:</span>
            <select name="patient_id" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-semibold text-slate-800 outline-none focus:bg-white w-72">
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ $selectedPatient && $selectedPatient->id === $p->id ? 'selected' : '' }}>
                        {{ $p->full_name }} ({{ $p->patient_id }} • {{ $p->mobile }})
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedPatient)
            <div class="flex items-center gap-2">
                <a href="{{ route('patients.show', $selectedPatient->id) }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Patient Profile
                </a>
                <button onclick="openModal('addProgressRecordModal')" class="px-4 py-2 bg-navy-900 text-white rounded-xl text-xs font-bold hover:bg-navy-800 transition shadow-sm flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Log Vitals Entry</span>
                </button>
            </div>
        @endif
    </div>

    @if($selectedPatient)
        <!-- GRAPHS ROW -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- WEIGHT & BMI TREND -->
            <div class="card-custom p-6 bg-white space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Weight & BMI Progress</h3>
                        <p class="text-xs text-slate-400">Recorded weight measurements across visits</p>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="weightProgressChart"></canvas>
                </div>
            </div>

            <!-- BLOOD PRESSURE & PULSE TREND -->
            <div class="card-custom p-6 bg-white space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Blood Pressure & Pulse Trends</h3>
                        <p class="text-xs text-slate-400">Systolic/Diastolic control and heart rate stability</p>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="bpProgressChart"></canvas>
                </div>
            </div>

        </div>

        <!-- PROGRESS TABLE LOG -->
        <div class="card-custom bg-white overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Clinical Progress Records for {{ $selectedPatient->full_name }}</h3>
                <span class="text-xs text-slate-400">Total {{ $progressRecords->count() }} observations</span>
            </div>

            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <tr class="border-b border-slate-100">
                            <th class="p-3 pl-5">Date</th>
                            <th class="p-3">Weight (kg)</th>
                            <th class="p-3">BP (mmHg)</th>
                            <th class="p-3">Pulse (bpm)</th>
                            <th class="p-3">Temp (°F)</th>
                            <th class="p-3">SpO2</th>
                            <th class="p-3">BMI</th>
                            <th class="p-3">Pain Score</th>
                            <th class="p-3 pr-5">Treatment Response & Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($progressRecords as $pr)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 pl-5 font-bold text-slate-900">{{ $pr->recorded_date->format('d M Y') }}</td>
                                <td class="p-3 font-semibold text-blue-700">{{ $pr->weight ? $pr->weight . ' kg' : '-' }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ $pr->bp_systolic ? $pr->bp_systolic . '/' . $pr->bp_diastolic : '-' }}</td>
                                <td class="p-3 text-slate-700">{{ $pr->pulse ? $pr->pulse . ' bpm' : '-' }}</td>
                                <td class="p-3 text-slate-700">{{ $pr->temperature ? $pr->temperature . ' °F' : '-' }}</td>
                                <td class="p-3 font-semibold text-emerald-700">{{ $pr->spo2 ? $pr->spo2 . '%' : '-' }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ $pr->bmi ?? '-' }}</td>
                                <td class="p-3 font-bold text-amber-700">{{ $pr->pain_level }}/10</td>
                                <td class="p-3 pr-5 text-slate-600">
                                    {{ $pr->treatment_response ?? $pr->symptoms_assessment ?? $pr->doctor_notes ?? 'Routine follow-up' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="p-12 text-center text-slate-400">No vitals progress logged yet for this patient.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: ADD PROGRESS RECORD -->
        <div id="addProgressRecordModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 text-sm">Log Vitals for {{ $selectedPatient->full_name }}</h3>
                    <button onclick="closeModal('addProgressRecordModal')" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form action="{{ route('progress.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                        <input type="date" name="recorded_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Weight (kg)</label>
                            <input type="number" step="0.1" name="weight" placeholder="e.g. 76.5" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Height (cm)</label>
                            <input type="number" name="height" placeholder="e.g. 170" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pulse (bpm)</label>
                            <input type="number" name="pulse" placeholder="e.g. 78" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">BP Systolic</label>
                            <input type="number" name="bp_systolic" placeholder="124" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">BP Diastolic</label>
                            <input type="number" name="bp_diastolic" placeholder="82" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pain (0-10)</label>
                            <input type="number" min="0" max="10" name="pain_level" value="0" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Treatment Response Notes</label>
                        <input type="text" name="treatment_response" placeholder="Remarks on patient recovery..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('addProgressRecordModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-navy-900 text-white font-bold rounded-xl">Save Vitals</button>
                    </div>
                </form>
            </div>
        </div>

    @endif

</div>
@endsection

@push('scripts')
@if($selectedPatient)
<script>
    const wCtx = document.getElementById('weightProgressChart');
    if (wCtx) {
        new Chart(wCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Weight (kg)',
                    data: {!! json_encode($chartWeights) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { title: { display: true, text: 'kg' } }
                }
            }
        });
    }

    const bpCtx = document.getElementById('bpProgressChart');
    if (bpCtx) {
        new Chart(bpCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [
                    {
                        label: 'BP Systolic (mmHg)',
                        data: {!! json_encode($chartSystolic) !!},
                        borderColor: '#dc2626',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'BP Diastolic (mmHg)',
                        data: {!! json_encode($chartDiastolic) !!},
                        borderColor: '#ea580c',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Pulse (bpm)',
                        data: {!! json_encode($chartPulse) !!},
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { title: { display: true, text: 'Value' } }
                }
            }
        });
    }
</script>
@endif
@endpush
