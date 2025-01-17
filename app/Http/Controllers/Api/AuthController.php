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

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|email',
            // 'password' => 'required|string|min:6'
            'password' => 'required|numeric|min:4',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token
        ], 200);
    }





    public function signup(Request $request)
{
    $validator = Validator::make($request->all(), [
        // 'name' => 'required|string',
        // 'email' => 'required|email|unique:users,email',
        // 'password' => 'required|string|min:6',
        'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    $user = new User();
    // $user->name = $request->name;
    // $user->email = $request->email;
    // $user->password = Hash::make($request->password); 
    $user->mobile_number = $request->mobile_number;
    $user->save();

    $tenant = new Tenant();
    $tenant->user_id = $user->id;
    $tenant->save();
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
        'message' => 'User created successfully',
        'user' => $user,
        'token' => $token
    ], 200);
}


public function profile(){
    dd(Auth::user());

    $user = auth()->user();
    return response()->json([
        'message' => 'User details retrieved successfully',
        'user' => $user
    ], 200);
}
}
