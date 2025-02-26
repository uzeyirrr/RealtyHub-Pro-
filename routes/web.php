<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ana sayfa
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // İlan Yönetimi
    Route::middleware('permission:view-listings')->group(function () {
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    });

    Route::middleware('permission:create-listings')->group(function () {
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    });

    Route::middleware('permission:edit-listings')->group(function () {
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    });

    Route::middleware('permission:delete-listings')->group(function () {
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    });

    // Danışman Yönetimi
    Route::middleware('permission:view-agents')->group(function () {
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
    });

    Route::middleware('permission:create-agents')->group(function () {
        Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    });

    Route::middleware('permission:edit-agents')->group(function () {
        Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
        Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    });

    Route::middleware('permission:delete-agents')->group(function () {
        Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
    });

    // Müşteri Yönetimi
    Route::middleware('permission:view-customers')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });

    Route::middleware('permission:create-customers')->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    });

    Route::middleware('permission:edit-customers')->group(function () {
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    });

    Route::middleware('permission:delete-customers')->group(function () {
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    // Ayarlar
    Route::middleware('permission:view-settings')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        
        // Rol Yönetimi
        Route::get('/settings/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/settings/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });

    Route::middleware('permission:edit-settings')->group(function () {
        Route::get('/settings/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/settings/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/settings/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/settings/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/settings/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Ayarlar Rotaları
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.upload-logo');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Raporlama Rotaları
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/custom-date-range', [ReportController::class, 'customDateRange'])->name('reports.custom-date-range');
    Route::post('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

require __DIR__.'/auth.php';
