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
use App\Models\Productonlinestore;
use App\Models\Productstockadjectment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
   
   
    public function addProduct(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_hsn' => 'required',
            'base_unit_id' => 'required',
            'secondary_unit_id' => 'required',
            'conversion_rate' => 'required',
            'description' => 'required',    
            'mrp' => 'required',
            'product_category'=> 'required',
            'assign_code'=> 'required',

            // sale price
            'sale_price'=> 'required',
            'sale_withorwithouttax'=> 'required',
            'discount_amount'=> 'required',
            'discount_percentageoramount'=> 'required',

            // wholesale price
            'wholesale_price'=> 'required',
            'wholesale_withorwithouttax'=> 'required',
            'wholesale_min_quantity'=> 'required',
            'purchese_price'=> 'required',
            'purchese_withorwithouttax'=> 'required',
            'tax_id'=> 'required',
            // stock
            'opening_stock'=> 'required',
            'at_price'=> 'required',
            'min_stock'=> 'required',
            'location'=> 'required',

            //online store
            'online_store_price'=> 'required',
            'online_store_product_description'=> 'required',

            //product images
            // 'product_images' => 'required|array',
            // 'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',


        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if($request->base_unit_id == $request->secondary_unit_id){
            return response()->json(['message' => 'Base unit and secondary unit cannot be same'], 400);
        }
        $product = new Product();
        $product->user_id = $user->id;
        $product->product_name = $request->product_name;
        $product->product_hsn = $request->product_hsn;
        $product->item_code = $request->assign_code;
        $product->description = $request->description;
        $product->mrp = $request->mrp;
        $product->product_base_unit = $request->base_unit_id;
        $product->category_id = $request->product_category;
        $product->tax_id = $request->tax_id;
        $product->save();

        $unitconversion = new Productunitconversion();
        $unitconversion->product_base_unit_id = $request->base_unit_id;
        $unitconversion->product_secondary_unit_id = $request->secondary_unit_id;
        $unitconversion->conversion_rate = $request->conversion_rate;
        $unitconversion->product_id = $product->id;
        $unitconversion->save();
        
        $saleprice = new Productpricing();
        $saleprice->sale_price = $request->sale_price;
        $saleprice->withorwithouttax = $request->sale_withorwithouttax;
        $saleprice->discount = $request->discount_amount;
        $saleprice->percentageoramount = $request->discount_percentageoramount;
        $saleprice->product_id = $product->id;
        $saleprice->save();


        $wholesaleprice = new Productwholesaleprice();
        $wholesaleprice->whole_sale_price = $request->wholesale_price;
        $wholesaleprice->withorwithouttax = $request->wholesale_withorwithouttax;
        $wholesaleprice->wholesale_min_quantity = $request->wholesale_min_quantity;
        $wholesaleprice->product_id = $product->id;
        $wholesaleprice->save();

        $stock = new Productstock();
        $stock->product_stock = $request->opening_stock;
        $stock->at_price = $request->at_price;
        $stock->min_stock = $request->min_stock;
        $stock->location = $request->location;
        $stock->product_id = $product->id;
        $stock->save();

        $onlinestore = new Productonlinestore();
        $onlinestore->online_store_price = $request->online_store_price;
        $onlinestore->online_product_description = $request->online_store_product_description;
        $onlinestore->product_id = $product->id;
        $onlinestore->save();

    //     if ($request->has('product_images')) {
    // foreach ($request->file('product_images') as $image) {
    //     $path = $image->store('product_images', 'public');
    //     $productImage = new ProductImage();
    //     $productImage->product_id = $product->id;
    //     $productImage->image_path = $path;
    //     $productImage->save();
    // }

        return response()->json(['message' => 'Product created successfully'], 200);
    // }
}







    public function getProducts(Request $request)
{
    $user = Auth::user();
    $products = Product::where('user_id', $user->id)
        ->with([
            'unitConversion', 
            'pricing',       
            'wholesalePrice', 
            'stock',        
            'onlineStore'   
        ])
        ->get();

    if ($products->isEmpty()) {
        return response()->json(['message' => 'No products found'], 404);
    }

    return response()->json(['products' => $products], 200);
}








public function editProdutDetails(Request $request){

    $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'product_name' => 'required',
            'product_hsn' => 'required',
            'base_unit_id' => 'required',
            'secondary_unit_id' => 'required',
            'conversion_rate' => 'required',
            'description' => 'required',    
            'mrp' => 'required',
            'product_category'=> 'required',
            'assign_code'=> 'required',

            // sale price
            'sale_price'=> 'required',
            'sale_withorwithouttax'=> 'required',
            'discount_amount'=> 'required',
            'discount_percentageoramount'=> 'required',

            // wholesale price
            'wholesale_price'=> 'required',
            'wholesale_withorwithouttax'=> 'required',
            'wholesale_min_quantity'=> 'required',
            'purchese_price'=> 'required',
            'purchese_withorwithouttax'=> 'required',
            'tax_id'=> 'required',
            // stock
            'opening_stock'=> 'required',
            'at_price'=> 'required',
            'min_stock'=> 'required',
            'location'=> 'required',

            //online store
            'online_store_price'=> 'required',
            'online_store_product_description'=> 'required',

            //product images
            // 'product_images' => 'required|array',
            // 'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $product = Product::find($request->product_id);
    
    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    $product->update([
        'product_name' => $request->product_name,
        'product_hsn' => $request->product_hsn,
        'product_base_unit' => $request->base_unit_id,
        'description' => $request->description,
        'mrp' => $request->mrp,
        'category_id' => $request->product_category,
        'item_code' => $request->assign_code,
        'tax_id' => $request->tax_id
    ]);

    $unitconversion = Productunitconversion::where('product_id', $product->id)->first();
   
    $unitconversion->update([
        'product_base_unit_id' => $request->base_unit_id,
        'product_secondary_unit_id' => $request->secondary_unit_id,
        'conversion_rate' => $request->conversion_rate
    ]);

    $saleprice = Productpricing::where('product_id', $product->id)->first();
    $saleprice->update([
        'sale_price' => $request->sale_price,
        'withorwithouttax' => $request->sale_withorwithouttax,
        'discount' => $request->discount_amount,
        'percentageoramount' => $request->discount_percentageoramount
    ]);

    $wholesaleprice = Productwholesaleprice::where('product_id', $product->id)->first();
    $wholesaleprice->update([
        'whole_sale_price' => $request->wholesale_price,
        'withorwithouttax' => $request->wholesale_withorwithouttax,
        'wholesale_min_quantity' => $request->wholesale_min_quantity
    ]);

    $stock = Productstock::where('product_id', $product->id)->first();
    $stock->update([
        'product_stock' => $request->opening_stock,
        'at_price' => $request->at_price,
        'min_stock' => $request->min_stock,
        'location' => $request->location
    ]);

    $onlinestore = Productonlinestore::where('product_id', $product->id)->first();
    $onlinestore->update([
        'online_store_price' => $request->online_store_price,
        'online_product_description' => $request->online_store_product_description
    ]);
    
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
            Productunitconversion::where('product_id', $product_id)->delete();
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


    public function assignCode(){
        $randomNumber = mt_rand(10000000000, 99999999999);
        return response()->json(['code' => $randomNumber], 200);
    }


    public function getProductDetails(Request $request){
        $user = Auth::user();

        $product = Product::where('id', $request->product_id)
        ->where('user_id', Auth::user()->id)
            ->with([
                'unitConversion', 
                'pricing',       
                'wholesalePrice', 
                'stock',        
                'onlineStore'   
            ])
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['product' => $product], 200);
    }




    public function adjectProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'addorreduct_product_stock' => 'required',
            'product_id' => 'required',
            'stock'=> 'required',
            'priceperunit'=> 'required',
            'details'=> 'required'
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
        $productstockadjectment->save();

        $stock = Productstock::where('product_id', $request->product_id)->first();
        $stock->product_stock = $stock->product_stock + $request->stock;
        $stock->at_price = $request->priceperunit;
        $stock->save();

        return response()->json(['message' => 'Product stock adjected successfully'], 200);

        
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




    public function addProductCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'product_category' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $productcategory = new Productcategory();
        $productcategory->product_category = $request->product_category;
        $productcategory->save();
        return response()->json(['message' => 'Product category created successfully'], 200);
    }





    public function getProductCategory(){
        $productcategories = Productcategory::all();
        return response()->json($productcategories, 200);
    }

    public function addBaseUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'product_base_unit' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $productbaseunit = new Productbaseunit();
        $productbaseunit->product_base_unit = $request->product_base_unit;
        $productbaseunit->save();
        return response()->json(['message' => 'Product base unit created successfully'], 200);
    }





    public function getBaseUnit(){
        $productbaseunits = Productbaseunit::all();
        return response()->json($productbaseunits, 200);
    }
     
}
