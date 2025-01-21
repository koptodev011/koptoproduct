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
use App\Models\User;
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
            'business_name' => 'nullable|string',
            'business_type' => 'nullable|numeric|exists:business_types,id',
            'business_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'business_category' => 'nullable|numeric|exists:business_categories,id',
            'TIN_number' => 'nullable|string',
            'state' => 'nullable|numeric|exists:states,id',
            'business_email' => 'nullable|email',
            'pin_code' => 'nullable|numeric|digits:6',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',   
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        $user = auth()->user();
        $user->update([
            'name' => $request->business_name,
            // 'email' => $request->business_email,
        ]);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 400);
        }
        
        $tenant = Tenant::where('user_id', $user->id)
        ->where('id',$request->tenant_id)->first();

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }

        if ($request->hasFile('business_logo')) {
            if ($tenant->business_logo) {
                Storage::delete(str_replace('/storage/', '', $tenant->business_logo));
            }
            $logoPath = $request->file('business_logo')->store('logos', 'public');
            $tenant->business_logo = Storage::url($logoPath);
        }
    
        if ($request->hasFile('business_signature')) {
            if ($tenant->business_signature) {
                Storage::delete(str_replace('/storage/', '', $tenant->business_signature));
            }
            $signaturePath = $request->file('business_signature')->store('signatures', 'public');
            $tenant->business_signature = Storage::url($signaturePath);
        }
        $tenant->update($request->except(['business_logo', 'business_signature']));
    
        return response()->json([
            'message' => 'Tenant details updated successfully',
            'tenant' => $tenant
        ], 200);
    }



    public function addNewFirm(Request $request){

        $validator = Validator::make($request->all(), [
            'business_name' => 'nullable|string',
            'business_type' => 'nullable|numeric|exists:business_types,id',
            'business_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'business_category' => 'nullable|numeric|exists:business_categories,id',
            'TIN_number' => 'nullable|string',
            'state' => 'nullable|numeric|exists:states,id',
            'business_email' => 'nullable|email',
            'pin_code' => 'nullable|numeric|digits:6',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',   
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
            ], 400);
        }

        Tenant::where('user_id', $user->id)
            ->where('isactive', 1)
            ->update(['isactive' => 0]);

        $tenant = new Tenant();
        $tenant->user_id = $user->id;
        $tenant->business_name = $request->business_name;
        $tenant->business_type = $request->business_type;
        $tenant->business_address = $request->business_address;
        $tenant->phone_number = $request->phone_number;
        $tenant->business_category = $request->business_category;
        $tenant->TIN_number = $request->TIN_number;
        $tenant->state = $request->state;
        $tenant->business_email = $request->business_email;
        $tenant->pin_code = $request->pin_code;
        $tenant->business_logo = Storage::url($request->file('business_logo')->store('logos', 'public'));
        $tenant->business_signature = Storage::url($request->file('business_signature')->store('signatures', 'public'));
        $tenant->save();


        return response()->json([
            'message' => 'Tenant added successfully',
            'tenant' => $tenant
        ], 200);
    }

    public function switchFirm(Request $request){
        $user = auth()->user(); 
       $validator = Validator::make($request->all(), [
           'tenant_id' => 'required|numeric',
       ]);


       if ($validator->fails()) {
           return response()->json([
               'message' => 'Validation failed',
               'errors' => $validator->errors()
           ], 400);
       }

       Tenant::where('user_id', $user->id)
       ->where('isactive', 1)
       ->update(['isactive' => 0]);

       $searchTenant = Tenant::where('user_id', $user->id)
       ->where('id', $request->tenant_id)
       ->first();
       $searchTenant->update(['isactive' => 1]);

       return response()->json([
           'message' => 'Tenant switched successfully'
       ], 200);
    }
    
    public function getActiveTanent(){
        $user = auth()->user(); 
        $tenants = Tenant::with(['user', 'businesstype', 'businesscategory', 'state'])->where('user_id', $user->id)->where('isactive', 1)->get();
        return response()->json([
            'message' => 'Tenant details retrieved successfully',
            'tenants' => $tenants
        ], 200);
    }
    
    public function deleteParty(){
        dd("Working");
    }
}
