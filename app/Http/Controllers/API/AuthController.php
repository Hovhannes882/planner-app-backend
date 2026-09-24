<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\ForgotPasswordRequest;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\ResetPasswordRequest;
use App\Http\Requests\API\Auth\SignupRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Summary of signup
     * 
     * @param SignupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(SignupRequest $request): \Illuminate\Http\JsonResponse
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
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
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

    /**
     * Summary of getMe
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "message" => "ok",
            "data" => $request->user(),
        ]);
    }

    /**
     * Summary of logout
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "message" => "Logged out successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Sends password reset link email with token and email parameteres
     * 
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $status = Password::sendResetLink($request->only("email"));
            return match ($status) {
                Password::RESET_LINK_SENT => response()->json([
                    "message" => "Password reset link sent"
                ]),

                Password::RESET_THROTTLED => response()->json([
                    "message" => "Too many requests. Please try later."
                ], 429),

                default => response()->json([
                    "message" => "Unable to send password reset link."
                ], 422),
            };
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Takes email, password and token checks if token is valid and there is a user with this email
     * Also password_confirmation to repeat the password, 
     * if all successfull updates user password with a new hash
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $status = Password::reset(
                $request->only("email", "password", "password_confirmation", "token"),
                function (User $user, string $password) {
                    $user->forceFill([
                        "password" => Hash::make($password)
                    ]);
                    $user->save();
                }
            );


            return match ($status) {
                Password::PASSWORD_RESET => response()->json([
                    "message" => "Password reseted successfully."
                ]),
                Password::INVALID_USER => response()->json([
                    "message" => "No account was found for this email address."
                ], 422),
                Password::INVALID_TOKEN => response()->json([
                    "message" => "Invalid or expired password reset token."
                ], 422),
                default => response()->json([
                    "message" => "Could not reset the password."
                ], 422)
            };
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}
