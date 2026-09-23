<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\SignupRequest;
use App\Models\User;
use Hash;

class AuthController extends Controller
{
    /**
     * Summary of signup
     * 
     * @param SignupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(SignupRequest $request)
    {
        try {
            User::create($request->only("username", "email", "password"));

            return response()->json([
                "message" => "success"
            ]);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    /**
     * Summary of login
     * 
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $loginType = "email";
            if ($request->has("username")) {
                $loginType = "username";
            }

            $user = User::where($loginType, $request->input($loginType))->first();

            if (!$user) {
                return response()->json(["message" => "User not found"], 401);
            }

            if (!Hash::check($request->input("password"), $user->password)) {
                return response()->json(["message" => "Invalid credentials"], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;


            return response()->json([
                "message" => "success",
                "data" => $user,
                "token" => $token,
            ]);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
}
