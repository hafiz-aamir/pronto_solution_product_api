<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\PlanDetailsController;
use App\Http\Controllers\API\InvitationController;



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
    Route::get('get_plan', [PlanController::class, 'get_plan']);
    Route::get('get_plan_with_details', [PlanController::class, 'get_plan_with_details']);


    Route::post('add_plandetails', [PlanDetailsController::class, 'add_plandetails']);
    Route::get('edit_plandetails/{uuid?}', [PlanDetailsController::class, 'edit_plandetails']);
    Route::post('update_plandetails', [PlanDetailsController::class, 'update_plandetails']);
    Route::delete('delete_plandetails/{uuid?}', [PlanDetailsController::class, 'delete_plandetails']);
    Route::get('get_plandetails', [PlanDetailsController::class, 'get_plandetails']);


    Route::post('add_invitation', [InvitationController::class, 'add_invitation']);
    Route::get('edit_invitation/{uuid?}', [InvitationController::class, 'edit_invitation']);
    Route::post('update_invitation', [InvitationController::class, 'update_invitation']);
    Route::delete('delete_invitation/{uuid?}', [InvitationController::class, 'delete_invitation']);
    Route::get('get_invitation', [InvitationController::class, 'get_invitation']);
    
    
}); 
