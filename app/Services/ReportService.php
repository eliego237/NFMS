<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;

class ReportService
{
    /**
     * Rapport des paiements.
     */
    public static function payments(array $filters = [])
    {
        $query = Payment::with([
            'enrollment.student',
            'enrollment.training',
            'paymentMethod',
            'receiver',
        ]);

        if (!empty($filters['start_date'])) {
            $query->whereDate('payment_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('payment_date', '<=', $filters['end_date']);
        }

        return [
            'payments' => $query->latest()->get(),
            'total_amount' => (clone $query)->sum('amount'),
            'total_payments' => (clone $query)->count(),
        ];
    }

    /**
     * Rapport des dépenses.
     */
    public static function expenses(array $filters = [])
    {
        $query = Expense::with([
            'paymentMethod',
            'recorder',
        ]);

        if (!empty($filters['start_date'])) {
            $query->whereDate('expense_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('expense_date', '<=', $filters['end_date']);
        }

        return [
            'expenses' => $query->latest()->get(),
            'total_amount' => (clone $query)->sum('amount'),
            'total_expenses' => (clone $query)->count(),
        ];
    }

    /**
     * Journal de caisse.
     */
    public static function cashBook(array $filters = [])
    {
        $query = CashTransaction::with([
            'payment',
            'expense',
            'paymentMethod',
            'recorder',
        ]);

        if (!empty($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        $income = (clone $query)
            ->where('type', 'Entrée')
            ->sum('amount');

        $expense = (clone $query)
            ->where('type', 'Sortie')
            ->sum('amount');

        return [

            'transactions' => $query
                ->latest()
                ->get(),

            'income' => $income,

            'expense' => $expense,

            'balance' => $income - $expense,

        ];
    }

    /**
     * Rapport des étudiants.
     */
    public static function students(array $filters = [])
    {
        $query = Student::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return [

            'students' => $query
                ->latest()
                ->get(),

            'total_students' => (clone $query)->count(),

        ];
    }

    /**
     * Rapport des inscriptions.
     */
    public static function enrollments(array $filters = [])
    {
        $query = Enrollment::with([
            'student',
            'training',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['training_id'])) {
            $query->where('training_id', $filters['training_id']);
        }

        return [

            'enrollments' => $query
                ->latest()
                ->get(),

            'total_enrollments' => (clone $query)->count(),

        ];
    }

    /**
     * Résumé financier.
     */
    public static function financialSummary(array $filters = [])
    {
        $payments = Payment::query();

        $expenses = Expense::query();

        if (!empty($filters['start_date'])) {

            $payments->whereDate(
                'payment_date',
                '>=',
                $filters['start_date']
            );

            $expenses->whereDate(
                'expense_date',
                '>=',
                $filters['start_date']
            );
        }

        if (!empty($filters['end_date'])) {

            $payments->whereDate(
                'payment_date',
                '<=',
                $filters['end_date']
            );

            $expenses->whereDate(
                'expense_date',
                '<=',
                $filters['end_date']
            );
        }

        $totalIncome = $payments->sum('amount');

        $totalExpense = $expenses->sum('amount');

        return [

            'total_income' => $totalIncome,

            'total_expense' => $totalExpense,

            'balance' => $totalIncome - $totalExpense,

        ];
    }
}