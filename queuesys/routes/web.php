<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeQueueController;

// Public landing page
Route::get('/', function () {
    return view('welcome');
});

// Public visitor queue registration (GET + POST)
Route::get('/register-queue', [VisitorController::class, 'create'])->name('visitor.create');
Route::post('/register-queue', [VisitorController::class, 'store'])->name('visitor.store');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Per Office routes
Route::prefix('office')->middleware('auth')->group(function () {
    Route::get('{office}/queue', [OfficeQueueController::class, 'index'])->name('office.queue');
    Route::post('{office}/next', [OfficeQueueController::class, 'next'])->name('office.queue.next');
    Route::post('/office/{office}/done', [OfficeQueueController::class, 'markDone'])->name('office.queue.done');

});

require __DIR__.'/auth.php';
