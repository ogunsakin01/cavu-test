<?php

use App\Http\Controllers\AuthController;
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
Route::post('/test', function (){
    return json_encode(['I got here']);
});
Route::prefix('v1')->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
//    Route::post('/login', '');
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/user', function (Request $request) {return $request->user();});
    });
});

