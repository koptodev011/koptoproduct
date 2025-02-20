<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Productcategory;
use App\Models\Productbaseunit;
use App\Models\Producttaxgroup;
use App\Models\Producttaxrate;
use App\Models\Product;
use App\Models\Productunitconversion;
use App\Models\Productpricing;
use App\Models\Productwholesaleprice;
use App\Models\Productstock;
use App\Models\Productimages;
use App\Models\Productpurchesprice;
use App\Models\Productonlinestore;
use App\Models\Productstockadjectment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Carbon\Carbon;
class ProductController extends Controller
{
   
   
    public function addProduct(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product_name' => 'nullable|string',
            'product_hsn' => 'required|unique:products,product_hsn',
            'base_unit_id' => 'required|exists:productbaseunits,id',
            'secondary_unit_id' => 'nullable|exists:productbaseunits,id',
            'conversion_rate' => 'nullable',
            'description' => 'nullable|string',    
            'mrp' => 'nullable|numeric',
            'product_category_id'=> 'nullable|exists:productcategories,id',
            'assign_code'=> 'nullable|numeric|unique:products,item_code',
            
            // sale price
            'sale_price'=> 'required',
            'sale_withorwithouttax'=> 'nullable',
            'discount_amount'=> 'nullable|numeric',
            'discount_percentageoramount'=> 'nullable|numeric',
        
            // wholesale price
            'wholesale_price'=> 'nullable|numeric',
            'wholesale_withorwithouttax'=> 'nullable',
            'wholesale_min_quantity'=> 'nullable|numeric',

            //Product Purchase price
            'purchese_price'=> 'required|numeric',
            'purchese_withorwithouttax'=> 'required|numeric',
            'tax_id'=> 'required|numeric',
            
            // stock
            'opening_stock'=> 'nullable|numeric',
            'at_price'=> 'nullable|numeric',
            'min_stock'=> 'nullable|numeric',
            'location'=> 'nullable|string',
            'date' => 'nullable|date',
        
            // online store
            'online_store_price'=> 'nullable|numeric',
            'online_store_product_description'=> 'nullable|string',

            //product images
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif',


        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        

        if($request->base_unit_id == $request->secondary_unit_id){
            return response()->json(['message' => 'Base unit and secondary unit cannot be same'], 400);
        }

        $searchMainTanant = Tenant::where('user_id', $user->id)->first();

        $unitconversion = new Productunitconversion();
        $unitconversion->product_base_unit_id = $request->base_unit_id;
        $unitconversion->product_secondary_unit_id = $request->secondary_unit_id;
        $unitconversion->conversion_rate = $request->conversion_rate;
        $unitconversion->tenant_id = $searchMainTanant->id;
        $unitconversion->save();

        

        $product = new Product();
        $product->tenant_id = $searchMainTanant->id;
        $product->product_name = $request->product_name;
        $product->product_hsn = $request->product_hsn;
        $product->item_code = $request->assign_code;
        $product->description = $request->description;
        $product->mrp = $request->mrp;
        $product->product_base_unit = $request->base_unit_id;
        $product->product_category_id = $request->product_category_id;
        $product->tax_id = $request->tax_id;
        $product->productconversion_id = $unitconversion->id;
        $product->save();

        
        $saleprice = new Productpricing();
        $saleprice->sale_price = $request->sale_price;
        $saleprice->withorwithouttax = $request->sale_withorwithouttax;
        $saleprice->discount = $request->discount_amount;
        $saleprice->percentageoramount = $request->discount_percentageoramount;
        $saleprice->product_id = $product->id;
        $saleprice->save();


        if ($request->has('wholesale_price')) {
            $wholesaleprice = new Productwholesaleprice();
            $wholesaleprice->whole_sale_price = $request->wholesale_price;
            $wholesaleprice->withorwithouttax = $request->wholesale_withorwithouttax;
            $wholesaleprice->wholesale_min_quantity = $request->wholesale_min_quantity;
            $wholesaleprice->product_id = $product->id;
            $wholesaleprice->save();
        }

        if ($request->has('opening_stock')) {
        $stock = new Productstock();
        $stock->product_stock = $request->opening_stock;
        $stock->at_price = $request->at_price;
        $stock->min_stock = $request->min_stock;
        $stock->location = $request->location;
        $stock->date = $request->date;
        $stock->product_id = $product->id;
        $stock->previous_stock = $request->opening_stock;
        $stock->save();
        }

        if($request->has('base_unit_id') && $request->secondary_unit_id == null){
            $stock->secondaryunit_stock_value = $request->opening_stock;
            $stock->save();
        }else{
            $stock->secondaryunit_stock_value = $request->opening_stock * $unitconversion->conversion_rate;
            $stock->save();
        }

        $purchaseprice = new Productpurchesprice();
        $purchaseprice->product_purches_price = $request->purchese_price;
        $purchaseprice->withorwithouttax = $request->purchese_withorwithouttax;
        $purchaseprice->product_id = $product->id;
        $purchaseprice->save();


        $onlinestore = new Productonlinestore();
        $onlinestore->online_store_price = $request->online_store_price;
        $onlinestore->online_product_description = $request->online_store_product_description;
        $onlinestore->product_id = $product->id;
        $onlinestore->save();

        if ($request->has('online_store_price') || $request->has('online_store_product_description')) {
            $product->isonlineproduct = 1;
        }
    
        $product->save();

        if ($request->has('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('product_images', 'public');
                $productImage = new ProductImages();
                $productImage->product_id = $product->id;
                $productImage->product_image = 'storage/' . $path; // Add 'storage/' before saving
                $productImage->save();
            }  
        // }
        
    }
    return response()->json(['message' => 'Product created successfully'], 200);
}




public function getProducts(Request $request)
{
    $user = Auth::user();
    $searchMainTanant = Tenant::where('user_id', $user->id)->first();
    $products = Product::where('tenant_id', $searchMainTanant->id)
        ->with([ 
            'pricing',       
            'wholesalePrice', 
            'stock',        
            'onlineStore',
            'productUnitConversion',
            'purchasePrice',
            'images'
        ])
        ->get();

    if ($products->isEmpty()) {
        return response()->json(['message' => 'No products found'], 404);
    }

    return response()->json(['products' => $products], 200);
}






public function editProdutDetails(Request $request) {
    
    // Validate incoming request data
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|numeric',
        'product_name' => 'nullable|string',
        'product_hsn' => 'nullable|string',
        'base_unit_id' => 'nullable|numeric',
        'secondary_unit_id' => 'nullable|numeric',
        'conversion_rate' => 'nullable|numeric',
        'description' => 'nullable|string',    
        'mrp' => 'nullable|numeric',
        'product_category'=> 'nullable|numeric',
        'assign_code'=> 'nullable|string',

        // Sale price
        'sale_price'=> 'nullable|numeric',
        'sale_withorwithouttax'=> 'nullable|numeric',
        'discount_amount'=> 'nullable|numeric',
        'discount_percentageoramount'=> 'nullable|numeric',

        // Wholesale price
        'wholesale_price'=> 'nullable|numeric',
        'wholesale_withorwithouttax'=> 'nullable|numeric',
        'wholesale_min_quantity'=> 'nullable|numeric',
        'purchese_price'=> 'nullable|numeric',
        'purchese_withorwithouttax'=> 'nullable|numeric',
        'tax_id'=> 'nullable|numeric',
        
        // Stock
        'opening_stock'=> 'nullable|numeric',
        'at_price'=> 'nullable|numeric',
        'min_stock'=> 'nullable|numeric',
        'location'=> 'nullable|string',

        // Online store
        'online_store_price'=> 'nullable|numeric',
        'online_store_product_description'=> 'nullable|string',

        // Product images
        'product_images' => 'nullable|array',
        'product_images.*' => 'image|mimes:jpeg,png,jpg,gif',
    ]);

    // Return validation errors if any
    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Find the product by ID
    $product = Product::find($request->product_id);

    // Return error if product not found
    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Update product attributes only if they are present in the request
    $product->update(array_filter([
        'product_name' => $request->product_name,
        'product_hsn' => $request->product_hsn,
        'product_base_unit' => $request->base_unit_id,
        'description' => $request->description,
        'mrp' => $request->mrp,
        'category_id' => $request->product_category,
        'item_code' => $request->assign_code,
        'tax_id' => $request->tax_id
    ]));

    // Handle new product images upload only if provided
    if ($request->hasFile('product_images')) {
        // Delete existing product images
        DB::table('productimages')->where('product_id', $product->id)->delete();

        foreach ($request->file('product_images') as $image) {
            $imagePath = $image->store('product_images', 'public');
            DB::table('productimages')->insert([
                'product_id' => $product->id,
                'product_image' => 'storage/' . $imagePath // Add 'storage/' before saving
            ]);
        }
    }

    // Update unit conversion details
    $unitconversion = $product->productUnitConversion;
    if ($unitconversion) {
        $unitconversion->update(array_filter([
            'product_base_unit_id' => $request->base_unit_id,
            'product_secondary_unit_id' => $request->secondary_unit_id,
            'conversion_rate' => $request->conversion_rate
        ]));
    }

    // Update sale price details
    $saleprice = Productpricing::where('product_id', $product->id)->first();
    if ($saleprice) {
        $saleprice->update(array_filter([
            'sale_price' => $request->sale_price,
            'withorwithouttax' => $request->sale_withorwithouttax,
            'discount' => $request->discount_amount,
            'percentageoramount' => $request->discount_percentageoramount
        ]));
    }

    // Update wholesale price details
    $wholesaleprice = Productwholesaleprice::where('product_id', $product->id)->first();
    if ($wholesaleprice) {
        $wholesaleprice->update(array_filter([
            'whole_sale_price' => $request->wholesale_price,
            'withorwithouttax' => $request->wholesale_withorwithouttax,
            'wholesale_min_quantity' => $request->wholesale_min_quantity
        ]));
    }

    // Update stock details
    $stock = Productstock::where('product_id', $product->id)->first();
    if ($stock) {
        $stock->update(array_filter([
            'product_stock' => $request->opening_stock,
            'at_price' => $request->at_price,
            'min_stock' => $request->min_stock,
            'location' => $request->location
        ]));
    }

    // Update online store details
    $onlinestore = Productonlinestore::where('product_id', $product->id)->first();
    if ($onlinestore) {
        $onlinestore->update(array_filter([
            'online_store_price' => $request->online_store_price,
            'online_product_description' => $request->online_store_product_description
        ]));
    }

    // Return success response
    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product
    ], 200);
}








public function deleteProduct($product_id){
    try {
        $product = Product::findOrFail($product_id);
       
        DB::beginTransaction();
        try {
            // Delete related records
            // Productunitconversion::where('product_id', $product_id)->delete();
            Productpricing::where('product_id', $product_id)->delete();
            Productwholesaleprice::where('product_id', $product_id)->delete();
            Productstock::where('product_id', $product_id)->delete();
            Productonlinestore::where('product_id', $product_id)->delete();

            // Delete the product
            $product->delete();
            
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Product and all related data deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting product data',
                'error' => $e->getMessage()
            ], 500);
        }

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Product not found'
        ], 404);
    }
}









    public function assignCode()
{
    do {
        $randomNumber = mt_rand(10000000000, 99999999999);
        $exists = Product::where('item_code', $randomNumber)->exists();
    } while ($exists);

    return response()->json(['code' => $randomNumber], 200);
}



    public function getProductDetails(Request $request)
{
    $user = Auth::user();
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|numeric'
    ]);

    $searchMainTanant = Tenant::where('user_id', $user->id)->first();

    $product = Product::where('id', $request->product_id)
        ->where('tenant_id', $searchMainTanant->id)
        ->with([
            'productUnitConversion',
            'pricing',
            'wholesalePrice',
            'stock',
            'onlineStore',
            'images',
            'purchasePrice' // Add the purchasePrice relationship here
        ])
        ->first();

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $salePrice = $product->pricing->sale_price ?? 0;
    $stockQuantity = $product->stock->product_stock ?? 0;
    $stockValue = $salePrice * $stockQuantity;
    $purchasePrice = $product->purchasePrice->product_purches_price ?? 0; // Fetch purchase price

    $product->stock_value = $stockValue;
    $product->purchase_price = $purchasePrice; // Add purchase price to response

    return response()->json([
        'product' => $product
    ], 200);
}




    public function bulkDeleteProducts(Request $request){
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        
        Product::whereIn('id', $request->product_ids)->delete();
        return response()->json([
            'message' => 'Products deleted successfully'
        ], 200);
     
    }






    public function adjectProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'addorreduct_product_stock' => 'required',
            'product_id' => 'required|exists:products,id',
            'stock'=> 'required',
            'priceperunit'=> 'required',
            'details'=> 'required',
            'date'=> 'required' // Expecting '7/02/2025'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $productstockadjectment = new Productstockadjectment();
        $productstockadjectment->addorreduct_product_stock = $request->addorreduct_product_stock;
        $productstockadjectment->product_id = $request->product_id;
        $productstockadjectment->stock_quantity = $request->stock;
        $productstockadjectment->priceperunit = $request->priceperunit;
        $productstockadjectment->details = $request->details;
        $productstockadjectment->productadjectmentdate = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'); // Convert format
        $productstockadjectment->save();
    
        $stock = Productstock::where('product_id', $request->product_id)->first();
    
        if ($request->addorreduct_product_stock == 1) {
            $stock->product_stock = $stock->product_stock + $request->stock;
        } else {
            $stock->product_stock = $stock->product_stock - $request->stock;
            if ($stock->product_stock < 0) {
                $stock->product_stock = 0;
            }
        }
    
        $stock->at_price = $request->priceperunit;
        $stock->save();
    
        return response()->json(['message' => 'Product stock adjusted successfully'], 200); 
    }

    






    public function addTaxGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'product_tax_group' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $producttaxgroup = new Producttaxgroup();
        $producttaxgroup->product_tax_group = $request->product_tax_group;
        $producttaxgroup->save();
        return response()->json(['message' => 'Product tax group created successfully'], 200);
    }



    public function getTaxGroup(){
        $producttaxgroups = Producttaxgroup::all();
        return response()->json($producttaxgroups, 200);
    }



  
    public function addTaxRate(Request $request){
        $validator = Validator::make($request->all(), [
            'tax_name' => 'required|string',
            'tax_rate' => 'required|numeric',
            'tax_group_id' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $producttaxrate = new Producttaxrate();
        $producttaxrate->product_tax_name = $request->tax_name;
        $producttaxrate->product_tax_rate = $request->tax_rate;
        $producttaxrate->product_tax_group_id = $request->tax_group_id;
        $producttaxrate->save();
        return response()->json(['message' => 'Product tax rate created successfully'], 200);
    }

    

    public function getTaxRate(){
        $producttaxrates = Producttaxrate::all();
        return response()->json($producttaxrates, 200);
    }



    // Caterories section
    public function addProductCategory(Request $request){
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'product_category' => 'required'
        ]);
        $searchForMaintanant = Tenant::where('user_id', $user->id)->first();
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $productcategory = new Productcategory();
        $productcategory->product_category = $request->product_category;
        $productcategory->tenant_id = $searchForMaintanant->id;
        $productcategory->save();
        return response()->json(['message' => 'Product category created successfully'], 200);
    }





 

    public function getProductCategory()
    {
        $user = Auth::user();
        $searchForMaintanant = Tenant::where('user_id', $user->id)->first();
    
        $productcategories = Productcategory::where('is_delete', false)
            ->where(function ($query) use ($searchForMaintanant) {
                $query->where('tenant_id', $searchForMaintanant->id);
            })
            ->withCount('products') // Count products for each category
            ->get();
    
        return response()->json($productcategories, 200);
    }
    


// public function getPerticularProductCategory(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'product_category_id' => 'required|numeric'
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['error' => $validator->errors()], 400);
//     }
//     $searchForMaintanant = Tenant::where('user_id', $user->id)->first();
//     $productcategories = Productcategory::where('is_delete', false)
//         ->where('id', $request->product_category_id)
//         ->where('tenant_id', $searchForMaintanant->id)
//         ->with(['products.stock', 'products.pricing' , 'products.images'])
//         ->first();

//     $user = Auth::user();


//     if (!$productcategories) {
//         return response()->json(['message' => 'No product category found'], 404);
//     }

//     $productcategories->product_count = $productcategories->products->count();
//     foreach ($productcategories->products as $product) {
//         $salePrice = optional($product->pricing)->sale_price ?? 0;
//         $productStock = optional($product->stock)->product_stock ?? 0;
//         $product->stock_value = $salePrice * $productStock;
//     }

//     return response()->json($productcategories, 200);
// }




public function getPerticularProductCategory(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_category_id' => 'required|numeric'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $user = Auth::user();
    $searchForMaintanant = Tenant::where('user_id', $user->id)->first();
    $productcategories = Productcategory::where('is_delete', false)
        ->where('id', $request->product_category_id)
        ->where('tenant_id', $searchForMaintanant->id)
        ->with(['products.stock', 'products.pricing', 'products.images'])
        ->first();

    if (!$productcategories) {
        return response()->json(['message' => 'No product category found'], 404);
    }

    $productcategories->product_count = $productcategories->products->count();
    foreach ($productcategories->products as $product) {
        $salePrice = optional($product->pricing)->sale_price ?? 0;
        $productStock = optional($product->stock)->product_stock ?? 0;
        $product->stock_value = $salePrice * $productStock;
    }

    return response()->json($productcategories, 200);
}


    

    public function updateCategory(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_category_id' => 'required|numeric',
        'product_category' => 'required|string'
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }
    $productcategory = Productcategory::findOrFail($request->product_category_id);
    $productcategory->update([
        'product_category' => $request->product_category,
    ]);

    return response()->json(['message' => 'Product category updated successfully'], 200);
}




public function deleteProductCategory($category_id)
{
    $productcategory = Productcategory::where('id', $category_id)->first();
    if (!$productcategory) {
        return response()->json(['message' => 'Product category not found'], 404);
    }
    $productcategory->update([
        'is_delete' => true
    ]);
    return response()->json(['message' => 'Product category deleted successfully'], 200);
}



public function bulkDeleteCategories(Request $request)
{
    $validator = Validator::make($request->all(), [
        'category_ids' => 'required|array',
        'category_ids.*' => 'exists:productcategories,id',
    ]);
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }
    $productCategories = Productcategory::whereIn('id', $request->category_ids)->get();
    if ($productCategories->isEmpty()) {
        return response()->json(['message' => 'No categories found'], 404);
    }
    Productcategory::whereIn('id', $request->category_ids)->update(['is_delete' => true]);

    return response()->json(['message' => 'Categories deleted successfully'], 200);
}






    public function addBaseUnit(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product_base_unit' => 'required',
            'short_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $searchMainTanant = Tenant::where('user_id', $user->id)->first();

        $productbaseunit = new Productbaseunit();
        $productbaseunit->product_base_unit = $request->product_base_unit;
        $productbaseunit->shortname = $request->short_name;
        $productbaseunit->tenant_id = $searchMainTanant->id;
        $productbaseunit->save();
        return response()->json(['message' => 'Product base unit created successfully'], 200);
    }



    
    public function updateBaseUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'product_base_unit_id' => 'required|numeric',
            'product_base_unit' => 'required',
            'short_name' => 'required'
        ]);
      
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $productbaseunit = Productbaseunit::findOrFail($request->product_base_unit_id);
        if(!$productbaseunit){
            return response()->json(['message' => 'Product base unit not found'], 404);
        }
        $productbaseunit->update([
            'product_base_unit' => $request->product_base_unit,
            'shortname' => $request->short_name
        ]);
        return response()->json(['message' => 'Product base unit updated successfully'], 200);
    }


    public function getBaseUnit()
    {
        $user = Auth::user();
        $searchMainTanant = Tenant::where('user_id', $user->id)->first();
        
        $productbaseunits = Productbaseunit::where('is_delete', false)
            ->where(function ($query) use ($searchMainTanant) {
                $query->whereNull('tenant_id')
                      ->orWhere('tenant_id', $searchMainTanant->id);
            })
            ->get();
            
        return response()->json($productbaseunits, 200);
    }

  
  
    public function deleteBaseUnit($base_unit_id){
        $productbaseunit = Productbaseunit::where('id', $base_unit_id)->first();
        if (!$productbaseunit) {
            return response()->json(['message' => 'Product base unit not found'], 404);
        }
        $productbaseunit->update([
            'is_delete' => true
        ]);
        return response()->json(['message' => 'Product base unit deleted successfully'], 200);
    }



    public function bulkDeleteBaseUnits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base_unit_ids' => 'required|array',
            'base_unit_ids.*' => 'exists:productbaseunits,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $productBaseUnits = Productbaseunit::whereIn('id', $request->base_unit_ids)->get();
        if ($productBaseUnits->isEmpty()) {
            return response()->json(['message' => 'No base units found'], 404);
        }
        Productbaseunit::whereIn('id', $request->base_unit_ids)->update(['is_delete' => true]);
    
        return response()->json(['message' => 'Base units deleted successfully'], 200);
    }

  
  
    public function addConversionunits(Request $request)
    { 
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'base_unit_id' => 'required|exists:productbaseunits,id',
            'secondary_unit_id' => 'required|exists:productbaseunits,id',
            'conversion_rate' => 'required|numeric'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $searchMainTanant = Tenant::where('user_id', $user->id)->first();
        $unitconversions = new Productunitconversion();
        $unitconversions->product_base_unit_id = $request->base_unit_id;
        $unitconversions->product_secondary_unit_id = $request->secondary_unit_id;
        $unitconversions->conversion_rate = $request->conversion_rate;
        $unitconversions->tenant_id = $searchMainTanant->id;
        $unitconversions->save();
    
        return response()->json(['message' => 'Product unit conversion created successfully'], 200);
    }

    // public function getUnitConversion(Request $request)
    // {
    //     $user = Auth::user();
    //     $searchMainTanant = Tenant::where('user_id', $user->id)->first();
    //     $validator = Validator::make($request->all(), [
    //         'unit_id' => 'required|exists:productbaseunits,id'
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }
    
    //     $baseUnit = Productbaseunit::find($request->unit_id);
    //     $baseUnitName = $baseUnit->product_base_unit . ' (' . $baseUnit->shortname . ')';
    
    //     $getconversions = Productunitconversion::where('product_base_unit_id', $request->unit_id)
    //         ->join('productbaseunits as base_unit', 'productunitconversions.product_base_unit_id', '=', 'base_unit.id')
    //         ->join('productbaseunits as secondary_unit', 'productunitconversions.product_secondary_unit_id', '=', 'secondary_unit.id')
    //         ->select('base_unit.product_base_unit as base_unit_name', 'secondary_unit.product_base_unit as secondary_unit_name', 'productunitconversions.conversion_rate')
    //         ->where('productunitconversions.tenant_id', $searchMainTanant->id)
    //         ->get();
    
    //     $formattedConversions = $getconversions->map(function ($conversion) {
    //         return [
    //             'base_unit_name' => $conversion->base_unit_name . ' (' . $conversion->shortname . ')',
    //             'secondary_unit_name' => $conversion->secondary_unit_name . ' (' . $conversion->shortname . ')',
    //             'conversion_rate' => $conversion->conversion_rate
    //         ];
    //     });
    
    //     return response()->json([
    //         'product_base_units' => $baseUnitName,
    //         'productconversions' => $formattedConversions
    //     ], 200);
    // }


 
 
    public function getUnitConversion(Request $request)
{
    $user = Auth::user();
    $searchMainTanant = Tenant::where('user_id', $user->id)->first();
    $validator = Validator::make($request->all(), [
        'unit_id' => 'required|exists:productbaseunits,id'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $baseUnit = Productbaseunit::find($request->unit_id);
    $baseUnitName = $baseUnit->product_base_unit;

    $getconversions = Productunitconversion::where('product_base_unit_id', $request->unit_id)
        ->join('productbaseunits as base_unit', 'productunitconversions.product_base_unit_id', '=', 'base_unit.id')
        ->join('productbaseunits as secondary_unit', 'productunitconversions.product_secondary_unit_id', '=', 'secondary_unit.id')
        ->select('base_unit.product_base_unit as base_unit_name', 'secondary_unit.product_base_unit as secondary_unit_name', 'productunitconversions.conversion_rate', 'base_unit.shortname as base_unit_shortname', 'secondary_unit.shortname as secondary_unit_shortname')
        ->where('productunitconversions.tenant_id', $searchMainTanant->id)
        ->get();

    $formattedConversions = $getconversions->map(function ($conversion) {
        return [
            'base_unit_name' => $conversion->base_unit_name,
            'secondary_unit_name' => $conversion->secondary_unit_name ,
            'conversion_rate' => $conversion->conversion_rate
        ];
    });

    return response()->json([
        'product_base_units' => $baseUnitName,
        'productconversions' => $formattedConversions
    ], 200);
}
    
    
}


