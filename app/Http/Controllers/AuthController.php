<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' => 'Registration successful',
            'data' => $user
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $passwordCheck = Hash::check($request->password, $user->password);
        if(!$passwordCheck){
            return response()->json([
                'message' => 'Invalid password'
            ], 422);
        }
        $token = $user->createToken('Testing');
        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token->plainTextToken
            ]
        ]);
    }
}
