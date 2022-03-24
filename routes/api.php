<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CardsController;

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

Route::prefix('users')->group(function(){
    Route::post('/login',[UsersController::class,'login']);
    Route::put('/register',[UsersController::class, 'register']);
    Route::get('/resetPassword',[UsersController::class, 'resetPassword']);
    Route::get('/searchSales',[CardsController::class,'searchSales']);
});

Route::middleware(['apitoken','admin'])->prefix('admin')->group(function(){
    Route::put('/createCard',[CardsController::class,'createCard']);
    Route::put('/createCollection',[CardsController::class,'createCollection']);
    Route::put('/linkCardCollection',[CardsController::class, 'linkCardCollection']);
});

Route::middleware(['apitoken','users'])->prefix('users')->group(function(){
    Route::put('/buyCard',[UsersController::class,'buyCard']);
    Route::put('/sellCard',[UsersController::class,'sellCard']);
    Route::get('/searchNames',[CardsController::class,'searchNames']);
});
