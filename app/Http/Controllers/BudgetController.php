<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use App\Http\Requests\BudgetRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BudgetResource;
use App\Http\Resources\ViewBudgetResource;

class BudgetController extends Controller
{
    public function index()
    {
        try {
            $this->authorize('viewAny', Budget::class);
            $budgets = Budget::join('categories', 'budgets.category_id', '=', 'categories.id')
                ->where('budgets.user_id', Auth::user()->id)
                ->select(
                    'budgets.id',
                    'categories.name as category_name',
                    'budgets.max_amount',
                    'budgets.frequency'
                )
                ->get();

            $budgets = BudgetResource::collection($budgets);

            return inertia('Budgets/Index', [
                'budgets' => $budgets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching budgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(BudgetRequest $request)
    {
        try {
            $data = $request->validated();
            $data['category_id'] = $data['category'];
            $data['user_id'] = Auth::user()->id;

            Budget::create($data);

            session()->flash('success', 'Budget created successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while creating the budget');
        }

        return redirect()->back();
    }

    public function show(string $id)
    {
        try {
            $budget = Budget::with('category')
            ->where('id', $id)
            ->firstOrFail();

            $this->authorize('view', $budget);
            $budget = ViewBudgetResource::make($budget);

            return response()->json([
                'budget' => $budget,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            $budget = Budget::join('categories', 'budgets.category_id', '=', 'categories.id')
                ->where('budgets.id', $id)
                ->where('budgets.user_id', Auth::user()->id)
                ->select(
                    'categories.name as category_name',
                    'budgets.max_amount',
                    'budgets.frequency',
                    'budgets.created_at',
                    'budgets.updated_at',
                )
                ->firstOrFail();
            $this->authorize('view', $budget);

            return response()->json([
                'budget' => $budget,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the budget',
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
        //
    }
}
