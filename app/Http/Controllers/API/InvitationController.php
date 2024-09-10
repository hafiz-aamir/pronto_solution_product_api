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
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Invitation;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;


class InvitationController extends Controller
{
    
    public function add_invitation(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'team_email' => 'required',
            'team_role' => 'required',
            'status' => 'required',
        
        ]);
    
        if($validator->fails()){
            
            $message = $validator->messages();
            
            return response()->json([
                
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => strval($validator->errors())
            
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
    
        }
    
        try {
    
            $check_if_already = Invitation::where('team_email', $request->team_email)->where('team_role', $request->team_role)->first();
    
            if($check_if_already){
    
                return response()->json([
    
                    'status_code' => Response::HTTP_CONFLICT,
                    'message' => 'This Invitation Detail has already been taken.',
    
                ], Response::HTTP_CONFLICT);
    
    
            }else{
    
                $invitation = $request->all();
                $invitation['uuid'] = Str::uuid();
                $invitation['auth_id'] = Auth::user()->uuid;
    
                $save_invitation = Invitation::create($invitation);
    
                if($save_invitation){ 
                    
                    return response()->json([
                            
                        'status_code' => Response::HTTP_CREATED,
                        'message' => 'Invitation add successfully',
    
                    ], Response::HTTP_CREATED);
    
                }
    
            }
            
        
        }catch (\Exception $e) { 
            
            return response()->json([
    
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                // 'error' => $e->getMessage(),
    
            ], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
        
    
    }


    public function edit_invitation($uuid){

        $edit_invitation = Invitation::where('uuid', $uuid)->first();

        if($edit_invitation)
        {

            return response()->json([

                'status_code' => Response::HTTP_OK,
                'data' => $edit_invitation,

            ], Response::HTTP_OK);


        }else{

            return response()->json([

                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Record Not Found',

            ], Response::HTTP_NOT_FOUND);

        }


    }


    public function update_invitation(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'team_email' => 'required',
            'team_role' => 'required',
            'status' => 'required',
        
        ]);

        if($validator->fails()){
            
            $message = $validator->messages();
            
            return response()->json([
                
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => strval($validator->errors())
            
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        
        try{
            
            $uuid = request()->header('uuid');
            $upd_invitation = Invitation::where('uuid', $uuid)->first();

            if (!$upd_invitation) {

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);

            }

            $upd_invitation['auth_id'] = Auth::user()->uuid;
            $upd_invitation['team_email'] = $request->team_email;
            $upd_invitation['team_role'] = $request->team_role;
            $upd_invitation['status'] = $request->status; 

            $update_invitation = $upd_invitation->save();

            if($update_invitation){
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_OK,
                    'message' => 'Invitation has been updated',
                
                ], Response::HTTP_OK);

            }


        }catch (\Exception $e) { 
            // Handle general exceptions
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                // 'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        }

        
    }


    public function delete_invitation($uuid){

        try{

            $del_invitation = Invitation::where('uuid', $uuid)->first();
            
            if(!$del_invitation)
            {
                
                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);


            }else{

                $delete_invitation = Invitation::destroy($del_invitation->id);

                if($delete_invitation){
                
                    return response()->json([
                        
                        'status_code' => Response::HTTP_OK,
                        'message' => 'Invitation has been deleted',
                    
                    ], Response::HTTP_OK);
    
                }

            }


        }catch (\Exception $e) { 
            // Handle general exceptions
            return response()->json([

                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error',
                // 'error' => $e->getMessage(),

            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500 Internal Server Error
        } 
        
    }


    
    public function get_invitation(){

        $get_all_invitation = Invitation::all();
    
        if($get_all_invitation){
            
            return response()->json([
                    
                'status_code' => Response::HTTP_OK,
                'data' => $get_all_invitation,
    
            ], Response::HTTP_OK);
    
        }
    
    }



}
