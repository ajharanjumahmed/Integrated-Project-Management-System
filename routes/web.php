<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\DashboardController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware(['auth', 'role:CEO'])->group(function () {

    Route::get('/ceo-dashboard', function () {
        return "CEO Dashboard";
    });

});

Route::middleware(['auth', 'role:Project Manager'])->group(function () {

    Route::get('/manager-dashboard', function () {
        return "Manager Dashboard";
    });

});

Route::middleware(['auth', 'role:Designer,Developer'])->group(function () {

    Route::get('/tasks', function () {
        return "Task Board";
    });

});

Route::middleware(['auth','role:CEO'])->get('/ceo-dashboard', [DashboardController::class, 'ceoDashboard']);

require __DIR__.'/settings.php';
