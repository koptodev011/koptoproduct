<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Party;
use App\Models\ShippingAddress;
use App\Models\AdditionalField;
use App\Models\User;
use App\Models\Tenant;

class PartyController extends Controller
{
    public function addParty(Request $request){
        $validator = Validator::make($request->all(), [
            'party_name' => 'required|string',
            'tin_number' => 'required|string',
            'phone_number' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:users,email',
            'billing_address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'topayortorecive' => 'nullable|boolean',
            'creditlimit' => 'nullable|numeric',
            'shipping_addresses' => 'required|array',
            'shipping_addresses.*.shipping_addresses' => 'required|string',
            'addatioal_fields' => 'required|array',
            'addatioal_fields.*.addational_field_name' => 'required|string',
            'addatioal_fields.*.addational_field_data' => 'required|string',
            'group_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = auth()->user(); 
        $tenant = Tenant::where('user_id', $user->id)
        ->where('isactive',1)
        ->first();


        $party = new Party();
        $party->party_name = $request->party_name;
        $party->TIN_number = $request->TIN_number;
        $party->email = $request->email;
        $party->phone_number = $request->phone_number;
        $party->billing_address = $request->billing_address;
        $party->opening_balance = $request->opening_balance;
        $party->topayortorecive = $request->topayortorecive;
        $party->creditlimit = $request->creditlimit;
        $party->user_id = $user->id;
        $party->tenant_id = $tenant->id;
        $party->group_id = $request->group_id;
        $party->save();
        
        foreach ($request->shipping_addresses as $shipping_address) {
            ShippingAddress::create([
                'shipping_address' => $shipping_address['shipping_addresses'],
                'user_id' => auth()->id(),
                'party_id' => $party->id,
            ]);
        }
    
        foreach ($request->addatioal_fields as $field) {
            AdditionalField::create([
                'addational_field_name' => $field['addational_field_name'],
                'addational_field_data' => $field['addational_field_data'],
                'party_id' => $party->id,
            ]);
        }
    }


    }

