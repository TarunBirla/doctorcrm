@extends('landing.layout')

@section('title', 'Book Appointment - SDPC Shyama Devi Physiotherapy Clinic || Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            Instant OPD Booking
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">I Want To Book My Appointment</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            Schedule your personalized physical consultation with Dr. Mahesh Sahu PT at our Khatiwala Tank or Mahalaxmi Nagar clinic.
        </p>
    </div>
</section>

<!-- Appointment Booking Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-8">
            
            <div class="text-center space-y-2 border-b border-slate-100 pb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 text-xl mx-auto">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Patient Appointment Form</h2>
                <p class="text-xs text-slate-500 font-semibold">Select branch & slot to confirm via WhatsApp or Phone</p>
            </div>

            <!-- Booking Form with WhatsApp Message Generator -->
            <form id="appointmentBookingForm" onsubmit="sendWhatsAppAppointment(event)" class="space-y-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Patient Name *</label>
                        <input type="text" id="b_name" required placeholder="Enter your full name" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Mobile Phone Number *</label>
                        <input type="tel" id="b_phone" required placeholder="10-digit phone number" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" id="b_email" placeholder="e.g. name@example.com (optional)" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Clinic Branch *</label>
                        <select id="b_branch" required onchange="handleBranchSlotChange()" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            <option value="">-- Choose Branch Location --</option>
                            <option value="Khatiwala tank Indore">Branch 1: Khatiwala tank Indore</option>
                            <option value="Mahalaxmi Nagar Indore">Branch 2: Mahalaxmi Nagar Indore</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Timing Slot *</label>
                        <select id="b_timing" required disabled class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none disabled:bg-slate-100 disabled:text-slate-400">
                            <option value="">-- Select Branch First --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Preferred Date</label>
                        <input type="date" id="b_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Chief Complaint / Condition Description</label>
                    <textarea id="b_message" rows="3" placeholder="Describe symptoms (e.g. Sharp pain in lower back radiating to right leg, cervical stiffness, shoulder frozen...)" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 rounded-xl font-black text-xs uppercase tracking-wider text-white bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 hover:from-blue-800 hover:to-purple-900 shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2.5">
                        <i class="fa-brands fa-whatsapp text-lg text-emerald-300"></i>
                        <span>Confirm Appointment Via WhatsApp (+91 98933 62477)</span>
                    </button>
                </div>
            </form>

            <!-- Branch Summary Badges -->
            <div class="border-t border-slate-100 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <strong class="text-slate-900 block font-black">Branch 1: Khatiwala Tank</strong>
                    <p class="mt-0.5">562 / 584C, Near Brilliant School, Indore</p>
                    <p class="text-blue-700 font-bold mt-1">Ph: 0731-4976163 / 98933 62477</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <strong class="text-slate-900 block font-black">Branch 2: Mahalaxmi Nagar</strong>
                    <p class="mt-0.5">MR6-110, Mahalaxmi Nagar, Indore</p>
                    <p class="text-blue-700 font-bold mt-1">Ph: 0731-4979170 / 98933 62477</p>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    const clinicTimingData = {
        "Khatiwala tank Indore": ["08:30 AM", "09:00 AM", "10:30 AM", "12:00 PM", "02:00 PM", "04:30 PM", "06:00 PM", "07:30 PM"],
        "Mahalaxmi Nagar Indore": ["04:00 PM", "04:30 PM", "05:00 PM", "05:30 PM", "06:30 PM", "07:00 PM", "07:30 PM"]
    };

    function handleBranchSlotChange() {
        const branchSelect = document.getElementById('b_branch');
        const timingSelect = document.getElementById('b_timing');
        const selectedBranch = branchSelect.value;

        timingSelect.innerHTML = '<option value="">-- Choose Slot --</option>';

        if (selectedBranch && clinicTimingData[selectedBranch]) {
            timingSelect.disabled = false;
            clinicTimingData[selectedBranch].forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot;
                opt.textContent = slot;
                timingSelect.appendChild(opt);
            });
        } else {
            timingSelect.disabled = true;
            timingSelect.innerHTML = '<option value="">-- Select Branch First --</option>';
        }
    }

    function sendWhatsAppAppointment(e) {
        e.preventDefault();
        const name = document.getElementById('b_name').value.trim();
        const email = document.getElementById('b_email').value.trim();
        const phone = document.getElementById('b_phone').value.trim();
        const branch = document.getElementById('b_branch').value;
        const timing = document.getElementById('b_timing').value;
        const date = document.getElementById('b_date').value;
        const message = document.getElementById('b_message').value.trim();

        const whatsappMessage = `Hello Dr. Mahesh Sahu,\n` +
                                `I am ${name}.\n` +
                                (email ? `Email: ${email}\n` : '') +
                                `Phone: ${phone}\n` +
                                `Date: ${date}\n` +
                                `Interested in appointment at: ${branch} at ${timing}.\n` +
                                `Message: ${message || 'Need consultation for pain relief'}`;

        const whatsappURL = `https://api.whatsapp.com/send?phone=9111758467&text=${encodeURIComponent(whatsappMessage)}`;
        window.open(whatsappURL, '_blank');
        e.target.reset();
        document.getElementById('b_timing').disabled = true;
    }
</script>
@endpush
