<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;
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

    public function store(TransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['category_id'] = $data['category'];
            $data['date'] = Carbon::parse($data['date'])->toDateString();
            $userId = Auth::user()->id;
            $data['user_id'] = $userId;

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store("attachments/user_{$userId}");
                $data['attachment'] = $path;
            }

            Transaction::create($data);

            session()->flash('success', 'Transaction created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating transaction: ' . $e->getMessage());

            session()->flash('error', 'An error occurred while creating the transaction');
        }
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
