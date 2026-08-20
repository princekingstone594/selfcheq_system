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
use App\Http\Controllers\HabitController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\WeeklyReviewController;
use App\Http\Controllers\BibleChapterController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoalController;

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
    Route::post('/calendar/quick-add', [CalendarController::class, 'quickAdd'])->name('calendar.quickAdd');
    Route::post('/calendar/move', [CalendarController::class, 'move'])->name('calendar.move');

    // Focus
    Route::get('/focus', [FocusController::class, 'index'])->name('focus.index');
    Route::get('/focus/today', [FocusController::class, 'today'])->name('focus.today');
    Route::post('/focus/start', [FocusController::class, 'start'])->name('focus.start');
    Route::patch('/focus/{session}/stop', [FocusController::class, 'stop'])->name('focus.stop');

    //Devotional
    Route::get('/devotional', [DevotionalController::class, 'today'])->name('devotional.today');
    Route::post('/devotional/prayer-plans', [DevotionalController::class, 'storePrayerPlan'])->name('devotional.prayer.store');
    Route::patch('/devotional/prayer-plans/{prayerPlan}/complete', [DevotionalController::class, 'completePrayerPlan'])->name('devotional.prayer.complete');
    Route::delete('/devotional/prayer-plans/{prayerPlan}', [DevotionalController::class, 'destroyPrayerPlan'])->name('devotional.prayer.destroy');

    Route::post('/devotional/morning-devotion', [DevotionalController::class, 'storeMorningDevotion'])->name('devotional.morning.store');
    Route::patch('/devotional/morning-devotion/{morningDevotion}/toggle', [DevotionalController::class, 'toggleMorningDevotion'])->name('devotional.morning.toggle');

    Route::post('/devotional/fasting-plans', [DevotionalController::class, 'storeFastingPlan'])->name('devotional.fasting.store');
    Route::patch('/devotional/fasting-plans/{fastingPlan}/start', [DevotionalController::class, 'startFastingPlan'])->name('devotional.fasting.start');
    Route::patch('/devotional/fasting-plans/{fastingPlan}/complete', [DevotionalController::class, 'completeFastingPlan'])->name('devotional.fasting.complete');
    Route::delete('/devotional/fasting-plans/{fastingPlan}', [DevotionalController::class, 'destroyFastingPlan'])->name('devotional.fasting.destroy');

    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::get('/coach', [AiCoachController::class, 'index'])->name('coach.index');
    Route::post('/ai-coach-chat', [AiCoachController::class, 'chat'])->name('coach.chat');
    Route::post('/coach-mode', [AiCoachController::class, 'setMode'])->name('coach.mode');

    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // Weekly Review
    Route::get('/weekly-review', [WeeklyReviewController::class, 'index'])->name('weekly-review.index');

    // Data Export
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/json', [ExportController::class, 'exportJson'])->name('export.json');
    Route::get('/export/text', [ExportController::class, 'exportText'])->name('export.text');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::patch('/goals/{goal}/update-score', [GoalController::class, 'updateScore'])->name('goals.update-score');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');

    // Bible Chapters
    Route::get('/bible-chapter/{reference}', [BibleChapterController::class, 'show'])->name('bible-chapter.show');
    Route::get('/bible-chapters', [BibleChapterController::class, 'availableReferences'])->name('bible-chapters.available');
    Route::post('/bible-chapter/personalize', [BibleChapterController::class, 'personalize'])->name('bible-chapter.personalize');

    // Notes
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Financials
    Route::get('/financials', [FinancialController::class, 'index'])->name('financials.index');
    Route::post('/financials', [FinancialController::class, 'store'])->name('financials.store');
    Route::patch('/financials/{financial}/toggle', [FinancialController::class, 'toggle'])->name('financials.toggle');
    Route::delete('/financials/{financial}', [FinancialController::class, 'destroy'])->name('financials.destroy');

    // Habits
    Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::patch('/habits/{habit}/toggle', [HabitController::class, 'toggle'])->name('habits.toggle');
    Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');
    Route::post('/settings/permissions', [SettingsController::class, 'updatePermissions'])->name('settings.permissions');

});

require __DIR__.'/auth.php';
