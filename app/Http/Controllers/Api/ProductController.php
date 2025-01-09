<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Productcategory;
use App\Models\Productbaseunit;

class ProductController extends Controller
{
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
        dd("working");
        $productbaseunits = Productbaseunit::all();
        return response()->json($productbaseunits, 200);
    }
}
