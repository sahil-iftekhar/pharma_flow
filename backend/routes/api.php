<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Medicine\CategoryController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Shop\CartController;


Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

Route::post('/users', [UserController::class, 'createCompanyUser'])
->name('api.createUser');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.getUsers');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.getUser');

    Route::post('/users/admin', [UserController::class, 'createAdminUser'])
        ->name('api.createAdminUser');

    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->name('api.updateUser');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('api.deleteUser');

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('api.getCategories');
    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('api.createCategory');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])
        ->name('api.getCategory');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('api.updateCategory');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('api.deleteCategory');

    // Medicine Routes
    Route::get('/medicines', [MedicineController::class, 'index'])
        ->name('api.getMedicines');
    Route::post('/medicines', [MedicineController::class, 'store'])
        ->name('api.createMedicine');
    Route::get('/medicines/{id}', [MedicineController::class, 'show'])
        ->name('api.getMedicine');
    Route::put('/medicines/{id}', [MedicineController::class, 'update'])
        ->name('api.updateMedicine');
    Route::delete('/medicines/{id}', [MedicineController::class, 'destroy'])
        ->name('api.deleteMedicine');
        
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])
        ->name('api.getCart');
    Route::put('/cart/items/{id}', [CartController::class, 'update'])
        ->name('api.updateCartItem');
    Route::delete('/cart/items/{id}', [CartController::class, 'destroy'])
        ->name('api.deleteCartItem');
    });
