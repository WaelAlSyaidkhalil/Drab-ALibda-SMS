<?php

namespace App\Http\Controllers\Parent;

use App\Models\Communication\SchoolInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolInfoController extends ParentController
{
    public function show(Request $request): JsonResponse
    {
        $info = SchoolInfo::query()->first();

        if (! $info) {
            return $this->successResponse([
                'name' => null,
                'logo' => null,
                'address' => null,
                'phone' => null,
                'email' => null,
                'description' => null,
                'website' => null,
            ], 'لم توجد معلومات مدرسية حتى الآن.');
        }

        return $this->successResponse([
            'name' => $info->name,
            'logo' => null,
            'address' => $info->address,
            'phone' => $info->phone,
            'email' => $info->email,
            'description' => $info->description,
            'website' => $info->website,
        ], 'تم جلب معلومات المدرسة بنجاح.');
    }
}
