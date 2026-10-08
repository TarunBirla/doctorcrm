<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Sign In - CarePoint Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0a2540',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-4 sm:p-6 text-slate-800">

    <div class="w-full max-w-md space-y-6">
        <!-- Logo & Title -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary text-white shadow-lg shadow-primary/20 mb-1">
                <i data-lucide="cross" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">CarePoint Clinic</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Doctor & Patient Management System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Welcome Back</h2>
                <p class="text-xs text-slate-500 mt-0.5">Please enter your clinic staff credentials to sign in</p>
            </div>

            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Email Address <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" name="email" id="emailInput" value="{{ old('email', 'doctor@carepoint.com') }}" required autofocus
                               placeholder="doctor@carepoint.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Password <span class="text-rose-500">*</span></label>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="password" id="passwordInput" value="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                            <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20">
                        <span class="text-xs text-slate-600 font-medium">Remember this session</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-slate-800 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 group">
                    <span>Sign In to Dashboard</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition"></i>
                </button>
            </form>

            <!-- 1-Click Quick Demo Login Credentials -->
            <div class="pt-4 border-t border-slate-100 space-y-2.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block text-center">
                    1-Click Auto Fill Demo Roles
                </span>

                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillRole('admin@carepoint.com')"
                            class="p-2 rounded-xl border border-slate-200 hover:border-primary/40 hover:bg-slate-50 text-center transition group">
                        <span class="text-xs font-bold text-slate-800 block group-hover:text-primary">Admin</span>
                        <span class="text-[10px] text-slate-400 block">Super Admin</span>
                    </button>

                    <button type="button" onclick="fillRole('doctor@carepoint.com')"
                            class="p-2 rounded-xl border border-slate-200 hover:border-primary/40 hover:bg-slate-50 text-center transition group">
                        <span class="text-xs font-bold text-slate-800 block group-hover:text-primary">Doctor</span>
                        <span class="text-[10px] text-slate-400 block">Dr. Rajiv</span>
                    </button>

                    <button type="button" onclick="fillRole('staff@carepoint.com')"
                            class="p-2 rounded-xl border border-slate-200 hover:border-primary/40 hover:bg-slate-50 text-center transition group">
                        <span class="text-xs font-bold text-slate-800 block group-hover:text-primary">Staff</span>
                        <span class="text-[10px] text-slate-400 block">Reception Desk</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Setup Database Helper Link -->
        <div class="text-center text-xs text-slate-400 space-y-1">
            <p>Deploying on live server? <a href="{{ route('database.setup') }}" class="font-bold text-primary hover:underline">Run Database Setup</a></p>
            <p>© {{ date('Y') }} CarePoint Clinic Management. All rights reserved.</p>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function fillRole(email) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = 'password';
        }

        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
