<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;

// Main route
Route::get('/', [MainController::class, 'index']);

// Login routes
Route::get('/login', [UserController::class, 'login']);
Route::post('/login/auth', [UserController::class, 'authorizeUser']);

// Register routes
Route::get('/register', [UserController::class, 'registerUser']);
Route::post('/register', [UserController::class, 'storeUser']);

Route::get('/reset', [UserController::class, 'passwordReset']);
Route::post('/reset', [UserController::class, 'sendPasswordEmail']);
Route::get('/reset/password', [UserController::class, 'resetPassword']);
Route::post('/reset/password', [UserController::class, 'passwordResetUpdate']);
Route::get('/logout', [UserController::class, 'logout']);

// Budget routes
Route::get('/budget', [BudgetController::class, 'budget']);
Route::get('/add-budget', [BudgetController::class, 'editBudget']);
Route::post('/add-budget', [BudgetController::class, 'storeBudget']);

// Add expense routes
Route::get('/add', [MainController::class, 'addExpense']);
Route::post('/add', [MainController::class, 'storeExpense']);
Route::get('/import', [ImportController::class, 'importExpenses']);
Route::get('/import/choose-bank', [ImportController::class, 'showBanks']);
Route::post('/import/choose-bank', [ImportController::class, 'afterBankSelection']);
Route::get('/import/account-selection', [ImportController::class, 'accountSelection']);
Route::post('/import/transaction-selection', [ImportController::class, 'transactionSelection']);
Route::post('/import/store-transactions', [ImportController::class, 'storeTransactions']);

// Report routes
Route::get('/reports', [MainController::class, 'reports']);
Route::get('category/{id}', [MainController::class, 'category']);

// History routes
Route::get('/history', [MainController::class, 'history']);
Route::post('/history', [MainController::class, 'history']);
Route::post('/delete-history', [MainController::class, 'deleteHistory']);

// Export routes
Route::get('/export', [ExportController::class, 'showExport']);
Route::post('/export', [ExportController::class, 'exportData']);

// Edit record routes
Route::get('/edit-record/{id}', [MainController::class, 'editRecord']);
Route::post('/edit-record', [MainController::class, 'updateRecord']);

// Profile routes
Route::get('/profile', [UserController::class, 'editProfile']);
Route::post('/profile', [UserController::class, 'updateProfile']);
Route::get('/profile/change-password', [UserController::class, 'editPassword']);
Route::post('/profile/change-password', [UserController::class, 'updatePassword']);
Route::post('/profile/change-currency', [UserController::class, 'updateCurrency']);

// Delete routes
Route::post('/delete-record/{id}', [MainController::class, 'destroyRecord']);
Route::get('/delete', [UserController::class, 'showDelete']);
Route::post('/delete', [UserController::class, 'destroyAccout']);