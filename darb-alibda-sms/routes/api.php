<?php

use App\Http\Controllers\Teacher\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('teacher/login', [AuthController::class, 'login']);
Route::get('teacher/support', [AuthController::class, 'supportMessage']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('teacher/me', [AuthController::class, 'me']);
    Route::post('teacher/profile', [AuthController::class, 'updateProfile']);
    Route::post('teacher/logout', [AuthController::class, 'logout']);
});
