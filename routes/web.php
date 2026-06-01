<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home.index');
});

// Admin Receipts Routes
Route::prefix('admin/receipts')->name('admin.receipts.')->group(function () {
    Route::get('/', [ReceiptController::class, 'index'])->name('index');
    Route::get('/upload', [ReceiptController::class, 'create'])->name('upload.form');
    Route::post('/upload', [ReceiptController::class, 'upload'])->name('upload');
    Route::get('/{receipt}/show', [ReceiptController::class, 'show'])->name('show');
    Route::get('/{receipt}/validate', [ReceiptController::class, 'validate'])->name('validate');
    Route::post('/{receipt}/validate', [ReceiptController::class, 'validateSubmit'])->name('validateSubmit');
});

// Products Routes (assuming these are for admin or general use)
Route::resource('products', ProductsController::class);

// Stores Routes
Route::resource('stores', StoreController::class);

// Debts Routes
use App\Http\Controllers\DebtController;
Route::prefix('debts')->name('debts.')->group(function () {
    Route::get('/', [DebtController::class, 'index'])->name('index');
    Route::get('/store/{store}', [DebtController::class, 'showStore'])->name('showStore');
    Route::post('/{debt}/pay', [DebtController::class, 'pay'])->name('pay');
});

