<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Exception; 
use Mail;
use Auth;
use Session;
use Hash;
use DB;
use App\Models\User;
use App\Models\Invitation;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use Illuminate\Support\Str;


class RegisterController extends Controller
{
    
    public function register(Request $request){ 

        try {

            $validator = Validator::make($request->all(), [
                
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6|max:16',
                
            ]); 
            
            
            if($validator->fails()) {
                
                $message = $validator->messages();
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => strval($validator->errors())
                
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

            }

            $user = $request->all();
            $user['uuid'] = Str::uuid();
            $user['password'] = bcrypt($request->password);
            $user['role_id'] = "2";
            $user['ip'] = $request->ip();
        
            $save_user = User::create($user);
            
            // $access_Token = $save_user->createToken('MyToken')->accessToken; 

            $user = auth()->user();

            if($save_user) { 
                
                // Email Send To Admin Start : 
                    
                $data = [
                    
                    'details'=>[
                    
                        'WebsiteName' => config('app.name'),
                        'heading' => "WELCOME",
                        'FromEmail' => config('app.from_email'),
                        'SignupEmail' => $request->email, 
                        'hi_message' => $request->first_name.' '.$request->last_name,
                        'currentDate'  => Carbon::now()->format('d-M-Y'),
                        
                    ]
                
                ];
                
                //  dd($data);
                
                // User Email
                Mail::send('emailtemplate/welcome_email', $data, function($message) use ($data){
                
                    $message->from($data['details']['FromEmail'], $data['details']['WebsiteName']); 
                    
                    $message->to($data['details']['SignupEmail'])->subject($data['details']['heading']);
                
                });

                
                return response()->json([
                    
                    'status_code' => 201,
                    'message' => 'Registration Successfull',
                    'uuid' => $save_user->uuid

                ], 201);

            }
        
        }catch (QueryException $e) {
            
            if ($e->getCode() === '23000') { // SQLSTATE code for integrity constraint violation
                // Handle unique constraint violation
                return response()->json([

                    'status_code' => Response::HTTP_CONFLICT,
                    'message' => 'Duplicate entry detected',
                    'error' => 'The email address has already been taken.',

                ], Response::HTTP_CONFLICT); // 409 Conflict
            }

            // For other SQL errors
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error',
                'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        
        }catch (\Exception $e) { 
            // Handle general exceptions
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        }

            
    }


    public function bring_you_here_today(Request $request){ 
    
        try {
            

            $validator = Validator::make($request->all(), [
                
                'bring_you_here_today' => 'required',
                
            ]); 
            
            
            if($validator->fails()) {
                
                $message = $validator->messages();
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => strval($validator->errors())
                
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

            }

            $uuid = request()->header('user-uuid');
            $upd_user = User::where('uuid', $uuid)->first();

            if($upd_user)
            {

                $upd_user->bring_you_here_today = $request->bring_you_here_today;
                $updated = $upd_user->save();
                
                if($updated)
                {

                    return response()->json([

                        'status_code' => Response::HTTP_OK,
                        'message' => 'Record has been updated',
    
                    ], Response::HTTP_OK);

                }
                
            }
            else{

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'No Record Found',

                ], Response::HTTP_NOT_FOUND);

            }


        }catch (\Exception $e) { 
            
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }



    public function do_you_heard_about_us(Request $request){ 
    
        try {
            

            $validator = Validator::make($request->all(), [
                
                'do_you_heard_about_us' => 'required',
                
            ]); 
            
            
            if($validator->fails()) {
                
                $message = $validator->messages();
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => strval($validator->errors())
                
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

            }

            $uuid = request()->header('user-uuid');
            $upd_user = User::where('uuid', $uuid)->first();

            if($upd_user)
            {

                $upd_user->do_you_heard_about_us = $request->do_you_heard_about_us;
                $updated = $upd_user->save();
                
                if($updated)
                {

                    return response()->json([

                        'status_code' => Response::HTTP_OK,
                        'message' => 'Record has been updated',
    
                    ], Response::HTTP_OK);

                }
                
            }
            else{

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'No Record Found',

                ], Response::HTTP_NOT_FOUND);

            }


        }catch (\Exception $e) { 
            
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    
    public function add_invite(Request $request){ 
    
        try {
            

            $validator = Validator::make($request->all(), [
                
               'team_member' => 'required|string'
                
            ]);
            
            
            if($validator->fails()) {
                
                $message = $validator->messages();
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => strval($validator->errors())
                
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

            }

            $uuid = request()->header('user-uuid');
            $user = User::where('uuid', $uuid)->first();

            if($user)
            {

                // Decode the JSON string to an array
                $teamMembers = json_decode($request->team_member, true);

                if (is_array($teamMembers)) {
                    // Loop through the decoded array
                    foreach ($teamMembers as $member) {
                        // Validate each member's data
                        if (isset($member['team_email'], $member['team_role'])) {
                            // Save each member's data in the invitations table using the Invitation model
                            Invitation::create([
                                'uuid' => (string) Str::uuid(),
                                'auth_id' => $user->uuid, // Assuming you're using Laravel's Auth system
                                'team_email' => $member['team_email'],
                                'team_role' => $member['team_role'],
                                'status' => 1, // Set status as active or any default value
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                    
                    return response()->json([
                        'status_code' => 201,
                        'message' => 'Team members invited successfully.'
                    ], 201);

                    } else {
                        return response()->json([
                            'status_code' => 400,
                            'error' => 'Invalid team_member data format.'
                        ], 400);
                    }
                
            }
            else{

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'No Record Found',

                ], Response::HTTP_NOT_FOUND);

            }


        }catch (\Exception $e) { 
            
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }


    // Login Via API
    public function login(Request $request){


        $validator = Validator::make($request->all(), [
             
            'email' => 'required|email',
            'password' => 'required',

        ]); 
        
        if($validator->fails()){
            
            $message = $validator->messages();
            
            return response()->json([
                
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => strval($validator->errors())
            
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } 
        
        $email = $request->email;
        $password = $request->password;

        if(Auth::attempt(['email' => $email, 'password' => $password])) {

            $user = Auth::user();
            // $accessToken = $user->createToken('MyToken')->accessToken;
            
            // OTP Process 
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = now()->addMinutes(config('app.expire_minute')); 

            // Save OTP in DB
            OtpVerification::create([
                'user_id' => $user->id,
                'otp' => Hash::make($otp), // Store hashed OTP for security
                'otp_show' => $otp,
                'expires_at' => $expiresAt,
            ]);

            $data = [
                
                'details'=>[              
                    'heading'   => 'Verify OTP',
                    'FromEmail' => config('app.from_email'),
                    'name' => "HI ".$user->name.' '.$user->lname, 
                    'email'   => $user->email,
                    'verification_code' => $otp,
                    'currentDate'   => Carbon::now()->format('d-M-Y'),
                    'website' => config('app.name'),
                ]
            
            ];
            
            // User Email
            $sendMail = Mail::send('emailtemplate/otp_verification', $data, function($message) use ($data){
            
                $message->from($data['details']['FromEmail'], $data['details']['website']);
                $message->to($data['details']['email'])->subject($data['details']['heading']);
            
            });

            if($sendMail){
                    
                try {

                    // Logic to send OTP
                    // For example, using a service or library to send the OTP
        
                    // If sending is successful
                    return response()->json([
                        'status_code' => Response::HTTP_OK,
                        'message' => 'OTP has been sent',
                    ], Response::HTTP_OK);

                } catch (\Exception $e) {

                    // Log the error
                    Log::error('Failed to send OTP: ' . $e->getMessage());
        
                    // Handle server error
                    return response()->json([
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Failed to send OTP',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);

                }
                
            }
            
        }else{

            return response()->json([
                'message' => 'Invalid credentials',
                'status_code' => Response::HTTP_UNAUTHORIZED,
            ], Response::HTTP_UNAUTHORIZED);

        }


    } 



    public function logout(Request $request)
    {
        
        $user = Auth::user(); 
        $user->token()->revoke();

        if($user){
            activity()->useLog('Logout')->causedBy($user)->withProperties($user)->log('You have Been Logout');
        }

        return response()->json([
            
            'status_code' => Response::HTTP_OK,
            'message' => 'Successfully logged out'
        
        ], Response::HTTP_OK);

    }


    public function verify_otp(Request $request)
    {

        $inputOtp = $request->otp;
        $inputEmail = $request->email;
        $currentDateTime = Carbon::now();

        $user = User::where('email', $inputEmail)->first();
        $otpRecord = OtpVerification::where('user_id', $user->id)->latest()->first();

        if($otpRecord && Hash::check($inputOtp, $otpRecord->otp) && $otpRecord->expires_at > $currentDateTime) {

            $delete_otp = OtpVerification::where('user_id', $user->id)->delete();
            $accessToken = $user->createToken('MyToken')->accessToken;

            activity()->useLog('Login')->causedBy($user)->withProperties($user)->log('User successfully logged in');
            
            return response()->json([
            
                'status_code'=> Response::HTTP_OK,
                'message' => 'Login Successful',
                'accessToken'=> $accessToken,
                'user' => [
                    'uuid' => $user->uuid,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
            
            ], Response::HTTP_OK);

        }else {

            return response()->json([
            
                'message' => 'Otp has been Invalid Or Expired',
            
            ]);

        }

    }  


    public function resend_otp(Request $request){

        $validator = Validator::make($request->all(), [
             
            'email' => 'required|email|exists:users,email',

        ]); 
        
        if($validator->fails()){
            
            $message = $validator->messages();
            
            return response()->json([
                
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => strval($validator->errors())
            
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } 
        
        $email = $request->email;

        if(isset($email)) {

            $user = User::where('email', $email)->first();
            $delete_otp = OtpVerification::where('user_id', $user->id)->delete();

            // OTP Process 
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = now()->addMinutes(config('app.expire_minute')); 

            // Save OTP in DB
            OtpVerification::create([
                'user_id' => $user->id,
                'otp' => Hash::make($otp), // Store hashed OTP for security
                'otp_show' => $otp,
                'expires_at' => $expiresAt,
            ]);

            $data = [
                
                'details'=>[              
                    'heading'   => 'Verify OTP',
                    'FromEmail' => config('app.from_email'),
                    'name' => "HI ".$user->name.' '.$user->lname, 
                    'email'   => $user->email,
                    'verification_code' => $otp,
                    'currentDate'   => Carbon::now()->format('d-M-Y'),
                    'website' => config('app.name'),
                ]
            
            ];
            
            // User Email
            $sendMail = Mail::send('emailtemplate/otp_verification', $data, function($message) use ($data){
            
                $message->from($data['details']['FromEmail'], $data['details']['website']);
                $message->to($data['details']['email'])->subject($data['details']['heading']);
            
            });

            if($sendMail){
                    
                try {

                    // Logic to send OTP
                    // For example, using a service or library to send the OTP
        
                    // If sending is successful
                    return response()->json([
                        'status_code' => Response::HTTP_OK,
                        'message' => 'OTP has been sent',
                    ], Response::HTTP_OK);

                } catch (\Exception $e) {

                    // Log the error
                    Log::error('Failed to send OTP: ' . $e->getMessage());
        
                    // Handle server error
                    return response()->json([
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Failed to send OTP',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);

                }
                
            }
            
        }

    }


}