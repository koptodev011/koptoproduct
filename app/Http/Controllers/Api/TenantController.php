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
use App\Models\City;
use App\Models\TenantUnit;
use App\Models\UserTenantUnit;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{



    // public function getAllBranchDetails()
    // {
    //     $user = auth()->user();
        // $tenants = User::with('tenants','tenants.businesstype','tenants.businesscategory','tenants.state')->where('id', $user->id)->get();
        
        // $tenants = UserTenantUnit::where('user_id', $user->id)
        // ->with('tenantable',) // This will load the polymorphic relation
        // ->get();

        // $tenants = UserTenantUnit::where('user_id', $user->id)
        // ->with([
        //     'tenantable' => function ($query) {
        //         $query->with('businesstype', 'businesscategory', 'state', 'city');
        //     }
        // ])
        // ->get();
      
    
    //     return response()->json([
    //         'message' => 'Tenant details retrieved successfully',
    //         'tenants' => $tenants
    //     ], 200);
    // }

    public function getAllBranchDetails()
    {
        $user = auth()->user();
        $tenants = Tenant::with('tenantUnits.businesstype', 'tenantUnits.businesscategory', 'tenantUnits.state', 'tenantUnits.city')
        ->where('user_id', $user->id)
        ->get();
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


    public function getCitiesOfState(Request $request){
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $cities = City::where('state_id', $request->state_id)->get();
        $mappedCities = $cities->map(function($city) {
            return [
                'id' => $city->id,
                'city_name' => $city->city_name,
            ];
        });
        return response()->json($mappedCities, 200);
    }


    public function getCityPinCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $city = City::find($request->city_id);
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }
        return response()->json([
            'id' => $city->id,
            'pin_code' => $city->pin_code,
        ], 200);
    }
    







    // public function updateMainBranchDetails(Request $request){
      
    //     $validator = Validator::make($request->all(), [
    //         'tenant_id' => 'required|numeric|exists:tenants,id',
    //         'business_name' => 'required|string',
    //         'business_types_id' => 'nullable|numeric|exists:business_types,id',
    //         'business_address' => 'nullable|string',
    //         'business_email' => 'nullable|email',
    //         'phone_number' => 'nullable|string',
    //         'business_categories_id' => 'nullable|numeric|exists:business_categories,id',
    //         'TIN_number' => 'nullable|string',
    //         'state_id' => 'nullable|numeric|exists:states,id',
    //         'business_email' => 'nullable|email',
    //         'pin_code' => 'nullable|numeric|digits:6',
    //         'city_id' => 'nullable|numeric',
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
    //     if (!$tenant) {
    //         return response()->json([
    //             'message' => 'Tenant not found'
    //         ], 404);
    //     }

    //     $tenantUnit = TenantUnit::where('tenant_id', $tenant->id)->first();
    //     if (!$tenantUnit) {
    //         return response()->json([
    //             'message' => 'Tenant not found'
    //         ], 404);
    //     }

    //     $tenant->update([
    //         'business_name' => $request->business_name,
    //         'business_types_id' => $request->business_types_id,
    //         'business_address' => $request->business_address,
    //         'phone_number' => $request->phone_number,
    //         'business_categories_id' => $request->business_categories_id,
    //         'TIN_number' => $request->TIN_number,
    //         'state_id' => $request->state_id,
    //         'business_email' => $request->business_email,
    //         'pin_code' => $request->pin_code,
    //         'city_id' => $request->city_id
    //     ]);

    //     $tenantUnit->update([
    //         'business_name' => $request->business_name,
    //         'business_types_id' => $request->business_types_id,
    //         'business_address' => $request->business_address,
    //         'phone_number' => $request->phone_number,
    //         'business_categories_id' => $request->business_categories_id,
    //         'TIN_number' => $request->TIN_number,
    //         'state_id' => $request->state_id,
    //         'business_email' => $request->business_email,
    //         'pin_code' => $request->pin_code,
    //         'city_id' => $request->city_id
    //     ]);
        
      

        

    //     if ($request->hasFile('business_logo')) {
    //         if ($tenant->business_logo) {
    //             $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
    //             if (Storage::disk('public')->exists($previousLogoPath)) {
    //                 Storage::disk('public')->delete($previousLogoPath);
    //             }
    //         }
    //         $logoPath = $request->file('business_logo')->store('logos', 'public');
    //         $tenant->business_logo = Storage::url($logoPath);
    //     } elseif ($request->input('business_logo') === null && $tenant->business_logo) {
    //         $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
    //         if (Storage::disk('public')->exists($previousLogoPath)) {
    //             Storage::disk('public')->delete($previousLogoPath);
    //         }
    //         $tenant->business_logo = null;
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






    public function updateMainBranchDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|numeric|exists:tenant_units,id',
            'business_name' => 'required|string',
            'business_types_id' => 'nullable|numeric|exists:business_types,id',
            'business_address' => 'nullable|string',
            'business_email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'business_categories_id' => 'nullable|numeric|exists:business_categories,id',
            'TIN_number' => 'nullable|string',
            'state_id' => 'nullable|numeric|exists:states,id',
            'pin_code' => 'nullable|numeric|digits:6',
            'city_id' => 'nullable|numeric',
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
    
    
        $tenantUnit = TenantUnit::where('id', $request->tenant_id)->first();
           
        if (!$tenantUnit) {
            return response()->json([
                'message' => 'Tenant unit not found'
            ], 404);
        }

        $tenant = Tenant::where('id', $tenantUnit->tenant_id)->first();
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }
    
        // Update tenant details
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
            'city_id' => $request->city_id
        ]);
    
        // Update tenant unit details
        $tenantUnit->update([
            'business_name' => $request->business_name,
            'business_type_id' => $request->business_types_id,
            'business_address' => $request->business_address,
            'phone_number' => $request->phone_number,
            'business_category_id' => $request->business_categories_id,
            'TIN_number' => $request->TIN_number,
            'state_id' => $request->state_id,
            'business_email' => $request->business_email,
            'pin_code' => $request->pin_code,
            'city_id' => $request->city_id
        ]);
    
        // Handling business logo upload
        if ($request->hasFile('business_logo')) {
            if ($tenant->business_logo) {
                $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
                if (Storage::disk('public')->exists($previousLogoPath)) {
                    Storage::disk('public')->delete($previousLogoPath);
                }
            }
            $logoPath = $request->file('business_logo')->store('logos', 'public');
            $tenant->business_logo = Storage::url($logoPath);
            $tenantUnit->business_logo = Storage::url($logoPath); // Assign to tenantUnit as well
        } elseif ($request->input('business_logo') === null && $tenant->business_logo) {
            $previousLogoPath = str_replace('/storage/', '', $tenant->business_logo);
            if (Storage::disk('public')->exists($previousLogoPath)) {
                Storage::disk('public')->delete($previousLogoPath);
            }
            $tenant->business_logo = null;
            $tenantUnit->business_logo = null; // Remove from tenantUnit as well
        }
    
        // Handling business signature upload
        if ($request->hasFile('business_signature')) {
            if ($tenant->business_signature) {
                Storage::delete(str_replace('/storage/', '', $tenant->business_signature));
            }
            $signaturePath = $request->file('business_signature')->store('signatures', 'public');
            $tenant->business_signature = Storage::url($signaturePath);
            $tenantUnit->business_signature = Storage::url($signaturePath); // Assign to tenantUnit as well
        } elseif ($request->input('business_signature') === null && $tenant->business_signature) {
            $previousSignaturePath = str_replace('/storage/', '', $tenant->business_signature);
            if (Storage::disk('public')->exists($previousSignaturePath)) {
                Storage::disk('public')->delete($previousSignaturePath);
            }
            $tenant->business_signature = null;
            $tenantUnit->business_signature = null; // Remove from tenantUnit as well
        }
    
        // Save changes to database
        $tenant->save();
        $tenantUnit->save();
    
        return response()->json([
            'message' => 'Tenant details updated successfully',
            'tenant' => $tenant
        ], 200);
    }
    

    
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
            'city_id' => 'nullable|numeric',
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

        $searchMainTeanat = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
        $searchActiveTenants = TenantUnit::where('tenant_id', $searchMainTeanat->id)->where('isactive',1)->get();
        TenantUnit::where('tenant_id', $searchMainTeanat->id)->where('isactive', 1)->update(['isactive' => 0]);

        $tenant = new TenantUnit();
        $tenant->tenant_id = $searchMainTeanat->id;
        $tenant->business_name = $request->business_name;
        $tenant->business_type_id = $request->business_type_id;
        $tenant->business_address = $request->business_address;
        $tenant->phone_number = $request->phone_number;
        $tenant->business_category_id = $request->business_category_id;
        $tenant->TIN_number = $request->TIN_number;
        $tenant->state_id = $request->state_id;
        $tenant->business_email = $request->business_email;
        $tenant->pin_code = $request->pin_code;
        $tenant->city_id = $request->city_id;
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
    
        UserTenantUnit::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'tenant_type' => TenantUnit::class, // Storing the model class
        ]);
        return response()->json([
            'message' => 'Tenant added successfully',
            'tenant' => $tenant
        ], 200);
    }
    







    public function switchFirm(Request $request){
        $user = auth()->user(); 
       $validator = Validator::make($request->all(), [
           'tenant_id' => 'required|numeric|exists:tenant_units,id',
       ]);
       if ($validator->fails()) {
           return response()->json([
               'message' => 'Validation failed',
               'errors' => $validator->errors()
           ], 400);
       }

       // here i deactivate all the active tenants
       $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
       $getcurrentactivetananet = TenantUnit::where('tenant_id', $maintenant->id)->where('isactive', 1)->get(); 
         TenantUnit::where('tenant_id', $maintenant->id)->where('isactive', 1)->update(['isactive' => 0]);

         // here i activate the selected tenant

            $tenant = TenantUnit::where('id', $request->tenant_id)->first();
            $tenant->update([
                'isactive' => 1
            ]);
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



    public function getActiveTanent()
    {
        $user = auth()->user();
        $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
     
        $tenants = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->get();

       
    
        if ($tenants->isEmpty()) {
            return response()->json([
                'message' => 'No active tenant found'
            ], 404);
        }
    
        $tenantsData = $tenants->map(function ($tenant) {
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
                'user' => $tenant->user ? [
                    'id' => $tenant->user->id,
                    'role_id' => $tenant->user->role_id,
                    'name' => $tenant->user->name,
                    'email' => $tenant->user->email,
                    'mobile_number' => $tenant->user->mobile_number
                ] : null,
                'businesstype' => $tenant->businesstype ? [
                    'id' => $tenant->businesstype->id,
                    'business_type' => $tenant->businesstype->business_type
                ] : null,
                'businesscategory' => $tenant->businesscategory ? [
                    'id' => $tenant->businesscategory->id,
                    'business_category' => $tenant->businesscategory->business_category
                ] : null,
                'state' => $tenant->state ? [
                    'id' => $tenant->state->id,
                    'state' => $tenant->state->state
                ] : null,
                'city' => $tenant->city ? [ // Corrected from 'cities' to 'city'
                    'id' => $tenant->city->id,
                    'city_name' => $tenant->city->city_name, // Use city_name from the city relationship
                    'pin_code' => $tenant->city->pin_code // Use the pin_code from the city relationship
                ] : null
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
