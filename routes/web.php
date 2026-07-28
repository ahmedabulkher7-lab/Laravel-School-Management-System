<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Teacher;
use App\Http\Controllers\Student;

// ─── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (!Auth::check()) return redirect()->route('login');
    $user = Auth::user();
    if ($user->hasRole('admin'))   return redirect()->route('admin.dashboard');
    if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
    if ($user->hasRole('student')) return redirect()->route('student.dashboard');
    return redirect()->route('login');
});

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

    // Assignments
    Route::get('/assignments', [Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [Admin\AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/assignments/{assignment}', [Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Schedule
    Route::resource('schedules', Admin\ScheduleController::class);

    // Progress view (read-only for admin)
    Route::get('/progress', [Admin\ProgressController::class, 'index'])->name('progress.index');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate/{student}', [Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/download/{report}', [Admin\ReportController::class, 'download'])->name('reports.download');

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
