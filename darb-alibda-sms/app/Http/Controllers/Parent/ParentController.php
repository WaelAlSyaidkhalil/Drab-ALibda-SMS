<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class ParentController extends Controller
{
    use ApiResponse, AuthorizesRequests;
}
