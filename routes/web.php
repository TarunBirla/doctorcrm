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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Role Switching
Route::get('/switch-role/{role}', [RoleController::class, 'switchRole'])->name('role.switch');

// Dashboard & Global Search
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/search', [DashboardController::class, 'search'])->name('global.search');

// Patient Management
Route::get('/patients/export', [PatientController::class, 'export'])->name('patients.export');
Route::resource('patients', PatientController::class);
Route::post('/patients/{id}/medical-history', [PatientController::class, 'updateMedicalHistory'])->name('patients.medical-history');
Route::get('/patients/{id}/print-summary', [PatientController::class, 'printSummary'])->name('patients.print-summary');

// Appointments & Calendar
Route::resource('appointments', AppointmentController::class)->except(['show', 'edit']);
Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar.index');

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
Route::delete('/reports/medical/{id}', [MedicalReportController::class, 'destroy'])->name('medical-reports.destroy');

// Patient Progress
Route::get('/progress', [PatientProgressController::class, 'index'])->name('progress.index');
Route::post('/progress', [PatientProgressController::class, 'store'])->name('progress.store');

// Follow-ups
Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('followups.index');
Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('followups.store');
Route::post('/follow-ups/{id}/status', [FollowUpController::class, 'updateStatus'])->name('followups.status');

// Billing, Invoices & Due Payments
Route::resource('invoices', InvoiceController::class)->except(['edit', 'update', 'destroy']);
Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::post('/invoices/{id}/pay', [InvoiceController::class, 'recordPayment'])->name('invoices.pay');
Route::get('/due-payments', [InvoiceController::class, 'duePayments'])->name('due-payments.index');
Route::get('/receipts/{id}/print', [InvoiceController::class, 'printReceipt'])->name('receipts.print');

// Payment History
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

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
