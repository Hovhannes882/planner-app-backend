<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\SignupRequest;
use Request;

class AuthController extends Controller
{
    public function signup(SignupRequest $request) {
        return response()->json([
            "message" => "success"
        ]);
    }
}
