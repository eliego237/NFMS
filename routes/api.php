<?php

/*
|--------------------------------------------------------------------------
| API Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CashTransactionController;
use App\Http\Controllers\Api\DashboardChartController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TrainingModuleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UtilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test-token', function (Request $request) {
    return response()->json([
        'authorization' => $request->header('Authorization'),
        'bearerToken' => $request->bearerToken(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get(
    '/payments/{payment}/receipt',
    [PdfController::class, 'receipt']
);

Route::get(
    '/receipt/{receipt}',
    [PdfController::class, 'verifyReceipt']
)->name('receipt.verify');

/*
|--------------------------------------------------------------------------
| Dashboard (Temporairement public)
|--------------------------------------------------------------------------
|
| Cette route est publique uniquement pendant le développement
| du frontend React.
| Elle sera replacée dans auth:sanctum une fois
| l'authentification terminée.
|
*/

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
);

/*
|--------------------------------------------------------------------------
| Dashboard Charts (Temporairement publics)
|--------------------------------------------------------------------------
*/

Route::prefix('dashboard/charts')->group(function () {

    Route::get('/payments', [DashboardChartController::class, 'payments']);

    Route::get('/expenses', [DashboardChartController::class, 'expenses']);

    Route::get('/enrollments', [DashboardChartController::class, 'enrollments']);

    Route::get('/payment-methods', [DashboardChartController::class, 'paymentMethods']);

    Route::get('/trainings', [DashboardChartController::class, 'trainings']);

});

/*
|--------------------------------------------------------------------------
| Students (Temporairement publics)
|--------------------------------------------------------------------------
|
| Cette route est publique uniquement pendant le développement
| du frontend React.
| Elle sera replacée dans auth:sanctum une fois
| l'authentification terminée.
|
*/

Route::apiResource('students', StudentController::class);


/*
|--------------------------------------------------------------------------
| Payments (Temporairement publics)
|--------------------------------------------------------------------------
|
| Cette route est publique uniquement pendant le développement
| du frontend React.
| Elle sera replacée dans auth:sanctum une fois
| l'authentification terminée.
|
*/

/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

/*
|--------------------------------------------------------------------------
| Enrollments (Temporairement publics)
|--------------------------------------------------------------------------
*/

Route::apiResource('enrollments', EnrollmentController::class);

Route::apiResource('payments', PaymentController::class);

/*
|--------------------------------------------------------------------------
| Payment Methods (Temporairement publiques)
|--------------------------------------------------------------------------
*/

Route::apiResource('payment-methods', PaymentMethodController::class);

/*
|--------------------------------------------------------------------------
| Payments (Temporairement publics)
|--------------------------------------------------------------------------
*/


    /*
    |--------------------------------------------------------------------------
    | Authentification
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);

    /*
    |--------------------------------------------------------------------------
    | Paramètres
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [SettingController::class, 'index']);

    Route::put('/settings', [SettingController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Utilisateurs
    |--------------------------------------------------------------------------
    */

    Route::apiResource('users', UserController::class);

    /*
    |--------------------------------------------------------------------------
    | Utilitaires
    |--------------------------------------------------------------------------
    */

    Route::get('/roles', [UtilityController::class, 'roles']);

    Route::get('/permissions', [UtilityController::class, 'permissions']);

    /*
    |--------------------------------------------------------------------------
    | Journal d'activité
    |--------------------------------------------------------------------------
    */

    Route::get('/activity-logs', [ActivityLogController::class, 'index']);

    Route::get('/activity-logs/{activity}', [ActivityLogController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Ressources principales
    |--------------------------------------------------------------------------
    */

    Route::apiResource('trainings', TrainingController::class);

    Route::apiResource('training-modules', TrainingModuleController::class);

    Route::apiResource('expenses', ExpenseController::class);

    /*
    |--------------------------------------------------------------------------
    | Caisse
    |--------------------------------------------------------------------------
    */

    Route::prefix('cash-transactions')->group(function () {

        Route::get('/summary', [CashTransactionController::class, 'summary']);

        Route::get('/income', [CashTransactionController::class, 'income']);

        Route::get('/expenses', [CashTransactionController::class, 'expenses']);

    });

    Route::apiResource('cash-transactions', CashTransactionController::class)
        ->only([
            'index',
            'show',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Rapports
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {

        Route::get('/test-pdf', function () {
            return 'PDF OK';
        });

        Route::get('/payments', [ReportController::class, 'payments']);

        Route::get('/expenses', [ReportController::class, 'expenses']);

        Route::get('/cash-book', [ReportController::class, 'cashBook']);

        Route::get('/students', [ReportController::class, 'students']);

        Route::get('/enrollments', [ReportController::class, 'enrollments']);

        Route::get('/financial-summary', [ReportController::class, 'financialSummary']);

    });

});