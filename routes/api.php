<?php

use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->get('/health', HealthController::class);
