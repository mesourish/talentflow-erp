<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\ActivityController;
use App\Http\Controllers\Talent\CelebrityController;
use App\Http\Controllers\Talent\TalentCategoryController;
use App\Http\Controllers\Quotations\QuotationController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\ProjectTaskController;
use App\Http\Controllers\Bookings\BookingController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\Documents\ContractController;
use App\Http\Controllers\Claims\ClaimController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\CommissionController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\SettingsController;

// Public & Authentication Routes
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Admin Routes (Blueprint Section 9.1)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRM
    Route::resource('clients', ClientController::class);
    Route::resource('leads', LeadController::class);
    Route::resource('activities', ActivityController::class)->only(['index', 'store', 'update']);

    // Talent
    Route::resource('celebrities', CelebrityController::class);
    Route::post('celebrities/{celebrity}/rates', [CelebrityController::class, 'storeRate'])->name('celebrities.rates.store');
    Route::resource('categories', TalentCategoryController::class)->only(['index', 'store', 'update']);

    // Quotations
    Route::post('quotations/calculate-ajax', [QuotationController::class, 'calculateAjax'])->name('quotations.calculate-ajax');
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/submit', [QuotationController::class, 'submit'])->name('quotations.submit');
    Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', ProjectTaskController::class)->only(['store', 'update', 'destroy']);

    // Bookings & Calendar
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::get('calendar/events', [BookingController::class, 'calendarEvents'])->name('calendar.events');
    Route::post('bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');

    // Contracts and DMS
    Route::resource('contracts', ContractController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Claims
    Route::resource('claims', ClaimController::class);
    Route::post('claims/{claim}/submit', [ClaimController::class, 'submit'])->name('claims.submit');
    Route::post('claims/{claim}/approve', [ClaimController::class, 'approve'])->name('claims.approve');
    Route::post('claims/{claim}/reject', [ClaimController::class, 'reject'])->name('claims.reject');

    // Finance
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments.store');

    // Commissions & Payouts
    Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('commissions/rules', [CommissionController::class, 'storeRule'])->name('commissions.rules.store');
    Route::post('commissions/payouts', [CommissionController::class, 'storePayout'])->name('commissions.payouts.store');
    Route::post('commissions/payouts/{payout}/approve', [CommissionController::class, 'approvePayout'])->name('commissions.payouts.approve');
    Route::post('commissions/payouts/{payout}/paid', [CommissionController::class, 'markPaid'])->name('commissions.payouts.paid');

    // Reports & Analytics (Blueprint Section 9.1 & 14)
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/finance', 'finance')->name('finance');
        Route::get('/projects', 'projects')->name('projects');
        Route::get('/celebrities', 'celebrities')->name('celebrities');
        Route::get('/quotations', 'quotations')->name('quotations');
        Route::get('/claims', 'claims')->name('claims');
        Route::get('/forecast', 'forecast')->name('forecast');
    });

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
});
