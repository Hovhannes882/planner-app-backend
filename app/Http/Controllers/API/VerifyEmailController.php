<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Gets id and hash 
     * Finds user by id if no user returns error with message user not found with given id
     * Compares given hash with user email verification hash, 
     * if they are the same updates user email verified to true,
     * otherwise sends error message invalid verification link
     * @param mixed $id
     * @param mixed $hash
     * @return JsonResponse
     */
    public function verifyEmail($id, $hash): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "User not found with given id."
                ], 404);
            }

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return response()->json([
                    "message" => 'Invalid verification link'
                ], 403);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email already verified.',
                ], 400);
            }

            $user->markEmailAsVerified();

            return response()->json([
                'message' => 'Email verified successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Sending verification notification to the user via email
     * @param Request $request
     * @return JsonResponse
     */
    public function sendNotification(Request $request): JsonResponse
    {
        try {
            $id = $request->input("id");
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "No user found with given id"
                ]);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    "message" => "Email already verified"
                ]);
            }

            $user->sendEmailVerificationNotification();
            return response()->json([
                'message' => 'Verification email sent.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }
}
