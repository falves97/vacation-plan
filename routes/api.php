<?php

use App\Http\Controllers\HolidayPlanController;
use App\Http\Controllers\SecurityApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [SecurityApiController::class, 'login']);
Route::post('/register', [SecurityApiController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/tokens/create', [SecurityApiController::class, 'createToken']);
    Route::get('/logout', [SecurityApiController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('holiday-plans', HolidayPlanController::class);
});

