<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);

Route::get('/test-firebase', [PageController::class, 'testFirebase']);

Route::get('/locale/{locale}', [PageController::class, 'switchLocale'])->name('locale.switch');

// توثيق واجهات API (Swagger UI)
Route::get('/docs', [ApiDocsController::class, 'index'])->name('api.docs');
