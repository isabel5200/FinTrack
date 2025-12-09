<?php

use App\Http\Controllers\BudgetController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
    Route::get('/dashboard/years', [DashboardController::class, 'years'])->name('dashboard.years');
    Route::get('/dashboard/months', [DashboardController::class, 'months'])->name('dashboard.months');

    // Categories
    Route::resource('/categories', CategoryController::class);
    Route::get('/get-categories', [CategoryController::class, 'getCategories'])->name('categories.getCategories');
    Route::post('/get-categories-by-type', [CategoryController::class, 'getCategoriesByType'])->name('categories.getCategoriesByType');

    // Transactions
    Route::resource('/transactions', TransactionController::class);
    Route::post('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');

    // Attachments
    Route::get('/transactions/{transaction}/view', [TransactionController::class, 'viewFile'])->name('transactions.viewFile');
    Route::get('/transactions/{transaction}/download', [TransactionController::class, 'downloadFile'])->name('transactions.downloadFile');

    // Budgets
    Route::resource('/budgets', BudgetController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
