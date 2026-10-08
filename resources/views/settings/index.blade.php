@extends('layouts.app')

@section('title', 'Clinic Settings')
@section('breadcrumb', 'Administration / Settings')
@section('page_title', 'Clinic Profile & System Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Clinic Operational Profile</h3>
                <p class="text-xs text-slate-400">Configure clinic branding, legal numbers, consulting fees and prescription headers</p>
            </div>
            <a href="{{ route('settings.availability') }}" class="px-3.5 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Doctor Availability</span>
            </a>
        </div>

        <form action="{{ route('settings.clinic') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- CLINIC IDENTITY -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Clinic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Clinic Name *</label>
                        <input type="text" name="name" value="{{ $clinic->name }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tagline / Subheading</label>
                        <input type="text" name="tagline" value="{{ $clinic->tagline }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Contact Phone *</label>
                        <input type="text" name="phone" value="{{ $clinic->phone }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Official Email *</label>
                        <input type="email" name="email" value="{{ $clinic->email }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Website URL</label>
                        <input type="text" name="website" value="{{ $clinic->website }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Clinic Address *</label>
                        <input type="text" name="address" value="{{ $clinic->address }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">City & State *</label>
                        <div class="flex gap-2">
                            <input type="text" name="city" value="{{ $clinic->city }}" required class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                            <input type="text" name="state" value="{{ $clinic->state }}" required class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- REGISTRATION & FEES -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Doctor Credentials & Tariffs</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Chief Physician Name *</label>
                        <input type="text" name="doctor_name" value="{{ $clinic->doctor_name }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Medical Council Reg. Number *</label>
                        <input type="text" name="doctor_reg_no" value="{{ $clinic->doctor_reg_no }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Standard Consultation Fee (₹) *</label>
                        <input type="number" name="consultation_fee" value="{{ $clinic->consultation_fee }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Default Slot Duration (Minutes) *</label>
                        <input type="number" name="appointment_duration" value="{{ $clinic->appointment_duration }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">GSTIN Tax Registration</label>
                        <input type="text" name="gst_number" value="{{ $clinic->gst_number }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- OPERATIONAL HOURS -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Operating Schedule</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Working Days *</label>
                        <input type="text" name="working_days" value="{{ $clinic->working_days }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Clinic Hours *</label>
                        <input type="text" name="working_hours" value="{{ $clinic->working_hours }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lunch Break Hours</label>
                        <input type="text" name="break_hours" value="{{ $clinic->break_hours }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <!-- PRESCRIPTION & INVOICE HEADERS -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Prescription & Receipt Custom Texts</h4>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Prescription Header Text</label>
                    <textarea name="prescription_header" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ $clinic->prescription_header }}</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Invoice Footer Disclaimer</label>
                    <textarea name="invoice_footer" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ $clinic->invoice_footer }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm">
                    Save Clinic Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
