<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProjectController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::middleware(['auth', 'role:CEO'])->group(function () {
    Route::get('/ceo-dashboard', [DashboardController::class, 'ceoDashboard'])
        ->name('ceo.dashboard');

    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'role:Project Manager'])->group(function () {
    Route::get('/manager-dashboard', [DashboardController::class, 'managerDashboard'])
        ->name('manager.dashboard');
});

Route::middleware(['auth', 'role:Designer'])->group(function () {
    Route::get('/designer-dashboard', [DashboardController::class, 'designerDashboard'])
        ->name('designer.dashboard');
});

Route::middleware(['auth', 'role:Developer'])->group(function () {
    Route::get('/developer-dashboard', [DashboardController::class, 'developerDashboard'])
        ->name('developer.dashboard');
});

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/client-dashboard', [DashboardController::class, 'clientDashboard'])
        ->name('client.dashboard');
});

Route::middleware(['auth', 'role:CEO,Project Manager'])->group(function () {
    Route::resource('projects', ProjectController::class);
});

require __DIR__.'/settings.php';