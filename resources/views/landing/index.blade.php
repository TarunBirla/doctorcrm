@extends('landing.layout')

@section('title', 'SDPC - Shyama Devi Physiotherapy Clinic || Faster Recovery & Lasting Results || Indore')

@section('content')

<!-- ========================================== -->
<!-- HERO SECTION                               -->
<!-- ========================================== -->
<section class="relative bg-gradient-to-b from-blue-50/60 via-white to-slate-50 pt-10 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Column: Catchy Text & Action CTAs -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-900 text-xs font-black tracking-wide border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>DRUG FREE &bull; SURGERY FREE &bull; PAIN FREE SPINE SPECIALIST</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-[1.15] tracking-tight">
                    Faster Recovery & <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800">Lasting Results</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Welcome to <strong class="text-slate-900">SDPC (Shyama Devi Physiotherapy Clinic)</strong>. Directed by 
                    <strong class="text-blue-800">Dr. Mahesh Sahu PT</strong> (M.P.T Neuro, FOMT Australia, Chiropractic Sweden). 
                    We provide world-class non-invasive rehabilitation for spine, neurological disorders, and joint pains in Indore.
                </p>

                <!-- Core Badges -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 pt-1 text-xs font-bold text-slate-700">
                    <span class="px-3 py-1 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center gap-1.5">
                        <i class="fa-solid fa-brain text-blue-600"></i> Neuro Rehabilitation
                    </span>
                    <span class="px-3 py-1 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center gap-1.5">
                        <i class="fa-solid fa-bone text-purple-600"></i> Swedish Chiropractic
                    </span>
                    <span class="px-3 py-1 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center gap-1.5">
                        <i class="fa-solid fa-hand-holding-medical text-emerald-600"></i> Osteopathy & Spine Care
                    </span>
                </div>

                <!-- Call to action buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-3">
                    <a href="{{ route('landing.enquiry') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-xl font-bold text-sm text-white bg-blue-700 hover:bg-blue-800 shadow-md shadow-blue-500/25 transition">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Book Clinic Appointment</span>
                    </a>
                    <a href="https://api.whatsapp.com/send?phone=9893362477&text=Hello%20Dr.%20Mahesh%20Sahu%2C%20I%20would%20like%20to%20consult%20for%20physiotherapy." target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-2xs transition">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                        <span>WhatsApp Quick Chat</span>
                    </a>
                    <a href="tel:9893362477" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold text-sm text-blue-700 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-phone"></i>
                        <span>+91 98933 62477</span>
                    </a>
                </div>

                <!-- Stats summary bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-200">
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl font-black text-slate-900">50,000+</span>
                        <span class="text-xs text-slate-500 font-semibold">Patients Empowered</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl font-black text-slate-900">17+ Yrs</span>
                        <span class="text-xs text-slate-500 font-semibold">Experience (Since 2007)</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl font-black text-slate-900">2 Centers</span>
                        <span class="text-xs text-slate-500 font-semibold">In Indore City</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl font-black text-emerald-600">98%</span>
                        <span class="text-xs text-slate-500 font-semibold">Recovery Rate</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Doctor Feature Card & Clinic Highlight -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-800 text-white flex items-center justify-center text-2xl font-black shadow-md shrink-0">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Chief Consultant</span>
                            <h3 class="text-xl font-black text-slate-900 mt-1">Dr. Mahesh Sahu PT</h3>
                            <p class="text-xs text-slate-500 font-bold">M.P.T Neuro &bull; FOMT Australia</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-certificate text-blue-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Specialization:</strong>
                                <p>Spine Specialist, Neuro Rehab & Swedish Chiropractic</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-id-card text-blue-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Reg No:</strong>
                                <p>M.I.A.P. L-40612 &bull; MPPC-32862</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-hospital text-blue-600 text-sm mt-0.5"></i>
                            <div>
                                <strong class="text-slate-800">Clinic Branches:</strong>
                                <p>562 Khatiwala Tank & MR6 Mahalaxmi Nagar, Indore</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-100 text-xs text-blue-900">
                        <p class="italic font-semibold leading-relaxed">
                            "About 80% health problems arise due to mechanical issues that can be treated only by physical means which is Physiotherapy, Osteopathy and Chiropractic."
                        </p>
                    </div>

                    <a href="{{ route('landing.enquiry') }}" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-black uppercase tracking-wider text-white bg-slate-900 hover:bg-slate-800 transition">
                        <span>Schedule Doctor Consultation</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- ABOUT CLINIC PHILOSOPHY & PILLARS          -->
<!-- ========================================== -->
<section class="py-16 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">ABOUT OUR CLINIC</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Osteopathy &bull; Physiotherapy &bull; Chiropractic Center
            </h2>
            <p class="text-sm text-slate-600 leading-relaxed">
                SDPC is a fully equipped modern rehabilitation facility. We integrate manual therapy, advanced spinal decompression, and neuro-motor re-education to eliminate the root causes of pain.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Mission -->
            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200/80 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Mission</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Transforming lives through holistic, drug-free care since 2007. Providing cutting-edge physical medicine that prevents unnecessary spinal surgeries.
                </p>
            </div>

            <!-- Vision -->
            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200/80 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purplePrimary flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Vision</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Revitalize movement, restore active life. Over 50,000+ patients empowered with long-term pain relief and improved musculoskeletal posture.
                </p>
            </div>

            <!-- Values -->
            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200/80 hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Our Values</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Reviving lives with holistic, surgery-free therapy solutions. Ethical practice, evidence-based physical techniques, and patient-centered healing.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- OUR SPECIALIZED SERVICES (10 KEY CONDITIONS) -->
<!-- ========================================== -->
<section class="py-16 bg-slate-50" id="services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-12">
            <div>
                <span class="text-xs font-black text-blue-700 uppercase tracking-widest">CLINICAL SPECIALITIES</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                    Conditions We Successfully Treat
                </h2>
            </div>
            <a href="{{ route('landing.services') }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 hover:text-blue-800 transition">
                <span>View Complete Musculoskeletal Conditions</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            @php
            $conditions = [
                ['name' => 'Migraine & Headaches', 'icon' => 'fa-head-side-virus', 'desc' => 'Cervicogenic headaches, muscular tension and occipital neuralgia relief.'],
                ['name' => 'Cervical Spine Pain', 'icon' => 'fa-bone', 'desc' => 'Neck spasm, cervical spondylosis, radiculopathy and posture strain.'],
                ['name' => 'Shoulder & Elbow Pain', 'icon' => 'fa-person-walking-luggage', 'desc' => 'Frozen shoulder, rotator cuff tear, tennis elbow and impingement.'],
                ['name' => 'Low Back Pain', 'icon' => 'fa-person-booth', 'desc' => 'Mechanical low back ache, lumbar spondylosis and muscle guarding.'],
                ['name' => 'Slip Disc & Herniation', 'icon' => 'fa-compress', 'desc' => 'Decompression therapy for disc bulge, annular tears and extruded discs.'],
                ['name' => 'Sciatica Nerve Pain', 'icon' => 'fa-bolt', 'desc' => 'Relief from sharp radiating leg pain, numbness and tingling sensation.'],
                ['name' => 'Knee Pain & Arthritis', 'icon' => 'fa-person-cane', 'desc' => 'Osteoarthritis care, meniscus rehab, ligament strengthening & mobility.'],
                ['name' => 'Ankle Pain & Sprains', 'icon' => 'fa-shoe-prints', 'desc' => 'Ligament recovery, plantar fasciitis, heel spurs and biomechanics.'],
                ['name' => "Bell's Palsy & Facial Nerve", 'icon' => 'fa-face-meh', 'desc' => 'Facial muscle re-education, galvanic stimulation and synkinesis control.'],
                ['name' => 'Paralysis & Stroke Rehab', 'icon' => 'fa-wheelchair', 'desc' => 'Hemiplegia, gait re-training, motor balance and neuro restoration.']
            ];
            @endphp

            @foreach($conditions as $c)
            <div class="bg-white p-5 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 group-hover:bg-blue-700 group-hover:text-white transition flex items-center justify-center text-lg mb-3">
                        <i class="fa-solid {{ $c['icon'] }}"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 group-hover:text-blue-700 transition">{{ $c['name'] }}</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $c['desc'] }}</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('landing.enquiry') }}" class="text-[11px] font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-1">
                        <span>Book Care</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- HIGH-TECH EQUIPMENT LIST                   -->
<!-- ========================================== -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">WORLD-CLASS TECHNOLOGY</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Advanced Rehabilitation Equipment
            </h2>
            <p class="text-sm text-slate-600 leading-relaxed">
                We combine specialized hands-on techniques with the world's most modern robotic & electro-therapeutic modalities to accelerate nerve and tissue healing.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-bed"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Flexion Distraction Machine</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Automated motorized decompression table designed to reduce intradiscal pressure and withdraw disc herniations without surgery.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purplePrimary flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-magnet"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Super Inductive System (SIS)</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    High-intensity electromagnetic field triggers muscle contractions, pain blockade, fracture healing, and rapid joint mobilization.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Class 4 & 3 Laser Therapy</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Deep penetrating therapeutic laser stimulates cellular ATP energy, dramatically reduces inflammation, and speeds up tendon repair.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-wave-square"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Matrix Rhythm Therapy</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Micro-biological rhythmic resonance therapy restoring tissue elasticity and cleansing extracellular metabolic waste.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-person-walking"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Unweighing Body Support System</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Enables stroke and spinal cord patients to practice walking upright in a zero-gravity supported harness without danger of falls.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-arrows-up-down"></i>
                </div>
                <h3 class="text-base font-black text-slate-900">Drop Table for Chiropractic</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Precision segmental mechanical drop pieces allow high-velocity low-amplitude spinal adjustments with zero patient discomfort.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- VOICES OF SATISFACTION (VIDEO TESTIMONIALS) -->
<!-- ========================================== -->
<section class="py-16 bg-slate-950 text-white" id="testimonials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12">
            <span class="text-xs font-black text-blue-400 uppercase tracking-widest">VOICES OF SATISFACTION</span>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Real Patients &bull; Real Recovery Stories
            </h2>
            <p class="text-sm text-slate-400 leading-relaxed">
                Watch our patient video testimonials sharing their journey from severe spine & joint pain to active, healthy lives.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-lg">
                <div class="aspect-video w-full">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/RZcmC6vy-BM" title="SDPC Patient Testimonial 1" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="p-4">
                    <h4 class="text-sm font-bold text-white">Spine & Disc Bulge Recovery</h4>
                    <p class="text-xs text-slate-400 mt-1">Patient shares relief from chronic sciatica and numbness without surgery.</p>
                </div>
            </div>

            <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-lg">
                <div class="aspect-video w-full">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/ET1FI26hLMc" title="SDPC Patient Testimonial 2" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="p-4">
                    <h4 class="text-sm font-bold text-white">Cervical & Neck Pain Treatment</h4>
                    <p class="text-xs text-slate-400 mt-1">Faster recovery with chiropractic adjustment and physiotherapy exercises.</p>
                </div>
            </div>

            <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-lg">
                <div class="aspect-video w-full">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/aqv7dKRMBJg" title="SDPC Patient Testimonial 3" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="p-4">
                    <h4 class="text-sm font-bold text-white">Paralysis & Neuro Rehabilitation</h4>
                    <p class="text-xs text-slate-400 mt-1">Remarkable functional mobility restoration under Dr. Mahesh Sahu PT.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('landing.testimonials') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-900 bg-white hover:bg-slate-100 transition shadow">
                <span>Watch More Patient Reviews (6+ Videos)</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- QUICK BOOKING & WHATSAPP FORM              -->
<!-- ========================================== -->
<section class="py-16 bg-blue-700 text-white relative overflow-hidden" id="booking">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <div class="lg:col-span-6 space-y-4">
                <span class="text-xs font-black uppercase tracking-widest text-blue-200">START YOUR RECOVERY</span>
                <h2 class="text-2xl sm:text-4xl font-black leading-tight">
                    Kick Discomfort To The Curb With Personalized Care
                </h2>
                <p class="text-sm text-blue-100 leading-relaxed">
                    Book an OPD slot at either of our Indore branches or send a direct appointment request via WhatsApp. Our team will verify your preferred time promptly.
                </p>

                <div class="space-y-3 pt-2 text-xs text-blue-100">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-300 text-base"></i>
                        <span>Detailed Musculoskeletal / Neurological physical assessment</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-300 text-base"></i>
                        <span>Personalized targeted exercise program & postural education</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-300 text-base"></i>
                        <span>100% Drug Free, Surgery Free & Pain Free healing</span>
                    </div>
                </div>
            </div>

            <!-- Booking Form Card -->
            <div class="lg:col-span-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-800 shadow-2xl">
                    <h3 class="text-lg font-black text-slate-900 mb-1">Book Your Appointment</h3>
                    <p class="text-xs text-slate-500 mb-5">Select your branch & preferred time for instant booking</p>

                    <form id="homeAppointmentForm" onsubmit="handleHomeAppointmentSubmit(event)" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Your Full Name *</label>
                                <input type="text" id="h_name" required placeholder="e.g. Rahul Sharma" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number *</label>
                                <input type="tel" id="h_phone" required placeholder="e.g. 9826012345" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Select Clinic Branch *</label>
                                <select id="h_branch" required onchange="updateHomeTimings()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                                    <option value="">-- Choose Branch --</option>
                                    <option value="Khatiwala Tank Branch (Indore)">Branch 1: Khatiwala Tank</option>
                                    <option value="Mahalaxmi Nagar Branch (Indore)">Branch 2: Mahalaxmi Nagar</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Preferred Timing *</label>
                                <select id="h_timing" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
                                    <option value="">-- Select Branch First --</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Chief Complaint / Condition</label>
                            <textarea id="h_message" rows="2" placeholder="e.g. Lower back pain, neck stiffness, slip disc..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl font-black text-xs uppercase tracking-wider text-white bg-blue-700 hover:bg-blue-800 shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-base text-emerald-300"></i>
                            <span>Send Booking Via WhatsApp</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========================================== -->
<!-- LOCATION MAP & CLINIC INFO                 -->
<!-- ========================================== -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto space-y-2 mb-10">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">VISIT OUR CLINICS</span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Easy Accessibility in Indore</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <span class="inline-block px-2.5 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-black uppercase mb-2">Branch 1</span>
                    <h3 class="text-base font-black text-slate-900">SDPC - Khatiwala Tank</h3>
                    <p class="text-xs text-slate-600 mt-1">562 / 584C, Near Brilliant School, Khatiwala Tank, Indore, MP 452014</p>
                    <p class="text-xs font-bold text-slate-800 mt-2"><i class="fa-solid fa-phone text-blue-600"></i> 0731-4976163 / 98933 62477</p>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <span class="inline-block px-2.5 py-0.5 rounded-md bg-purple-100 text-purplePrimary text-[10px] font-black uppercase mb-2">Branch 2</span>
                    <h3 class="text-base font-black text-slate-900">SDPC - Mahalaxmi Nagar</h3>
                    <p class="text-xs text-slate-600 mt-1">MR6-110, Mahalaxmi Nagar, Indore, MP 452010</p>
                    <p class="text-xs font-bold text-slate-800 mt-2"><i class="fa-solid fa-phone text-blue-600"></i> 0731-4979170 / 98933 62477</p>
                </div>
            </div>

            <div class="lg:col-span-7 rounded-2xl overflow-hidden border border-slate-200 shadow-md">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58879.00841936737!2d75.84532091296121!3d22.730544748895017!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962fdc634125af5%3A0x2f06500f64753718!2sSD%20PHYSIOTHERAPY%20CLINIC!5e0!3m2!1sen!2sin!4v1710319700731!5m2!1sen!2sin" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    const timingsData = {
        "Khatiwala Tank Branch (Indore)": ["08:30 AM", "09:00 AM", "10:30 AM", "12:00 PM", "04:00 PM", "06:00 PM", "07:30 PM"],
        "Mahalaxmi Nagar Branch (Indore)": ["04:00 PM", "04:30 PM", "05:00 PM", "05:30 PM", "06:30 PM", "07:00 PM", "07:30 PM"]
    };

    function updateHomeTimings() {
        const branch = document.getElementById('h_branch').value;
        const timingSelect = document.getElementById('h_timing');
        timingSelect.innerHTML = '<option value="">-- Choose Preferred Slot --</option>';

        if (branch && timingsData[branch]) {
            timingsData[branch].forEach(time => {
                const opt = document.createElement('option');
                opt.value = time;
                opt.textContent = time;
                timingSelect.appendChild(opt);
            });
        }
    }

    function handleHomeAppointmentSubmit(e) {
        e.preventDefault();
        const name = document.getElementById('h_name').value.trim();
        const phone = document.getElementById('h_phone').value.trim();
        const branch = document.getElementById('h_branch').value;
        const timing = document.getElementById('h_timing').value;
        const msg = document.getElementById('h_message').value.trim();

        const fullMsg = `*SDPC Clinic Appointment Request*\n` +
                        `Name: ${name}\n` +
                        `Phone: ${phone}\n` +
                        `Branch: ${branch}\n` +
                        `Preferred Slot: ${timing}\n` +
                        `Complaint: ${msg || 'General Physiotherapy Consultation'}`;

        const url = `https://api.whatsapp.com/send?phone=9893362477&text=${encodeURIComponent(fullMsg)}`;
        window.open(url, '_blank');
        e.target.reset();
    }
</script>
@endpush
