@extends('landing.layout')

@section('title', 'Contact Us - SDPC Shyama Devi Physiotherapy Clinic || Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            Get In Touch
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">Contact SDPC Clinic Indore</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            We are here to assist your recovery. Visit our Khatiwala Tank or Mahalaxmi Nagar branch, call us, or send a direct WhatsApp inquiry.
        </p>
    </div>
</section>

<!-- Contact Info Cards & Form Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left 5 Cols: Both Branch Cards & Details -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="text-xs font-black text-blue-700 uppercase tracking-widest">CLINICAL CENTERS</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">Our Branches in Indore</h2>
                </div>

                <!-- Branch 1 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-3 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded bg-blue-100 text-blue-900">Branch 1 (South Indore)</span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Open Today
                        </span>
                    </div>
                    <h3 class="text-base font-black text-slate-900">SDPC - Khatiwala Tank Center</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <i class="fa-solid fa-location-dot text-blue-600 mr-1.5"></i>
                        562 / 584C, Near Brilliant School, Khatiwala Tank, Indore, MP 452014
                    </p>
                    <div class="text-xs space-y-1 text-slate-700 pt-1">
                        <p><i class="fa-solid fa-phone text-blue-600 mr-1.5"></i> <strong>Landline:</strong> 0731-4976163</p>
                        <p><i class="fa-solid fa-mobile-screen text-blue-600 mr-1.5"></i> <strong>Mobile / WhatsApp:</strong> +91 98933 62477</p>
                    </div>
                    <div class="pt-2">
                        <a href="https://api.whatsapp.com/send?phone=9111758467&text=Hello%20SDPC%20Clinic%2C%20I%20want%20to%20visit%20Khatiwala%20Tank%20branch." target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat With Khatiwala Tank Reception</span>
                        </a>
                    </div>
                </div>

                <!-- Branch 2 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-3 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded bg-purple-100 text-purplePrimary">Branch 2 (Vijay Nagar / East)</span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Open Today
                        </span>
                    </div>
                    <h3 class="text-base font-black text-slate-900">SDPC - Mahalaxmi Nagar Center</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <i class="fa-solid fa-location-dot text-purplePrimary mr-1.5"></i>
                        MR6-110, Mahalaxmi Nagar, Near Bombay Hospital Ring Road, Indore, MP 452010
                    </p>
                    <div class="text-xs space-y-1 text-slate-700 pt-1">
                        <p><i class="fa-solid fa-phone text-purplePrimary mr-1.5"></i> <strong>Landline:</strong> 0731-4979170</p>
                        <p><i class="fa-solid fa-mobile-screen text-purplePrimary mr-1.5"></i> <strong>Mobile / WhatsApp:</strong> +91 98933 62477</p>
                    </div>
                    <div class="pt-2">
                        <a href="https://api.whatsapp.com/send?phone=9893362477&text=Hello%20SDPC%20Clinic%2C%20I%20want%20to%20visit%20Mahalaxmi%20Nagar%20branch." target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat With Mahalaxmi Nagar Reception</span>
                        </a>
                    </div>
                </div>

                <!-- Timings Notice -->
                <div class="bg-blue-50/70 p-4 rounded-xl border border-blue-200 text-xs text-blue-900 space-y-1">
                    <p class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-clock text-blue-600"></i> Clinic Consultation Timings:
                    </p>
                    <p>Monday - Saturday: 09:00 AM - 08:00 PM</p>
                    <p>Afternoon Break: 01:30 PM - 03:30 PM &bull; <strong>Sunday Closed</strong></p>
                </div>
            </div>

            <!-- Right 7 Cols: Interactive Contact & WhatsApp Form -->
            <div class="lg:col-span-7">
                <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6">
                    <div>
                        <span class="text-xs font-black text-blue-700 uppercase tracking-widest">SEND ENQUIRY</span>
                        <h3 class="text-xl font-black text-slate-900 mt-1">Enroll With Us & Request A Call Back</h3>
                        <p class="text-xs text-slate-500 mt-1">Fill out the form below to receive quick doctor guidance on your symptoms.</p>
                    </div>

                    <form id="contactPageForm" onsubmit="handleContactPageSubmit(event)" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Your Name *</label>
                                <input type="text" id="c_name" required placeholder="Enter full name" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                                <input type="email" id="c_email" placeholder="Optional email" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number *</label>
                                <input type="tel" id="c_phone" required placeholder="10-digit mobile number" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Preferred Branch *</label>
                                <select id="c_branch" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                                    <option value="Khatiwala Tank Branch">Branch 1: Khatiwala Tank</option>
                                    <option value="Mahalaxmi Nagar Branch">Branch 2: Mahalaxmi Nagar</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Your Problem / Condition *</label>
                            <textarea id="c_message" rows="4" required placeholder="Describe your symptoms (e.g. Back pain since 2 weeks, MRI report available...)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl font-black text-xs uppercase tracking-wider text-white bg-blue-700 hover:bg-blue-800 shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg text-emerald-300"></i>
                            <span>Send Message Directly To Doctor Via WhatsApp</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Google Map Embed Section -->
        <div class="mt-16 rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58879.00841936737!2d75.84532091296121!3d22.730544748895017!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962fdc634125af5%3A0x2f06500f64753718!2sSD%20PHYSIOTHERAPY%20CLINIC!5e0!3m2!1sen!2sin!4v1710319700731!5m2!1sen!2sin" width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    function handleContactPageSubmit(e) {
        e.preventDefault();
        const name = document.getElementById('c_name').value.trim();
        const phone = document.getElementById('c_phone').value.trim();
        const email = document.getElementById('c_email').value.trim();
        const branch = document.getElementById('c_branch').value;
        const msg = document.getElementById('c_message').value.trim();

        const messageText = `*SDPC Clinic Patient Inquiry*\n` +
                            `Patient Name: ${name}\n` +
                            `Phone: ${phone}\n` +
                            (email ? `Email: ${email}\n` : '') +
                            `Branch: ${branch}\n` +
                            `Symptoms / Message: ${msg}`;

        const url = `https://api.whatsapp.com/send?phone=9893362477&text=${encodeURIComponent(messageText)}`;
        window.open(url, '_blank');
        e.target.reset();
    }
</script>
@endpush
