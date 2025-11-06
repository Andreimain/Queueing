<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\OfficeQueueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\OfficeController;
use App\Models\Office;
use Illuminate\Support\Facades\Route;

// -----------------
// Public Routes
// -----------------

// Landing page
Route::get('/', function () {
    $offices = Office::all();
    return view('welcome', compact('offices'));
})->name('welcome');

// Monitor-Style Preview
Route::get('/monitor/{office}', [OfficeQueueController::class, 'monitor'])->name('monitor.show');
Route::get('/monitor/{office}/data', [OfficeQueueController::class, 'monitorData'])->name('monitor.data');

// Visitor Queue Registration (Public)
Route::get('/register-queue', [VisitorController::class, 'create'])->name('visitor.create');
Route::post('/register-queue', [VisitorController::class, 'store'])->name('visitor.store');

// Dashboard Refresh
Route::get('/dashboard/data', [DashboardController::class, 'liveData'])->name('dashboard.data');
Route::get('/dashboard/staff-data', [DashboardController::class, 'staffData'])->name('dashboard.staff.data');

// -----------------
// Authenticated User Routes
// -----------------
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Skipped Queues
    Route::prefix('skipped')->group(function () {
        Route::get('/', [OfficeQueueController::class, 'viewSkippedAll'])->name('skipped.list');
        Route::post('/restore', [OfficeQueueController::class, 'restoreSkipped'])->name('skipped.restore');
    });

    // Staff Management
    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // -----------------
    // Office Management (Admin Page)
    // -----------------
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/offices/create', [OfficeController::class, 'create'])->name('offices.create');
        Route::post('/offices', [OfficeController::class, 'store'])->name('offices.store');
    });
});

// -----------------
// Office Routes (must be logged in)
// -----------------
Route::prefix('office')->middleware('auth')->group(function () {
    Route::get('{office}/queue', [OfficeQueueController::class, 'index'])->name('office.queue');
    Route::post('{office}/next', [OfficeQueueController::class, 'next'])->name('office.queue.next');
    Route::post('{office}/done', [OfficeQueueController::class, 'markDone'])->name('office.queue.done');
    Route::post('{office}/skip', [OfficeQueueController::class, 'markSkip'])->name('office.queue.skip');
});

require __DIR__ . '/auth.php';
