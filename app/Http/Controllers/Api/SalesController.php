<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Salespaymenttype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class SalesController extends Controller
{

    // public function salesCalculation(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         "product_id" => "required|integer",
    //         "quantity" => "required|integer",
    //         "unit_id"=>"required|integer",
    //         "priceperunit" => "required|integer",
    //         "discount_percentage" => "nullable|integer",
    //         "discount_amount"=>"nullable|integer",
    //         "tax_percentage" => "nullable|integer",
    //         "tax_amount" => "nullable|integer",
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(),
    //             'data' => $request->all(), // Include request data for debugging
    //         ], 400);
    //     }

    //     dd($request->all());
    
    //     return response()->json([
    //         'message' => 'Sale invoice created successfully',
    //         'data' => $request->all(), // Return data to confirm visibility
    //     ], 200);
    // }

    public function addSaleInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "sale_type" => "nullable|string",
            "party_id" => "nullable|integer",
            "billing_name" => "nullable|string",
            "phone_number" => "nullable|string",
            "po_number" => "required|numeric",
            "po_date" => "required|string",
            "product_id" => "required|integer",
            "quantity" => "required|integer",
            "amount" => "required|integer",
            "priceperunit" => "required|integer",
            "discount_percentage" => "nullable|integer",
            "tax_percentage" => "nullable|integer",
            "received_amount" => "required|integer",
            "payment_type" => "required|integer",
            "sale_description" => "nullable|string",
            "sale_image" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "user_id" => "required|integer",
        ]);
        $user = auth()->user();

        $sale= new Sale();
        $sale->sale_type = $request->sale_type;
        $sale->party_id = $request->party_id;
        $sale->billing_name = $request->billing_name;
        $sale->phone_number = $request->phone_number;
        $sale->po_number = $request->po_number;
        $sale->po_date = $request->po_date;
        $sale->received_amount = $request->received_amount;
        $sale->payment_type_id = $request->payment_type;
        $sale->sale_description = $request->sale_description;
        $sale->sale_image = $request->sale_image;
        $sale->user_id = $user->id;
        $sale->status = "sale";
        $sale->save();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => $request->all(),
            ], 400);
        }
        return response()->json([
            'message' => 'Sale invoice created successfully',
            'data' => $request->all(),
        ], 200);
    }
    
    public function addSalesPaymentType(Request $request){
        $validator = Validator::make($request->all(), [
            "payment_type" => "required|string",
        ]);
        $salespaymenttype = new Salespaymenttype();
        $salespaymenttype->sales_payment_type = $request->payment_type;
        $salespaymenttype->save();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => $request->all(),
            ], 400);
        }
        return response()->json([
            'message' => 'Payment type created successfully',
            'data' => $request->all(),
        ], 200);
    }

  
    public function getSalesPaymentType(){
        $salespaymenttype = Salespaymenttype::all();
        return response()->json($salespaymenttype, 200);
    }
    
}
