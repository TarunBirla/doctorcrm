<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhysioPii Clinic CRM | Next-Gen Multi-Clinic & Physiotherapy Practice Platform</title>
    <meta name="description" content="PhysioPii is an intelligent multi-clinic CRM platform featuring strict patient data isolation, live calendar slot management, digital prescriptions, token queues, and zero double-booking.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#0f2744',
                            900: '#0a2540',
                            950: '#071828',
                        },
                        clinic: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: #0f172a;
        }
        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .glow-primary {
            box-shadow: 0 10px 40px -10px rgba(10, 37, 64, 0.25);
        }
        .glow-blue {
            box-shadow: 0 10px 30px -8px rgba(37, 99, 235, 0.25);
        }
    </style>
</head>
<body class="antialiased selection:bg-blue-600 selection:text-white">

    <!-- ========================================== -->
    <!-- STICKY HEADER & NAVIGATION -->
    <!-- ========================================== -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-linear-to-tr from-navy-950 via-navy-900 to-blue-700 flex items-center justify-center text-white shadow-md group-hover:scale-105 transition transform">
                        <i data-lucide="activity" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold tracking-tight text-navy-900">Physio<span class="text-blue-600">Pii</span></span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-800 tracking-wide">CRM</span>
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium tracking-wide">Multi-Clinic Healthcare Platform</p>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden lg:flex items-center gap-8 text-xs font-semibold text-slate-600">
                    <a href="#features" class="hover:text-blue-600 transition">Features</a>
                    <a href="#workflow" class="hover:text-blue-600 transition">Workflow</a>
                    <a href="#physio-modules" class="hover:text-blue-600 transition">Physiotherapy</a>
                    <a href="#slot-engine" class="hover:text-blue-600 transition">Live Slot Engine</a>
                    <a href="#role-security" class="hover:text-blue-600 transition">Data Isolation</a>
                    <a href="#faq" class="hover:text-blue-600 transition">FAQ</a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition transform hover:-translate-y-0.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Open Dashboard</span>
                        </a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold transition">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Sign Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <i data-lucide="lock" class="w-3.5 h-3.5 text-blue-400"></i>
                            <span>Doctor & Staff Login</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-0.5"></i>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3 text-xs font-semibold">
            <a href="#features" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">Features</a>
            <a href="#workflow" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">Workflow</a>
            <a href="#physio-modules" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">Physiotherapy Modules</a>
            <a href="#slot-engine" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">Live Slot Engine</a>
            <a href="#role-security" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">Data Isolation</a>
            <a href="#faq" onclick="toggleMobileMenu()" class="block py-2 text-slate-700 hover:text-blue-600">FAQ</a>
            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-navy-900 text-white rounded-xl font-bold">
                    <i data-lucide="lock" class="w-4 h-4 text-blue-400"></i>
                    <span>Doctor & Staff Login</span>
                </a>
            </div>
        </div>
    </header>


    <!-- ========================================== -->
    <!-- SECTION 1: HERO SECTION -->
    <!-- ========================================== -->
    <section class="hero-pattern relative pt-12 pb-20 lg:pt-20 lg:pb-32 overflow-hidden border-b border-slate-200">
        <!-- Background Gradient Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-b from-blue-100/60 via-indigo-50/30 to-transparent blur-3xl pointer-events-none -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Left Copy -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold shadow-2xs">
                        <span class="flex h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>Clinical Intelligence 2.0 • Multi-Clinic Architecture</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-5xl font-extrabold tracking-tight text-navy-900 leading-[1.15]">
                        Intelligent Clinic CRM. <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-navy-900">
                            Strict Patient Isolation.
                        </span> <br>
                        Zero Double-Booking.
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        PhysioPii is the definitive practice management software engineered for doctors, physical therapists, and multi-branch clinics. Manage multiple practice locations, live calendar slots, real-time token queues, and isolated clinical records in one unified system.
                    </p>

                    <!-- CTAs -->
                    <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <i data-lucide="stethoscope" class="w-4 h-4 text-blue-400"></i>
                            <span>Doctor & Staff Portal Login</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>

                        <a href="#slot-engine" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 text-slate-700 font-bold rounded-xl text-xs sm:text-sm shadow-xs transition">
                            <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                            <span>View Live Slot Engine</span>
                        </a>
                    </div>

                    <!-- Trust Micro-Pills -->
                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs font-semibold text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Doctor Data Isolation</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                            <span>Multi-Clinic Practice Linking</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                            <span>Instant Live Token Dispatch</span>
                        </div>
                    </div>

                </div>

                <!-- Hero Right: Interactive Live Simulation Mockup -->
                <div class="lg:col-span-5">
                    <div class="relative bg-white rounded-3xl p-6 shadow-2xl border border-slate-200 glow-primary">
                        
                        <!-- Header of mockup -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shadow-xs">
                                    TB
                                </div>
                                <div>
                                    <h4 class="font-bold text-navy-900 text-sm">Dr. Tarun Birla</h4>
                                    <p class="text-[11px] text-slate-500 font-medium">Birla Physiotherapy & Rehab Clinic</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                                Clinic Active
                            </span>
                        </div>

                        <!-- Live Slot Booking Grid Preview -->
                        <div class="py-4 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
                                    Today's Slot Schedule
                                </span>
                                <span class="text-[11px] font-semibold text-blue-600">20 Min Consultation</span>
                            </div>

                            <div class="grid grid-cols-3 gap-2 text-center text-[11px] font-bold">
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    09:00 AM
                                    <span class="block text-[9px] font-normal text-emerald-600">Available</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-2xs">
                                    09:20 AM
                                    <span class="block text-[9px] font-bold text-indigo-600">#T-001 Confirmed</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    09:40 AM
                                    <span class="block text-[9px] font-normal text-emerald-600">Available</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
                                    10:00 AM
                                    <span class="block text-[9px] font-normal text-amber-600">Blocked</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    10:20 AM
                                    <span class="block text-[9px] font-normal text-emerald-600">Available</span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    10:40 AM
                                    <span class="block text-[9px] font-normal text-emerald-600">Available</span>
                                </div>
                            </div>
                        </div>

                        <!-- Live Mini KPI Chips -->
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Active Tokens</span>
                                <span class="font-extrabold text-navy-900 text-sm">8 Patients in Queue</span>
                            </div>
                            <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-100">
                                <span class="text-[10px] text-blue-600 font-semibold block">Schedule Sync</span>
                                <span class="font-extrabold text-blue-900 text-sm">Zero Conflicts</span>
                            </div>
                        </div>

                        <!-- Mini Direct Action Button -->
                        <div class="mt-4 pt-2">
                            <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition">
                                <span>Enter Doctor Dashboard</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 2: KEY STATISTICS & TRUST BAR -->
    <!-- ========================================== -->
    <section class="py-14 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                <!-- Stat 1 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">100%</div>
                    <div class="text-xs font-bold text-blue-600 mt-1 uppercase tracking-wider">Patient Isolation</div>
                    <p class="text-xs text-slate-500 mt-1">Zero cross-doctor data exposure</p>
                </div>

                <!-- Stat 2 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">0.0 sec</div>
                    <div class="text-xs font-bold text-emerald-600 mt-1 uppercase tracking-wider">Double-Booking Risk</div>
                    <p class="text-xs text-slate-500 mt-1">Real-time slot lock engine</p>
                </div>

                <!-- Stat 3 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">50k+</div>
                    <div class="text-xs font-bold text-indigo-600 mt-1 uppercase tracking-wider">Consultations Logged</div>
                    <p class="text-xs text-slate-500 mt-1">Across top clinics & hospitals</p>
                </div>

                <!-- Stat 4 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">99.9%</div>
                    <div class="text-xs font-bold text-purple-600 mt-1 uppercase tracking-wider">Cloud Availability</div>
                    <p class="text-xs text-slate-500 mt-1">Encrypted & fast performance</p>
                </div>
            </div>

            <!-- Compliance Bar -->
            <div class="mt-8 pt-6 border-t border-slate-200/80 flex flex-wrap items-center justify-center gap-6 sm:gap-10 text-xs font-semibold text-slate-500">
                <span class="flex items-center gap-1.5"><i data-lucide="badge-check" class="w-4 h-4 text-emerald-600"></i> HIPAA Aligned Protocols</span>
                <span class="flex items-center gap-1.5"><i data-lucide="lock" class="w-4 h-4 text-blue-600"></i> 256-Bit TLS In-Transit Encryption</span>
                <span class="flex items-center gap-1.5"><i data-lucide="shield" class="w-4 h-4 text-indigo-600"></i> Multi-Role RBAC Security</span>
                <span class="flex items-center gap-1.5"><i data-lucide="database" class="w-4 h-4 text-purple-600"></i> Automatic Daily Cloud Backups</span>
            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 3: CORE ARCHITECTURAL MODULES -->
    <!-- ========================================== -->
    <section id="features" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Enterprise Modules</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Comprehensive Clinical Operations Engine</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Everything your clinical practice requires — from branch schedule coordination to electronic medical records, token dispatch, and automated accounts.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Card 1 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Multi-Clinic Practice Management</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        A single doctor can practice across multiple branch clinics. Configure separate operating days, custom consultation fees, independent durations, and address details.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i> Primary & secondary clinic tagging</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i> Clinic-specific consultation pricing</li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="calendar-check" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Real-Time Calendar Slot Engine</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Visual slot grid with Day, Week, and Month views. Automatic lunch exclusions, slot block overrides for surgeries or meetings, and zero double bookings.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Auto slot generation (10-60 min)</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Emergency extra slot injection</li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Strict Patient Data Isolation</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data privacy by architectural design. Doctor A cannot see, search, or access Doctor B's patients, appointments, progress notes, or prescriptions.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-indigo-600"></i> Strict Doctor-level query scoping</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-indigo-600"></i> 403 Forbidden cross-doctor shield</li>
                    </ul>
                </div>

                <!-- Card 4 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="clock-4" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Smart Token & Live Queue Tracker</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Automatic token numbering, patient waiting times counter, and one-click consultation lifecycle: Waiting $\rightarrow$ In Consultation $\rightarrow$ Completed.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600"></i> Live queue waiting time monitor</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-amber-600"></i> Front-desk receptionist dispatch</li>
                    </ul>
                </div>

                <!-- Card 5 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="file-plus-2" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Digital Prescriptions & Rx Dossier</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Prescribe medications with dosage, frequency, and treatment advice. Instant PDF/printable prescription summaries with clinic branding.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i> One-click print consultation summaries</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i> Diagnosis & clinical notes archive</li>
                    </ul>
                </div>

                <!-- Card 6 -->
                <div class="p-7 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-lg transition space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="receipt" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">Automated Billing & Revenue Audits</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Automatic invoice generation on appointment booking. Supports multi-service billing, payment status (Paid, Partial, Unpaid), and clinic financial logs.
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 font-medium">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-600"></i> Automated appointment invoice links</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-600"></i> Full financial audit trail & logs</li>
                    </ul>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 4: HOW IT WORKS / CLINICAL WORKFLOW -->
    <!-- ========================================== -->
    <section id="workflow" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Step-by-Step Flow</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">The Smooth 4-Step Patient Journey</h2>
                <p class="text-slate-600 text-xs sm:text-sm">From initial doctor scheduling to seamless patient arrival, real-time consultation, and discharge.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Step 1 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs relative space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                        1
                    </div>
                    <h4 class="font-bold text-navy-900 text-sm">Doctor & Clinic Setup</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Doctor sets weekly hours (e.g. 09:00 - 17:00), lunch breaks, slot durations, and consultation fee per clinic branch.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs relative space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                        2
                    </div>
                    <h4 class="font-bold text-navy-900 text-sm">Patient Registration & Slot Pick</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Register patient profile with optional instant booking checkbox. Pick Clinic $\rightarrow$ Date $\rightarrow$ Live Slot with zero collisions.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs relative space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                        3
                    </div>
                    <h4 class="font-bold text-navy-900 text-sm">Token Dispatch & Queue</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Instant token number (#T-001) is assigned. Receptionist marks arrival, doctor views patient in live queue.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-xs relative space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                        4
                    </div>
                    <h4 class="font-bold text-navy-900 text-sm">Consultation & Records</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Doctor logs vitals, symptoms, rehabilitation exercise plan, prints prescription, and attaches diagnostic test reports.
                    </p>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 5: SPECIALIZED PHYSIOTHERAPY & CLINICAL MODULES -->
    <!-- ========================================== -->
    <section id="physio-modules" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-6 space-y-6">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wider">Clinical Specialization</span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight leading-tight">
                        Built Specifically for Physiotherapists & Physical Medicine Specialists
                    </h2>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        Standard CRMs lack biomechanical tracking. PhysioPii integrates specialized physiotherapy parameters directly into the clinical workflow.
                    </p>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="gauge" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-navy-900 text-sm">Numerical Pain Scale (0-10) Tracking</h4>
                                <p class="text-xs text-slate-500">Record baseline pain levels and visualize progressive recovery curves across sessions.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="move" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-navy-900 text-sm">Range of Motion (ROM) & Flexibility Logs</h4>
                                <p class="text-xs text-slate-500">Document joint angles (flexion, extension, rotation) and track mobility progress over time.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="file-check-2" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-navy-900 text-sm">Diagnostic Lab & Imaging Reports</h4>
                                <p class="text-xs text-slate-500">Upload and preview X-Rays, MRI scans, and blood reports inline with full download security.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="bg-linear-to-br from-slate-900 to-navy-950 text-white p-7 rounded-3xl shadow-xl border border-slate-800 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                            <div>
                                <span class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">Clinical Biometrics Tracker</span>
                                <h4 class="text-sm font-bold text-white">Patient Vitals & Progress Metrics</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                Live Tracking
                            </span>
                        </div>

                        <!-- Progress Metrics Grid -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                <span class="text-[10px] text-slate-400">Pain Level (VAS)</span>
                                <div class="text-xl font-extrabold text-emerald-400 mt-1">2 / 10</div>
                                <span class="text-[9px] text-emerald-300">Reduced from 8/10 (-75%)</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                <span class="text-[10px] text-slate-400">Blood Pressure</span>
                                <div class="text-xl font-extrabold text-blue-400 mt-1">120 / 80</div>
                                <span class="text-[9px] text-blue-300">Normotensive & Optimal</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                <span class="text-[10px] text-slate-400">Pulse & SpO2</span>
                                <div class="text-xl font-extrabold text-amber-400 mt-1">72 bpm • 99%</div>
                                <span class="text-[9px] text-amber-300">Stable cardiac profile</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                <span class="text-[10px] text-slate-400">Recovery Status</span>
                                <div class="text-xl font-extrabold text-purple-400 mt-1">Phase 3</div>
                                <span class="text-[9px] text-purple-300">Strength Rehabilitation</span>
                            </div>
                        </div>

                        <div class="p-3 bg-blue-900/40 rounded-xl border border-blue-500/30 text-[11px] text-blue-200">
                            <strong>Physical Therapy Note:</strong> Quadriceps activation restored. Isometric squats and resistance band training indicated for next 2 weeks.
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 6: MULTI-ROLE EXPERIENCE & DATA ISOLATION -->
    <!-- ========================================== -->
    <section id="role-security" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 uppercase tracking-wider">Multi-Role Security</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Granular Roles & Strict Data Isolation</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Empower super admins, practicing doctors, and front-desk receptionists with customized views while safeguarding patient data privacy.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Role 1: Super Admin -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            <i data-lucide="shield-alert" class="w-5 h-5"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Governance</span>
                    </div>
                    <h3 class="font-bold text-navy-900 text-base">Super Administrator</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Complete central clinic oversight. Onboards multiple doctors, sets up clinics, configures sidebar menu permissions for each role, and views audit trails.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-slate-700 space-y-1">
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-600"></i> Manage Doctors & Clinics</div>
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-600"></i> Role & Sidebar Permissions</div>
                    </div>
                </div>

                <!-- Role 2: Doctor -->
                <div class="bg-white rounded-2xl p-7 border-2 border-blue-500 shadow-md space-y-4 relative">
                    <div class="absolute -top-3 right-6 px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-600 text-white tracking-wide uppercase">
                        Core Clinician
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <i data-lucide="stethoscope" class="w-5 h-5"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Isolated</span>
                    </div>
                    <h3 class="font-bold text-navy-900 text-base">Doctor & Specialist</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Dedicated personal practice workspace. Manages multiple practice clinics, custom slot schedule, digital consultations, and private isolated patient files.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-slate-700 space-y-1">
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i> Only Own Patients Visible</div>
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-blue-600"></i> Custom Slot Schedule & Overrides</div>
                    </div>
                </div>

                <!-- Role 3: Receptionist -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Front Desk</span>
                    </div>
                    <h3 class="font-bold text-navy-900 text-base">Receptionist & Staff</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Streamlined front desk operations. Patient arrival check-in, token dispatch, booking appointments, and invoice fee receipts without clinical data clutter.
                    </p>
                    <div class="pt-2 text-xs font-semibold text-slate-700 space-y-1">
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Quick Token Generation</div>
                        <div class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Billing & Invoice Collection</div>
                    </div>
                </div>

            </div>

            <!-- Isolation Assurance Banner -->
            <div class="mt-12 p-6 rounded-2xl bg-blue-900 text-white flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">Strict Zero-Leakage Guarantee</h4>
                        <p class="text-xs text-blue-200">Patient records, appointment schedules, and medical dossiers are strictly segregated per registered doctor.</p>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-white text-navy-900 hover:bg-blue-50 font-bold text-xs transition">
                    Access Your Isolated Portal
                </a>
            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 7: INTERACTIVE SCHEDULE & SLOT BOOKING SHOWCASE -->
    <!-- ========================================== -->
    <section id="slot-engine" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Slot Intelligence</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Interactive Calendar Slot Engine</h2>
                <p class="text-slate-600 text-xs sm:text-sm">See how PhysioPii dynamically computes live slot availability per Doctor + Clinic + Date while blocking overlaps.</p>
            </div>

            <div class="bg-slate-50 rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm max-w-4xl mx-auto space-y-6">
                
                <!-- Mockup Controls -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500">Practice Branch:</span>
                        <span class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-navy-900">
                            Birla Physiotherapy & Rehab Clinic
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Available</span>
                        <span class="flex items-center gap-1 text-indigo-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Booked</span>
                        <span class="flex items-center gap-1 text-amber-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Blocked</span>
                    </div>
                </div>

                <!-- Slot Grid Simulation -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-bold">
                    <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition cursor-pointer">
                        09:00 AM
                        <span class="block text-[10px] font-normal text-emerald-600">Free Slot</span>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition cursor-pointer">
                        09:15 AM
                        <span class="block text-[10px] font-normal text-emerald-600">Free Slot</span>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-xl shadow-xs">
                        09:30 AM
                        <span class="block text-[10px] font-bold text-indigo-600">Booked (#T-001)</span>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition cursor-pointer">
                        09:45 AM
                        <span class="block text-[10px] font-normal text-emerald-600">Free Slot</span>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl">
                        10:00 AM
                        <span class="block text-[10px] font-medium text-amber-600">Doctor Meeting</span>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition cursor-pointer">
                        10:15 AM
                        <span class="block text-[10px] font-normal text-emerald-600">Free Slot</span>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition cursor-pointer">
                        10:30 AM
                        <span class="block text-[10px] font-normal text-emerald-600">Free Slot</span>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-xl shadow-xs">
                        10:45 AM
                        <span class="block text-[10px] font-bold text-indigo-600">Booked (#T-002)</span>
                    </div>
                </div>

                <!-- Lunch Notice -->
                <div class="p-3 bg-slate-100 rounded-xl border border-slate-200 text-center text-xs font-semibold text-slate-600 flex items-center justify-center gap-2">
                    <i data-lucide="coffee" class="w-4 h-4 text-amber-600"></i>
                    <span>01:00 PM – 02:00 PM: Official Lunch & Break Window (Auto Excluded from Patient Bookings)</span>
                </div>

                <!-- Callout text -->
                <p class="text-xs text-slate-500 text-center">
                    Whenever a patient is booked or a doctor blocks a slot, the system automatically marks it unavailable across all booking endpoints in real-time.
                </p>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 8: DOCTOR & CLINIC TESTIMONIALS -->
    <!-- ========================================== -->
    <section class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">Clinician Endorsements</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Trusted by Practicing Specialists</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Read how PhysioPii streamlined operations for multi-specialty practices and physiotherapy centers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex text-amber-400 gap-1">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "The multi-clinic schedule synchronization is flawless. I consult at two separate clinics, and having isolated patients with distinct fees has eliminated scheduling headaches completely."
                    </p>
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                            RS
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-900 text-xs">Dr. Rajiv Sharma</h4>
                            <p class="text-[10px] text-slate-500">Senior Medical Specialist</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex text-amber-400 gap-1">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "The slot management calendar and instant patient registration flow save us at least 15 minutes per patient. Patients love the live token numbers and clear waiting times."
                    </p>
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">
                            TB
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-900 text-xs">Dr. Tarun Birla</h4>
                            <p class="text-[10px] text-slate-500">Physiotherapy & Rehab Specialist</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex text-amber-400 gap-1">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "As a clinic manager, the role-based sidebar permissions are a lifesaver. Our front desk staff can manage registrations and invoices without accidentally seeing sensitive doctor notes."
                    </p>
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                            PK
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-900 text-xs">Pooja Kulkarni</h4>
                            <p class="text-[10px] text-slate-500">Clinic Operations Director</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 9: FAQ ACCORDION -->
    <!-- ========================================== -->
    <section id="faq" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center space-y-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Frequently Asked Questions</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Everything You Need to Know</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Common questions regarding doctor management, patient isolation, and calendar slot booking.</p>
            </div>

            <div class="space-y-4">
                
                <!-- FAQ Item 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq1')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>How does a doctor manage multiple practice clinics?</span>
                        <i data-lucide="chevron-down" id="faq1-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq1" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        Doctors can register unlimited practice locations in the "My Practice Clinics" section. Each clinic has its own independent working days, consultation fee, appointment durations, and contact address.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq2')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>Can another doctor see my registered patients or appointments?</span>
                        <i data-lucide="chevron-down" id="faq2-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq2" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        No. The system enforces strict architectural patient data isolation. Every patient, appointment, diagnostic report, and prescription is strictly scoped to the doctor who registered them. Other doctors receive a 403 Forbidden response if attempting to access foreign records.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq3')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>How does the system prevent double bookings?</span>
                        <i data-lucide="chevron-down" id="faq3-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq3" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        Availability is evaluated in real-time across Doctor + Clinic + Date + Time. If a slot is already booked or overridden, it is automatically marked unavailable on both the public booking dropdown and the doctor modal. Submitting a duplicate slot triggers a conflict validation error.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq4')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>Can the doctor block slots for personal leave or clinical emergencies?</span>
                        <i data-lucide="chevron-down" id="faq4-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq4" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        Yes! In the Slot & Schedule Manager, doctors can simply click on any available slot to block it and provide a reason (e.g. Surgery, Hospital Round, Clinical Meeting). Click again anytime to unblock it.
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq5')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>How do Receptionists and Staff interact with the CRM?</span>
                        <i data-lucide="chevron-down" id="faq5-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq5" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        Receptionists have a customized role view configured by the Super Admin. They can register patients, dispatch tokens, monitor the waiting queue, and generate invoices while medical history and private clinical consultation notes remain protected.
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button onclick="toggleFaq('faq6')" class="w-full p-5 text-left flex items-center justify-between font-bold text-navy-900 text-sm hover:bg-slate-100/70 transition">
                        <span>Where do doctors and staff log in?</span>
                        <i data-lucide="chevron-down" id="faq6-icon" class="w-4 h-4 text-slate-500 transition transform"></i>
                    </button>
                    <div id="faq6" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200/50 bg-white">
                        You can click the <strong>"Doctor & Staff Login"</strong> button in the top right header or the bottom banner, which takes you directly to the secure authentication portal at <a href="{{ route('login') }}" class="text-blue-600 font-semibold underline">crm.physiopii.in/login</a>.
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ========================================== -->
    <!-- SECTION 10: HIGH-IMPACT CTA BANNER & COMPREHENSIVE FOOTER -->
    <!-- ========================================== -->
    <section class="py-20 bg-linear-to-b from-navy-900 to-navy-950 text-white relative overflow-hidden">
        
        <!-- Glow ornament -->
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                Transform Your Clinic Today
            </span>

            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                Ready to Experience Seamless Practice Management?
            </h2>

            <p class="text-slate-300 text-xs sm:text-base max-w-2xl mx-auto leading-relaxed">
                Join doctors and clinical teams who rely on PhysioPii every day for zero-conflict scheduling, patient data privacy, and effortless queue management.
            </p>

            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-extrabold rounded-2xl text-sm shadow-xl hover:shadow-2xl transition transform hover:-translate-y-0.5">
                    <i data-lucide="lock" class="w-4 h-4 text-white"></i>
                    <span>Login to Clinic CRM Portal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="pt-6 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i> Enterprise Encrypted</span>
                <span class="flex items-center gap-1.5"><i data-lucide="check-circle" class="w-4 h-4 text-blue-400"></i> Immediate Portal Access</span>
                <span class="flex items-center gap-1.5"><i data-lucide="smartphone" class="w-4 h-4 text-purple-400"></i> Mobile & Tablet Responsive</span>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-navy-950 text-slate-400 text-xs py-14 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-12 border-b border-slate-800">
                
                <!-- Brand col -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">PhysioPii <span class="text-blue-500">CRM</span></span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        PhysioPii Healthcare CRM is an advanced clinical management platform designed to deliver isolated doctor workflows, multi-clinic management, live token queues, and specialized physiotherapy tracking.
                    </p>
                    <div class="flex items-center gap-2 text-emerald-400 font-semibold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>All Systems Operational • Cloud Synced</span>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="space-y-3">
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider">Clinical Modules</h5>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white transition">Multi-Clinic Practice</a></li>
                        <li><a href="#slot-engine" class="hover:text-white transition">Live Slot Calendar</a></li>
                        <li><a href="#physio-modules" class="hover:text-white transition">Physiotherapy Records</a></li>
                        <li><a href="#workflow" class="hover:text-white transition">Smart Token Queue</a></li>
                        <li><a href="#role-security" class="hover:text-white transition">Data Privacy Shield</a></li>
                    </ul>
                </div>

                <!-- Roles & Portals -->
                <div class="space-y-3">
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider">Access Portals</h5>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Doctor Login</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Receptionist Portal</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Super Admin Access</a></li>
                        <li><a href="{{ route('database.setup') }}" class="hover:text-white transition">Database Setup</a></li>
                    </ul>
                </div>

                <!-- Contact & Support -->
                <div class="space-y-3">
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider">Clinical Support</h5>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i> Wellness Square, Indore (MP)</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i> support@physiopii.in</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i> +91 98765 43210</li>
                    </ul>
                </div>

            </div>

            <!-- Copyright bar -->
            <div class="pt-8 flex flex-wrap items-center justify-between gap-4 text-[11px] text-slate-500">
                <p>© {{ date('Y') }} PhysioPii Healthcare Systems. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="hover:text-slate-300 transition">Doctor Login</a>
                    <span>•</span>
                    <a href="{{ route('landing') }}" class="hover:text-slate-300 transition">Privacy & Compliance</a>
                    <span>•</span>
                    <a href="{{ route('landing') }}" class="hover:text-slate-300 transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // FAQ Accordion Toggle
        function toggleFaq(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            if (el) {
                const isHidden = el.classList.contains('hidden');
                // Close all faqs first
                document.querySelectorAll('[id^="faq"]').forEach(item => {
                    if (item.id.startsWith('faq') && !item.id.includes('-icon')) {
                        item.classList.add('hidden');
                    }
                });
                document.querySelectorAll('[id$="-icon"]').forEach(i => {
                    i.classList.remove('rotate-180');
                });

                if (isHidden) {
                    el.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                }
            }
        }
    </script>
</body>
</html>
