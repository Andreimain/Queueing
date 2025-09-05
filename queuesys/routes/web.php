<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeQueueController;
use App\Http\Controllers\Controller;
use App\Models\Office;

// Public landing page
Route::get('/', function () {
    return view('welcome');
});

// Monitor-Style Preview
Route::get('/monitor/{office}', [Controller::class, 'monitor'])->name('monitor.show');
Route::get('/monitor/{office}/data', [Controller::class, 'monitorData'])->name('monitor.data');

// Public visitor queue registration
Route::get('/register-queue', [VisitorController::class, 'create'])->name('visitor.create');
Route::post('/register-queue', [VisitorController::class, 'store'])->name('visitor.store');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated profile + skipped routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/skipped', [OfficeQueueController::class, 'viewSkippedAll'])->name('skipped.list');
    Route::post('/skipped/restore', [OfficeQueueController::class, 'restoreSkipped'])->name('skipped.restore');
});

// Per Office routes
Route::prefix('office')->middleware('auth')->group(function () {
    Route::get('{office}/queue', [OfficeQueueController::class, 'index'])->name('office.queue');
    Route::post('{office}/next', [OfficeQueueController::class, 'next'])->name('office.queue.next');
    Route::post('{office}/done', [OfficeQueueController::class, 'markDone'])->name('office.queue.done');
    Route::post('{office}/skip', [OfficeQueueController::class, 'markSkip'])->name('office.queue.skip');
});

require __DIR__.'/auth.php';
