<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Training;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Durée du cache (secondes).
     */
    private const CACHE_TTL = 60;

    /**
     * Données du tableau de bord.
     */
    public static function index(): array
    {
        return Cache::remember(
            'dashboard.index',
            self::CACHE_TTL,
            function () {

                /*
                |--------------------------------------------------------------------------
                | Dates
                |--------------------------------------------------------------------------
                */

                $today = today();

                $currentMonth = now()->month;

                $currentYear = now()->year;

                /*
                |--------------------------------------------------------------------------
                | Statistiques générales
                |--------------------------------------------------------------------------
                */

                $students = Student::count();

                $trainings = Training::count();

                $enrollments = Enrollment::count();

                /*
                |--------------------------------------------------------------------------
                | Statistiques financières
                |--------------------------------------------------------------------------
                */

                $expectedRevenue = Enrollment::sum('total_amount');

                $collectedRevenue = Enrollment::sum('amount_paid');

                $remainingRevenue = Enrollment::sum('balance');

                $paymentsToday = Payment::whereDate(
                    'payment_date',
                    $today
                )->sum('amount');

                $paymentsMonth = Payment::whereYear(
                    'payment_date',
                    $currentYear
                )
                    ->whereMonth(
                        'payment_date',
                        $currentMonth
                    )
                    ->sum('amount');

                $expensesMonth = Expense::whereYear(
                    'expense_date',
                    $currentYear
                )
                    ->whereMonth(
                        'expense_date',
                        $currentMonth
                    )
                    ->sum('amount');

                /*
                |--------------------------------------------------------------------------
                | Situation de caisse
                |--------------------------------------------------------------------------
                */

                $cashIn = CashTransaction::onlyIncome()
                    ->sum('amount');

                $cashOut = CashTransaction::onlyExpenses()
                    ->sum('amount');

                $cashBalance = $cashIn - $cashOut;

                /*
                |--------------------------------------------------------------------------
                | Statistiques des inscriptions
                |--------------------------------------------------------------------------
                */

                $pendingEnrollments = Enrollment::where(
                    'status',
                    'pending'
                )->count();

                $partialEnrollments = Enrollment::where(
                    'status',
                    'partial'
                )->count();

                $paidEnrollments = Enrollment::where(
                    'status',
                    'paid'
                )->count();

                $studentsWithBalance = Enrollment::where(
                    'balance',
                    '>',
                    0
                )->count();

                /*
                |--------------------------------------------------------------------------
                | Taux de paiement
                |--------------------------------------------------------------------------
                */

                $paymentRate = $expectedRevenue > 0
                    ? round(
                        ($collectedRevenue / $expectedRevenue) * 100,
                        2
                    )
                    : 0;

                                    /*
                |--------------------------------------------------------------------------
                | Dernières inscriptions
                |--------------------------------------------------------------------------
                */

                $latestEnrollments = Enrollment::query()
                    ->with([
                        'student',
                        'training',
                    ])
                    ->latest('id')
                    ->limit(5)
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Derniers paiements
                |--------------------------------------------------------------------------
                */

                $latestPayments = Payment::query()
                    ->with([
                        'enrollment.student',
                        'enrollment.training',
                        'paymentMethod',
                        'receiver',
                    ])
                    ->latest('payment_date')
                    ->latest('id')
                    ->limit(5)
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Dernières dépenses
                |--------------------------------------------------------------------------
                */

                $latestExpenses = Expense::query()
                    ->with([
                        'paymentMethod',
                        'recorder',
                    ])
                    ->latest('expense_date')
                    ->latest('id')
                    ->limit(5)
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Dernières opérations de caisse
                |--------------------------------------------------------------------------
                */

                $latestTransactions = CashTransaction::query()
                    ->with([
                        'payment.enrollment.student',
                        'payment.enrollment.training',
                        'expense',
                        'paymentMethod',
                        'recorder',
                    ])
                    ->latest('transaction_date')
                    ->latest('id')
                    ->limit(5)
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Retour des données
                |--------------------------------------------------------------------------
                */

                return [

                    'statistics' => [

                        'students' => $students,

                        'trainings' => $trainings,

                        'enrollments' => $enrollments,

                        'pending_enrollments' => $pendingEnrollments,

                        'partial_enrollments' => $partialEnrollments,

                        'paid_enrollments' => $paidEnrollments,

                        'students_with_balance' => $studentsWithBalance,

                    ],

                                        'finance' => [

                        'expected_revenue' => $expectedRevenue,

                        'collected_revenue' => $collectedRevenue,

                        'remaining_revenue' => $remainingRevenue,

                        'payments_today' => $paymentsToday,

                        'payments_month' => $paymentsMonth,

                        'expenses_month' => $expensesMonth,

                        'cash_in' => $cashIn,

                        'cash_out' => $cashOut,

                        'cash_balance' => $cashBalance,

                        'payment_rate' => $paymentRate,

                    ],

                    'latest' => [

                        'enrollments' => $latestEnrollments,

                        'payments' => $latestPayments,

                        'expenses' => $latestExpenses,

                        'transactions' => $latestTransactions,

                    ],

                ];
            }
        );
    }

    /**
     * Vide le cache du tableau de bord.
     */
    public static function clearCache(): void
    {
        Cache::forget('dashboard.index');
    }
}