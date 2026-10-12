<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('plants', PlantController::class)->except('show');
Route::resource('categories', CategoryController::class)->except('show');
Route::resource('suppliers', SupplierController::class)->except('show');
Route::resource('purchases', PurchaseController::class)->except('show');
