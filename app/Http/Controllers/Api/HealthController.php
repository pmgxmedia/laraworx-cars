<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'service' => 'worxcars',
            'status' => 'ok',
            'time' => now()->toIso8601String(),
            'db' => DB::select('select 1') ? 'ok' : 'down',
        ]);
    }
}
