<?php

use App\Http\Controllers\Teacher\AuthController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AbsenceJustificationController;
use App\Http\Controllers\Teacher\NewsController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\AttendanceController;
use Illuminate\Support\Facades\Route;


Route::post('teacher/login', [AuthController::class, 'login']);
Route::get('teacher/support', [AuthController::class, 'supportMessage']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('teacher/me', [AuthController::class, 'me']);
    Route::post('teacher/profile', [AuthController::class, 'updateProfile']);
    Route::post('teacher/logout', [AuthController::class, 'logout']);
    Route::get('teacher/dashboard', [DashboardController::class, 'overview']);

    // طلبات تبرير الغياب
    Route::get('teacher/absence-justifications', [AbsenceJustificationController::class, 'index']);
    Route::post('teacher/absence-justifications/update/{justificationId}', [AbsenceJustificationController::class, 'update']);
    Route::post('teacher/absence-justifications/destroy/{justificationId}', [AbsenceJustificationController::class, 'destroy']);

    // البرنامج الدراسي
    Route::get('teacher/schedule/today', [ScheduleController::class, 'today']);
    Route::get('teacher/schedule/week', [ScheduleController::class, 'week']);

    // حضور الفصل والشعب
    Route::get('teacher/sections-with-students', [AttendanceController::class, 'sectionsWithStudents']);
    Route::post('teacher/attendance/sections/{sectionId}/batch-update', [AttendanceController::class, 'batchUpdateSectionAttendance']);

    // الأخبار
    Route::get('teacher/news', [NewsController::class, 'index']);
    Route::get('teacher/news/unread-count', [NewsController::class, 'unreadCount']);
    Route::post('teacher/news/{newsId}/mark-as-read', [NewsController::class, 'markAsRead']);
    Route::post('teacher/news/mark-all-as-read', [NewsController::class, 'markAllAsRead']);
});
