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
use App\Http\Controllers\Parent\FeedbackController;
use App\Http\Controllers\Parent\SchoolInfoController;
use App\Http\Controllers\Teacher\AuthController as TeacherAuthController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AbsenceJustificationController;
use App\Http\Controllers\Teacher\NewsController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherMarkController;
use App\Http\Controllers\Teacher\SuggestionController;
use App\Http\Controllers\Teacher\ComplaintController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\NotificationController;
use App\Http\Controllers\TestTableController;

// مسار اختباري للتحقق من عمل خط النشر
Route::get('test-table', [TestTableController::class, 'index']);

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

    // ملاحظات المعلم للأهالي
    Route::get('teacher/parent-notes', [\App\Http\Controllers\Teacher\TeacherNoteController::class, 'index']);
    Route::post('teacher/parent-notes', [\App\Http\Controllers\Teacher\TeacherNoteController::class, 'store']);
    Route::post('teacher/parent-notes/{noteId}', [\App\Http\Controllers\Teacher\TeacherNoteController::class, 'update']);
    Route::post('teacher/parent-notes/delete/{noteId}', [\App\Http\Controllers\Teacher\TeacherNoteController::class, 'destroy']);

    Route::get('teacher/marks/students', [TeacherMarkController::class, 'students']);
    Route::post('teacher/marks', [TeacherMarkController::class, 'store']);
    Route::post('teacher/marks/{markId}', [TeacherMarkController::class, 'update']);
    Route::post('teacher/marks/delete/{markId}', [TeacherMarkController::class, 'destroy']);

    Route::get('teacher/notification',[NotificationController::class,'showNotification']);
    Route::post('teacher/notification/mark-all-as-read',[NotificationController::class,'markAllAsRead']);
    Route::post('teacher/notification/{notificationId}/mark-as-read',[NotificationController::class,'markAsRead']);
    Route::post('teacher/notification/{notificationId}',[NotificationController::class,'deleteNotification']);




Route::get('teacher/suggestions',[SuggestionController::class, 'index']);
Route::post('teacher/suggestion',[SuggestionController::class, 'store']);
Route::get('teacher/suggestions/{suggestion}',[SuggestionController::class, 'show']);


Route::get('teacher/complaints',[ComplaintController::class, 'index']);
Route::post('teacher/complaints',[ComplaintController::class, 'store']);
Route::get('teacher/complaints/{complaint}',[ComplaintController::class, 'show']);





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
    Route::get('parent/school-info', [SchoolInfoController::class, 'show']);
    Route::post('parent/suggestions', [FeedbackController::class, 'storeSuggestion']);
    Route::post('parent/complaints', [FeedbackController::class, 'storeComplaint']);
});
