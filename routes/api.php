<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('category')->group(function () {
  Route::get('all', [CategoryController::class, 'index']);
  Route::post('create', [CategoryController::class, 'store']);
  Route::post('update/{id}', [CategoryController::class, 'update']);
  Route::get('show/{id}', [CategoryController::class, 'find']);
});

Route::prefix('product')->group(function () {
  Route::get('all', [ProductController::class, 'index']);
  Route::post('create', [ProductController::class, 'store']);
  Route::post('update/{id}', [ProductController::class, 'update']);
  Route::get('show/{id}', [ProductController::class, 'find']);
});