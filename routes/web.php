<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// المسارات العامة
Route::get('/', function () {
    return view('welcome');
});

// المسارات المحمية بـ Auth
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects (Resource)
    Route::resource('projects', ProjectController::class);

    // Tasks (Nested under projects where needed)
    Route::get('projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('projects/{project}/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');

    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Project Members
    Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
});

require __DIR__.'/auth.php';
