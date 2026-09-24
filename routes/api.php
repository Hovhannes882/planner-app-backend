<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])->name('verification.verify')->middleware('signed');
Route::post('/email/verification-notification/', [VerifyEmailController::class, 'sendNotification'])->middleware(["throttle:verification-email"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getMe', [AuthController::class, 'getMe']);
    Route::post('/logout', [AuthController::class, 'logout']);
});