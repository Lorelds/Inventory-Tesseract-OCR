<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DebtController;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Admin Receipts Routes
    Route::prefix('admin/receipts')->name('admin.receipts.')->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('index');
        Route::get('/upload', [ReceiptController::class, 'create'])->name('upload.form');
        Route::post('/upload', [ReceiptController::class, 'upload'])->name('upload');
        Route::get('/{receipt}/show', [ReceiptController::class, 'show'])->name('show');
        Route::get('/{receipt}/validate', [ReceiptController::class, 'validate'])->name('validate');
        Route::post('/{receipt}/validate', [ReceiptController::class, 'validateSubmit'])->name('validateSubmit');
        
        // Super Admin only
        Route::delete('/{receipt}', [ReceiptController::class, 'destroy'])->name('destroy')->middleware('super_admin');
    });

    // Products Routes
    Route::resource('products', ProductsController::class)->except(['edit', 'update', 'destroy']);
    Route::resource('products', ProductsController::class)->only(['edit', 'update', 'destroy'])->middleware('super_admin');

    // Stores Routes
    Route::resource('stores', StoreController::class)->except(['edit', 'update', 'destroy']);
    Route::resource('stores', StoreController::class)->only(['edit', 'update', 'destroy'])->middleware('super_admin');

    // Debts Routes
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/store/{store}', [DebtController::class, 'showStore'])->name('showStore');
        Route::post('/{debt}/pay', [DebtController::class, 'pay'])->name('pay');
    });

    // Breeze Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
