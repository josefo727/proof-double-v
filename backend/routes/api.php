<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserOrdersController;
use App\Http\Controllers\Api\UserProductsController;
use App\Http\Controllers\Api\ProductOrdersController;
use App\Http\Controllers\Api\OrderProductsController;
use App\Http\Controllers\Api\CustomerOrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/api-auth.php';

// Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('users', UserController::class);

        // User Orders
        Route::get('/users/{user}/orders', [
            UserOrdersController::class,
            'index',
        ])->name('users.orders.index');
        Route::post('/users/{user}/orders', [
            UserOrdersController::class,
            'store',
        ])->name('users.orders.store');

        // User Products
        Route::get('/users/{user}/products', [
            UserProductsController::class,
            'index',
        ])->name('users.products.index');
        Route::post('/users/{user}/products', [
            UserProductsController::class,
            'store',
        ])->name('users.products.store');

        Route::apiResource('customers', CustomerController::class);

        // Customer Orders
        Route::get('/customers/{customer}/orders', [
            CustomerOrdersController::class,
            'index',
        ])->name('customers.orders.index');
        Route::post('/customers/{customer}/orders', [
            CustomerOrdersController::class,
            'store',
        ])->name('customers.orders.store');

        Route::apiResource('products', ProductController::class);

        // Product Orders
        Route::get('/products/{product}/orders', [
            ProductOrdersController::class,
            'index',
        ])->name('products.orders.index');
        Route::post('/products/{product}/orders/{order}', [
            ProductOrdersController::class,
            'store',
        ])->name('products.orders.store');
        Route::delete('/products/{product}/orders/{order}', [
            ProductOrdersController::class,
            'destroy',
        ])->name('products.orders.destroy');

        Route::apiResource('orders', OrderController::class);

        // Order Products
        Route::get('/orders/{order}/products', [
            OrderProductsController::class,
            'index',
        ])->name('orders.products.index');
        Route::post('/orders/{order}/products/{product}', [
            OrderProductsController::class,
            'store',
        ])->name('orders.products.store');
        Route::delete('/orders/{order}/products/{product}', [
            OrderProductsController::class,
            'destroy',
        ])->name('orders.products.destroy');
    });
