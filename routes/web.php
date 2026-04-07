<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\WorkSessionController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MessageController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// ── CEO ────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:CEO'])->group(function () {
    Route::get('/ceo-dashboard', [DashboardController::class, 'ceoDashboard'])->name('ceo.dashboard');
    Route::resource('users', UserController::class);
});

// ── Project Manager ────────────────────────────────────────────
Route::middleware(['auth', 'role:Project Manager'])->group(function () {
    Route::get('/manager-dashboard', [DashboardController::class, 'managerDashboard'])->name('manager.dashboard');
});

// ── Designer ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:Designer'])->group(function () {
    Route::get('/designer-dashboard', [DashboardController::class, 'designerDashboard'])->name('designer.dashboard');
});

// ── Developer ──────────────────────────────────────────────────
Route::middleware(['auth', 'role:Developer'])->group(function () {
    Route::get('/developer-dashboard', [DashboardController::class, 'developerDashboard'])->name('developer.dashboard');
});

// ── Designer + Developer shared ────────────────────────────────
Route::middleware(['auth', 'role:Designer,Developer'])->group(function () {
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::patch('/kanban/tasks/{task}', [KanbanController::class, 'updateStatus'])->name('kanban.update');
    Route::post('/projects/{project}/sign-in', [WorkSessionController::class, 'signIn'])->name('projects.sign-in');
    Route::post('/projects/{project}/sign-out', [WorkSessionController::class, 'signOut'])->name('projects.sign-out');
    Route::post('/tasks/{task}/submit', [SubmissionController::class, 'store'])->name('tasks.submit');
});

// ── Client ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/client-dashboard', [DashboardController::class, 'clientDashboard'])->name('client.dashboard');
    Route::get('/client/projects', [ClientController::class, 'projects'])->name('client.projects');
    Route::get('/client/projects/{project}', [ClientController::class, 'show'])->name('client.projects.show');
    Route::post('/client/submissions/{submission}/approve', [ClientController::class, 'approveSubmission'])->name('client.submissions.approve');
    Route::post('/client/submissions/{submission}/revision', [ClientController::class, 'requestRevision'])->name('client.submissions.revision');
});

// ── CEO + Project Manager ──────────────────────────────────────
Route::middleware(['auth', 'role:CEO,Project Manager'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::put('/projects/{project}/milestones/{milestone}', [MilestoneController::class, 'update'])->name('projects.milestones.update');
    Route::delete('/projects/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('projects.milestones.destroy');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::post('/submissions/{submission}/approve', [SubmissionController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{submission}/revision', [SubmissionController::class, 'requestRevision'])->name('submissions.revision');
    Route::post('/submissions/{submission}/submit-to-client', [SubmissionController::class, 'submitToClient'])->name('submissions.submit-to-client');
    Route::post('/submissions/{submission}/resubmit-to-client', [SubmissionController::class, 'resubmitToClient'])->name('submissions.resubmit-to-client');
});

// ── Chat — accessible by all authenticated users ────────────
// Auth middleware applied, role check handled inside the controller
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{project}', [MessageController::class, 'show'])->name('chat.show');
    Route::post('/chat/{project}/messages', [MessageController::class, 'store'])->name('chat.store');
});

require __DIR__.'/settings.php';