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
use App\Models\Password_reset_token;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use Illuminate\Support\Str;


class ForgotPasswordController extends Controller
{
    

    public function forgot_password(Request $request){ 

        try {

            $validator = Validator::make($request->all(), [
                
                'email' => 'required|email|exists:users,email',
            
            ]); 
            
            
            if($validator->fails()) {
                
                $message = $validator->messages();
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => strval($validator->errors())
                
                ], Response::HTTP_UNPROCESSABLE_ENTITY);

            }

            $delete_old_token = Password_reset_token::where('email',$request->email)->delete();
             
            // $token = Str::random(64);
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
             
            $save_token = Password_reset_token::insert([
                'email' => $request->email, 
                'token' => $otp, 
                'created_at' => Carbon::now()
            ]);

            if($save_token){ 
                
                // Email Send To Admin Start: 
                $data = [
            
                    'details'=>[
                        'heading' => "Forgot Password",
                        'FromEmail' => config('app.from_email'),
                        'Email'   => $request->email,
                        'WebsiteName'   => config('app.name'),
                        'currentDate'   => Carbon::now()->format('d-M-Y'),
                        'token'         => $otp
                    ]
                
                ]; 
                
                $sendMail = Mail::send('emailtemplate/forgot_password', $data, function($message) use ($data){

                    $message->from($data['details']['FromEmail'], $data['details']['WebsiteName']);
                    $message->to($data['details']['Email'])->subject($data['details']['heading']);
                
                });


                if($sendMail){
                    
                    try {
                            
                        return response()->json([
                            'status_code' => Response::HTTP_OK,
                            'message' => 'Password reset code has been sent',
                            // 'token' => $otp,
                        ], Response::HTTP_OK);
    
                    } catch (\Exception $e) {
    
                        // Log the error
                        Log::error('Failed to send Password reset code: ' . $e->getMessage());
            
                        // Handle server error
                        return response()->json([
                            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => 'Failed to send Password reset code',
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    
                    }
                    
                }

                

            }
        
        }catch (\Exception $e) { 
            // Handle general exceptions
            return response()->json([
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        }

            
    }


    public function reset_password(Request $request)
    {
       
        // dd($request->all());
        
        $validator = Validator::make($request->all(), [
          
          'email' => 'required|email|exists:users,email',
          'token' => 'required',
          'password' => 'required|min:6|confirmed',
          'password_confirmation' => 'required|min:6'
        
        ]);
        
        if($validator->fails()) {
                
            $message = $validator->messages();
            
            return response()->json([
                
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => strval($validator->errors())
            
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        $updatePassword = Password_reset_token::where(['email' => $request->email,'token' => $request->token])->get();

        if(count($updatePassword) > 0){
            
            try{

                $user = User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
                $del_token = Password_reset_token::where(['email' => $request->email,'token' => $request->token])->delete();
                
                
                // Email Send To Admin Start : 
                        
                $data = [
                
                    'details'=>[
                    
                        'heading' => "RESET PASSWORD",
                        'FromEmail' => config('app.from_email'),
                        'SignupEmail'   => $request->email,
                        'WebsiteName'   => config('app.name'),
                        'currentDate'   => Carbon::now()->format('d-M-Y'),
                        
                    ]
                
                ];
                
                //  dd($data);
                
                $sendMail = Mail::send('emailtemplate/reset_password_email_template', $data, function($message) use ($data){
                
                    $message->from($data['details']['FromEmail'], $data['details']['WebsiteName']); 
                    $message->to($data['details']['SignupEmail'])->subject($data['details']['heading']);
                
                });
                
                if($sendMail)
                {
                    
                    return response()->json([
                        'status_code' => Response::HTTP_OK,
                        'message' => 'Password has been reset',
                    ], Response::HTTP_OK);

                }
                

            }catch (\Exception $e) {

                // Log the error
                Log::error('Failed to send reset password code: ' . $e->getMessage());
    
                // Handle server error
                return response()->json([
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Failed to send reset password code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);

            }
        
        }else{
            
            return response()->json([
                'status_code' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Token is invalid'
            ], Response::HTTP_UNAUTHORIZED);

        } 
        
        
    
    }


}
