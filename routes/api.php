<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\CashTransactionController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentification
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard')->group(function () {

        Route::get('/', [DashboardController::class, 'index']);

    });

    /*
    |--------------------------------------------------------------------------
    | Étudiants
    |--------------------------------------------------------------------------
    */

    Route::apiResource('students', StudentController::class);

    /*
    |--------------------------------------------------------------------------
    | Formations
    |--------------------------------------------------------------------------
    */

    Route::apiResource('trainings', TrainingController::class);

    /*
    |--------------------------------------------------------------------------
    | Inscriptions
    |--------------------------------------------------------------------------
    */

    Route::apiResource('enrollments', EnrollmentController::class);

    /*
    |--------------------------------------------------------------------------
    | Paiements
    |--------------------------------------------------------------------------
    */

    Route::apiResource('payments', PaymentController::class);

    /*
    |--------------------------------------------------------------------------
    | Moyens de paiement
    |--------------------------------------------------------------------------
    */

    Route::apiResource('payment-methods', PaymentMethodController::class);

    /*
    |--------------------------------------------------------------------------
    | Dépenses
    |--------------------------------------------------------------------------
    */

    Route::apiResource('expenses', ExpenseController::class);

    /*
    |--------------------------------------------------------------------------
    | Journal de caisse
    |--------------------------------------------------------------------------
    */

    Route::apiResource('cash-transactions', CashTransactionController::class)
        ->only([
            'index',
            'show',
        ]);

    Route::prefix('cash-transactions')->group(function () {

        Route::get('/income', [CashTransactionController::class, 'income']);

        Route::get('/expenses', [CashTransactionController::class, 'expenses']);

        Route::get('/summary', [CashTransactionController::class, 'summary']);

    });

    /*
    |--------------------------------------------------------------------------
    | Rapports
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {

        Route::get('/payments', [ReportController::class, 'payments']);

        Route::get('/expenses', [ReportController::class, 'expenses']);

        Route::get('/cash-book', [ReportController::class, 'cashBook']);

        Route::get('/students', [ReportController::class, 'students']);

        Route::get('/enrollments', [ReportController::class, 'enrollments']);

        Route::get('/financial-summary', [ReportController::class, 'financialSummary']);

    });

});