<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\VisitController;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/visits',[\App\Http\Controllers\UserWorks::class, 'visits'])->middleware(['auth', 'verified'])->name('visits');
Route::get('/adv_dashboard', function () {
    return Inertia::render('Adv_dashboard');
})->middleware(['auth', 'verified'])->name('adv_dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('visits/{visit}/perform', [\App\Http\Controllers\UserWorks::class, 'performView'])->name('visits.perform');

    Route::get('adv/visits', [VisitController::class, 'index'])->name('adv.visits.index');
    Route::get('adv/visits/create', [VisitController::class, 'create'])->name('adv.visits.create');
    Route::post('adv/visits', [VisitController::class, 'store'])->name('adv.visits.store');
    Route::get('adv/visits/{visit}/edit', [VisitController::class, 'edit'])->name('adv.visits.edit');
    Route::put('adv/visits/{visit}/update', [VisitController::class, 'update'])->name('adv.visits.update');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
