@extends('layouts.app')

@section('title', 'Write New Prescription')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">New Prescription</h1>
                <p class="text-sm text-slate-500">Generate a digital prescription for patient medication</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('prescriptions.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8" id="rxForm">
        @csrf

        <!-- Patient & Doctor Selection -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Patient <span class="text-rose-500">*</span></label>
                <select name="patient_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id', optional($selectedPatient)->id) == $p->id) ? 'selected' : '' }}>
                            {{ $p->patient_id }} - {{ $p->full_name }} ({{ $p->age }}y, {{ $p->gender }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor <span class="text-rose-500">*</span></label>
                <select name="doctor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->specialization }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Date <span class="text-rose-500">*</span></label>
                <input type="date" name="prescription_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
        </div>

        <!-- Diagnosis Summary -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Clinical Diagnosis <span class="text-rose-500">*</span></label>
            <input type="text" name="diagnosis_summary" required placeholder="e.g., Acute Upper Respiratory Tract Infection, Essential Hypertension"
                   value="{{ old('diagnosis_summary') }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        </div>

        <!-- Dynamic Medicine Table -->
        <div class="space-y-4 pt-6 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="pill" class="w-4 h-4 text-emerald-600"></i> Prescribed Medicines
                    </h3>
                    <p class="text-xs text-slate-500">Add medicines, dosage, schedule and intake directions</p>
                </div>
                <button type="button" onclick="addMedRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition border border-emerald-200">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Medicine
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="medTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                            <th class="py-2.5 px-3">Medicine Name</th>
                            <th class="py-2.5 px-2 w-28">Dosage</th>
                            <th class="py-2.5 px-2 w-28">Frequency</th>
                            <th class="py-2.5 px-2 w-24">Duration</th>
                            <th class="py-2.5 px-2 w-28">Timing</th>
                            <th class="py-2.5 px-3">Instructions</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="medRowsContainer">
                        <!-- Initial Row -->
                        <tr class="med-row">
                            <td class="py-2 px-3">
                                <input type="text" name="items[0][medicine_name]" required placeholder="e.g. Paracetamol 650mg"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][dosage]" placeholder="1 Tab"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                            </td>
                            <td class="py-2 px-2">
                                <select name="items[0][frequency]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                                    <option value="1-0-1">1-0-1 (BD)</option>
                                    <option value="1-1-1">1-1-1 (TDS)</option>
                                    <option value="1-0-0">1-0-0 (OD Morn)</option>
                                    <option value="0-0-1">0-0-1 (OD Night)</option>
                                    <option value="SOS">SOS (As needed)</option>
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][duration]" value="5 Days" placeholder="5 Days"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                            </td>
                            <td class="py-2 px-2">
                                <select name="items[0][timing]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                                    <option value="After Food">After Food</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="With Food">With Food</option>
                                    <option value="Empty Stomach">Empty Stomach</option>
                                </select>
                            </td>
                            <td class="py-2 px-3">
                                <input type="text" name="items[0][instructions]" placeholder="e.g. Drink plenty of water"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeMedRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Advice & Follow-up -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor Advice & Dietary Guidelines</label>
                <textarea name="advice" rows="3" placeholder="e.g. Rest well, avoid oily food, repeat BP check in 7 days..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Scheduled Follow-up Date (Optional)</label>
                <input type="date" name="follow_up_date"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <p class="text-[11px] text-slate-400 mt-1">If provided, automatically enters into follow-up tracker.</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('prescriptions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-sm hover:bg-slate-800 transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save & Generate Prescription
            </button>
        </div>
    </form>
</div>

<script>
let medIndex = 1;
function addMedRow() {
    const container = document.getElementById('medRowsContainer');
    const tr = document.createElement('tr');
    tr.className = 'med-row';
    tr.innerHTML = `
        <td class="py-2 px-3">
            <input type="text" name="items[${medIndex}][medicine_name]" required placeholder="Medicine Name"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="items[${medIndex}][dosage]" placeholder="Dosage"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
        </td>
        <td class="py-2 px-2">
            <select name="items[${medIndex}][frequency]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                <option value="1-0-1">1-0-1 (BD)</option>
                <option value="1-1-1">1-1-1 (TDS)</option>
                <option value="1-0-0">1-0-0 (OD Morn)</option>
                <option value="0-0-1">0-0-1 (OD Night)</option>
                <option value="SOS">SOS (As needed)</option>
            </select>
        </td>
        <td class="py-2 px-2">
            <input type="text" name="items[${medIndex}][duration]" value="5 Days" placeholder="Duration"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
        </td>
        <td class="py-2 px-2">
            <select name="items[${medIndex}][timing]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
                <option value="After Food">After Food</option>
                <option value="Before Food">Before Food</option>
                <option value="With Food">With Food</option>
                <option value="Empty Stomach">Empty Stomach</option>
            </select>
        </td>
        <td class="py-2 px-3">
            <input type="text" name="items[${medIndex}][instructions]" placeholder="Instructions"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-primary">
        </td>
        <td class="py-2 px-2 text-center">
            <button type="button" onclick="removeMedRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);
    lucide.createIcons();
    medIndex++;
}

function removeMedRow(btn) {
    const rows = document.querySelectorAll('.med-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('Prescription must have at least one medicine.');
    }
}
</script>
@endsection
