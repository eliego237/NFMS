<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Training;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardChartService
{
    /**
     * Durée du cache (5 minutes).
     */
    private const CACHE_TIME = 300;

    /**
     * Paiements par mois.
     */
    public static function monthlyPayments(): array
    {
        return Cache::remember(
            'dashboard.chart.payments',
            self::CACHE_TIME,
            function () {

                $data = Payment::query()
                    ->selectRaw('MONTH(payment_date) as month')
                    ->selectRaw('SUM(amount) as total')
                    ->whereYear('payment_date', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month');

                return self::formatMonths($data);

            }
        );
    }

    /**
     * Dépenses par mois.
     */
    public static function monthlyExpenses(): array
    {
        return Cache::remember(
            'dashboard.chart.expenses',
            self::CACHE_TIME,
            function () {

                $data = Expense::query()
                    ->selectRaw('MONTH(expense_date) as month')
                    ->selectRaw('SUM(amount) as total')
                    ->whereYear('expense_date', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month');

                return self::formatMonths($data);

            }
        );
    }

    /**
     * Inscriptions par mois.
     */
    public static function monthlyEnrollments(): array
    {
        return Cache::remember(
            'dashboard.chart.enrollments',
            self::CACHE_TIME,
            function () {

                $data = Enrollment::query()
                    ->selectRaw('MONTH(created_at) as month')
                    ->selectRaw('COUNT(*) as total')
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month');

                return self::formatMonths($data);

            }
        );
    }

    /**
     * Répartition des moyens de paiement.
     */
    public static function paymentMethods()
    {
        return Cache::remember(
            'dashboard.chart.payment_methods',
            self::CACHE_TIME,
            function () {

                return PaymentMethod::query()
                    ->select(
                        'payment_methods.name',
                        DB::raw('COUNT(payments.id) as total')
                    )
                    ->leftJoin(
                        'payments',
                        'payments.payment_method_id',
                        '=',
                        'payment_methods.id'
                    )
                    ->groupBy(
                        'payment_methods.id',
                        'payment_methods.name'
                    )
                    ->orderByDesc('total')
                    ->get();

            }
        );
    }

    /**
     * Top 5 formations.
     */
    public static function topTrainings()
    {
        return Cache::remember(
            'dashboard.chart.trainings',
            self::CACHE_TIME,
            function () {

                return Training::query()
                    ->select(
                        'trainings.title',
                        DB::raw('COUNT(enrollments.id) as total')
                    )
                    ->leftJoin(
                        'enrollments',
                        'enrollments.training_id',
                        '=',
                        'trainings.id'
                    )
                    ->groupBy(
                        'trainings.id',
                        'trainings.title'
                    )
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();

            }
        );
    }

    /**
     * Formater les 12 mois.
     */
    private static function formatMonths($collection): array
    {
        $months = [];

        for ($month = 1; $month <= 12; $month++) {

            $months[] = [

                'month' => $month,

                'total' => (float) ($collection[$month] ?? 0),

            ];

        }

        return $months;
    }
}