<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Training;
use Illuminate\Support\Facades\DB;

class DashboardChartService
{
    /**
     * Paiements par mois.
     */
    public static function monthlyPayments(): array
    {
        $data = Payment::selectRaw("
                CAST(strftime('%m', payment_date) AS INTEGER) as month,
                SUM(amount) as total
            ")
            ->whereRaw("strftime('%Y', payment_date) = ?", [now()->format('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return self::formatMonths($data);
    }

    /**
     * Dépenses par mois.
     */
    public static function monthlyExpenses(): array
    {
        $data = Expense::selectRaw("
                CAST(strftime('%m', expense_date) AS INTEGER) as month,
                SUM(amount) as total
            ")
            ->whereRaw("strftime('%Y', expense_date) = ?", [now()->format('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return self::formatMonths($data);
    }

    /**
     * Inscriptions par mois.
     */
    public static function monthlyEnrollments(): array
    {
        $data = Enrollment::selectRaw("
                CAST(strftime('%m', created_at) AS INTEGER) as month,
                COUNT(*) as total
            ")
            ->whereRaw("strftime('%Y', created_at) = ?", [now()->format('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return self::formatMonths($data);
    }

    /**
     * Répartition des moyens de paiement.
     */
    public static function paymentMethods()
    {
        return PaymentMethod::select(
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
            ->get();
    }

    /**
     * Top 5 formations.
     */
    public static function topTrainings()
    {
        return Training::select(
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

    /**
     * Formater les 12 mois.
     */
    private static function formatMonths($collection): array
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {

            $months[] = [

                'month' => $i,

                'total' => (float) ($collection[$i] ?? 0),

            ];
        }

        return $months;
    }
}