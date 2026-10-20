<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Durée du cache en secondes.
     */
    private const CACHE_TTL = 300;

    /**
     * Nombre d'éléments par page.
     */
    private static function perPage(array $filters): int
    {
        return max(1, min((int) ($filters['per_page'] ?? 20), 100));
    }

    /**
     * Appliquer un filtre de période.
     */
    private static function applyDateFilter(
        $query,
        string $column,
        array $filters
    ): void {
        $startDate = $filters['start_date']
            ?? $filters['date_from']
            ?? null;

        $endDate = $filters['end_date']
            ?? $filters['date_to']
            ?? null;

        $query->when(
            !empty($startDate),
            fn ($q) => $q->whereDate($column, '>=', $startDate)
        );

        $query->when(
            !empty($endDate),
            fn ($q) => $q->whereDate($column, '<=', $endDate)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rapport des paiements
    |--------------------------------------------------------------------------
    */

    public static function payments(array $filters = []): array
    {
        $query = Payment::with([
            'enrollment.student',
            'enrollment.trainings',
            'paymentMethod',
            'receiver',
        ]);

        // Filtre par moyen de paiement.
        $query->when(
            !empty($filters['payment_method_id']),
            fn ($q) => $q->where(
                'payment_method_id',
                $filters['payment_method_id']
            )
        );

        // Filtre par formation via la relation many-to-many.
        $query->when(
            !empty($filters['training_id']),
            function ($q) use ($filters) {
                $q->whereHas(
                    'enrollment.trainings',
                    fn ($trainingQuery) => $trainingQuery->where(
                        'trainings.id',
                        $filters['training_id']
                    )
                );
            }
        );

        // Filtre de dates.
        self::applyDateFilter($query, 'payment_date', $filters);

        return [
            'payments' => (clone $query)
                ->latest('payment_date')
                ->paginate(self::perPage($filters)),

            'total_payments' => (clone $query)->count(),

            'total_amount' => (clone $query)->sum('amount'),

            'today_amount' => (clone $query)
                ->whereDate('payment_date', today())
                ->sum('amount'),

            'average_amount' => round(
                (clone $query)->avg('amount') ?? 0,
                2
            ),

            'payment_methods' => PaymentMethod::withCount('payments')
                ->withSum('payments', 'amount')
                ->get(),
        ];
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
                        ->paginate(self::perPage($filters)),

                    'total_amount' => (clone $query)->sum('amount'),

                    'total_expenses' => (clone $query)->count(),

                    'average_expense' => round(
                        (clone $query)->avg('amount') ?? 0,
                        2
                    ),

                    'largest_expense' => (clone $query)->max('amount'),

                    'smallest_expense' => (clone $query)->min('amount'),
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
                    'payment.enrollment.trainings',
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
                    ->paginate(self::perPage($filters));

                $income = (clone $query)
                    ->where('type', 'Entrée')
                    ->sum('amount');

                $expense = (clone $query)
                    ->where('type', 'Sortie')
                    ->sum('amount');

                return [
                    'transactions' => $transactions,

                    'total_transactions' => (clone $query)->count(),

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
                    'latestEnrollment.trainings',
                ]);

                $query->when(
                    array_key_exists('status', $filters)
                        && $filters['status'] !== '',
                    fn ($q) => $q->where(
                        'status',
                        $filters['status']
                    )
                );

                $students = (clone $query)
                    ->latest()
                    ->paginate(self::perPage($filters));

                $totalStudents = (clone $query)->count();

                $activeStudents = (clone $query)
                    ->where('status', true)
                    ->count();

                $inactiveStudents = (clone $query)
                    ->where('status', false)
                    ->count();

                return [
                    // Conserver le résultat paginé sous forme de tableau
                    // pour éviter de mettre un objet paginator en cache.
                    'students' => $students->toArray(),

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
        $query = Enrollment::with([
            'student',
            'trainings',
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

        // Une inscription peut contenir plusieurs formations.
        // Le filtre doit donc passer par la relation trainings().
        $query->when(
            !empty($filters['training_id']),
            function ($q) use ($filters) {
                $q->whereHas(
                    'trainings',
                    fn ($trainingQuery) => $trainingQuery->where(
                        'trainings.id',
                        $filters['training_id']
                    )
                );
            }
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
                ->paginate(self::perPage($filters)),

            'total_enrollments' => (clone $query)->count(),

            'pending' => (clone $query)
                ->where('status', 'pending')
                ->count(),

            'partial' => (clone $query)
                ->where('status', 'partial')
                ->count(),

            'paid' => (clone $query)
                ->where('status', 'paid')
                ->count(),

            'total_amount' => (clone $query)->sum('total_amount'),

            'total_paid' => (clone $query)->sum('amount_paid'),

            'total_balance' => (clone $query)->sum('balance'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Résumé financier
    |--------------------------------------------------------------------------
    */

    public static function financialSummary(array $filters = []): array
    {
        $cacheKey = 'report_financial_summary_' . md5(
            json_encode($filters)
        );

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

                $totalIncome = (clone $payments)->sum('amount');
                $totalExpense = (clone $expenses)->sum('amount');

                $paymentCount = (clone $payments)->count();
                $expenseCount = (clone $expenses)->count();

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
