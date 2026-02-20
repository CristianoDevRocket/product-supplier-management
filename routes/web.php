<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Suppliers
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)
        ->except(['show']);

    // Products
    Route::resource('products', \App\Http\Controllers\ProductController::class)
        ->except(['show']);

    // Product-Supplier links
    Route::get('/product-suppliers', [\App\Http\Controllers\ProductSupplierController::class, 'index'])
        ->name('product-suppliers.index');
    Route::post('/product-suppliers', [\App\Http\Controllers\ProductSupplierController::class, 'store'])
        ->name('product-suppliers.store');
    Route::delete('/product-suppliers/{product}/{supplier}', [\App\Http\Controllers\ProductSupplierController::class, 'destroy'])
        ->name('product-suppliers.destroy');
    Route::post('/product-suppliers/bulk-link', [\App\Http\Controllers\ProductSupplierController::class, 'bulkLink'])
        ->name('product-suppliers.bulk-link');
    Route::post('/product-suppliers/bulk-unlink', [\App\Http\Controllers\ProductSupplierController::class, 'bulkUnlink'])
        ->name('product-suppliers.bulk-unlink');

    // Orders
    Route::resource('orders', \App\Http\Controllers\OrderController::class)
        ->except(['edit', 'update', 'destroy']);
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])
        ->name('orders.update-status');
    Route::post('/orders/{order}/items', [\App\Http\Controllers\OrderController::class, 'addItem'])
        ->name('orders.add-item');
    Route::delete('/orders/{order}/items/{orderItem}', [\App\Http\Controllers\OrderController::class, 'removeItem'])
        ->name('orders.remove-item');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
