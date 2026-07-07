<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Training;

class DashboardService
{
    /**
     * Données du tableau de bord.
     */
    public static function index(): array
    {
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

        $paymentsMonth = Payment::whereMonth(
            'payment_date',
            $currentMonth
        )
        ->whereYear(
            'payment_date',
            $currentYear
        )
        ->sum('amount');

        $expensesMonth = Expense::whereMonth(
            'expense_date',
            $currentMonth
        )
        ->whereYear(
            'expense_date',
            $currentYear
        )
        ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Solde de caisse
        |--------------------------------------------------------------------------
        */

        $cashIn = CashTransaction::where(
            'type',
            'Entrée'
        )->sum('amount');

        $cashOut = CashTransaction::where(
            'type',
            'Sortie'
        )->sum('amount');

        $cashBalance = $cashIn - $cashOut;

        /*
        |--------------------------------------------------------------------------
        | Inscriptions
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
        | Activité récente
        |--------------------------------------------------------------------------
        */

        $latestEnrollments = Enrollment::with([
            'student',
            'training',
        ])
        ->latest()
        ->take(5)
        ->get();

        $latestPayments = Payment::with([
            'enrollment.student',
            'paymentMethod',
        ])
        ->latest()
        ->take(5)
        ->get();

        $latestExpenses = Expense::with([
            'paymentMethod',
            'recorder',
        ])
        ->latest()
        ->take(5)
        ->get();

        /*
        |--------------------------------------------------------------------------
        | Retour
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

                'cash_balance' => $cashBalance,

                'payment_rate' => $paymentRate,

            ],

            'latest' => [

                'enrollments' => $latestEnrollments,

                'payments' => $latestPayments,

                'expenses' => $latestExpenses,

            ],

        ];
    }
}