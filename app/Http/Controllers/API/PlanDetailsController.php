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
use App\Models\PlanDetail;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;


class PlanDetailsController extends Controller
{
    
    public function add_plandetails(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'name' => 'required',
            'plan_id' => 'required',
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
    
            $check_if_already = PlanDetail::where('name', $request->name)->where('plan_id', $request->plan_id)->first();
    
            if($check_if_already){
    
                return response()->json([
    
                    'status_code' => Response::HTTP_CONFLICT,
                    'message' => 'This Plan Detail Detail has already been taken.',
    
                ], Response::HTTP_CONFLICT);
    
    
            }else{
    
                $planDetail = $request->all();
                $planDetail['uuid'] = Str::uuid();
                $planDetail['auth_id'] = Auth::user()->uuid;
    
                $save_plandetails = PlanDetail::create($planDetail);
    
                if($save_plandetails){ 
                    
                    return response()->json([
                            
                        'status_code' => Response::HTTP_CREATED,
                        'message' => 'Plan Detail add successfully',
    
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


    public function edit_plandetails($uuid){

        $edit_plandetails = PlanDetail::where('uuid', $uuid)->first();

        if($edit_plandetails)
        {

            return response()->json([

                'status_code' => Response::HTTP_OK,
                'data' => $edit_plandetails,

            ], Response::HTTP_OK);


        }else{

            return response()->json([

                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Record Not Found',

            ], Response::HTTP_NOT_FOUND);

        }


    }


    public function update_plandetails(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'name' => 'required',
            'plan_id' => 'required',
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
            $upd_plandetails = PlanDetail::where('uuid', $uuid)->first();

            if (!$upd_plandetails) {

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);

            }

            $upd_plandetails['auth_id'] = Auth::user()->uuid;
            $upd_plandetails['name'] = $request->name;
            $upd_plandetails['plan_id'] = $request->plan_id;
            $upd_plandetails['status'] = $request->status; 

            $update_plandetails = $upd_plandetails->save();

            if($update_plandetails){
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_OK,
                    'message' => 'Plan Detail has been updated',
                
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


    public function delete_plandetails($uuid){

        try{

            $del_plandetails = PlanDetail::where('uuid', $uuid)->first();
            
            if(!$del_plandetails)
            {
                
                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);


            }else{

                $delete_plandetails = PlanDetail::destroy($del_plandetails->id);

                if($delete_plandetails){
                
                    return response()->json([
                        
                        'status_code' => Response::HTTP_OK,
                        'message' => 'Plan Detail has been deleted',
                    
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


    
    public function get_plandetails(){

        $get_all_plandetails = PlanDetail::all();
    
        if($get_all_plandetails){
            
            return response()->json([
                    
                'status_code' => Response::HTTP_OK,
                'data' => $get_all_plandetails,
    
            ], Response::HTTP_OK);
    
        }
    
    }



}
