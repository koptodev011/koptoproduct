<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:4',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
        ], 200);
    }
    


    public function signup(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email', // Ensure email is unique for new users
            'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
            'password' => 'required|min:6', // Added a minimum length for password
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        // Create a new user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;
        $user->password = Hash::make($request->password); // Hash the password
        $user->save();
    
        // Create an associated tenant
        $tenant = new Tenant();
        $tenant->user_id = $user->id;
        $tenant->save();
    
        // Generate an authentication token
        // $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            // 'token' => $token,
        ], 200); // Use 201 Created status code
    }
    

public function profile(){
    $user = auth()->user();
    return response()->json([
        'message' => 'User details retrieved successfully',
        'user' => $user
    ], 200);
}
}
