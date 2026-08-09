<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Teacher;
use App\Http\Controllers\Student;
use App\Http\Controllers\LandingPageController;

// ─── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::post('/contact', [LandingPageController::class, 'storeContact'])->name('contact.store');
Route::view('/privacy', 'legal', ['title' => 'Privacy policy', 'heading' => 'Your privacy matters', 'content' => 'Summit Academy uses contact information only to respond to your enquiry and support your relationship with the school. We do not sell personal information.'])->name('privacy');
Route::view('/terms', 'legal', ['title' => 'Terms of use', 'heading' => 'Terms of use', 'content' => 'Please use the Summit Academy website and family portal respectfully and only for their intended educational purpose.'])->name('terms');

// ─── Auth Routes ──────────────────────────────────────────────────────────────
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Students CRUD
    Route::resource('students', Admin\StudentController::class);

    // Teachers CRUD
    Route::resource('teachers', Admin\TeacherController::class);

    // Subjects CRUD
    Route::resource('subjects', Admin\SubjectController::class);

    // Grade Levels CRUD
    Route::resource('grade-levels', Admin\GradeLevelController::class);


    // Schedule
    Route::resource('schedules', Admin\ScheduleController::class);

    // Progress view (read-only for admin)
    Route::get('/progress', [Admin\ProgressController::class, 'index'])->name('progress.index');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate/{student}', [Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/download/{report}', [Admin\ReportController::class, 'download'])->name('reports.download');

    // Weekly plans
    Route::get('/weekly-plans', [Admin\WeeklyPlanController::class, 'index'])->name('weekly-plans.index');
    Route::get('/weekly-plans/{gradeLevel}/download', [Admin\WeeklyPlanController::class, 'download'])->name('weekly-plans.download');

    // Notifications
    Route::get('/notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [Admin\NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// ─── Teacher Routes ───────────────────────────────────────────────────────────
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [Teacher\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/students', [Teacher\StudentController::class, 'index'])->name('students.index');
    Route::get('/progress/log', [Teacher\ProgressController::class, 'log'])->name('progress.log');
    Route::get('/progress/history', [Teacher\ProgressController::class, 'history'])->name('progress.history');
    Route::get('/weekly-plans', [Teacher\WeeklyPlanController::class, 'index'])->name('weekly-plans.index');
    Route::post('/weekly-plans/{gradeLevel}', [Teacher\WeeklyPlanController::class, 'store'])->name('weekly-plans.store');
    Route::get('/notifications', [Teacher\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [Teacher\NotificationController::class, 'markRead'])->name('notifications.markRead');
});

// ─── Student Routes ───────────────────────────────────────────────────────────
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/performance', [Student\PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/schedule', [Student\ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/reports', [Student\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download/{report}', [Student\ReportController::class, 'download'])->name('reports.download');
    Route::get('/notifications', [Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [Student\NotificationController::class, 'markRead'])->name('notifications.markRead');
});
