@extends('landing.layout')

@section('title', 'About Us - SDPC Shyama Devi Physiotherapy Clinic || Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            About Our Clinic & Founder
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">Shyama Devi Physiotherapy Clinic</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            Transforming lives through drug-free, surgery-free and holistic physical rehabilitation in Indore since 2007.
        </p>
    </div>
</section>

<!-- Doctor & Clinic Story -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left: Doctor Bio Highlight Card -->
            <div class="lg:col-span-5">
                <div class="bg-slate-50 border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-800 text-white flex items-center justify-center text-3xl font-black shadow-md">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800">Founder & Director</span>
                            <h2 class="text-2xl font-black text-slate-900 mt-1">Dr. Mahesh Sahu PT</h2>
                            <p class="text-xs text-blue-700 font-bold">M.P.T Neuro &bull; FOMT Australia</p>
                            <p class="text-[11px] text-slate-500 font-semibold">CHIROPRACTIC Sweden &bull; MIAP L-40612</p>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-4 space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="font-bold text-slate-700">Specialization:</span>
                            <span class="font-semibold text-slate-900">Spine, Neuro Rehab & Chiropractic</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="font-bold text-slate-700">Council Reg No:</span>
                            <span class="font-semibold text-slate-900">MPPC-32862 / MIAP L-40612</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="font-bold text-slate-700">Clinical Experience:</span>
                            <span class="font-semibold text-slate-900">17+ Years (Since 2007)</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-bold text-slate-700">Centers:</span>
                            <span class="font-semibold text-slate-900">Khatiwala Tank & Mahalaxmi Nagar</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium">
                        <p class="leading-relaxed">
                            "Our goal is to liberate patients from spine surgeries and chronic painkillers through natural biomechanical restoration."
                        </p>
                    </div>

                    <a href="{{ route('landing.enquiry') }}" class="w-full block text-center py-3 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-blue-700 hover:bg-blue-800 transition shadow">
                        Book Consultation With Dr. Mahesh Sahu
                    </a>
                </div>
            </div>

            <!-- Right: Clinic Philosophy & Detailed Text -->
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <span class="text-xs font-black text-blue-700 uppercase tracking-widest">ABOUT SDPC INDORE</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tracking-tight">
                        Spine and Disability Physiotherapy Clinic
                    </h3>
                </div>

                <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
                    SDPC is a fully equipped modern physiotherapy, osteopathy, and chiropractic center located in Indore. 
                    Directed by <strong>Dr. Mahesh Sahu PT</strong>, master in neurological rehabilitation and spinal decompression disorders.
                </p>

                <div class="bg-blue-50/60 p-5 rounded-2xl border border-blue-200/80 text-blue-950 text-sm leading-relaxed font-semibold">
                    "About 80% of musculoskeletal health problems arise due to mechanical issues that can be treated only by physical means — which is Physiotherapy, Osteopathy, and Chiropractic care."
                </div>

                <p class="text-sm text-slate-600 leading-relaxed">
                    We treat patients with debilitating conditions including slip disc, cervical radiculopathy, shoulder and knee osteoarthritis, sciatica, migraine, paralysis, Bell's palsy, carpal tunnel syndrome, cerebral ataxia, sports injuries, and spinal deformities.
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <span class="block text-xl font-black text-blue-700">50,000+</span>
                        <span class="text-xs text-slate-600 font-bold">Happy Patients</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <span class="block text-xl font-black text-purplePrimary">Drug Free</span>
                        <span class="text-xs text-slate-600 font-bold">Non-Invasive Care</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <span class="block text-xl font-black text-emerald-600">Surgery Free</span>
                        <span class="text-xs text-slate-600 font-bold">Spine Restoration</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Mission, Vision & Values (Pillars from prompt) -->
<section class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">FOUNDATIONAL PILLARS</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Our Core Commitment</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Mission</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Transforming lives through holistic, drug-free care since 2007. We are committed to eradicating root causes of mechanical pain with proven physical interventions.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purplePrimary flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Vision</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Revitalize movement, restore active life, with over 50,000+ patients empowered. To become Central India’s most trusted spine and neuro-rehabilitation institution.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Values</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Reviving lives with holistic, surgery-free therapy solutions. Compassion, scientific precision, patient autonomy, and non-invasive restorative excellence.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- Call to action -->
<section class="py-12 bg-slate-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-4 space-y-4">
        <h3 class="text-2xl font-black">Step Into A Pain-Free Future Today</h3>
        <p class="text-xs text-slate-400 max-w-xl mx-auto">
            Book your slot with Dr. Mahesh Sahu PT at our Khatiwala Tank or Mahalaxmi Nagar clinic.
        </p>
        <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('landing.enquiry') }}" class="px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-900 bg-white hover:bg-slate-100 transition shadow">
                Book Appointment
            </a>
            <a href="tel:9893362477" class="px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-blue-700 hover:bg-blue-800 transition">
                Call +91 98933 62477
            </a>
        </div>
    </div>
</section>

@endsection
