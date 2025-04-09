<?php

namespace App\Http\Controllers;

use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Budget::class);

        try {
            $budgets = Budget::join('categories', 'budgets.category_id', '=', 'categories.id')
                ->where('budgets.user_id', Auth::user()->id)
                ->select(
                    'budgets.id',
                    'categories.name as category_name',
                    'budgets.max_amount',
                    'budgets.duration'
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
