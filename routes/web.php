<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\PatientProgressController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DatabaseSetupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorManagementController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\SlotManagementController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\DoctorVideoController;
use App\Http\Controllers\PwaSetupController;
use App\Http\Controllers\HolidayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Live Server Database & PWA Setup Routes (https://crm.physiopii.in/setup-database & https://crm.physiopii.in/install-pwa)
Route::withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\VerifyCsrfToken::class,
])->group(function () {
    Route::get('/setup-database', [DatabaseSetupController::class, 'index'])->name('database.setup');
    Route::match(['get', 'post'], '/setup-database/run', [DatabaseSetupController::class, 'run'])->name('database.setup.run');
    Route::match(['get', 'post'], '/setup-database/fresh', [DatabaseSetupController::class, 'fresh'])->name('database.setup.fresh');
    Route::match(['get', 'post'], '/setup-database/clean', [DatabaseSetupController::class, 'clean'])->name('database.setup.clean');

    // Server URL Hit to Install/Publish PWA Package & Assets
    Route::match(['get', 'post'], '/install-pwa', [PwaSetupController::class, 'install'])->name('pwa.install');
    Route::match(['get', 'post'], '/setup-pwa', [PwaSetupController::class, 'install'])->name('pwa.setup');
});

// Role Switching
Route::get('/switch-role/{role}', [RoleController::class, 'switchRole'])->name('role.switch');

// Public Website Routes (SDPC Indore - Physiotherapy & Spine Clinic)
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/about', [LandingPageController::class, 'about'])->name('landing.about');
Route::get('/services', [LandingPageController::class, 'services'])->name('landing.services');
Route::get('/testimonials', [LandingPageController::class, 'testimonials'])->name('landing.testimonials');
Route::get('/gallery', [LandingPageController::class, 'gallery'])->name('landing.gallery');
Route::get('/contact', [LandingPageController::class, 'contact'])->name('landing.contact');
Route::get('/enquiry', [LandingPageController::class, 'enquiry'])->name('landing.enquiry');
Route::get('/book-appointment', [LandingPageController::class, 'enquiry'])->name('landing.appointment');
Route::get('/videos/watch/{token}', [DoctorVideoController::class, 'publicShare'])->name('videos.public');

// Dashboard & Global Search (CRM Portal)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/search', [DashboardController::class, 'search'])->name('global.search');

// Patient Management
Route::get('/patients/export', [PatientController::class, 'export'])->name('patients.export');
Route::resource('patients', PatientController::class);
Route::post('/patients/{id}/medical-history', [PatientController::class, 'updateMedicalHistory'])->name('patients.medical-history');
Route::get('/patients/{id}/print-summary', [PatientController::class, 'printSummary'])->name('patients.print-summary');

// Appointments & Calendar
Route::resource('appointments', AppointmentController::class)->except(['show']);
Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
Route::post('/appointments/{id}/extend', [AppointmentController::class, 'extendDays'])->name('appointments.extend');
Route::post('/appointments/{id}/skip-to-end', [AppointmentController::class, 'skipToEnd'])->name('appointments.skip-to-end');
Route::post('/appointments/{id}/recovery', [AppointmentController::class, 'updateRecovery'])->name('appointments.recovery');
Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar.index');

// Clinic Holidays & Doctor Emergency Off
Route::resource('holidays', HolidayController::class)->only(['index', 'store', 'destroy']);
Route::post('/holidays/emergency-off', [HolidayController::class, 'emergencyOff'])->name('holidays.emergency-off');

// Multi-Clinic Practice Management
Route::resource('clinics', ClinicController::class);
Route::post('/clinics/{id}/toggle-status', [ClinicController::class, 'toggleStatus'])->name('clinics.toggle-status');

// Calendar-Based Slot Management UI & Overrides
Route::get('/slots/manage', [SlotManagementController::class, 'index'])->name('slots.manage');
Route::post('/slots/schedule', [SlotManagementController::class, 'updateSchedule'])->name('slots.schedule.update');
Route::post('/slots/override', [SlotManagementController::class, 'toggleBlock'])->name('slots.override.toggle');
Route::post('/slots/custom', [SlotManagementController::class, 'addCustomSlot'])->name('slots.custom.create');

// Today's Queue
Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
Route::post('/queue/{id}/call', [QueueController::class, 'callPatient'])->name('queue.call');
Route::post('/queue/{id}/start', [QueueController::class, 'startConsultation'])->name('queue.start');
Route::post('/queue/{id}/complete', [QueueController::class, 'completeConsultation'])->name('queue.complete');

// Clinical Care & Consultations
Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
Route::get('/consultations/{id}', [ConsultationController::class, 'show'])->name('consultations.show');
Route::get('/visits', [ConsultationController::class, 'index'])->name('visits.index');

// Prescriptions
Route::resource('prescriptions', PrescriptionController::class)->except(['edit', 'update', 'destroy']);
Route::get('/prescriptions/{id}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');

// Medical Reports
Route::get('/reports/medical', [MedicalReportController::class, 'index'])->name('medical-reports.index');
Route::post('/reports/medical', [MedicalReportController::class, 'store'])->name('medical-reports.store');
Route::get('/reports/medical/{id}/preview', [MedicalReportController::class, 'preview'])->name('medical-reports.preview');
Route::get('/reports/medical/{id}/download', [MedicalReportController::class, 'download'])->name('medical-reports.download');
Route::delete('/reports/medical/{id}', [MedicalReportController::class, 'destroy'])->name('medical-reports.destroy');

// Patient Progress
Route::get('/progress', [PatientProgressController::class, 'index'])->name('progress.index');
Route::post('/progress', [PatientProgressController::class, 'store'])->name('progress.store');

// Follow-ups
Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('followups.index');
Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('followups.store');

// Physiotherapy: Treatment Categories & Exercises Library
Route::resource('categories', CategoryController::class);
Route::resource('exercises', ExerciseController::class);
Route::resource('videos', DoctorVideoController::class);
Route::post('/follow-ups/{id}/status', [FollowUpController::class, 'updateStatus'])->name('followups.status');

// Billing, Invoices & Due Payments
Route::resource('invoices', InvoiceController::class);
Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::post('/invoices/{id}/pay', [InvoiceController::class, 'recordPayment'])->name('invoices.pay');
Route::post('/invoices/{id}/record-payment', [InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
Route::get('/due-payments', [InvoiceController::class, 'duePayments'])->name('due-payments.index');
Route::get('/receipts/{id}/print', [InvoiceController::class, 'printReceipt'])->name('receipts.print');

// Payment Transactions Edit & Delete (Doctor / Admin)
Route::put('/payment-transactions/{id}', [InvoiceController::class, 'updateTransaction'])->name('payment-transactions.update');
Route::delete('/payment-transactions/{id}', [InvoiceController::class, 'deleteTransaction'])->name('payment-transactions.destroy');

// Payment Manage & Comprehensive Reports
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/report/print', [PaymentController::class, 'printReport'])->name('payments.report.print');
Route::get('/payments/export', [PaymentController::class, 'exportCsv'])->name('payments.export');

// Doctor Role: Staff Create / Show / Edit / Active-Inactive-Suspend & Staff Sidebar Show/Hide Permissions
Route::get('/doctor/staff', [\App\Http\Controllers\DoctorStaffController::class, 'index'])->name('doctor.staff.index');
Route::post('/doctor/staff', [\App\Http\Controllers\DoctorStaffController::class, 'store'])->name('doctor.staff.store');
Route::put('/doctor/staff/{id}', [\App\Http\Controllers\DoctorStaffController::class, 'update'])->name('doctor.staff.update');
Route::post('/doctor/staff/{id}/status', [\App\Http\Controllers\DoctorStaffController::class, 'updateStatus'])->name('doctor.staff.status');
Route::delete('/doctor/staff/{id}', [\App\Http\Controllers\DoctorStaffController::class, 'destroy'])->name('doctor.staff.destroy');
Route::post('/doctor/staff/permissions', [\App\Http\Controllers\DoctorStaffController::class, 'updatePermissions'])->name('doctor.staff.permissions.update');

// Clinic Expenses
Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

// Financial & Analytical Reports
Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
Route::get('/reports/financial/export', [ReportController::class, 'exportFinancial'])->name('reports.financial.export');
Route::get('/reports/patients', [ReportController::class, 'patients'])->name('reports.patients');
Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');

// Settings & Administration
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings/clinic', [SettingController::class, 'updateClinic'])->name('settings.clinic');
Route::get('/settings/availability', [SettingController::class, 'availability'])->name('settings.availability');
Route::post('/settings/availability', [SettingController::class, 'updateAvailability'])->name('settings.availability.update');
Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

// User Profile (All Roles)
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.password');

// Dynamic Doctor Slots API (For Live Availability Picker)
Route::get('/api/doctor-slots', [AppointmentController::class, 'getDoctorSlots'])->name('api.doctor-slots');

// Super Admin: Doctor Management (Create, Edit, Suspend, Delete)
Route::get('/admin/doctors', [DoctorManagementController::class, 'index'])->name('admin.doctors.index');
Route::post('/admin/doctors', [DoctorManagementController::class, 'store'])->name('admin.doctors.store');
Route::put('/admin/doctors/{id}', [DoctorManagementController::class, 'update'])->name('admin.doctors.update');
Route::post('/admin/doctors/{id}/toggle-status', [DoctorManagementController::class, 'toggleStatus'])->name('admin.doctors.toggle-status');
Route::delete('/admin/doctors/{id}', [DoctorManagementController::class, 'destroy'])->name('admin.doctors.destroy');

// Super Admin: Role & Sidebar Menu Permissions
Route::get('/settings/roles-permissions', [RolePermissionController::class, 'index'])->name('admin.roles.permissions');
Route::post('/settings/roles-permissions', [RolePermissionController::class, 'update'])->name('admin.roles.permissions.update');
