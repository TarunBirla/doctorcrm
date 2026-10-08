@extends('layouts.app')

@section('title', 'Register New Patient')
@section('breadcrumb', 'Patients / Register')
@section('page_title', 'New Patient Intake & Registration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Patient Registration Form</h3>
                <p class="text-xs text-slate-400">Complete demographic and clinical baseline intake for new outpatient record</p>
            </div>
            <div class="px-3 py-1 bg-blue-50 text-blue-700 font-mono font-bold text-xs rounded-xl border border-blue-200">
                Assigned ID: {{ $nextId }}
            </div>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- SECTION 1: PERSONAL INFORMATION -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Personal Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" required placeholder="e.g. Rajesh" value="{{ old('first_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" required placeholder="e.g. Sharma" value="{{ old('last_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Age (Years) *</label>
                        <input type="number" name="age" required min="0" max="120" placeholder="e.g. 45" value="{{ old('age') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Blood Group</label>
                        <select name="blood_group" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="">Unknown</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}">{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Marital Status</label>
                        <select name="marital_status" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="Single">Single</option>
                            <option value="Married" selected>Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Occupation</label>
                        <input type="text" name="occupation" placeholder="e.g. Executive, Teacher..." value="{{ old('occupation') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CONTACT INFORMATION -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Contact & Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Primary Mobile *</label>
                        <input type="text" name="mobile" required placeholder="10-digit number" value="{{ old('mobile') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alternate Phone</label>
                        <input type="text" name="alt_mobile" placeholder="Optional phone" value="{{ old('alt_mobile') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="patient@example.com" value="{{ old('email') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" placeholder="House/Flat, Street..." value="{{ old('address') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">City & State</label>
                        <div class="flex gap-2">
                            <input type="text" name="city" value="{{ old('city', 'Gurugram') }}" placeholder="City" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                            <input type="text" name="state" value="{{ old('state', 'Haryana') }}" placeholder="State" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Emergency Contact Person</label>
                        <input type="text" name="emergency_contact" placeholder="Relation & Name e.g. Sunita (Wife)" value="{{ old('emergency_contact') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Emergency Phone Number</label>
                        <input type="text" name="emergency_phone" placeholder="Emergency phone" value="{{ old('emergency_phone') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: MEDICAL BASELINE HISTORY -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Baseline Medical History</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Known Medical Conditions / Diseases</label>
                        <textarea name="conditions" rows="2" placeholder="e.g. Type 2 Diabetes, Hypertension..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('conditions') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-rose-700 mb-1">Drug / Food Allergies</label>
                        <textarea name="allergies" rows="2" placeholder="e.g. Penicillin, NSAIDs, Sulfa..." class="w-full px-3 py-2 border border-rose-200 rounded-xl bg-rose-50/50 outline-none">{{ old('allergies') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Current Active Medications</label>
                        <textarea name="current_medications" rows="2" placeholder="Prescription drugs currently taking..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('current_medications') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Past Surgeries & Family History</label>
                        <textarea name="surgeries" rows="2" placeholder="Major surgical history..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('surgeries') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: MISCELLANEOUS & NOTES -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Referral Source</label>
                        <input type="text" name="referral_source" placeholder="e.g. Doctor recommendation, Friend, Google..." value="{{ old('referral_source') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">General Notes / Remarks</label>
                        <input type="text" name="notes" placeholder="General clinical observation..." value="{{ old('notes') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('patients.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-bold rounded-xl shadow-sm transition">
                    Register Patient & Open File
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
