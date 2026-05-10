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
