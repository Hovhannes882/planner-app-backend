<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\SignupRequest;
use App\Models\User;

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
}
