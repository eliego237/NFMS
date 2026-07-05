<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\StudentController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function () {
        return auth()->user();
    });

    Route::apiResource('students', StudentController::class);

    Route::apiResource('trainings', TrainingController::class);

    Route::apiResource('enrollments', EnrollmentController::class);

});