<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\PlanController;


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login'])->middleware(["throttle:5,1"]);
Route::post('verify_otp', [AuthenticationController::class, 'verify_otp']);
Route::post('resend_otp', [AuthenticationController::class, 'resend_otp'])->middleware(["throttle:2,1"]);
Route::post('forgot_password', [ForgotPasswordController::class, 'forgot_password'])->middleware(["throttle:2,1"]);
Route::post('reset_password', [ForgotPasswordController::class, 'reset_password']);


Route::middleware('auth:sanctum')->group(function() {
    
    // Logout
    Route::post('logout', [AuthenticationController::class, 'logout']);


    Route::post('add_plan', [PlanController::class, 'add_plan']);
    Route::get('edit_plan/{uuid?}', [PlanController::class, 'edit_plan']);
    Route::post('update_plan', [PlanController::class, 'update_plan']);
    Route::delete('delete_plan/{uuid?}', [PlanController::class, 'delete_plan']);
    Route::get('get_plan/{uuid?}', [PlanController::class, 'get_plan']);
    
}); 
