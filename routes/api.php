<?php

use App\Http\Controllers\HolidayPlanController;
use App\Http\Controllers\SecurityApiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [SecurityApiController::class, 'login']);
Route::post('/register', [SecurityApiController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/tokens/create', [SecurityApiController::class, 'createToken']);
    Route::get('/logout', [SecurityApiController::class, 'logout']);

    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);

    Route::apiResource('holiday-plans', HolidayPlanController::class);
    Route::get('/holiday-plans/{holidayPlan}/pdf', [HolidayPlanController::class, 'exportPdf']);
});

