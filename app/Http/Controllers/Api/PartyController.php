<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Party;
use App\Models\ShippingAddress;
use App\Models\Partyaddationalfields;
use App\Models\User;
use App\Models\Partygroup;
use App\Models\Partyschedulereminder;
use App\Models\Tenant;

class PartyController extends Controller
{
    public function getParties() {
        $user = auth()->user();
        $parties = Party::where('user_id', $user->id)
            ->with(['shippingAddresses', 'additionalFields'])
            ->get();
    
        if ($parties->isEmpty()) {
            return response()->json([
                'message' => 'Parties not found or inactive'
            ], 404);
        }
        return response()->json($parties, 200);
    }


    public function getParty(Request $request) {
        $validator = Validator::make($request->all(), [
            'party_id' => 'required|numeric'
        ]);
        $user = auth()->user();
        $parties = Party::where('user_id', $user->id)
        ->with(['shippingAddresses', 'additionalFields'])
        ->where('id', $request->party_id)->first();

        if (!$parties) {
            return response()->json([
                'message' => 'Party not found or inactive'
            ], 404);
        }
        return response()->json($parties, 200);
    }

    public function getPartyGroup(){
        $partygroup = Partygroup::all();
        return response()->json($partygroup, 200);
    }

 


    public function addParty(Request $request) {
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
            'addational_fields' => 'required|array',
            'addational_fields.*.addational_field_name' => 'required|string',
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
            ->where('isactive', 1)
            ->first();
    
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found or inactive'
            ], 404);
        }
    
        $party = new Party();
        $party->party_name = $request->party_name;
        $party->TIN_number = $request->tin_number;
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
                'party_id' => $party->id,
            ]);
        }
    
        foreach ($request->addational_fields as $field) {
           
            Partyaddationalfields::create([
                'addational_field_name' => $field['addational_field_name'],
                'addational_field_data' => $field['addational_field_data'],
                'party_id' => $party->id,
            ]);
        }
    
        return response()->json(['message' => 'Party created successfully'], 200);
    }


    public function addPartyGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string',
        ]);

        $partygroup = new Partygroup();
        $partygroup->group_name = $request->group_name;
        $partygroup->save();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $user = auth()->user();
        return response()->json(['message' => 'Party group created successfully'], 200);
    }


    public function updatePartyDetails(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'party_id' => 'required|numeric',
        'party_name' => 'required|string',
        'tin_number' => 'required|string',
        'phone_number' => 'required|numeric|digits:10',
        'email' => 'required|email|unique:users,email,' . $request->party_id,
        'billing_address' => 'nullable|string',
        'opening_balance' => 'nullable|numeric',
        'topayortorecive' => 'nullable|boolean',
        'creditlimit' => 'nullable|numeric',
        'shipping_addresses' => 'required|array',
        'shipping_addresses.*.shipping_addresses' => 'required|string',
        'addational_fields' => 'required|array',
        'addational_fields.*.addational_field_name' => 'required|string',
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
        ->where('isactive', 1)
        ->first();

    if (!$tenant) {
        return response()->json([
            'message' => 'Tenant not found or inactive'
        ], 404);
    }

    $party = Party::where('id', $request->party_id)
        ->where('tenant_id', $tenant->id)
        ->where('user_id', $user->id)
        ->first();

    if (!$party) {
        return response()->json([
            'message' => 'Party not found or inactive'
        ], 404);
    }

    // Updating party details
    $party->update([
        'party_name' => $request->party_name,
        'tin_number' => $request->tin_number,
        'phone_number' => $request->phone_number,
        'email' => $request->email,
        'billing_address' => $request->billing_address,
        'opening_balance' => $request->opening_balance,
        'topayortorecive' => $request->topayortorecive,
        'creditlimit' => $request->creditlimit,
        'group_id' => $request->group_id,
    ]);

    foreach ($request->shipping_addresses as $shippingAddress) {
        $shippingAddressRecord = $party->shippingAddresses()->where('id', $shippingAddress['shipping_addresses_id'])->first();
      
        if ($shippingAddressRecord) {
            $shippingAddressRecord->update([
                'shipping_address' => $shippingAddress['shipping_addresses']
            ]);
        } else {
            $party->shippingAddresses()->create([
                'party_id' => $party->id,
                'shipping_address' => $shippingAddress['shipping_addresses']
            ]);
        }
    }

    foreach ($request->addational_fields as $additionalField) {
        $party->additionalFields()->updateOrCreate(
            ['id' => $additionalField['id'] ?? null],
            [
                'addational_field_name' => $additionalField['addational_field_name'],
                'addational_field_data' => $additionalField['addational_field_data']
            ]
        );
    }

    return response()->json([
        'message' => 'Party details updated successfully',
        'data' => $party
    ]);
}


public function scheduleReminder(Request $request) {
    $validator = Validator::make($request->all(), [
        'party_id' => 'required',
        'reminder_frequency' => 'required',
        'send_copy' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    $setreminder = new Partyschedulereminder();
    $setreminder->party_id = $request->party_id;
    $setreminder->reminder_frequency = $request->reminder_frequency;
    $setreminder->send_copy = $request->send_copy;
    $setreminder->save();
    return response()->json([
        'message' => 'Reminder scheduled successfully',
        'data' => $setreminder
    ]);
}


public function getPartyReminder(Request $request) {
    $validator = Validator::make($request->all(), [
        'party_id' => 'required',
    ]);
    $setreminder = Partyschedulereminder::where('party_id', $request->party_id)->first();
    if (!$setreminder) {
        return response()->json([
            'message' => 'Reminder not found'
        ], 404);
    }
    return response()->json([
        'message' => 'Reminder found',
        'data' => $setreminder
    ]);
}

    



    

}

