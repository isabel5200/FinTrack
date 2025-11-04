<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::user()->id;
            $totals = $this->getTotals($userId);

            return inertia('Dashboard', [
                'totals' => $totals,
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
}
