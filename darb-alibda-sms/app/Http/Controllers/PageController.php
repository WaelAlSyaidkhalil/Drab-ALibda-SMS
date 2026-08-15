<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Kreait\Firebase\Factory;

/**
 * صفحات الويب العامة.
 *
 * نُقلت من Closures في routes/web.php لأن `php artisan route:cache`
 * لا يستطيع تسلسل المسارات المبنية على Closure.
 */
class PageController extends Controller
{
    public function home(): View
    {
        return view('welcome');
    }

    public function testFirebase(): string
    {
        $credentials = config('firebase.projects.app.credentials');

        if (blank($credentials)) {
            return "❌ FIREBASE_CREDENTIALS غير مضبوط في ملف .env";
        }

        if (! is_readable($credentials)) {
            return "❌ ملف الاعتماد غير موجود أو غير قابل للقراءة: {$credentials}";
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentials);
            $auth = $factory->createAuth();

            return "✅ الاتصال ناجح مع Firebase";
        } catch (\Throwable $e) {
            // \Throwable وليس \Exception: تمرير قيمة غير صالحة يرمي TypeError
            // وهو من نوع \Error فلا يلتقطه catch (\Exception).
            return "❌ خطأ في الاتصال: " . $e->getMessage();
        }
    }

    public function switchLocale(string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['en', 'ar']), 404);

        session(['locale' => $locale]);

        return back();
    }
}
