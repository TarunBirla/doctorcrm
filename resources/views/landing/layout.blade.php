<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>@yield('title', 'SDPC - Shyama Devi Physiotherapy Clinic || Faster Recovery & Lasting Results || Indore')</title>
    <meta name="description" content="@yield('meta_description', 'SDPC (Shyama Devi Physiotherapy Clinic) Indore - Drug Free • Surgery Free • Pain Free Spine Specialist. Expert Osteopathy, Physiotherapy & Chiropractic care by Dr. Mahesh Sahu PT.')">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        purplePrimary: '#411670',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Nunito', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons & FontAwesome CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Nunito', 'Plus Jakarta Sans', sans-serif;
        }
        .gradient-text {
            background: linear-gradient(135deg, #0284c7 0%, #411670 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-mesh {
            background-color: #f8fafc;
            background-image: radial-gradient(at 0% 0%, rgba(2, 132, 199, 0.08) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(65, 22, 112, 0.08) 0px, transparent 50%);
        }
        /* Floating action buttons */
        .float-whatsapp {
            position: fixed;
            width: 58px;
            height: 58px;
            bottom: 24px;
            left: 24px;
            background-color: #25D366;
            color: #FFF;
            border-radius: 50%;
            text-align: center;
            font-size: 30px;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .float-whatsapp:hover {
            transform: scale(1.1);
            color: #FFF;
        }
        .float-call {
            position: fixed;
            width: 58px;
            height: 58px;
            bottom: 24px;
            right: 24px;
            background-color: #0284c7;
            color: #FFF;
            border-radius: 50%;
            text-align: center;
            font-size: 24px;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .float-call:hover {
            transform: scale(1.1);
            color: #FFF;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-900">

    <!-- TOP ANNOUNCEMENT & CONTACT BAR -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800 hidden sm:block">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-6">
                <span class="inline-flex items-center gap-1.5 text-slate-300">
                    <i class="fa-solid fa-location-dot text-brand-600 text-[11px]"></i>
                    <span><strong>Branch 1:</strong> Khatiwala Tank &bull; <strong>Branch 2:</strong> Mahalaxmi Nagar, Indore</span>
                </span>
                <span class="hidden md:inline-flex items-center gap-1.5 text-slate-300">
                    <i class="fa-solid fa-clock text-amber-400 text-[11px]"></i>
                    <span>Mon - Sat: 9:00 AM - 8:00 PM (Sunday Closed)</span>
                </span>
            </div>
            <div class="flex items-center gap-5">
                <a href="tel:9893362477" class="inline-flex items-center gap-1.5 font-bold text-white hover:text-brand-200 transition">
                    <i class="fa-solid fa-phone text-emerald-400"></i>
                    <span>+91 98933 62477</span>
                </a>
                <span class="text-slate-600">|</span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-bold text-brand-200 hover:text-white transition">
                    <i class="fa-solid fa-user-doctor"></i>
                    <span>Doctor Portal</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVIGATION HEADER -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- CLINIC LOGO & IDENTITY -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3.5 group">
                    <img src="{{ asset('logo.png') }}" alt="SDPC Clinic Logo" class="h-14 w-auto object-contain group-hover:scale-105 transition">
                    
                </a>

                <!-- DESKTOP NAV LINKS -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('landing') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        Home
                    </a>
                    <a href="{{ route('landing.about') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing.about') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        About Us
                    </a>
                    <a href="{{ route('landing.services') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing.services') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        Services
                    </a>
                    <a href="{{ route('landing.testimonials') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing.testimonials') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        Testimonials
                    </a>
                    <a href="{{ route('landing.gallery') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing.gallery') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        Gallery
                    </a>
                    <a href="{{ route('landing.contact') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('landing.contact') ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:text-brand-600 hover:bg-slate-50' }}">
                        Contact Us
                    </a>
                </nav>

                <!-- HEADER CTA BUTTONS -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('landing.enquiry') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-md shadow-blue-500/20 hover:shadow-lg transition">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                        <span>Book Appointment</span>
                    </a>
                </div>

                <!-- MOBILE HAMBURGER BUTTON -->
                <button type="button" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" class="lg:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- MOBILE MENU DRAWER -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2">
            <a href="{{ route('landing') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-house w-6 text-brand-600"></i> Home
            </a>
            <a href="{{ route('landing.about') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-user-doctor w-6 text-brand-600"></i> About Us
            </a>
            <a href="{{ route('landing.services') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-stethoscope w-6 text-brand-600"></i> Services & Conditions
            </a>
            <a href="{{ route('landing.testimonials') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-video w-6 text-brand-600"></i> Testimonials
            </a>
            <a href="{{ route('landing.gallery') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-images w-6 text-brand-600"></i> Gallery
            </a>
            <a href="{{ route('landing.contact') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold text-slate-800 hover:bg-brand-50 hover:text-brand-700 transition">
                <i class="fa-solid fa-envelope w-6 text-brand-600"></i> Contact Us
            </a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('landing.enquiry') }}" class="w-full text-center py-3 rounded-xl font-bold text-white bg-blue-700 shadow-md">
                    Book Appointment
                </a>
                <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl font-bold text-slate-700 bg-slate-100 border border-slate-200">
                    Doctor / Staff Login
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN PAGE CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">
                
                <!-- Col 1: Clinic Overview & Logo -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-1 rounded-xl">
                            <img src="{{ asset('logo.png') }}" alt="SDPC" class="h-12 w-auto object-contain">
                        </div>
                        <div>
                            <span class="text-xl font-black text-white">SDPC INDORE</span>
                            <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">Shyama Devi Physiotherapy Clinic</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        SDPC is a fully equipped Osteopathy, Physiotherapy & Chiropractic Center. Super-specialized in Neuro rehabilitation, spine decompression, and joint pain relief under Dr. Mahesh Sahu PT.
                    </p>
                    <div class="pt-2 flex items-center gap-3">
                        <a href="https://www.facebook.com/p/SD-Physiotherapy-Clinic-100088845596970/" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 transition">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="https://www.instagram.com/dr.mahesh_sahu_physiotherapist/" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-pink-600 transition">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UC3iYwG4ya99heTT8N_VV2dg" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-rose-600 transition">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                        <a href="https://twitter.com/MaheshSahuPhys1" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-sky-500 transition">
                            <i class="fa-brands fa-twitter text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Navigation Links -->
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-wider mb-4 border-l-2 border-brand-500 pl-2.5">
                        Quick Links
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('landing') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Home</a></li>
                        <li><a href="{{ route('landing.about') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> About Us & Doctor</a></li>
                        <li><a href="{{ route('landing.services') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Services & Conditions</a></li>
                        <li><a href="{{ route('landing.testimonials') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Patient Testimonials</a></li>
                        <li><a href="{{ route('landing.gallery') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Clinic Gallery</a></li>
                        <li><a href="{{ route('landing.contact') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Contact Us</a></li>
                        <li><a href="{{ route('landing.enquiry') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-brand-500"></i> Book Appointment</a></li>
                    </ul>
                </div>

                <!-- Col 3: Clinic Branches in Indore -->
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-wider mb-4 border-l-2 border-brand-500 pl-2.5">
                        Our Indore Branches
                    </h4>
                    <div class="space-y-4 text-xs">
                        <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-800">
                            <span class="font-extrabold text-white text-xs block text-brand-300">Branch 1 (Khatiwala Tank):</span>
                            <p class="text-slate-400 mt-1">562 / 584C, Near Brilliant School, Khatiwala Tank, Indore, MP 452014</p>
                            <p class="text-slate-300 font-semibold mt-1"><i class="fa-solid fa-phone text-emerald-400 text-[10px]"></i> 0731-4976163 / 98933 62477</p>
                        </div>
                        <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-800">
                            <span class="font-extrabold text-white text-xs block text-brand-300">Branch 2 (Mahalaxmi Nagar):</span>
                            <p class="text-slate-400 mt-1">MR6-110, Mahalaxmi Nagar, Indore, MP 452010</p>
                            <p class="text-slate-300 font-semibold mt-1"><i class="fa-solid fa-phone text-emerald-400 text-[10px]"></i> 0731-4979170 / 98933 62477</p>
                        </div>
                    </div>
                </div>

                <!-- Col 4: Timings & Call to Action -->
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-wider mb-4 border-l-2 border-brand-500 pl-2.5">
                        OPD Hours & Booking
                    </h4>
                    <div class="text-xs space-y-2 text-slate-400">
                        <div class="flex items-center justify-between py-1 border-b border-slate-800">
                            <span>Monday - Saturday:</span>
                            <span class="font-bold text-white">9:00 AM - 8:00 PM</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-800">
                            <span>Afternoon Break:</span>
                            <span class="font-bold text-amber-400">1:30 PM - 3:30 PM</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-800">
                            <span>Sunday:</span>
                            <span class="font-bold text-rose-400">Closed</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('landing.enquiry') }}" class="w-full block text-center py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow transition">
                            Book Instant Consultation
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
                <p>&copy; {{ date('Y') }} SDPC - Shyama Devi Physiotherapy Clinic. All Rights Reserved.</p>
                <div class="flex items-center gap-4">
                    <span>Drug Free &bull; Surgery Free &bull; Pain Free Spine Specialist</span>
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-white font-semibold">Staff Login</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- FLOATING WHATSAPP & CALL BUTTONS -->
    <a href="https://api.whatsapp.com/send?phone=9893362477&text=Hello%20SDPC%20Physiotherapy%20Clinic%2C%20I%20would%20like%20to%20inquire%20about%20treatment." target="_blank" class="float-whatsapp" title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <a href="tel:9893362477" class="float-call" title="Call SDPC Clinic">
        <i class="fa-solid fa-phone"></i>
    </a>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
