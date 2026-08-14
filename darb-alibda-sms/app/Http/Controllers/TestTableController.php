<?php

namespace App\Http\Controllers;

use App\Models\TestTable;
use Illuminate\Http\JsonResponse;

class TestTableController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = TestTable::query()->orderBy('id')->get([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم جلب السجلات الاختبارية بنجاح.',
            'count' => $rows->count(),
            'data' => $rows,
        ]);
    }
}
