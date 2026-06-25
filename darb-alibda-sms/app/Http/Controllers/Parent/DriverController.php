<?php

namespace App\Http\Controllers\Parent;

use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        return $this->successResponse([
            'driver_name' => null,
            'phone_number' => null,
            'vehicle_information' => null,
        ], 'تم جلب معلومات السائق بنجاح.');
    }
}
