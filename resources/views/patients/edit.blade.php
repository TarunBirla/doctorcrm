@extends('layouts.app')

@section('title', 'Edit Patient - ' . $patient->full_name)
@section('breadcrumb', 'Patients / Edit / ' . $patient->patient_id)
@section('page_title', 'Edit Patient Demographics')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Edit Patient Details</h3>
                <p class="text-xs text-slate-400">Update demographic and contact details for {{ $patient->full_name }}</p>
            </div>
            <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-xl text-xs">
                {{ $patient->patient_id }}
            </span>
        </div>

        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- CLINICAL PRACTICE & THERAPY ALLOCATION -->
            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Practice Clinic</label>
                        <select name="clinic_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                            <option value="">-- Select Clinic --</option>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}" {{ old('clinic_id', $patient->clinic_id) == $cl->id ? 'selected' : '' }}>
                                    {{ $cl->name }} ({{ $cl->city ?? 'Clinic' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Treatment Category</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $patient->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Chief Complaint & Symptoms</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none" placeholder="Primary complaint / symptoms...">{{ old('description', $patient->description) }}</textarea>
                </div>
            </div>

            <!-- PERSONAL DETAILS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="Male" {{ $patient->gender === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $patient->gender === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $patient->gender === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Age *</label>
                    <input type="number" name="age" value="{{ old('age', $patient->age) }}" required min="0" max="120" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="">Unknown</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" {{ $patient->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- CONTACT -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-100">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Primary Mobile *</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $patient->mobile) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alternate Phone</label>
                    <input type="text" name="alt_mobile" value="{{ old('alt_mobile', $patient->alt_mobile) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address', $patient->address) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">City & State</label>
                    <div class="flex gap-2">
                        <input type="text" name="city" value="{{ old('city', $patient->city) }}" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        <input type="text" name="state" value="{{ old('state', $patient->state) }}" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $patient->emergency_phone) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">General Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('notes', $patient->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('patients.show', $patient->id) }}" class="px-5 py-2.5 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-7 py-2.5 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Update Record</button>
            </div>
        </form>
    </div>

</div>
@endsection
