<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\PartyController;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/test',function (Request $request){
//     return response()->json([
//         'message' => 'Hello World'
//     ]);
// });



// Auth apis
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');


// Tenant apis
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updatetenantchdetails', [TenantController::class, 'updateMainBranchDetails']);
    Route::get('/getallbranchdetails', [TenantController::class, 'getAllBranchDetails']);
});



// All Party Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addparty', [PartyController::class, 'addParty']);
    Route::get('/getparties', [PartyController::class, 'getParties']);
    Route::get('/getparty', [PartyController::class, 'getParty']);
   
});
Route::post('/updatepartydetails', [PartyController::class, 'editPartyDetails']);

// Get business type
Route::get('/getbusinesstype', [TenantController::class, 'getBusinessType']);

// Get Business category
Route::get('/getbusinesscategory', [TenantController::class, 'getBusinessCategory']);

// get All states
Route::get('/getallstates', [TenantController::class, 'getAllStates']);
// Add party group
Route::post('/addpartygroup', [PartyController::class, 'addPartyGroup']);

Route::get('/getpartygroup', [PartyController::class, 'getPartyGroup']);