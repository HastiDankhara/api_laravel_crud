<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        // Handle user signup logic
        $validatorUser = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:5',
            ]
        );
        if ($validatorUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validatorUser->errors()->all()
            ], 401);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => $user,
        ], 200);
    }
    public function login(Request $request)
    {
        // Handle user login logic
        $validatorUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );
        if ($validatorUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validatorUser->errors()->all()
            ], 401);
        }


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authuser = Auth::user();

            // Generate Token
            $token = $authuser->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'user' => $authuser,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }
        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     $authuser = Auth::user();
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'User logged in successfully',
        //         'user' => $authuser,
        //         'token' => $authuser->createToken('auth_token')->plainTextToken,
        //         'token_type' => 'Bearer',
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Validation Error',
        //         // 'errors' => $validatorUser->errors()->all()
        //     ], 404);
        // }
    }
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'User logged out successfully',
        ], 200);
    }
}
