<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\EditCategoryResource;
use App\Http\Resources\ViewCategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        try {
            $categories = Category::where('user_id', Auth::user()->id)->get();
            $categories = CategoryResource::collection($categories);


            return inertia('Categories/Index', [
                'categories' => $categories,
                // 'flash' => [
                //     'success' => 'Categorías cargadas correctamente.',
                // ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            Category::create($data);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            // Log::error('Error creating category: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while creating the category');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('view', $category);
            $category = ViewCategoryResource::make($category);

            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();
            $this->authorize('view', $category);
            $category = EditCategoryResource::make($category);

            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $this->authorize('update', $category);
            $data = $request->validated();
            $category->update($data);

            return response()->json([
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::where('id', $id)->firstOrFail();

            $this->authorize('delete', $category);

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error ocurred while deleting the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get categories for the select input in the form
    public function getCategories()
    {
        try {
            $categories = Category::where('user_id', Auth::user()->id)
                ->where('type', '=', 'expense')
                ->orderBy('name', 'ASC')->get();
            $categories = CategoryResource::collection($categories);

            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get categories by type for the select input in the form
    public function getCategoriesByType(Request $request)
    {
        try {
            $type = $request->input('type');

            $categories = Category::where('user_id', Auth::user()->id)
                ->where('type', '=', $type)
                ->orderBy('name', 'ASC')->get();
            $categories = CategoryResource::collection($categories);

            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
