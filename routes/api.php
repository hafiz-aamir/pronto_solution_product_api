<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ForgotPasswordController;


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login'])->middleware(["throttle:5,1"]);
Route::post('verify_otp', [AuthenticationController::class, 'verify_otp']);
Route::post('resend_otp', [AuthenticationController::class, 'resend_otp'])->middleware(["throttle:1,1"]);
Route::post('forgot_password', [ForgotPasswordController::class, 'forgot_password'])->middleware(["throttle:1,1"]);
Route::post('reset_password', [ForgotPasswordController::class, 'reset_password']);


Route::middleware('auth:sanctum')->group(function() {
    
    // Logout
    Route::post('logout', [RegisterController::class, 'logout']);

});
