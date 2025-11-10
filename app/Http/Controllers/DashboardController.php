<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::user()->id;
            $totals = $this->getTotals($userId);
            $expensesByCategory = $this->getExpensesByCategory($userId);
            $incomeExpenseTrend = $this->getIncomeExpenseTrend($userId);
            $monthlyComparison = $this->getMonthlyComparison($userId);
            $budgetProgress = $this->getBudgetProgress($userId);

            return inertia('Dashboard', [
                'totals' => $totals,
                'expensesByCategory' => $expensesByCategory,
                'incomeExpenseTrend' => $incomeExpenseTrend,
                'monthlyComparison' => $monthlyComparison,
                'budgetProgress' => $budgetProgress,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error ocurred while fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getTotals($userId)
    {
        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');
        $expense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
        ];
    }

    private function getExpensesByCategory($userId)
    {
        $expenses = DB::table('transactions AS t')
            ->selectRaw("
            c.name AS category_name,
            ROUND(
                SUM(t.amount) * 100.0 / (
                    SELECT SUM(t2.amount)
                    FROM transactions t2
                    INNER JOIN categories c2 ON c2.id = t2.category_id
                    WHERE t2.type = 'expense'
                    AND c2.user_id = ?
                ),
                2
            ) AS percentage
        ", [$userId])
            ->join('categories AS c', 'c.id', '=', 't.category_id')
            ->where('t.type', 'expense')
            ->where('t.user_id', $userId)
            ->groupBy('c.name')
            ->orderByDesc('percentage')
            ->get();

        return $expenses;
    }

    private function getIncomeExpenseTrend($userId)
    {
        $trend = DB::table('transactions AS t')
            ->selectRaw("
            YEAR(t.date) AS year,
            MONTH(t.date) AS month,
            ROUND(SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END), 2)  AS total_income,
            ROUND(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 2) AS total_expense
            ")
            ->where('t.user_id', $userId)
            ->groupBy(DB::raw('YEAR(t.date), MONTH(t.date), MONTH(t.date)'))
            ->orderByRaw('YEAR(t.date), MONTH(t.date)')
            ->get();

        return $trend;
    }

    private function getMonthlyComparison($userId)
    {
        $comparison = DB::table('transactions AS t')
            ->selectRaw("
            YEAR(t.date) AS year,
            MONTH(t.date) AS month,
            ROUND(SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END), 2)  AS total_income,
            ROUND(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 2) AS total_expense
            ")
            ->where('t.user_id', $userId)
            ->groupBy(DB::raw('YEAR(t.date), MONTH(t.date), MONTH(t.date)'))
            ->orderByRaw('YEAR(t.date), MONTH(t.date)')
            ->get();

        return $comparison;
    }

    private function getBudgetProgress($userId)
    {
        $progress = DB::table('budgets AS b')
            ->selectRaw("
        c.name AS category_name,
        b.max_amount AS budget_amount,
        ROUND(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 2) AS total_expense,
        ROUND(
                (SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) / NULLIF(b.max_amount, 0)) * 100,
                2
        ) AS percent_used
        ")
            ->join('categories AS c', 'c.id', '=', 'b.category_id')
            ->leftJoin('transactions AS t', function ($join) {
                $join->on('t.category_id', '=', 'c.id')
                    ->where('t.type', '=', 'expense');
            })
            ->where('b.user_id', $userId)
            ->groupBy('c.id', 'c.name', 'b.max_amount')
            ->orderByDesc('percent_used')
            ->get();

        return $progress;
    }
}
