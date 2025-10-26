<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use App\Http\Requests\BudgetRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BudgetResource;
use App\Http\Requests\EditBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\EditBudgetResource;
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

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Budget created successfully');
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
            $budget = Budget::with('category')
                ->where('budgets.id', $id)
                ->where('budgets.user_id', Auth::user()->id)
                ->firstOrFail();

            $this->authorize('view', $budget);
            $budget = EditBudgetResource::make($budget);

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

    public function update(UpdateBudgetRequest $request, string $id)
    {
        try {
            $budget = Budget::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $this->authorize('update', $budget);
            $data = $request->validated();
            $budget->update($data);

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Budget created successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the budget');
        }

        return redirect()->back();
    }

    public function destroy(string $id)
    {
        try {
            $budget = Budget::where('id', $id)->firstOrFail();

            $this->authorize('delete', $budget);

            $budget->delete();

            return response()->json([
                'message' => 'Budget deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
