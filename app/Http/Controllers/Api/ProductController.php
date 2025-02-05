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

class ProductController extends Controller
{
   
   
    public function addProduct(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product_name' => 'nullable|string',
            'product_hsn' => 'required',
            'base_unit_id' => 'required',
            'secondary_unit_id' => 'nullable',
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
        
        $unitconversion = new Productunitconversion();
        $unitconversion->product_base_unit_id = $request->base_unit_id;
        $unitconversion->product_secondary_unit_id = $request->secondary_unit_id;
        $unitconversion->conversion_rate = $request->conversion_rate;
        $unitconversion->save();

        

        $product = new Product();
        $product->user_id = $user->id;
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
        $stock->product_id = $product->id;
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
        $productImage->product_image = $path;
        $productImage->save();
    }  
    }
    return response()->json(['message' => 'Product created successfully'], 200);
}




public function getProducts(Request $request)
{
    $user = Auth::user();
    $products = Product::where('user_id', $user->id)
        ->with([ 
            'pricing',       
            'wholesalePrice', 
            'stock',        
            'onlineStore',
            'productUnitConversion',
            'images'
        ])
        ->get();

    if ($products->isEmpty()) {
        return response()->json(['message' => 'No products found'], 404);
    }

    return response()->json(['products' => $products], 200);
}








public function editProdutDetails(Request $request) {
    
    $validator = Validator::make($request->all(), [
        'product_id' => 'required',
        'product_name' => 'nullable|string',
        'product_hsn' => 'nullable',
        'base_unit_id' => 'required|numeric',
        'secondary_unit_id' => 'nullable|numeric',
        'conversion_rate' => 'nullable|numeric',
        'description' => 'nullable|string',    
        'mrp' => 'nullable|numeric',
        'product_category'=> 'nullable|numeric',
        'assign_code'=> 'required',

        // sale price
        'sale_price'=> 'required|numeric',
        'sale_withorwithouttax'=> 'required|numeric',
        'discount_amount'=> 'nullable|numeric',
        'discount_percentageoramount'=> 'nullable|numeric',

        // wholesale price
        'wholesale_price'=> 'nullable|numeric',
        'wholesale_withorwithouttax'=> 'nullable|numeric',
        'wholesale_min_quantity'=> 'nullable|numeric',
        'purchese_price'=> 'required|numeric',
        'purchese_withorwithouttax'=> 'required|numeric',
        'tax_id'=> 'required|numeric',
        
        // stock
        'opening_stock'=> 'nullable|numeric',
        'at_price'=> 'nullable|numeric',
        'min_stock'=> 'nullable|numeric',
        'location'=> 'nullable|string',

        // online store
        'online_store_price'=> 'nullable|numeric',
        'online_store_product_description'=> 'nullable|string',

        // product images
        'product_images' => 'required|array',
        'product_images.*' => 'image|mimes:jpeg,png,jpg,gif',
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

    DB::table('productimages')->where('product_id', $product->id)->delete();
    if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $image) {
            $imagePath = $image->store('product_images', 'public');
            DB::table('productimages')->insert([
                'product_id' => $product->id,
                'product_image' => $imagePath
            ]);
        }
    }

    $unitconversion = $product->productUnitConversion;
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







    // public function assignCode(){
    //     $randomNumber = mt_rand(10000000000, 99999999999);
    //     $searchforuniquecode = Product::where('item_code',$randomNumber)->first();
    //     if($searchforuniquecode){
    //         dd("number was found");
    //     }else{
    //         dd("number was not found");
    //     }
    //     return response()->json(['code' => $randomNumber], 200);
    // }

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
        $product = Product::where('id', $request->product_id)
            ->where('user_id', $user->id)
            ->with([
                'productUnitConversion',
                'pricing',
                'wholesalePrice',
                'stock',
                'onlineStore',
                'images'
            ])
            ->first();
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $salePrice = $product->pricing->sale_price ?? 0;
        $stockQuantity = $product->stock->product_stock ?? 0;
        $stockValue = $salePrice * $stockQuantity;
    
        $product->stock_value = $stockValue;
    
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
        if($request->addorreduct_product_stock==1){
            $stock->product_stock = $stock->product_stock + $request->stock;
        }
        else{
            $stock->product_stock = $stock->product_stock - $request->stock;
            if($stock->product_stock < 0){
                $stock->product_stock = 0;
            }
        }
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



    // Caterories section
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
        $productcategories = Productcategory::where('is_delete', false)->get();
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



    
    public function updateBaseUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'product_base_unit_id' => 'required|numeric',
            'product_base_unit' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $productbaseunit = Productbaseunit::findOrFail($request->product_base_unit_id);
        $productbaseunit->update([
            'product_base_unit' => $request->product_base_unit,
        ]);
        return response()->json(['message' => 'Product base unit updated successfully'], 200);
    }


    public function getBaseUnit(){
        $productbaseunits = Productbaseunit::where('is_delete', false)->get();
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
        $validator = Validator::make($request->all(), [
            'base_unit_id' => 'required|exists:productbaseunits,id',
            'secondary_unit_id' => 'required|exists:productbaseunits,id',
            'conversion_rate' => 'required|numeric'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $unitconversions = new Productunitconversion();
        $unitconversions->product_base_unit_id = $request->base_unit_id;
        $unitconversions->product_secondary_unit_id = $request->secondary_unit_id;
        $unitconversions->conversion_rate = $request->conversion_rate;
        $unitconversions->save();
    
        return response()->json(['message' => 'Product unit conversion created successfully'], 200);
    }

    
}


