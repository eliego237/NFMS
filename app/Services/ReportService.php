<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Durée du cache (secondes).
     */
    private const CACHE_TTL = 300;

    /**
     * Nombre d'éléments par page.
     */
    private static function perPage(array $filters): int
    {
        return max(
            1,
            min((int) ($filters['per_page'] ?? 20), 100)
        );
    }

    /**
     * Appliquer un filtre de période.
     */
    private static function applyDateFilter(
        $query,
        string $column,
        array $filters
    ): void {

        $query->when(
            !empty($filters['start_date']),
            fn ($q) => $q->whereDate(
                $column,
                '>=',
                $filters['start_date']
            )
        );

        $query->when(
            !empty($filters['end_date']),
            fn ($q) => $q->whereDate(
                $column,
                '<=',
                $filters['end_date']
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rapport des paiements
    |--------------------------------------------------------------------------
    */

    public static function payments(array $filters = []): array
    {
        $cacheKey = 'report_payments_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $query = Payment::with([
                    'enrollment.student',
                    'enrollment.training',
                    'paymentMethod',
                    'receiver',
                ]);

                self::applyDateFilter(
                    $query,
                    'payment_date',
                    $filters
                );

                $query->when(
                    !empty($filters['payment_method_id']),
                    fn ($q) => $q->where(
                        'payment_method_id',
                        $filters['payment_method_id']
                    )
                );

                $query->when(
                    !empty($filters['student_id']),
                    fn ($q) => $q->whereHas(
                        'enrollment',
                        fn ($e) => $e->where(
                            'student_id',
                            $filters['student_id']
                        )
                    )
                );

                $query->when(
                    !empty($filters['training_id']),
                    fn ($q) => $q->whereHas(
                        'enrollment',
                        fn ($e) => $e->where(
                            'training_id',
                            $filters['training_id']
                        )
                    )
                );

                return [

                    'payments' => (clone $query)
                        ->latest('payment_date')
                        ->latest('id')
                        ->paginate(
                            self::perPage($filters)
                        ),

                    'total_amount' => (clone $query)
                        ->sum('amount'),

                    'total_payments' => (clone $query)
                        ->count(),

                    'average_payment' => round(
                        (clone $query)->avg('amount') ?? 0,
                        2
                    ),

                    'largest_payment' => (clone $query)
                        ->max('amount'),

                    'smallest_payment' => (clone $query)
                        ->min('amount'),

                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rapport des dépenses
    |--------------------------------------------------------------------------
    */

    public static function expenses(array $filters = []): array
    {
        $cacheKey = 'report_expenses_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $query = Expense::with([
                    'paymentMethod',
                    'recorder',
                ]);

                self::applyDateFilter(
                    $query,
                    'expense_date',
                    $filters
                );

                $query->when(
                    !empty($filters['category']),
                    fn ($q) => $q->where(
                        'category',
                        $filters['category']
                    )
                );

                $query->when(
                    !empty($filters['payment_method_id']),
                    fn ($q) => $q->where(
                        'payment_method_id',
                        $filters['payment_method_id']
                    )
                );

                return [

                    'expenses' => (clone $query)
                        ->latest('expense_date')
                        ->latest('id')
                        ->paginate(
                            self::perPage($filters)
                        ),

                    'total_amount' => (clone $query)
                        ->sum('amount'),

                    'total_expenses' => (clone $query)
                        ->count(),

                    'average_expense' => round(
                        (clone $query)->avg('amount') ?? 0,
                        2
                    ),

                    'largest_expense' => (clone $query)
                        ->max('amount'),

                    'smallest_expense' => (clone $query)
                        ->min('amount'),

                ];
            }
        );
    }

        /*
    |--------------------------------------------------------------------------
    | Journal de caisse
    |--------------------------------------------------------------------------
    */

    public static function cashBook(array $filters = []): array
    {
        $cacheKey = 'report_cashbook_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $query = CashTransaction::with([
                    'payment.enrollment.student',
                    'payment.enrollment.training',
                    'expense',
                    'paymentMethod',
                    'recorder',
                ]);

                self::applyDateFilter(
                    $query,
                    'transaction_date',
                    $filters
                );

                $transactions = (clone $query)
                    ->latest('transaction_date')
                    ->latest('id')
                    ->paginate(
                        self::perPage($filters)
                    );

                $income = (clone $query)
    ->where('type', 'Entrée')
    ->sum('amount');

$expense = (clone $query)
    ->where('type', 'Sortie')
    ->sum('amount');
                return [

                    'transactions' => $transactions,

                    'total_transactions' => (clone $query)
                        ->count(),

                    'income' => $income,

                    'expense' => $expense,

                    'balance' => $income - $expense,

                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rapport des étudiants
    |--------------------------------------------------------------------------
    */

    public static function students(array $filters = []): array
    {
        $cacheKey = 'report_students_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $query = Student::with([
                    'latestEnrollment.training',
                ]);

                $query->when(
                    array_key_exists('status', $filters),
                    fn ($q) => $q->where(
                        'status',
                        $filters['status']
                    )
                );

                $students = (clone $query)
                    ->latest()
                    ->paginate(
                        self::perPage($filters)
                    );

                $totalStudents = (clone $query)->count();

                $activeStudents = (clone $query)
                    ->where('status', true)
                    ->count();

                $inactiveStudents = (clone $query)
                    ->where('status', false)
                    ->count();

                return [

                    'students' => $students,

                    'total_students' => $totalStudents,

                    'active_students' => $activeStudents,

                    'inactive_students' => $inactiveStudents,

                    'active_percentage' => $totalStudents > 0
                        ? round(
                            ($activeStudents / $totalStudents) * 100,
                            2
                        )
                        : 0,

                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rapport des inscriptions
    |--------------------------------------------------------------------------
    */

    public static function enrollments(array $filters = []): array
    {
        $cacheKey = 'report_enrollments_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $query = Enrollment::with([
                    'student',
                    'training',
                    'creator',
                    'payments',
                ]);

                $query->when(
                    !empty($filters['status']),
                    fn ($q) => $q->where(
                        'status',
                        $filters['status']
                    )
                );

                $query->when(
                    !empty($filters['training_id']),
                    fn ($q) => $q->where(
                        'training_id',
                        $filters['training_id']
                    )
                );

                $query->when(
                    !empty($filters['student_id']),
                    fn ($q) => $q->where(
                        'student_id',
                        $filters['student_id']
                    )
                );

                return [

                    'enrollments' => (clone $query)
                        ->latest()
                        ->paginate(
                            self::perPage($filters)
                        ),

                    'total_enrollments' => (clone $query)
                        ->count(),

                    'pending' => (clone $query)
                        ->where('status', 'pending')
                        ->count(),

                    'partial' => (clone $query)
                        ->where('status', 'partial')
                        ->count(),

                    'paid' => (clone $query)
                        ->where('status', 'paid')
                        ->count(),

                    'total_amount' => (clone $query)
                        ->sum('total_amount'),

                    'total_paid' => (clone $query)
                        ->sum('amount_paid'),

                    'total_balance' => (clone $query)
                        ->sum('balance'),

                ];
            }
        );
    }

        /*
    |--------------------------------------------------------------------------
    | Résumé financier
    |--------------------------------------------------------------------------
    */

    public static function financialSummary(array $filters = []): array
    {
        $cacheKey = 'report_financial_summary_' . md5(json_encode($filters));

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters) {

                $payments = Payment::query();

                $expenses = Expense::query();

                self::applyDateFilter(
                    $payments,
                    'payment_date',
                    $filters
                );

                self::applyDateFilter(
                    $expenses,
                    'expense_date',
                    $filters
                );

                $totalIncome = (clone $payments)
                    ->sum('amount');

                $totalExpense = (clone $expenses)
                    ->sum('amount');

                $paymentCount = (clone $payments)
                    ->count();

                $expenseCount = (clone $expenses)
                    ->count();

                return [

                    'total_income' => $totalIncome,

                    'total_expense' => $totalExpense,

                    'balance' => $totalIncome - $totalExpense,

                    'net_income' => $totalIncome - $totalExpense,

                    'payment_count' => $paymentCount,

                    'expense_count' => $expenseCount,

                    'average_payment' => $paymentCount > 0
                        ? round($totalIncome / $paymentCount, 2)
                        : 0,

                    'average_expense' => $expenseCount > 0
                        ? round($totalExpense / $expenseCount, 2)
                        : 0,

                ];
            }
        );
    }
}