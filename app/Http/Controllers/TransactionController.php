<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Transaction::class);

        try {
            $transactions = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.user_id', Auth::user()->id)
                ->select(
                    'transactions.id',
                    'transactions.amount',
                    'transactions.type',
                    'categories.name as category_name',
                    'transactions.description',
                    'transactions.date'
                )
                ->get();

            $transactions = TransactionResource::collection($transactions);

            return inertia('Transactions/Index', [
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
