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
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use Illuminate\Support\Str;


class AuthenticationController extends Controller
{
    
    public function register(Request $request){ 

        try {

            $validator = Validator::make($request->all(), [
                
                'first_name' => 'required|regex:/^[a-zA-Z0-9\s\-]+$/',
                'last_name' => 'required|regex:/^[a-zA-Z0-9\s\-]+$/',
                'email' => 'required|email',
                'password' => 'required|min:6|max:16',
                'role_id' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'auth_id' => 'required',
                
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
            $user['password'] = bcrypt($user['password']);
            $user['ip'] = $request->ip();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName(); // Prepend timestamp for unique filename
                $folderName = '/upload_files/users/';
                $destinationPath = public_path() . $folderName;
        
                // Ensure the directory exists, if not create it
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
        
                // Move the file to the destination path
                $file->move($destinationPath, $fileName);
        
                // Update the menu's icon path
                $user['image'] = $folderName . $fileName;
            }

            
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
                Mail::send('emailtemplate/welcome_email_template', $data, function($message) use ($data){
                
                    $message->from($data['details']['FromEmail'], $data['details']['WebsiteName']); 
                    
                    $message->to($data['details']['SignupEmail'])->subject($data['details']['heading']);
                
                });

                
                return response()->json([
                    
                    'status_code' => 201,
                    'message' => 'Registration Successfull',
                    // 'accessToken' => $access_Token, 

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


    // Login Via API
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
             
            'email' => 'required|email',
            'password' => 'required|min:6',

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

        try {
            

            if(Auth::attempt(['email' => $email, 'password' => $password])) {

                $user = Auth::user();
                // $accessToken = $user->createToken('MyToken')->accessToken;
                
                // OTP Process 
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiresAt = now()->addMinutes(1);
    
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
                        'phone_number' => "(021) 333-145-749",
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


        }catch (\Exception $e) {
           
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => Response::HTTP_UNAUTHORIZED,
            ], Response::HTTP_UNAUTHORIZED);

        }


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
            $accessToken = $user->createToken('MyToken')->plainTextToken;

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
            $expiresAt = now()->addMinutes(1); 

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
                    'phone_number' => "(021) 333-145-749",
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


    public function logout(Request $request)
    {
        
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Revoke the token that was used to authenticate the current request
            $user->currentAccessToken()->delete();
    
            if($user){
                activity()->useLog('Logout')->causedBy($user)->withProperties($user)->log('You have been logged out');
            }
    
            return response()->json([
                'status_code' => Response::HTTP_OK,
                'message' => 'Successfully logged out',
            ], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    

}
