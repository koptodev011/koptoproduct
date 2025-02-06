<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class AuthController extends Controller
{

    
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'nullable|email|exists:users,email',
        'phone_number' => 'nullable|numeric|exists:users,mobile_number',
        // 'password' => 'required|min:4',
    ]);

    $user=User::where('mobile_number', $request->phone_number)->first();
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }
    $user = null;
    if ($request->has('email')) {
        $user = User::where('email', $request->email)->first();
    } elseif ($request->has('phone_number')) {
        $user = User::where('mobile_number', $request->phone_number)->first();
    }

      if (!$user) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 400);
    }
    // if (!$user || !Hash::check($request->password, $user->password)) {
    //     return response()->json([
    //         'message' => 'Invalid credentials',
    //     ], 400);
    // }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User logged in successfully',
        'token' => $token,
    ], 200);
}





    


    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
            'password' => 'required|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
      
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;
        $user->password = Hash::make($request->password);
        $user->role_id = 2;
        $user->save();

        $tenant = new Tenant();
        $tenant->user_id = $user->id;
        $tenant->save();

        $user->tenant_id = $tenant->id;
        $user->save(); 

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    






public function profile(){
    $user = auth()->user();
    return response()->json([
        'message' => 'User details retrieved successfully',
        'user' => $user
    ], 200);
}







public function staffRoles()
{
    $staffRoles = Role::whereNotIn('id', [1, 2])->get();
    return $staffRoles;
}







public function addStaff(Request $request)
{
    $user = auth()->user();

    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
        'role_id' => 'required|numeric|exists:roles,id',
        'profile_photo' => 'nullable|image|', // 2MB limit
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    $tenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    $newUser = new User();
    $newUser->name = $request->name;
    $newUser->email = $request->email;
    $newUser->mobile_number = $request->mobile_number;
    $newUser->role_id = $request->role_id;
    $newUser->tenant_id = $tenant->id;

    if ($request->hasFile('profile_photo')) {
        $file = $request->file('profile_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile_photo', $filename, 'public'); 
        $newUser->profile_photo = 'storage/' . $path; 
    }
    $newUser->save();
    return response()->json(['message' => 'Staff member created successfully'], 200);
}








 public function getAllStaff()
 {
     $user = auth()->user();
     $tenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
     
     if (!$tenant) {
         return response()->json(['message' => 'Tenant not found'], 404);
     }
     $users = User::where('tenant_id', $tenant->id)
         ->get()
         ->map(function ($user) {
             $user->role = $user->role_id == 2 ? 'Admin' : ($user->role_id == 3 ? 'Staff' : 'Unknown');
             return $user;
         });
     return response()->json($users, 200);
 }
 
 





 
public function updateStaffDetails(Request $request)
{
    $staff = User::find($request->staff_id);

    if (!$staff) {
        return response()->json([
            'message' => 'Staff member not found'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'staff_id' => 'required|numeric|exists:users,id',
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $staff->id,
        'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number,' . $staff->id,
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        'role_id' => 'required|numeric|exists:roles,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    if ($request->hasFile('profile_photo')) {
        if ($staff->profile_photo) {
            $oldPhotoPath = storage_path('app/public/' . $staff->profile_photo);
            
            if (file_exists($oldPhotoPath)) {
                if (unlink($oldPhotoPath)) {
                    \Log::info('Deleted old image: ' . $oldPhotoPath);
                } else {
                    \Log::error('Failed to delete: ' . $oldPhotoPath);
                }
            } else {
                \Log::warning('Old image not found: ' . $oldPhotoPath);
            }
        }
        $file = $request->file('profile_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile_photo', $filename, 'public');
        $staff->profile_photo = 'storage/' . $path;
    }

    // Update staff details
    $staff->update([
        'name' => $request->name,
        'email' => $request->email,
        'mobile_number' => $request->mobile_number,
        'role_id' => $request->role_id,
    ]);

    return response()->json(['message' => 'Staff member updated successfully'], 200);
}









public function deleteStaff(Request $request){
    $staff = User::where('id',$request->staff_id)->where('role_id',3)->first();

    if (!$staff) {
        return response()->json([
            'message' => 'Staff member not found'
        ], 404);
    }
    $staff->delete();
    return response()->json(['message' => 'Staff member deleted successfully'], 200);
  }




}
