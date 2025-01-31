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
        $tenants = User::with('tenants','tenants.businesstype','tenants.businesscategory','tenants.state')->where('id', $user->id)->get();
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
            'business_types_id' => 'nullable|numeric|exists:business_types,id',
            'business_address' => 'nullable|string',
            'business_email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'business_categories_id' => 'nullable|numeric|exists:business_categories,id',
            'TIN_number' => 'nullable|string',
            'state_id' => 'nullable|numeric|exists:states,id',
            'business_email' => 'nullable|email',
            'pin_code' => 'nullable|numeric|digits:6',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',   
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
        $tenant = Tenant::where('user_id', $user->id)
        ->where('id',$request->tenant_id)->first();

        $tenant->update([
            'business_name' => $request->business_name,
            'business_types_id' => $request->business_types_id,
            'business_address' => $request->business_address,
            'phone_number' => $request->phone_number,
            'business_categories_id' => $request->business_categories_id,
            'TIN_number' => $request->TIN_number,
            'state_id' => $request->state_id,
            'business_email' => $request->business_email,
            'pin_code' => $request->pin_code,
        ]);

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }

        if ($request->hasFile('business_logo')) {
            if ($tenant->business_logo) {
                $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
                if (Storage::disk('public')->exists($previousLogoPath)) {
                    Storage::disk('public')->delete($previousLogoPath);
                }
            }
            $logoPath = $request->file('business_logo')->store('logos', 'public');
            $tenant->business_logo = Storage::url($logoPath);
        } elseif ($request->input('business_logo') === null && $tenant->business_logo) {
            $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
            if (Storage::disk('public')->exists($previousLogoPath)) {
                Storage::disk('public')->delete($previousLogoPath);
            }
            $tenant->business_logo = null;
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


    // public function updateMainBranchDetails(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'tenant_id' => 'required|numeric',
    //         'business_name' => 'required|string',
    //         'business_types' => 'nullable|numeric|exists:business_types,id',
    //         'business_address' => 'nullable|string',
    //         'business_email' => 'nullable|email',
    //         'phone_number' => 'nullable|string',
    //         'business_categories' => 'nullable|numeric|exists:business_categories,id',
    //         'TIN_number' => 'nullable|string',
    //         'state' => 'nullable|numeric|exists:states,id',
    //         'business_email' => 'nullable|email',
    //         'pin_code' => 'nullable|numeric|digits:6',
    //         'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
    //         'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',   
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 400);
    //     }
    //     $user = auth()->user(); 
    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User not authenticated'
    //         ], 400);
    //     }
    //     $tenant = Tenant::where('user_id', $user->id)
    //     ->where('id',$request->tenant_id)->first();

    //     $tenant->update([
    //         'business_name' => $request->business_name,
    //         'business_type' => $request->business_types,
    //         'business_address' => $request->business_address,
    //         'phone_number' => $request->phone_number,
    //         'business_category' => $request->business_categories,
    //         'TIN_number' => $request->TIN_number,
    //         'state' => $request->state,
    //         'business_email' => $request->business_email,
    //         'pin_code' => $request->pin_code,
    //     ]);

    //     if (!$tenant) {
    //         return response()->json([
    //             'message' => 'Tenant not found'
    //         ], 404);
    //     }

    //     if ($request->hasFile('business_logo')) {
    //         if ($tenant->business_logo) {
    //             Storage::delete(str_replace('/storage/', '', $tenant->business_logo));
    //         }
    //         $logoPath = $request->file('business_logo')->store('logos', 'public');
    //         $tenant->business_logo = Storage::url($logoPath);
    //     }
    
    //     if ($request->hasFile('business_signature')) {
    //         if ($tenant->business_signature) {
    //             Storage::delete(str_replace('/storage/', '', $tenant->business_signature));
    //         }
    //         $signaturePath = $request->file('business_signature')->store('signatures', 'public');
    //         $tenant->business_signature = Storage::url($signaturePath);
    //     }
    //     $tenant->update($request->except(['business_logo', 'business_signature']));
    
    //     return response()->json([
    //         'message' => 'Tenant details updated successfully',
    //         'tenant' => $tenant
    //     ], 200);
    // }









    // public function addNewFirm(Request $request){

    //     $validator = Validator::make($request->all(), [
    //         'business_name' => 'nullable|string',
    //         'business_type_id' => 'nullable|numeric|exists:business_types,id',
    //         'business_address' => 'nullable|string',
    //         'phone_number' => 'nullable|string',
    //         'business_category_id' => 'nullable|numeric|exists:business_categories,id',
    //         'TIN_number' => 'nullable|string',
    //         'state_id' => 'nullable|numeric|exists:states,id',
    //         'business_email' => 'nullable|email',
    //         'pin_code' => 'nullable|numeric|digits:6',
    //         'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
    //         'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',   
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 400);
    //     }
    //     $user = auth()->user(); 
    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User not authenticated'
    //         ], 400);
    //     }

    //     Tenant::where('user_id', $user->id)
    //         ->where('isactive', 1)
    //         ->update(['isactive' => 0]);

    //     $tenant = new Tenant();
    //     $tenant->user_id = $user->id;
    //     $tenant->business_name = $request->business_name;
    //     $tenant->business_types_id = $request->business_type_id;
    //     $tenant->business_address = $request->business_address;
    //     $tenant->phone_number = $request->phone_number;
    //     $tenant->business_categories_id = $request->business_categories_id;
    //     $tenant->TIN_number = $request->TIN_number;
    //     $tenant->state_id = $request->state_id;
    //     $tenant->business_email = $request->business_email;
    //     $tenant->pin_code = $request->pin_code;
    //     $tenant->business_logo = Storage::url($request->file('business_logo')->store('logos', 'public'));
    //     $tenant->business_signature = Storage::url($request->file('business_signature')->store('signatures', 'public'));
    //     $tenant->save();


    //     return response()->json([
    //         'message' => 'Tenant added successfully',
    //         'tenant' => $tenant
    //     ], 200);
    // }


    
    public function addNewFirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'nullable|string',
            'business_type_id' => 'nullable|numeric|exists:business_types,id',
            'business_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'business_category_id' => 'nullable|numeric|exists:business_categories,id',
            'TIN_number' => 'nullable|string',
            'state_id' => 'nullable|numeric|exists:states,id',
            'business_email' => 'nullable|email|unique:tenants,business_email',
            'pin_code' => 'nullable|numeric|digits:6',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'business_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',   
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
        $tenant->business_types_id = $request->business_type_id;
        $tenant->business_address = $request->business_address;
        $tenant->phone_number = $request->phone_number;
        $tenant->business_categories_id = $request->business_category_id;
        $tenant->TIN_number = $request->TIN_number;
        $tenant->state_id = $request->state_id;
        $tenant->business_email = $request->business_email;
        $tenant->pin_code = $request->pin_code;


        $tenant->business_logo = null;
        $tenant->business_signature = null;
    
        // Store logo file if present
        if ($request->hasFile('business_logo')) {
            $tenant->business_logo = Storage::url($request->file('business_logo')->store('logos', 'public'));
        }
    
        // Store signature file if present
        if ($request->hasFile('business_signature')) {
            $tenant->business_signature = Storage::url($request->file('business_signature')->store('signatures', 'public'));
        }
    
        // Save tenant only once
        $tenant->save();
    
        return response()->json([
            'message' => 'Tenant added successfully',
            'tenant' => $tenant
        ], 200);
    }
    





    public function switchFirm(Request $request){
        $user = auth()->user(); 
       $validator = Validator::make($request->all(), [
           'tenant_id' => 'required|numeric|exists:tenants,id',
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




    
    // public function getActiveTanent(){
    //     $user = auth()->user(); 
    //     $tenants = Tenant::with(['user', 'businesstype', 'businesscategory', 'state'])->where('user_id', $user->id)->where('isactive', 1)->get();
    //     if (!$tenants) {
    //         return response()->json([
    //             'message' => 'No active tenant found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Tenant details retrieved successfully',
    //         'tenants' => $tenants
    //     ], 200);
    // }



    public function getActiveTanent(){
        $user = auth()->user(); 
        $tenants = Tenant::with(['user', 'businesstype', 'businesscategory', 'state'])
                        ->where('user_id', $user->id)
                        ->where('isactive', 1)
                        ->get();
    
        if ($tenants->isEmpty()) {
            return response()->json([
                'message' => 'No active tenant found'
            ], 404);
        }
    
        $tenantsData = $tenants->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name,
                'business_address' => $tenant->business_address,
                'phone_number' => $tenant->phone_number,
                'TIN_number' => $tenant->TIN_number,
                'business_email' => $tenant->business_email,
                'pin_code' => $tenant->pin_code,
                'business_logo' => $tenant->business_logo,
                'business_signature' => $tenant->business_signature,
                'isactive' => $tenant->isactive,
                'user' => [
                    'id' => $tenant->user->id,
                    'role_id' => $tenant->user->role_id,
                    'name' => $tenant->user->name,
                    'email' => $tenant->user->email,
                    'mobile_number' => $tenant->user->mobile_number
                ],
                'businesstype' => [
                    'id' => $tenant->businesstype->id,
                    'business_type' => $tenant->businesstype->business_type
                ],
                'businesscategory' => [
                    'id' => $tenant->businesscategory->id,
                    'business_category' => $tenant->businesscategory->business_category
                ],
                'state' => [
                    'id' => $tenant->state->id,
                    'state' => $tenant->state->state
                ]
            ];
        });
    
        return response()->json([
            'message' => 'Tenant details retrieved successfully',
            'tenants' => $tenantsData
        ], 200);
    }
    
    
    public function deleteParty(){
        dd("Working");
    }
}
