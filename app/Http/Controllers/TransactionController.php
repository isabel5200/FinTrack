<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\ViewTransactionResource;

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

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaction created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating transaction: ' . $e->getMessage());

            session()->flash('error', 'An error occurred while creating the transaction');
        }
    }

    public function show(string $id)
    {
        try {
            $transaction = Transaction::with('category')
                ->where('id', $id)
                ->firstOrFail();

            $this->authorize('view', $transaction);
            $transaction = ViewTransactionResource::make($transaction);

            return response()->json([
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            $transaction = Transaction::select(
                'transactions.id',
                'transactions.amount',
                'transactions.type',
                'categories.name as category_name',
                'transactions.description',
                'transactions.attachment',
                'transactions.date',
            )
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->where('transactions.id', $id)
                ->where('transactions.user_id', Auth::user()->id)
                ->firstOrFail();
            $this->authorize('show', $transaction);

            return response()->json([
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the transaction for editing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            $this->authorize('delete', $transaction);

            $transaction->delete();

            return response()->json([
                'message' => 'Transaction deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function viewFile(string $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            $this->authorize('view', $transaction);

            if ($transaction->attachment) {
                return response()->file(storage_path('app/private/' . $transaction->attachment));
            } else {
                return response()->json([
                    'message' => 'No attachment found for this transaction'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile(string $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            $this->authorize('view', $transaction);

            if ($transaction->attachment) {
                return response()->download(storage_path('app/private/' . $transaction->attachment));
            } else {
                return response()->json([
                    'message' => 'No attachment found for this transaction'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while downloading the file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
