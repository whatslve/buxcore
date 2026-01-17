<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\UserWorks;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| User visits
|--------------------------------------------------------------------------
*/
Route::get('/visits', [UserWorks::class, 'visits'])
    ->middleware(['auth', 'verified'])
    ->name('visits');

Route::get('/visits/{visit}/perform', [UserWorks::class, 'performView'])
    ->middleware('auth')
    ->name('visits.perform');

/*
|--------------------------------------------------------------------------
| Cabinet (ex Adv)
|--------------------------------------------------------------------------
*/
Route::get('/cabinet', function () {
    return Inertia::render('Adv_dashboard');
})->middleware(['auth', 'verified'])->name('cabinet.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/cabinet/visits', [VisitController::class, 'index'])
        ->name('cabinet.visits.index');

    Route::get('/cabinet/visits/create', [VisitController::class, 'create'])
        ->name('cabinet.visits.create');

    Route::post('/cabinet/visits', [VisitController::class, 'store'])
        ->name('cabinet.visits.store');

    Route::get('/cabinet/visits/{visit}/edit', [VisitController::class, 'edit'])
        ->name('cabinet.visits.edit');

    Route::put('/cabinet/visits/{visit}', [VisitController::class, 'update'])
        ->name('cabinet.visits.update');

    Route::get('/cabinet/quests/', [QuestsController::class, 'index'])
        ->name('cabinet.quests.index');
});

/*
|--------------------------------------------------------------------------
| Finish visit record (AJAX)
|--------------------------------------------------------------------------
*/
Route::post(
    '/visits-records/{visitsRecord}/finish',
    [VisitController::class, 'finish']
)->middleware('auth')
    ->name('visits-records.finish');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
