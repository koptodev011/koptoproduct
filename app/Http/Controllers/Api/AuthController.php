<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
        $user->role_id = 1;
        $user->save();

        $tenant = new Tenant();
        $tenant->user_id = $user->id;
        $tenant->save();
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



 public function addStaff(Request $request){
    $user = auth()->user();
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|email|unique:staff,email',
        'mobile_number' => 'required|numeric|digits:10|unique:staff,mobile_number'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    $staff = new Staff();
    $staff->name = $request->name;
    $staff->email = $request->email;
    $staff->mobile_number = $request->mobile_number;
    $staff->user_id = $user->id;
    $staff->save();
    return response()->json(['message' => 'Staff member created successfully'], 200);
 }


 
 public function getAllStaff()
 {
     $user = auth()->user()->load('staffs');
     return response()->json($user, 200);
 }
 


 public function updateStaffDetails(Request $request){
    $staff = Staff::find($request->staff_id);
    if (!$staff) {
        return response()->json([
            'message' => 'Staff member not found'
        ], 404);
    }
    $validator = Validator::make($request->all(), [
        'staff_id' => 'required|numeric|exists:staff,id',
        'name' => 'required|string',
        'email' => 'required|email|unique:staff,email,'.$staff->id,
        'mobile_number' => 'required|numeric|digits:10|unique:staff,mobile_number,'.$staff->id
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    $staff->update([
        'name' => $request->name,
        'email' => $request->email,
        'mobile_number' => $request->mobile_number
    ]);
    return response()->json(['message' => 'Staff member updated successfully'], 200);
  }

public function deleteStaff(Request $request){
    $staff = Staff::find($request->staff_id);
    if (!$staff) {
        return response()->json([
            'message' => 'Staff member not found'
        ], 404);
    }
    $staff->delete();
    return response()->json(['message' => 'Staff member deleted successfully'], 200);
  }

}
