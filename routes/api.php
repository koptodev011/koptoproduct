<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;

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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
});
// Tenant apis
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updatetenantchdetails', [TenantController::class, 'updateMainBranchDetails']);
    Route::get('/getallbranchdetails', [TenantController::class, 'getAllBranchDetails']);
    Route::post('/addnewfirm', [TenantController::class, 'addNewFirm']);
    Route::post('/switchfirm', [TenantController::class, 'switchFirm']); 
});



// All Party Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addparty', [PartyController::class, 'addParty']);
    Route::get('/getparties', [PartyController::class, 'getParties']);
    Route::get('/getparty', [PartyController::class, 'getParty']);
    Route::get('/getactivetenant', [TenantController::class, 'getActiveTanent']);
    Route::post('/updatepartydetails', [PartyController::class, 'updatePartyDetails']);
    Route::post('/schedulereminder', [PartyController::class, 'scheduleReminder']);
});
// Get business type
Route::get('/getbusinesstype', [TenantController::class, 'getBusinessType']);
// Get Business category
Route::get('/getbusinesscategory', [TenantController::class, 'getBusinessCategory']);
// get All states
Route::get('/getallstates', [TenantController::class, 'getAllStates']);
// Add party group
Route::post('/addpartygroup', [PartyController::class, 'addPartyGroup']);
Route::get('/getpartygroup', [PartyController::class, 'getPartyGroup']);
Route::get('/getpartyreminder', [PartyController::class, 'getPartyReminder']);
Route::get('/deleteparty', [TenantController::class, 'deleteParty']);







// Product apis
Route::middleware('auth:sanctum')->group(function () {
     Route::post('/addproduct', [ProductController::class, 'addProduct']);
     Route::get('/getproducts', [ProductController::class, 'getProducts']);
     Route::get('/getproductdetails', [ProductController::class, 'getProductDetails']);
     Route::post('/editproductdetails', [ProductController::class, 'editProdutDetails']);
     Route::delete('/deleteproduct/{product_id}', [ProductController::class, 'deleteProduct']);
});



Route::post('/adjectproduct', [ProductController::class, 'adjectProduct']);
Route::post('/addtaxgroup', [ProductController::class, 'addTaxGroup']);
Route::get('/gettaxgroup', [ProductController::class, 'getTaxGroup']);
Route::post('/addtaxrate', [ProductController::class, 'addTaxRate']);
Route::get('/gettaxrate', [ProductController::class, 'getTaxRate']);


Route::post('/addproductcategory', [ProductController::class, 'addProductCategory']);
Route::get('/getproductcategory', [ProductController::class, 'getProductCategory']);
Route::post('/addbaseunit', [ProductController::class, 'addBaseUnit']);
Route::get('/getbaseunit', [ProductController::class, 'getBaseUnit']);
Route::get('/assigncode', [ProductController::class, 'assignCode']);





// Sales Routes
 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/salescalculation', [SalesController::class, 'salesCalculation']);
    Route::post('/addsaleinvoice', [SalesController::class, 'addSaleInvoice']);
});

Route::post('/addsalespaymenttype', [SalesController::class, 'addSalesPaymentType']);
Route::get('/getsalespaymenttype', [SalesController::class, 'getSalesPaymentType']);