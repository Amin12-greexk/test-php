<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SalesTargetController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SalesOrderController;

Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/sales-targets', [SalesTargetController::class, 'index']);
Route::get('/sales-performance', [SalesTargetController::class, 'performance']);

Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{customer}', [CustomerController::class, 'update']);

Route::post('/sales-orders', [SalesOrderController::class, 'store']);