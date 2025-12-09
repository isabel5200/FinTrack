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
            return inertia('Dashboard');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error ocurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        $userId = Auth::id();
        $filters = [
            'year' => $request->integer('year'),
            'month' => $request->month !== 'all' ? (int)$request->month : null,
        ];
        try {
            $totals = $this->getTotals($userId, $filters);
            $expensesByCategory = $this->getExpensesByCategory($userId, $filters);
            $incomeExpenseTrend = $this->getIncomeExpenseTrend($userId, $filters);
            $monthlyComparison = $this->getMonthlyComparison($userId, $filters);
            $budgetProgress = $this->getBudgetProgress($userId, $filters);

            return ([
                'totals' => $totals,
                'expensesByCategory' => $expensesByCategory,
                'incomeExpenseTrend' => $incomeExpenseTrend,
                'monthlyComparison' => $monthlyComparison,
                'budgetProgress' => $budgetProgress,
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error ocurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function years()
    {
        $userId = Auth::id();

        $years = Transaction::where('user_id', $userId)
            ->selectRaw('YEAR(date) AS year')
            ->distinct()
            ->orderBy('year', 'DESC')
            ->get()
            ->pluck('year')
            ->map(fn($y) => ['id' => $y, 'name' => (string)$y]);

        return [
            'years' => $years,
            'defaultYear' => $years->first()['id'] ?? NOW()->year,
        ];
    }

    public function months(Request $request)
    {
        $userId = Auth::id();
        $year = $request->year;
        $months = Transaction::where('user_id', $userId)
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) AS month')
            ->distinct()
            ->orderBy('month', 'DESC')
            ->get()
            ->pluck('month')
            ->map(fn($m) => ['id' => $m, 'name' => date('F', mktime(0, 0, 0, $m, 1))]);

        return [
            'months' => $months->values()->toArray(),
            'defaultMonth' => $months->first()['id'] ?? NOW()->month,
        ];
    }

    private function getTotals($userId, $filters = [])
    {
        $query = Transaction::selectRaw("
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
    ")->where('user_id', $userId);

        if (!empty($filters['year'])) {
            $query->whereYear('date', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('date', $filters['month']);
        }

        $totals = $query->first();

        return [
            'income' => (float) $totals->income,
            'expense' => (float) $totals->expense,
            'balance' => (float) ($totals->income - $totals->expense),
        ];
    }

    private function getExpensesByCategory($userId, $filters = [])
    {
        $query = DB::table('transactions AS t')
            ->join('categories AS c', 'c.id', '=', 't.category_id')
            ->where('t.user_id', $userId)
            ->where('t.type', 'expense');

        if (!empty($filters['year'])) {
            $query->whereYear('t.date', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('t.date', $filters['month']);
        }

        $total = $query->sum('t.amount');

        if ($total == 0) {
            return [];
        }

        $categories = $query
            ->select(
                'c.name AS category_name',
                DB::raw('SUM(t.amount) AS total')
            )
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total')
            ->get();

        return $categories->map(function ($cat) use ($total) {
            $cat->percentage = round(($cat->total / $total) * 100, 2);

            return $cat;
        });
    }

    private function getIncomeExpenseTrend($userId, $filters = [])
    {
        $query = DB::table('transactions AS t')
            ->selectRaw("
            YEAR(t.date) AS year,
            MONTH(t.date) AS month,
            ROUND(SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END), 2) AS total_income,
            ROUND(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 2) AS total_expense
        ")
            ->where('t.user_id', $userId);

        // Aplicar filtros
        if (!empty($filters['year'])) {
            $query->whereYear('t.date', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('t.date', $filters['month']);
        }

        return $query
            ->groupBy(DB::raw('YEAR(t.date), MONTH(t.date)'))
            ->orderByRaw('YEAR(t.date), MONTH(t.date)')
            ->get();
    }

    private function getMonthlyComparison($userId, $filters = [])
    {
        $query = DB::table('transactions AS t')
            ->selectRaw("
            YEAR(t.date) AS year,
            MONTH(t.date) AS month,
            ROUND(SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END), 2) AS total_income,
            ROUND(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 2) AS total_expense
        ")
            ->where('t.user_id', $userId);

        if (!empty($filters['year'])) {
            $query->whereYear('t.date', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('t.date', $filters['month']);
        }

        return $query
            ->groupBy(DB::raw('YEAR(t.date), MONTH(t.date)'))
            ->orderByRaw('YEAR(t.date), MONTH(t.date)')
            ->get();
    }

    private function getBudgetProgress($userId, $filters = [])
    {
        $query = DB::table('budgets AS b')
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
            ->leftJoin('transactions AS t', function ($join) use ($filters) {
                $join->on('t.category_id', '=', 'c.id')
                    ->where('t.type', 'expense');

                if (!empty($filters['year'])) {
                    $join->whereYear('t.date', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $join->whereMonth('t.date', $filters['month']);
                }
            })
            ->where('b.user_id', $userId);

        return $query
            ->groupBy('c.id', 'c.name', 'b.max_amount')
            ->orderByDesc('percent_used')
            ->get();
    }
}
