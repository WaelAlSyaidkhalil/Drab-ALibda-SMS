<?php

use App\Http\Controllers\Parent\AuthController;
use App\Http\Controllers\Parent\ChildrenController;
use App\Http\Controllers\Parent\ScheduleController;
use App\Http\Controllers\Parent\GradesController;
use App\Http\Controllers\Parent\AttendanceController;
use App\Http\Controllers\Parent\ExcuseRequestController;
use App\Http\Controllers\Parent\NoteController;
use App\Http\Controllers\Parent\AnnouncementController;
use App\Http\Controllers\Parent\DriverController;
use App\Http\Controllers\Teacher\AuthController as TeacherAuthController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AbsenceJustificationController;
use App\Http\Controllers\Teacher\NewsController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use Illuminate\Support\Facades\Route;


Route::post('teacher/login', [TeacherAuthController::class, 'login']);
Route::post('parent/login', [AuthController::class, 'login']);
Route::get('teacher/support', [TeacherAuthController::class, 'supportMessage']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('teacher/me', [TeacherAuthController::class, 'me']);
    Route::post('teacher/profile', [TeacherAuthController::class, 'updateProfile']);
    Route::post('teacher/logout', [TeacherAuthController::class, 'logout']);
    Route::get('teacher/dashboard', [DashboardController::class, 'overview']);

    // طلبات تبرير الغياب
    Route::get('teacher/absence-justifications', [AbsenceJustificationController::class, 'index']);
    Route::post('teacher/absence-justifications/update/{justificationId}', [AbsenceJustificationController::class, 'update']);
    Route::post('teacher/absence-justifications/destroy/{justificationId}', [AbsenceJustificationController::class, 'destroy']);

    // البرنامج الدراسي
    Route::get('teacher/schedule/today', [TeacherScheduleController::class, 'today']);
    Route::get('teacher/schedule/week', [TeacherScheduleController::class, 'week']);

    // حضور الفصل والشعب
    Route::get('teacher/sections-with-students', [TeacherAttendanceController::class, 'sectionsWithStudents']);
    Route::post('teacher/attendance/sections/{sectionId}/batch-update', [TeacherAttendanceController::class, 'batchUpdateSectionAttendance']);

    // الأخبار
    Route::get('teacher/news', [NewsController::class, 'index']);
    Route::get('teacher/news/unread-count', [NewsController::class, 'unreadCount']);
    Route::post('teacher/news/{newsId}/mark-as-read', [NewsController::class, 'markAsRead']);
    Route::post('teacher/news/mark-all-as-read', [NewsController::class, 'markAllAsRead']);

    Route::post('parent/logout', [AuthController::class, 'logout']);
    Route::post('parent/change-password', [AuthController::class, 'changePassword']);
    Route::get('parent/profile', [AuthController::class, 'profile']);
    Route::post('parent/profile', [AuthController::class, 'updateProfile']);
    Route::get('parent/children', [ChildrenController::class, 'index']);
    Route::get('parent/children/{student}', [ChildrenController::class, 'show']);
    Route::get('parent/children/{student}/schedule', [ScheduleController::class, 'show']);
    Route::get('parent/children/{student}/grades', [GradesController::class, 'show']);
    Route::get('parent/children/{student}/attendance', [AttendanceController::class, 'show']);
    Route::get('parent/children/{student}/attendance/report', [AttendanceController::class, 'report']);
    Route::get('parent/children/{student}/attendance/monthly/{month}/{year}', [AttendanceController::class, 'monthlyStats']);
    Route::post('parent/excuse-requests', [ExcuseRequestController::class, 'store']);
    Route::get('parent/excuse-requests', [ExcuseRequestController::class, 'index']);
    Route::get('parent/excuse-requests/{id}', [ExcuseRequestController::class, 'show']);
    Route::get('parent/notes', [NoteController::class, 'index']);
    Route::get('parent/notes/{id}', [NoteController::class, 'show']);
    Route::get('parent/announcements', [AnnouncementController::class, 'index']);
    Route::get('parent/announcements/{id}', [AnnouncementController::class, 'show']);
    Route::get('parent/children/{student}/driver', [DriverController::class, 'show']);
});
