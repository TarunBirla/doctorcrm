@extends('layouts.app')

@section('title', 'Medical Reports & Diagnostics')
@section('breadcrumb', 'Clinical / Reports')
@section('page_title', 'Medical Reports & Diagnostics Management')

@section('content')
<div class="space-y-6">

    <!-- FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('medical-reports.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient, report name, lab..." 
                       class="w-72 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <select name="type" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">All Diagnostic Categories</option>
                    @foreach(['Blood Test', 'Urine Test', 'X-Ray', 'MRI', 'CT Scan', 'Ultrasound', 'ECG', 'Pathology', 'Other'] as $cat)
                        <option value="{{ $cat }}" {{ $type === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('medical-reports.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">Reset</a>
            </div>

            <button type="button" onclick="openModal('uploadReportModal')" class="flex items-center gap-1.5 px-4 py-2 bg-navy-900 text-white rounded-xl font-bold hover:bg-navy-800 transition shadow-sm">
                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                <span>Upload Patient Report</span>
            </button>
        </form>
    </div>

    <!-- REPORTS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($reports as $rep)
            <div class="card-custom p-6 bg-white space-y-4 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $rep->report_name }}</h4>
                            <span class="text-[10px] font-mono text-slate-400">{{ $rep->report_no }}</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                        {{ $rep->report_type }}
                    </span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Patient:</span>
                        <a href="{{ route('patients.show', $rep->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                            {{ $rep->patient->full_name }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Date:</span>
                        <span class="font-semibold text-slate-700">{{ $rep->report_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Laboratory:</span>
                        <span class="text-slate-600">{{ $rep->laboratory ?? 'Diagnostic Lab' }}</span>
                    </div>
                </div>

                @if($rep->description)
                    <div class="text-xs text-slate-600 leading-snug">
                        <strong class="text-slate-700 block text-[11px] mb-0.5">Findings Summary:</strong>
                        {{ $rep->description }}
                    </div>
                @endif

                @if($rep->doctor_notes)
                    <div class="p-2.5 bg-blue-50/50 rounded-lg text-xs text-blue-900 border border-blue-100">
                        <strong>Doctor Notes:</strong> {{ $rep->doctor_notes }}
                    </div>
                @endif

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400">{{ $rep->file_size ?? 'Document' }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('medical-reports.preview', $rep->id) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-[11px] font-bold transition">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Preview
                        </a>
                        <a href="{{ route('medical-reports.download', $rep->id) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-[11px] font-bold transition">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                        </a>
                        <form action="{{ route('medical-reports.destroy', $rep->id) }}" method="POST" onsubmit="return confirm('Delete this report?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Delete Report">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 card-custom p-16 text-center text-slate-400 bg-white">
                <i data-lucide="clipboard-list" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <h4 class="font-bold text-slate-700 text-sm">No Diagnostic Reports</h4>
                <p class="text-xs text-slate-400 mt-1">Upload and catalog patient pathology, imaging and blood reports here.</p>
            </div>
        @endforelse
    </div>

    @if($reports->hasPages())
        <div class="card-custom p-4 bg-white">
            {{ $reports->links() }}
        </div>
    @endif

</div>

<!-- UPLOAD MODAL -->
<div id="uploadReportModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Upload Medical Diagnostic Report</h3>
            <button onclick="closeModal('uploadReportModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('medical-reports.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
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
                <label class="block font-semibold text-slate-700 mb-1">Report Name / Test *</label>
                <input type="text" name="report_name" required placeholder="e.g. HbA1c, Liver Function Test, Ultrasound Abdomen" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Category *</label>
                    <select name="report_type" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        <option value="Blood Test">Blood Test</option>
                        <option value="Urine Test">Urine Test</option>
                        <option value="X-Ray">X-Ray</option>
                        <option value="MRI">MRI</option>
                        <option value="CT Scan">CT Scan</option>
                        <option value="Ultrasound">Ultrasound</option>
                        <option value="ECG">ECG</option>
                        <option value="Pathology">Pathology</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Report Date *</label>
                    <input type="date" name="report_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Testing Laboratory / Facility</label>
                <input type="text" name="laboratory" placeholder="e.g. Dr. Lal PathLabs, Quest Diagnostics" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Description / Key Values</label>
                <textarea name="description" rows="3" placeholder="Key report findings..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Doctor Impression</label>
                <input type="text" name="doctor_notes" placeholder="Clinical notes" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Attach File (PDF, JPG, PNG)</label>
                <input type="file" name="attachment" class="w-full text-slate-500 text-xs">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('uploadReportModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 text-white font-bold rounded-xl">Save & Attach Report</button>
            </div>
        </form>
    </div>
</div>
@endsection
