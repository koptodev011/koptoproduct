<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test',function (Request $request){
    return response()->json([
        'message' => 'Hello World'
    ]);
});



// Auth apis
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');


// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/parties', [PartyController::class, 'store']);
// });