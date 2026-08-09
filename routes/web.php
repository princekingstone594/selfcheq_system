<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\FocusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\AiCoachController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FinancialController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index'); // ✅ fixed name
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::patch('/tasks/{task}/alarm', [TaskController::class, 'alarmToggle'])->name('tasks.alarmToggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Routines
    Route::get('/routines', [RoutineController::class, 'index'])->name('routines.index');
    Route::post('/routines', [RoutineController::class, 'store'])->name('routines.store');
    Route::patch('/routines/{routine}/toggle', [RoutineController::class, 'toggle'])->name('routines.toggle');
    Route::delete('/routines/{routine}', [RoutineController::class, 'destroy'])->name('routines.destroy');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/toggle', [AppointmentController::class, 'toggle'])->name('appointments.toggle');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Journal
    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Focus
    Route::get('/focus', [FocusController::class, 'index'])->name('focus.index');
    Route::post('/focus/start', [FocusController::class, 'start'])->name('focus.start');
    Route::patch('/focus/{session}/stop', [FocusController::class, 'stop'])->name('focus.stop');

    //Devotional
    Route::get('/devotional', [DevotionalController::class, 'today'])->name('devotional.today');
    Route::post('/devotional/prayer-plans', [DevotionalController::class, 'storePrayerPlan'])->name('devotional.prayer.store');
    Route::patch('/devotional/prayer-plans/{prayerPlan}/complete', [DevotionalController::class, 'completePrayerPlan'])->name('devotional.prayer.complete');
    Route::delete('/devotional/prayer-plans/{prayerPlan}', [DevotionalController::class, 'destroyPrayerPlan'])->name('devotional.prayer.destroy');

    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::get('/coach', [AiCoachController::class, 'index'])->name('coach.index');
    Route::post('/ai-coach-chat', [AiCoachController::class, 'chat'])->name('coach.chat');
    Route::post('/coach-mode', [AiCoachController::class, 'setMode'])->name('coach.mode');

    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // Financials
    Route::get('/financials', [FinancialController::class, 'index'])->name('financials.index');
    Route::post('/financials', [FinancialController::class, 'store'])->name('financials.store');
    Route::patch('/financials/{financial}/toggle', [FinancialController::class, 'toggle'])->name('financials.toggle');
    Route::delete('/financials/{financial}', [FinancialController::class, 'destroy'])->name('financials.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');
    Route::post('/settings/permissions', [SettingsController::class, 'updatePermissions'])->name('settings.permissions');

});

require __DIR__.'/auth.php';