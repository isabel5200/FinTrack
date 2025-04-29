<?php

use App\Http\Controllers\BudgetController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Categories
    Route::resource('/categories', CategoryController::class);
    Route::get('/get-categories', [CategoryController::class, 'getCategories'])->name('categories.getCategories');
    Route::post('/get-categories-by-type', [CategoryController::class, 'getCategoriesByType'])->name('categories.getCategoriesByType');

    // Transactions
    Route::resource('/transactions', TransactionController::class);

    // Budgets
    Route::resource('/budgets', BudgetController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
