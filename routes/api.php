<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\GrowYourBusiness;

// Auth apis
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/addstaff',[AuthController::class,'addStaff']);
});
// Tenant apis
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updatetenantchdetails', [TenantController::class, 'updateMainBranchDetails']);
    Route::get('/getallbranchdetails', [TenantController::class, 'getAllBranchDetails']);
    Route::post('/addnewfirm', [TenantController::class, 'addNewFirm']);
    Route::post('/switchfirm', [TenantController::class, 'switchFirm']);
    Route::get('/getallstaff', [AuthController::class, 'getAllStaff']); 
});

//Roles
Route::get('/getroles', [AuthController::class, 'staffRoles']);


Route::post('/updatestaffdetails', [AuthController::class, 'updateStaffDetails']);
Route::delete('/deletestaff/{staff_id}', [AuthController::class, 'deleteStaff']);
Route::get('/getcitiesofstate', [TenantController::class, 'getCitiesOfState']);
Route::get('/getcitypincode', [TenantController::class, 'getCityPinCode']);

// All Party Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addparty', [PartyController::class, 'addParty']);
    Route::get('/getparties', [PartyController::class, 'getParties']);
    Route::get('/getparty', [PartyController::class, 'getParty']);
    Route::get('/getactivetenant', [TenantController::class, 'getActiveTanent']);
    Route::post('/schedulereminder', [PartyController::class, 'scheduleReminder']);
    Route::post('/updatepartydetails', [PartyController::class, 'updatePartyDetails']);
    Route::get('/getpartygroup', [PartyController::class, 'getPartyGroup']);
    // Add party group
Route::post('/addpartygroup', [PartyController::class, 'addPartyGroup']);
Route::post('/deleteparty', [PartyController::class, 'deleteParty']);

Route::post('/updatepartygroup', [PartyController::class, 'updatePartyGroup']);

Route::post('/deletepartygroup', [PartyController::class, 'deletePartyGroup']);

Route::get('/getpartiesbygroup', [PartyController::class, 'getPartiesByGroup']);

Route::get('/getpartiesexceptselectedcategory',[PartyController::class,'getPartiesExceptSelectedCategory']);
});



// Get business type
Route::get('/getbusinesstype', [TenantController::class, 'getBusinessType']);
Route::get('/getbusinesscategory', [TenantController::class, 'getBusinessCategory']);
Route::get('/getallstates', [TenantController::class, 'getAllStates']);
Route::get('/getpartyreminder', [PartyController::class, 'getPartyReminder']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/movetothisgroup', [PartyController::class,'moveToThisGroup']);

});

Route::post('/changepartystatus',[PartyController::class,'changePartyStatus']);





// Product apis
Route::middleware('auth:sanctum')->group(function () {
     Route::post('/addproduct', [ProductController::class, 'addProduct']);
     Route::get('/getproducts', [ProductController::class, 'getProducts']);
     Route::get('/getproductdetails', [ProductController::class, 'getProductDetails']);
     Route::delete('/deleteproduct/{product_id}', [ProductController::class, 'deleteProduct']);
});

Route::post('/editproductdetails', [ProductController::class, 'editProdutDetails']);
Route::post('/bulkdeleteproducts', [ProductController::class, 'bulkDeleteProducts']);


Route::post('/adjectproduct', [ProductController::class, 'adjectProduct']);
Route::post('/addtaxgroup', [ProductController::class, 'addTaxGroup']);
Route::get('/gettaxgroup', [ProductController::class, 'getTaxGroup']);
Route::post('/addtaxrate', [ProductController::class, 'addTaxRate']);
Route::get('/gettaxrate', [ProductController::class, 'getTaxRate']);

       // Caterories section
Route::middleware('auth:sanctum')->group(function () {
Route::post('/addproductcategory', [ProductController::class, 'addProductCategory']);
Route::get('/getproductcategory', [ProductController::class, 'getProductCategory']);
Route::get('/getperticularproductcategory',[ProductController::class,'getPerticularProductCategory']);
});
Route::post('/updatecategory', [ProductController::class, 'updateCategory']);
Route::delete('/deleteproductcategory/{product_category_id}', [ProductController::class, 'deleteProductCategory']);
Route::post('/bulkdeletecategories', [ProductController::class, 'bulkDeleteCategories']);


//units section
// Route::middleware('auth:sanctum')->group(function () {
// Route::post('/addbaseunit', [ProductController::class, 'addBaseUnit']);
// )};

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addbaseunit', [ProductController::class, 'addBaseUnit']);
    Route::get('/getbaseunit', [ProductController::class, 'getBaseUnit']);
    Route::post('/addunitconversion', [ProductController::class, 'addConversionunits']);
    Route::get('/getunitconversion', [ProductController::class, 'getUnitConversion']);
});


Route::post('/updatebaseunit', [ProductController::class, 'updateBaseUnit']);
Route::delete('/deletebaseunit/{product_base_unit_id}', [ProductController::class, 'deleteBaseUnit']);
Route::post('/bulkdeletebaseunits', [ProductController::class, 'bulkDeleteBaseUnits']);
Route::get('/assigncode', [ProductController::class, 'assignCode']);




// Sales Routes
 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addsaleinvoice', [SalesController::class, 'addSaleInvoice']);
});
Route::post('/addsalespaymenttype', [SalesController::class, 'addSalesPaymentType']);
Route::get('/getsalesdata',[SalesController::class,'getSalesData']);
Route::get('/getsalespaymenttype', [SalesController::class, 'getSalesPaymentType']);
Route::get('/getallsalesearchfilters',[SalesController::class,'getAllSaleSearchFilters']);

// Sales Estimate/Quotation
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addsalequotation', [SalesController::class, 'addSalesQuotation']);
});
Route::post('/convertquotationtosale', [SalesController::class, 'convertQuotationToSale']);

// Payment In
Route::post('/addpaymentin',[SalesController::class,'addPaymentIn']);

// Sale Order
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/addsaleorder',[SalesController::class,'addSaleOrder']);
    Route::post('/deliverychallan',[SalesController::class,'deliveryChallan']);
});



// Purchase Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/createpurchaseinvoice', [PurchaseController::class, 'createPurchaseInvoice']);
    Route::get('/getpurchasedata', [PurchaseController::class, 'getPurchaseData']);
    Route::post('/createpurchaseorderinvoice', [PurchaseController::class, 'createPurchaseOrderInvoice']);
});
Route::post('/paymentout', [PurchaseController::class, 'paymentOut']);
Route::post('/purchaseorder', [PurchaseController::class, 'purchaseOrder']);
Route::post('/convertpurchaseordertopurchase', [PurchaseController::class, 'convertPurchaseOrderToPurchase']);
Route::post('/purchasereturn', [PurchaseController::class, 'purchaseReturn']);




// Online store all apis
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/convertstoreintoonlinestore', [GrowYourBusiness::class, 'convertStoreIntoOnlineStore']);
    Route::get('/getonlineproducts', [GrowYourBusiness::class, 'getOnlineProducts']); 
});
Route::post('/addtocart',[GrowYourBusiness::class,'addToCart']);
Route::get('/getcartsummary',[GrowYourBusiness::class,'getCartSummary']);
Route::post('/placeorder',[GrowYourBusiness::class,'placeOrder']);