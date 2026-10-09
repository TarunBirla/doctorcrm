@extends('layouts.app')

@section('title', 'Edit Appointment & Extend Treatment')
@section('breadcrumb', 'Appointments / Edit')
@section('page_title', 'Edit Appointment & Therapy Days')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit Appointment & Extend Therapy</h1>
                <p class="text-xs text-slate-500 font-semibold">Appointment #{{ $appointment->appointment_no }} • Token #{{ $appointment->token_number }}</p>
            </div>
        </div>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
            {{ $appointment->patient->full_name }} ({{ $appointment->patient->patient_id }})
        </span>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- PATIENT & CLINIC OVERVIEW -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Patient</span>
                <span class="font-bold text-slate-800 text-sm">{{ $appointment->patient->full_name }}</span>
                <p class="text-[11px] text-slate-500">{{ $appointment->patient->mobile }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Clinic Branch</span>
                <select name="clinic_id" class="w-full mt-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white font-semibold text-slate-800 outline-none">
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ $appointment->clinic_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Therapy Category</span>
                <select name="category_id" class="w-full mt-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white font-semibold text-slate-800 outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $appointment->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- RECOVERY PERCENTAGE -->
        <div class="p-5 rounded-xl border border-slate-200 bg-gradient-to-r from-indigo-50/50 to-blue-50/40 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block font-bold text-slate-800 text-xs">Patient Recovery % (मरीज कितना % सही हुआ?)</label>
                    <p class="text-[11px] text-slate-500 font-semibold">Record overall functional recovery and relief percentage</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <input type="number" id="editPageRecoveryInput" name="recovery_percentage" min="0" max="100" 
                           value="{{ old('recovery_percentage', $appointment->recovery_percentage ?? 0) }}"
                           oninput="syncEditPageRecovery(this.value)"
                           class="w-16 px-2.5 py-1 text-right text-xs font-black text-indigo-700 bg-white border border-slate-300 rounded-lg outline-none">
                    <span class="font-bold text-slate-700">%</span>
                </div>
            </div>

            <input type="range" id="editPageRecoverySlider" min="0" max="100" step="5"
                   value="{{ old('recovery_percentage', $appointment->recovery_percentage ?? 0) }}"
                   oninput="syncEditPageRecoverySlider(this.value)"
                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">

            <div class="flex items-center justify-between pt-1">
                <span class="text-[10px] text-slate-400 font-bold">0% (Initial/Severe)</span>
                <span id="editPageRecoveryLabel" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">
                    {{ $appointment->getRecoveryLevelLabel() }}
                </span>
                <span class="text-[10px] text-emerald-600 font-bold">100% (Fully Cured)</span>
            </div>
        </div>

        <!-- EXTEND SESSIONS & DURATION -->
        <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Base Days Booked</label>
                    <input type="number" name="treatment_days" id="editPageBaseDays" value="{{ old('treatment_days', $appointment->treatment_days) }}" min="1" max="180"
                           oninput="calcEditPageMath()"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-bold text-slate-800 outline-none">
                    <span class="text-[10px] text-slate-400 mt-1 block">Originally scheduled</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Add Additional Days (+Days)</label>
                    <input type="number" name="additional_days" id="editPageAdditionalDays" value="0" min="0" max="180"
                           oninput="calcEditPageMath()"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-black text-blue-700 outline-none">
                    <div class="flex gap-1 mt-1">
                        <button type="button" onclick="setEditExtra(1)" class="px-2 py-0.5 rounded bg-slate-200 text-[10px] font-bold">+1d</button>
                        <button type="button" onclick="setEditExtra(2)" class="px-2 py-0.5 rounded bg-slate-200 text-[10px] font-bold">+2d</button>
                        <button type="button" onclick="setEditExtra(3)" class="px-2 py-0.5 rounded bg-slate-200 text-[10px] font-bold">+3d</button>
                        <button type="button" onclick="setEditExtra(5)" class="px-2 py-0.5 rounded bg-slate-200 text-[10px] font-bold">+5d</button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Daily Session Fee (₹)</label>
                    <input type="number" step="0.01" name="daily_fee" id="editPageDailyFee" value="{{ old('daily_fee', $appointment->daily_fee ?? 800) }}" min="0"
                           oninput="calcEditPageMath()"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-bold text-slate-800 outline-none">
                    <span class="text-[10px] text-slate-400 mt-1 block">Fee per daily session</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-indigo-700 block">Total Treatment Duration</span>
                    <span id="editPageDurationFormula" class="text-xs font-bold text-indigo-900">
                        {{ $appointment->treatment_days }} Days + 0 Days = {{ $appointment->treatment_days }} Total Days
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-emerald-700 block">Total Package Fee</span>
                    <span id="editPageTotalFeeDisplay" class="text-sm font-black text-emerald-700">
                        ₹{{ number_format($appointment->consultation_fee, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- DOCTOR REMARKS -->
        <div>
            <label class="block font-bold text-slate-700 mb-1">Doctor Recovery Remarks & Clinical Notes</label>
            <textarea name="recovery_notes" rows="3" placeholder="Condition improvement notes, rationale for session extension..."
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 outline-none">{{ old('recovery_notes', $appointment->recovery_notes) }}</textarea>
        </div>

        <!-- ACTIONS -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save & Update Appointment
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function syncEditPageRecovery(val) {
    let num = parseInt(val) || 0;
    if (num < 0) num = 0;
    if (num > 100) num = 100;
    document.getElementById('editPageRecoverySlider').value = num;
    updateLabel(num);
}

function syncEditPageRecoverySlider(val) {
    document.getElementById('editPageRecoveryInput').value = val;
    updateLabel(parseInt(val));
}

function updateLabel(pct) {
    const el = document.getElementById('editPageRecoveryLabel');
    if (!el) return;
    if (pct >= 100) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300";
        el.innerText = `🌟 Fully Recovered (100%)`;
    } else if (pct >= 75) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
        el.innerText = `🟢 High Recovery (${pct}%)`;
    } else if (pct >= 50) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200";
        el.innerText = `🟡 Significant Improvement (${pct}%)`;
    } else if (pct >= 25) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200";
        el.innerText = `🟠 Moderate Recovery (${pct}%)`;
    } else if (pct > 0) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200";
        el.innerText = `🔴 Mild / Initial Relief (${pct}%)`;
    } else {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700";
        el.innerText = `Assessment Pending (0%)`;
    }
}

function setEditExtra(days) {
    document.getElementById('editPageAdditionalDays').value = days;
    calcEditPageMath();
}

function calcEditPageMath() {
    const base = parseInt(document.getElementById('editPageBaseDays')?.value) || 1;
    const add = parseInt(document.getElementById('editPageAdditionalDays')?.value) || 0;
    const totalDays = base + add;
    const dailyFee = parseFloat(document.getElementById('editPageDailyFee')?.value) || 0;
    const totalFee = totalDays * dailyFee;

    document.getElementById('editPageDurationFormula').innerText = `${base} Days + ${add} Added = ${totalDays} Total Days`;
    document.getElementById('editPageTotalFeeDisplay').innerText = `₹${totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}
</script>
@endpush
@endsection
