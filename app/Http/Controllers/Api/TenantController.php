<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\TenantController;
use App\Models\Tenant;
use App\Models\Businesstype;
use App\Models\Businesscategory;
use App\Models\State;
use Illuminate\Support\Facades\Storage;
class TenantController extends Controller
{

    
    // public function getAllBranchDetails(){
    //     $user = auth()->user(); 
    //     $tenant = Tenant::where('user_id', $user->id)->get();
    //     return response()->json([
    //         'message' => 'Tenant details retrieved successfully',
    //         'tenant' => $tenant
    //     ], 200);
    // }


    public function getAllBranchDetails()
    {
        $user = auth()->user(); 
        $tenants = Tenant::with(['user', 'businesstype', 'businesscategory', 'state'])->where('user_id', $user->id)->get();
        return response()->json([
            'message' => 'Tenant details retrieved successfully',
            'tenants' => $tenants
        ], 200);
    }


    public function getBusinessCategory(){
        $businesscategory = Businesscategory::all();
        return response()->json($businesscategory, 200);
    }

    public function getBusinessType(){
        $businesstype = Businesstype::all();
        return response()->json($businesstype, 200);
    }

    public function getAllStates(){
        $states= State::all();
        return response()->json($states, 200);
    }

    public function updateMainBranchDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|numeric',
            'business_name' => 'required|string',
            'business_type' => 'required|numeric',
            'business_address' => 'required|string',
            'phone_number' => 'required|numeric|  digits:10',
            'business_category' => 'required|numeric',
            'TIN_number' => 'required|string',
            'state' => 'required|numeric',
            'business_email' => 'required|email',
            'pin_code' => 'required|numeric|digits:6',
            'business_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'business_signature' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',   
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }


    
        $user = auth()->user(); 
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }
        $tenant = Tenant::where('user_id', $user->id)
        ->where('id',$request->tenant_id)->first();

    
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }
        if ($request->hasFile('business_logo')) {
            $logoPath = $request->file('business_logo')->store('logos', 'public');
            $tenant->business_logo = Storage::url($logoPath);
        }
    
        if ($request->hasFile('business_signature')) {
            $signaturePath = $request->file('business_signature')->store('signatures', 'public');
            $tenant->business_signature = Storage::url($signaturePath);
        }
        $tenant->update($request->except(['business_logo', 'business_signature']));
    
        return response()->json([
            'message' => 'Tenant details updated successfully',
            'tenant' => $tenant
        ], 200);
    }
}

