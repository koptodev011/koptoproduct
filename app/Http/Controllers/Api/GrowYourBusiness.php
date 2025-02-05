<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GrowYourBusiness extends Controller
{
    public function convertStoreIntoOnlineStore(Request $request){
        $user = Auth::user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated user'],400);
        }
        $tenants = Tenant::where('user_id',$user->id)->get();
        Tenant::where('user_id', $user->id)->update(['isonlinestore' => 1]);
        return response()->json(['message' => 'Stores converted to online stores successfully.']);
    }


    public function getOnlineProducts(Request $request)
{
    $user = Auth::user();
    $products = Product::where('user_id', $user->id)
        ->where('isonlineproduct', 1)
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
    $uniqueKey = (string) Str::uuid();
    return response()->json(['products' => $products,'key' => $uniqueKey], 200);
}

  





public function addToCart(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'method' => 'nullable',
            'key' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        
    }

}
