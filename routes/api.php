<?php

declare(strict_types=1);

use App\Http\Controllers\API\RegisterPatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::prefix('auth/register')->group(function (): void {
    Route::post('/patients', RegisterPatientController::class)->middleware('throttle:4,1');
});
