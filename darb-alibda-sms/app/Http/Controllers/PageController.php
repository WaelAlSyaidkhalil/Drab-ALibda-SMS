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
        try {
            $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
            $auth = $factory->createAuth();

            return "✅ الاتصال ناجح مع Firebase";
        } catch (\Exception $e) {
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
