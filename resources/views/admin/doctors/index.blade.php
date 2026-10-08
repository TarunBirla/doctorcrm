@extends('layouts.app')

@section('title', 'Manage Doctors')
@section('page_title', 'Clinic Doctors Management')
@section('breadcrumb', 'Doctors')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Clinic Doctors</h1>
            <p class="text-sm text-slate-500">Register new doctors, configure fees, manage accounts & login access</p>
        </div>
        <button onclick="openModal('addDoctorModal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0a2540] text-white text-sm font-bold shadow-sm hover:bg-slate-800 transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Add New Doctor
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.doctors.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-8 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search doctor by name, email, specialization, license..."
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Statuses</option>
                    <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="suspended" {{ ($status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Doctor Profile</th>
                        <th class="py-3.5 px-4">Specialization & Reg</th>
                        <th class="py-3.5 px-4">Contact & Login</th>
                        <th class="py-3.5 px-4">Consultation Fee</th>
                        <th class="py-3.5 px-4">Total Activity</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($doctors as $doc)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary font-bold text-sm flex items-center justify-center">
                                        {{ substr($doc->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $doc->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $doc->qualification ?? 'MBBS, MD' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 text-xs block">{{ $doc->specialization }}</span>
                                <span class="text-[11px] font-mono text-slate-400">Reg: {{ $doc->registration_no }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="font-mono text-slate-700 font-semibold">{{ $doc->email }}</div>
                                <div class="text-slate-400 mt-0.5">{{ $doc->phone }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 text-xs">
                                ₹{{ number_format($doc->consultation_fee, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="text-slate-700 font-semibold">{{ $doc->appointments_count }} Appointments</div>
                                <div class="text-slate-400">{{ $doc->visits_count }} Encounters</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($doc->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('settings.availability', ['doctor_id' => $doc->id]) }}" class="p-1.5 rounded-lg border border-slate-200 text-blue-600 hover:bg-blue-50 transition" title="Doctor Schedule & Slots">
                                        <i data-lucide="clock-4" class="w-4 h-4"></i>
                                    </a>

                                    <button type="button" onclick="editDoctor({{ json_encode($doc) }})" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="Edit Doctor">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <form action="{{ route('admin.doctors.toggle-status', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Change status for this doctor?');">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg border border-slate-200 {{ $doc->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition" title="{{ $doc->is_active ? 'Suspend Doctor' : 'Activate Doctor' }}">
                                            <i data-lucide="{{ $doc->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.doctors.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50 transition" title="Delete Doctor">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No doctors registered yet. Click "Add New Doctor" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($doctors->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ADD DOCTOR MODAL -->
<div id="addDoctorModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-primary"></i> Register New Doctor
            </h3>
            <button onclick="closeModal('addDoctorModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('admin.doctors.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Doctor Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Dr. Rajiv Sharma" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" required placeholder="e.g. +91 98765 43210" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Login Email Address *</label>
                    <input type="email" name="email" required placeholder="doctor@carepoint.com" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Login Password *</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Specialization *</label>
                    <input type="text" name="specialization" required placeholder="e.g. Diabetologist / Cardiologist" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Qualifications</label>
                    <input type="text" name="qualification" placeholder="e.g. MBBS, MD (Medicine)" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">MCI / State Reg. No.</label>
                    <input type="text" name="registration_no" placeholder="e.g. MCI-84729-D" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Consultation Fee (₹) *</label>
                    <input type="number" step="0.01" name="consultation_fee" value="800.00" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary font-bold">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Doctor Biography & Introduction</label>
                <textarea name="bio" rows="2" placeholder="Brief background, clinical focus and expertise..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('addDoctorModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-[#0a2540] text-white rounded-xl font-bold hover:bg-slate-800 transition">Save & Create Doctor</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT DOCTOR MODAL -->
<div id="editDoctorModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-primary"></i> Edit Doctor Details
            </h3>
            <button onclick="closeModal('editDoctorModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editDoctorForm" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Doctor Full Name *</label>
                    <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" id="edit_phone" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Login Email Address *</label>
                    <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reset Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep unchanged" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Specialization *</label>
                    <input type="text" name="specialization" id="edit_specialization" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Qualifications</label>
                    <input type="text" name="qualification" id="edit_qualification" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">MCI / State Reg. No.</label>
                    <input type="text" name="registration_no" id="edit_registration_no" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Consultation Fee (₹) *</label>
                    <input type="number" step="0.01" name="consultation_fee" id="edit_consultation_fee" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary font-bold">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Doctor Biography</label>
                <textarea name="bio" id="edit_bio" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-primary"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('editDoctorModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-[#0a2540] text-white rounded-xl font-bold hover:bg-slate-800 transition">Update Doctor</button>
            </div>
        </form>
    </div>
</div>

<script>
function editDoctor(doc) {
    document.getElementById('edit_name').value = doc.name;
    document.getElementById('edit_email').value = doc.email;
    document.getElementById('edit_phone').value = doc.phone;
    document.getElementById('edit_specialization').value = doc.specialization;
    document.getElementById('edit_qualification').value = doc.qualification || '';
    document.getElementById('edit_registration_no').value = doc.registration_no || '';
    document.getElementById('edit_consultation_fee').value = doc.consultation_fee;
    document.getElementById('edit_bio').value = doc.bio || '';
    document.getElementById('editDoctorForm').action = "/admin/doctors/" + doc.id;
    openModal('editDoctorModal');
}
</script>
@endsection
