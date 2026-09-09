<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\OfficeQueueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\GuardController;
use App\Models\Office;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'liveData'])->name('dashboard.data');
    Route::get('/dashboard/staff-data', [DashboardController::class, 'staffData'])->name('dashboard.staff.data');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Non-Guard Routes
    Route::middleware('not.guard')->group(function () {
        // Skipped Queues
        Route::prefix('skipped')->group(function () {
            Route::get('/', [OfficeQueueController::class, 'viewSkippedAll'])->name('skipped.list');
            Route::post('/restore', [OfficeQueueController::class, 'restoreSkipped'])->name('skipped.restore');
            Route::post('/swap', [OfficeQueueController::class, 'swapSkipped'])->name('skipped.swap');
        });

        // Transfer Visitor
        Route::post('/queue/transfer/{visitor}', [OfficeQueueController::class, 'transfer'])->name('office.queue.transfer');

        // Queue History
        Route::get('/queue/history', [OfficeQueueController::class, 'history'])->name('queue.history');

        // Statistics
        Route::get('/statistics', [OfficeQueueController::class, 'statistics'])->name('queue.statistics');

        // Staff Management
        Route::prefix('staff')->middleware('admin.head')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('staff.index');
            Route::post('/', [StaffController::class, 'store'])->name('staff.store');
            Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
            Route::put('/{id}', [StaffController::class, 'update'])->name('staff.update');
            Route::delete('/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        });

        // Office Management
        Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
            Route::get('/offices/create', [OfficeController::class, 'create'])->name('offices.create');
            Route::post('/offices', [OfficeController::class, 'store'])->name('offices.store');
            Route::put('/offices/{office}', [OfficeController::class, 'update'])->name('offices.update');
        });
    });

    // Guard Routes
    Route::middleware('guard')->prefix('guard')->name('guard.')->group(function () {
        Route::get('/', [GuardController::class, 'index'])->name('visitors');
        Route::get('/history', [GuardController::class, 'history'])->name('history');
        Route::get('/visitors/data', [GuardController::class, 'data'])->name('visitors.data');
    });
});

// Office Queue
Route::prefix('office')->middleware(['auth', 'not.guard'])->group(function () {
    Route::get('{office}/queue', [OfficeQueueController::class, 'index'])->name('office.queue');
    Route::post('{office}/next', [OfficeQueueController::class, 'next'])->name('office.queue.next');
    Route::post('{office}/done', [OfficeQueueController::class, 'markDone'])->name('office.queue.done');
    Route::post('{office}/skip', [OfficeQueueController::class, 'markSkip'])->name('office.queue.skip');
    Route::post('{office}/queue/call-again', [OfficeQueueController::class, 'callAgain'])->name('office.queue.call-again');
});

require __DIR__ . '/auth.php';
