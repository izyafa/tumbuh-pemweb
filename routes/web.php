<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiConsultationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\EducationalVideoController;
use App\Http\Controllers\GrowthCalculatorController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\KaderDashboardController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kader.dashboard');
    }
    return redirect()->route('login');
});

// Authentication (Protected against brute-force attacks)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Quick Anthropometry & Stunting Calculator (Tanpa Login)
Route::get('/kalkulator-gizi', [GrowthCalculatorController::class, 'index'])->name('kalkulator.index');
Route::post('/kalkulator-gizi/calculate', [GrowthCalculatorController::class, 'calculate'])
    ->name('kalkulator.calculate')
    ->middleware('throttle:60,1');

// Passwordless Parent Portal (Public, Token-based, UUID-validated & Throttled)
Route::get('/pantau/{token}', [ParentPortalController::class, 'show'])
    ->name('parent.portal')
    ->whereUuid('token');

Route::get('/pantau/{token}/kms-print', [ParentPortalController::class, 'printKms'])
    ->name('parent.kms-print')
    ->whereUuid('token');

Route::post('/pantau/{token}/ai-consult', [ParentPortalController::class, 'generateAiConsult'])
    ->name('parent.ai-consult')
    ->whereUuid('token')
    ->middleware('throttle:10,1');

// Public or Authenticated Academy view
Route::get('/edukasi', [EducationalVideoController::class, 'index'])->name('edukasi.index');
Route::get('/edukasi/{id}', [EducationalVideoController::class, 'show'])->name('edukasi.show');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Rapid preview for batch calculation (accessible by kader and admin)
    Route::post('/api/preview-zscores', [MeasurementController::class, 'preview'])
        ->name('measurements.preview')
        ->middleware('throttle:60,1');

    // Cadre Area (accessible by kader and admin)
    Route::middleware(['role:kader,admin'])->prefix('kader')->name('kader.')->group(function () {
        Route::get('/dashboard', [KaderDashboardController::class, 'index'])->name('dashboard');

        // Batch measurement entry
        Route::get('/measurements/batch', [MeasurementController::class, 'batch'])->name('measurements.batch');
        Route::post('/measurements/batch', [MeasurementController::class, 'storeBatch'])->name('measurements.storeBatch');
        Route::post('/measurements', [MeasurementController::class, 'store'])->name('measurements.store');
        Route::delete('/measurements/{id}', [MeasurementController::class, 'destroy'])->name('measurements.destroy');

        // Children management & KMS
        Route::get('/children', [ChildController::class, 'index'])->name('children.index');
        Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
        Route::post('/children', [ChildController::class, 'store'])->name('children.store');
        Route::get('/children/{id}', [ChildController::class, 'show'])->name('children.show');
        Route::get('/children/{id}/edit', [ChildController::class, 'edit'])->name('children.edit');
        Route::put('/children/{id}', [ChildController::class, 'update'])->name('children.update');
        Route::delete('/children/{id}', [ChildController::class, 'destroy'])->name('children.destroy');
        Route::get('/children/{id}/kms-print', [ChildController::class, 'printKms'])->name('children.kms-print');

        // Immunization & Supplementation management
        Route::post('/children/{id}/immunizations', [ImmunizationController::class, 'store'])->name('children.immunizations.store');
        Route::delete('/immunizations/{id}', [ImmunizationController::class, 'destroy'])->name('immunizations.destroy');

        // AI Nutrition Consultation
        Route::post('/children/{id}/ai-consult', [AiConsultationController::class, 'generate'])
            ->name('children.ai-consult')
            ->middleware('throttle:15,1');

        // Monthly Reports & Export (SIP / SKDN)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

        // Broadcast WhatsApp Reminder
        Route::get('/broadcast-wa', [BroadcastController::class, 'index'])->name('broadcast.index');
    });

    // Admin Area (Admin only)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Educational Video Management
        Route::get('/videos', [AdminController::class, 'videos'])->name('videos');
        Route::post('/videos', [EducationalVideoController::class, 'store'])->name('videos.store');
        Route::put('/videos/{id}', [EducationalVideoController::class, 'update'])->name('videos.update');
        Route::delete('/videos/{id}', [EducationalVideoController::class, 'destroy'])->name('videos.destroy');
    });
});
