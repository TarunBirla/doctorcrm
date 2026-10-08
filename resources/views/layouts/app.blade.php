<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - CarePoint Clinic Management System</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04);
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex antialiased selection:bg-blue-100 selection:text-blue-900">

    <!-- SIDEBAR -->
    @include('partials.sidebar')

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- TOP HEADER NAVBAR -->
        @include('partials.header')

        <!-- FLASH NOTIFICATIONS -->
        <div class="px-6 pt-4 no-print">
            @if(session('success'))
                <div class="flex items-center justify-between p-4 mb-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-semibold text-sm">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                        Please check the following errors:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- PAGE CONTENT CONTAINER -->
        <main class="flex-1 px-6 pb-12">
            @yield('content')
        </main>
    </div>

    <!-- GLOBAL QUICK MODALS -->
    @include('partials.modals')

    <script>
        // Initialize lucide icons
        lucide.createIcons();

        // Sidebar menu live filter search
        const sidebarSearchInput = document.getElementById('sidebarSearch');
        if (sidebarSearchInput) {
            sidebarSearchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                const menuLinks = document.querySelectorAll('.sidebar-nav-link');
                menuLinks.forEach(link => {
                    const text = link.textContent.toLowerCase();
                    if (text.includes(term) || term === '') {
                        link.style.display = 'flex';
                    } else {
                        link.style.display = 'none';
                    }
                });
            });
        }

        // Global search modal open/close
        function openGlobalSearch() {
            document.getElementById('globalSearchModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('globalSearchInput').focus(), 50);
        }
        function closeGlobalSearch() {
            document.getElementById('globalSearchModal').classList.add('hidden');
        }

        // Live search API query
        let searchTimeout = null;
        function handleLiveSearch(query) {
            clearTimeout(searchTimeout);
            const resultsContainer = document.getElementById('globalSearchResults');
            if (!query || query.trim().length < 2) {
                resultsContainer.innerHTML = '<p class="text-xs text-slate-400 text-center py-6">Type at least 2 characters to search patients, appointments, invoices...</p>';
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        if (data.patients && data.patients.length > 0) {
                            html += '<div class="mb-4"><h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Patients</h4>';
                            data.patients.forEach(p => {
                                html += `<a href="/patients/${p.id}" class="flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl transition mb-1 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">${p.first_name[0]}${p.last_name[0]}</div>
                                        <div>
                                            <span class="font-semibold text-slate-800">${p.first_name} ${p.last_name}</span>
                                            <span class="text-xs text-slate-400 ml-2">(${p.patient_id})</span>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-500">${p.mobile} • ${p.age}y / ${p.gender}</span>
                                </a>`;
                            });
                            html += '</div>';
                        }

                        if (data.appointments && data.appointments.length > 0) {
                            html += '<div class="mb-4"><h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Appointments</h4>';
                            data.appointments.forEach(a => {
                                html += `<a href="/appointments?search=${a.appointment_no}" class="flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl transition mb-1 text-sm">
                                    <span class="font-semibold text-blue-700">${a.appointment_no}</span>
                                    <span class="text-xs text-slate-500">${a.patient ? a.patient.first_name + ' ' + a.patient.last_name : ''} (${a.appointment_date})</span>
                                </a>`;
                            });
                            html += '</div>';
                        }

                        if (data.invoices && data.invoices.length > 0) {
                            html += '<div class="mb-4"><h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Invoices</h4>';
                            data.invoices.forEach(inv => {
                                html += `<a href="/invoices/${inv.id}" class="flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl transition mb-1 text-sm">
                                    <span class="font-semibold text-slate-800">${inv.invoice_no}</span>
                                    <span class="text-xs text-slate-500">₹${inv.total_amount} (Due: ₹${inv.due_amount})</span>
                                </a>`;
                            });
                            html += '</div>';
                        }

                        if (!html) {
                            html = '<p class="text-xs text-slate-400 text-center py-6">No matching records found.</p>';
                        }
                        resultsContainer.innerHTML = html;
                    });
            }, 250);
        }

        // Global Modal Controls
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) {
                m.classList.remove('hidden');
                if (id === 'quickAppointmentModal') {
                    fetchModalDoctorSlots();
                }
            }
        }
        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('hidden');
        }

        // Live Doctor Slot Engine for Quick Appointment Modal
        function fetchModalDoctorSlots() {
            const docSelect = document.getElementById('modalDoctorSelect');
            const clinicSelect = document.getElementById('modalClinicSelect');
            const dateInput = document.getElementById('modalDateSelect');
            const slotsGrid = document.getElementById('modalSlotsGrid');
            const statusText = document.getElementById('modalSlotStatusText');
            const timeInput = document.getElementById('modalTimeInput');
            const badge = document.getElementById('modalSelectedSlotBadge');

            if (!docSelect || !dateInput || !slotsGrid) return;

            const docId = docSelect.value;
            const clinicId = clinicSelect ? clinicSelect.value : '';
            const apptDate = dateInput.value;

            if (!docId || !apptDate) {
                slotsGrid.innerHTML = '<span class="text-[11px] text-slate-400 italic">Select clinic, doctor & date to load live availability.</span>';
                if (statusText) statusText.innerText = 'Select doctor to check slots';
                return;
            }

            slotsGrid.innerHTML = '<span class="text-[11px] text-blue-600 animate-pulse font-semibold">Checking available slots...</span>';
            if (statusText) statusText.innerText = 'Loading...';

            fetch(`/api/doctor-slots?doctor_id=${encodeURIComponent(docId)}&clinic_id=${encodeURIComponent(clinicId)}&date=${encodeURIComponent(apptDate)}`)
                .then(res => res.json())
                .then(res => {
                    if (res.is_available === false) {
                        slotsGrid.innerHTML = `<span class="text-[11px] text-amber-600 font-semibold">${res.message || 'Doctor is not available on this date.'}</span>`;
                        if (statusText) statusText.innerText = 'Unavailable';
                        return;
                    }

                    if (res.error) {
                        slotsGrid.innerHTML = `<span class="text-[11px] text-rose-500">${res.error}</span>`;
                        if (statusText) statusText.innerText = 'Error';
                        return;
                    }

                    if (statusText) {
                        statusText.innerHTML = `<span class="text-emerald-700 font-bold">${res.available_count} Available</span> • <span class="text-rose-600 font-bold">${res.booked_count} Booked</span>`;
                    }

                    if (!res.slots || res.slots.length === 0) {
                        slotsGrid.innerHTML = '<span class="text-[11px] text-slate-400 italic">No schedule configured for this day.</span>';
                        return;
                    }

                    let html = '';
                    res.slots.forEach(slot => {
                        if (slot.is_booked) {
                            html += `<button type="button" disabled title="Slot already booked for another patient" 
                                class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-50 border border-rose-200 text-rose-400 cursor-not-allowed line-through opacity-70">
                                ${slot.time} (Booked)
                            </button>`;
                        } else {
                            const isSelected = (timeInput && timeInput.value === slot.time);
                            html += `<button type="button" onclick="selectModalSlot('${slot.time}')" 
                                id="slot_btn_${slot.time.replace(':', '_')}"
                                class="slot-pill px-2.5 py-1 text-[11px] font-bold rounded-lg border transition ${isSelected ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500'}">
                                ${slot.time}
                            </button>`;
                        }
                    });

                    slotsGrid.innerHTML = html;
                })
                .catch(err => {
                    slotsGrid.innerHTML = '<span class="text-[11px] text-rose-500">Failed to load doctor slots</span>';
                    console.error(err);
                });
        }

        function selectModalSlot(slotTime) {
            const timeInput = document.getElementById('modalTimeInput');
            const badge = document.getElementById('modalSelectedSlotBadge');
            if (timeInput) timeInput.value = slotTime;
            if (badge) badge.innerText = `Selected: ${slotTime}`;

            // Update UI styles of slot buttons
            document.querySelectorAll('#modalSlotsGrid .slot-pill').forEach(btn => {
                btn.className = 'slot-pill px-2.5 py-1 text-[11px] font-bold rounded-lg border transition bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500';
            });
            const selectedBtn = document.getElementById(`slot_btn_${slotTime.replace(':', '_')}`);
            if (selectedBtn) {
                selectedBtn.className = 'slot-pill px-2.5 py-1 text-[11px] font-bold rounded-lg border transition bg-blue-600 text-white border-blue-600 shadow-xs ring-2 ring-blue-300';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
