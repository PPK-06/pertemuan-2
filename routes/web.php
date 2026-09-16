<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Hanya untuk yang BELUM login. Kalau sudah login, Laravel
// otomatis mengalihkan ke /dashboard.
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Hanya untuk yang SUDAH login.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Hanya untuk admin.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| Task Management Routes - Orang 2 (feature/task)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.status');
});

/*
|--------------------------------------------------------------------------
| Task List Routes — Ferdy (NFR-02 + NFR-03)
| Semua route di bawah ini dijaga middleware 'auth'.
| Otorisasi kepemilikan ditangani oleh TaskListPolicy.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('task-lists', TaskListController::class)
         ->except(['edit', 'update']);   // hanya endpoint yang ada di SRS
});

/*
|--------------------------------------------------------------------------
| Project & Team Collaboration Routes (feature/project-team-wip)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);

    Route::post(
        '/projects/{project}/members',
        [ProjectController::class, 'addMember']
    )->name('projects.members.add');

    Route::delete(
        '/projects/{project}/members/{user}',
        [ProjectController::class, 'removeMember']
    )->name('projects.members.remove');
});
