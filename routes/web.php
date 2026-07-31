<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tasks', [TaskController::class, 'index'])->name('task.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/routines', [RoutineController::class, 'index'])->name('routines.index');
    Route::post('/routines', [RoutineController::class, 'store'])->name('routines.store');
    Route::patch('/routines/{routine}/toggle', [RoutineController::class, 'toggle'])->name('routines.toggle');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/toggle', [AppointmentController::class, 'toggle'])->name('appointments.toggle');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');

    Route::get('/focus', [FocusController::class, 'index'])->name('focus.index');
    Route::post('/focus/start', [FocusController::class, 'start'])->name('focus.start');
    Route::patch('/focus/{session}/stop', [FocusController::class, 'stop'])->name('focus.stop');
});

require __DIR__.'/auth.php';
