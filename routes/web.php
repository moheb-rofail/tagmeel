<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockReturnController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('/');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('customers', CustomerController::class);
Route::resource('items', ItemController::class);
Route::resource('purchases', PurchaseController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('expenses', ExpenseController::class);
Route::resource('sales', SaleController::class);
Route::resource('returns', StockReturnController::class);
Route::resource('stock_movements', StockMovementController::class);
Route::get('stock-movements/item/{item}', [StockMovementController::class, 'itemHistory'])->name('stock_movements.item_history');

// مسار الطباعة الحرارية (استخدام اسم sale)
Route::get('/sales/{sale}/print-thermal', [SaleController::class, 'printThermalSale'])->name('sales.print.thermal');
require __DIR__.'/auth.php';
