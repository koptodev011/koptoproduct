<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SalesController extends Controller
{
    public function addSaleInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "sale_type" => "nullable|string",
            "party_id" => "nullable|integer",
            "billing name"=>"nullable|string",
            "phone_number"=>"nullable|string",
            "po_number"=>"required|numeric",
            "po_date"=>"required|string",
            "product_id"=>"required|integer",
            "quantity"=>"required|integer",
            "amount"=>"required|integer",
            // "unit_id"=>"required|integer",
            "priceperunit"=>"required|integer",
            "discount_percentage"=>"nullable|integer",
            // "discount_amount"=>"nullable|integer",
            "tax_percentage"=>"nullable|integer",
            // "tax_amount"=>"nullable|integer",
            "received_amount"=>"required|integer",
            "payment_type"=>"required|integer",
            "sale_description"=>"nullable|string",
            "sale_image"=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            "user_id"=>"required|integer",
        ]);
        dd($request->all());
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
    
        return response()->json([
            'message' => 'Sale invoice created successfully',
        ], 200);
    }
    
}
