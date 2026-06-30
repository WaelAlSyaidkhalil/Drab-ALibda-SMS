<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-firebase', function () {
    try {
        $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $auth = $factory->createAuth();

        return "✅ الاتصال ناجح مع Firebase";
    } catch (\Exception $e) {
        return "❌ خطأ في الاتصال: " . $e->getMessage();
    }
});

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar']), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');
