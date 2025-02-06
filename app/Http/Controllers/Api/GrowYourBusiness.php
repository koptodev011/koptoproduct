<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Cart;
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

  





// public function addToCart(Request $request) {
//     $validator = Validator::make($request->all(), [
//         'product_id' => 'required|exists:products,id',
//         'method' => 'nullable',
//         'key' => 'required',
//         'tenant_id' => 'required'
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'message' => 'Validation failed',
//             'errors' => $validator->errors()
//         ], 400);
//     }

//     // Check if the item already exists in the cart
//     $cartitem = Cart::where('unique_key', $request->key)
//                     ->where('product_id', $request->product_id)
//                     ->where('tenant_id', $request->tenant_id)
//                     ->first();
//     $searchproducts = Product::where('id', $request->product_id)
//                     ->where('isonlineproduct', 1)
//                     ->with([ 
//                         'pricing',       
//                         'wholesalePrice', 
//                         'stock',        
//                         'onlineStore',
//                         'productUnitConversion',
//                         'images'
//                     ])->first();

//      if (!$cartitem) {
//         $cartitem = new Cart();
//         $cartitem->product_id = $request->product_id;
//         $cartitem->unique_key = $request->key;
//         $cartitem->tenant_id = $request->tenant_id;
//         $cartitem->quantity = 1;
//         $cartitem->save();
//     } else {
//         if ($request->method == 'add') {
//             $cartitem->update([
//                 'quantity' => $cartitem->quantity + 1,
//                 'product_amount' => $cartitem->product_amount + $searchproducts->pricing->selling_price,
                
//             ]);

//         } elseif ($request->method == 'substrate') {
//             $cartitem->update(['quantity' => $cartitem->quantity - 1]);
//             if ($cartitem->quantity <= 0) {
//                 $cartitem->delete();
//             }
//         }
//     }

//     return response()->json(['message' => 'Cart updated successfully']);
// }

public function addToCart(Request $request) {
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'method' => 'nullable',
        'key' => 'required',
        'tenant_id' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    // Check if the item already exists in the cart
    $cartitem = Cart::where('unique_key', $request->key)
                    ->where('product_id', $request->product_id)
                    ->where('tenant_id', $request->tenant_id)
                    ->first();
    // if(!$cartitem){
    //     return response()->json(['message' => 'Cart not found'], 404);
    // }
                    
    $searchproducts = Product::where('id', $request->product_id)
                    ->where('isonlineproduct', 1)
                    ->with([ 
                        'pricing',       
                        'wholesalePrice', 
                        'stock',        
                        'onlineStore',
                        'productUnitConversion',
                        'images'
                    ])->first();
    
    

    if (!$searchproducts) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    if (!$cartitem) {
        $cartitem = new Cart();
        $cartitem->product_id = $request->product_id;
        $cartitem->unique_key = $request->key;
        $cartitem->tenant_id = $request->tenant_id;
        $cartitem->quantity = 1;
        $cartitem->product_amount = $searchproducts->pricing->mrp ?? $searchproducts->pricing->selling_price;
        $cartitem->save();
    } else {
        if ($request->method == 'add') {
            $priceToAdd = $searchproducts->mrp ?? $searchproducts->pricing->sale_price;
            $cartitem->update([
                'quantity' => $cartitem->quantity + 1,
                'product_amount' => $cartitem->product_amount + $priceToAdd,
            ]);

        } elseif ($request->method == 'substrate') {
            $cartitem->update(['quantity' => $cartitem->quantity - 1]);
            if ($cartitem->quantity <= 1) {
                $cartitem->delete();
                return response()->json(['message' => 'Cart is Empty', 'cart' => $cartitem], 200);
            }
        }
    }

    return response()->json(['message' => 'Cart updated successfully', 'cart' => $cartitem], 200);
}





}
