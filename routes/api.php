<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Permisos de user
Route::prefix('/user')->group(function() {

    Route::put('/register', [UsersController::class, 'register']);
    Route::put('/login', [UsersController::class, 'login']);
    Route::put('/recoveryPassword', [UsersController::class, 'recoveryPassword']);

    Route::get('/searchToBuy', [CardsAndCollectionsController::class, 'searchToBuy']);


});

// Permisos de admin
Route::middleware(['validateToken', 'validateRole'])->group(function () {

    Route::put('/register', [CardsAndCollectionsController::class, 'register']);
    Route::put('/registerCollection', [CardsAndCollectionsController::class, 'registerCollection']);
    Route::put('/addCardToCollection', [CardsAndCollectionsController::class, 'addCardToCollection']);

});

// Permisos de profesional
Route::middleware(['validateToken', 'verifyToSell'])->group(function () {

    Route::put('/cardsToSale', [CardsAndCollectionsController::class, 'cardsToSale']);
    Route::get('/searchCard', [CardsAndCollectionsController::class, 'searchCard']);

});
