<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\Admin\GameController as AdminGameController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/search', [GameController::class, 'index']);
Route::get('/games/{slug}', [GameController::class, 'show']);
Route::post('/games', [GameController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
// routes/api.php - tambahkan di paling bawah

Route::get('/test', function() {
    return response()->json([
        'message' => 'API berjalan!',
        'timestamp' => now()
    ]);
});

Route::get('/games/{slug}/play', [GameController::class, 'play']);


Route::prefix('admin')->group(function () {

    Route::get('/games', [AdminGameController::class, 'index']);

    Route::get('/games/{game}', [AdminGameController::class, 'show']);

    Route::post('/games', [AdminGameController::class, 'store']);

    Route::put('/games/{game}', [AdminGameController::class, 'update']);

    Route::delete('/games/{game}', [AdminGameController::class, 'destroy']);

});