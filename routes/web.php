<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseSetupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyWizardController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Redirect
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// ==========================================
// 🚀 LIVE DATABASE AUTO-SETUP ROUTE
// Direct hit on live server creates all tables + default admin
// ==========================================
Route::get('/setup-db', [DatabaseSetupController::class, 'setup'])->name('setup.db');
Route::get('/migrate-live-db', [DatabaseSetupController::class, 'setup']); // Alias

// ==========================================
// 🔐 AUTHENTICATION
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 🏢 COMPANY WORKBOOK WIZARD (Tabs 1 to 9 + Summary)
// ==========================================
Route::middleware(['auth'])->prefix('company')->name('company.')->group(function () {
    Route::get('/wizard/{step?}', [CompanyWizardController::class, 'showStep'])->name('wizard');
    Route::get('/summary', [CompanyWizardController::class, 'summary'])->name('summary');
    Route::post('/submit', [CompanyWizardController::class, 'submitAssessment'])->name('submit');

    // Tab 1: Company Details
    Route::post('/step/1', [CompanyWizardController::class, 'saveStep1'])->name('step1.save');

    // Tab 2: Staff
    Route::post('/step/2/staff', [CompanyWizardController::class, 'addStaff'])->name('staff.add');
    Route::delete('/step/2/staff/{id}', [CompanyWizardController::class, 'deleteStaff'])->name('staff.delete');

    // Tab 3: Computers
    Route::post('/step/3/computer', [CompanyWizardController::class, 'addComputer'])->name('computer.add');
    Route::delete('/step/3/computer/{id}', [CompanyWizardController::class, 'deleteComputer'])->name('computer.delete');

    // Tab 4: Mobiles & Tablets
    Route::post('/step/4/mobile', [CompanyWizardController::class, 'addMobile'])->name('mobile.add');
    Route::delete('/step/4/mobile/{id}', [CompanyWizardController::class, 'deleteMobile'])->name('mobile.delete');

    // Tab 5: Servers
    Route::post('/step/5/server', [CompanyWizardController::class, 'addServer'])->name('server.add');
    Route::delete('/step/5/server/{id}', [CompanyWizardController::class, 'deleteServer'])->name('server.delete');

    // Tab 6: Network
    Route::post('/step/6/network', [CompanyWizardController::class, 'addNetworkDevice'])->name('network.add');
    Route::delete('/step/6/network/{id}', [CompanyWizardController::class, 'deleteNetworkDevice'])->name('network.delete');

    // Tab 7: Cloud Services
    Route::post('/step/7/cloud', [CompanyWizardController::class, 'addCloudService'])->name('cloud.add');
    Route::delete('/step/7/cloud/{id}', [CompanyWizardController::class, 'deleteCloudService'])->name('cloud.delete');

    // Tab 8: Software
    Route::post('/step/8/software', [CompanyWizardController::class, 'addSoftware'])->name('software.add');
    Route::delete('/step/8/software/{id}', [CompanyWizardController::class, 'deleteSoftware'])->name('software.delete');

    // Tab 9: IT Contact
    Route::post('/step/9', [CompanyWizardController::class, 'saveStep9'])->name('step9.save');
});

// ==========================================
// 🛡️ ADMIN COMMAND CENTER
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/company/{id}', [AdminController::class, 'showCompany'])->name('company.show');
    Route::post('/company', [AdminController::class, 'storeCompany'])->name('company.store');
    Route::patch('/company/{id}/status', [AdminController::class, 'updateStatus'])->name('company.status');
    Route::delete('/company/{id}', [AdminController::class, 'deleteCompany'])->name('company.delete');
});
