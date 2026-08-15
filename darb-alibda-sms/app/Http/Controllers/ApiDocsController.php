<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * صفحة توثيق واجهات API (Swagger UI).
 *
 * ليست Closure حتى يبقى `php artisan route:cache` قابلاً للتنفيذ.
 */
class ApiDocsController extends Controller
{
    public function index(): View
    {
        return view('api-docs');
    }
}
