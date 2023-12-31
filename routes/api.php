<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\IconController;
use App\Http\Controllers\Api\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::get('icons', [IconController::class, 'index']);
    Route::resource('categories', CategoryController::class)->middleware('jwtAuth');
    Route::patch('todo/pinned/{id}', [TodoController::class,'updatePinned'])->middleware('jwtAuth');
    Route::resource('todo', TodoController::class)->middleware('jwtAuth');
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);

        Route::get('/me', [AuthController::class, 'me'])->middleware('jwtAuth');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwtAuth');
        Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('jwtAuth');
    });
});
