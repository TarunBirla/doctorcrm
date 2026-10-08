@extends('layouts.app')

@section('title', 'My Account Profile')
@section('page_title', 'Account Settings & Profile')
@section('breadcrumb', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Profile</h1>
            <p class="text-sm text-slate-500">Manage your personal information, login credentials and preferences</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
            {{ $user->role === 'super_admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
            {{ $user->role === 'doctor' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
            {{ $user->role === 'receptionist' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $user->role === 'super_admin' ? 'bg-purple-600' : ($user->role === 'doctor' ? 'bg-blue-600' : 'bg-emerald-600') }}"></span>
            Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </span>
    </div>

    <!-- User Information Form -->
    <form action="{{ route('profile.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-base">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Personal Information</h3>
                    <p class="text-xs text-slate-400">Account details visible on clinic communications</p>
                </div>
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#0a2540] hover:bg-slate-800 text-white font-bold text-xs transition">
                Update Profile
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary font-mono text-xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
            </div>
        </div>

        @if($doctor)
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Doctor Professional Profile</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Academic Qualifications</label>
                        <input type="text" name="qualification" value="{{ old('qualification', $doctor->qualification) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Medical Council Registration No.</label>
                        <input type="text" name="registration_no" value="{{ old('registration_no', $doctor->registration_no) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Default Consultation Fee (₹)</label>
                        <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor Bio & Credentials</label>
                    <textarea name="bio" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">{{ old('bio', $doctor->bio) }}</textarea>
                </div>
            </div>
        @endif
    </form>

    <!-- Change Password Form -->
    <form action="{{ route('profile.password') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
                    <i data-lucide="key-round" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Security & Password</h3>
                    <p class="text-xs text-slate-400">Ensure your clinic account uses a strong, private password</p>
                </div>
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                Change Password
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Current Password <span class="text-rose-500">*</span></label>
                <input type="password" name="current_password" required placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">New Password <span class="text-rose-500">*</span></label>
                <input type="password" name="new_password" required placeholder="Min 6 characters"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm New Password <span class="text-rose-500">*</span></label>
                <input type="password" name="new_password_confirmation" required placeholder="Repeat new password"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-primary">
            </div>
        </div>
    </form>
</div>
@endsection
