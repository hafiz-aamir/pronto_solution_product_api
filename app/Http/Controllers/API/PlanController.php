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
use App\Models\Plan;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;


class PlanController extends Controller
{
    
    public function add_plan(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'amount' => 'required',
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
    
            $check_if_already = Plan::where('name', $request->name)->where('type', $request->type)->first();
    
            if($check_if_already){
    
                return response()->json([
    
                    'status_code' => Response::HTTP_CONFLICT,
                    'message' => 'This Plan has already been taken.',
    
                ], Response::HTTP_CONFLICT); // 409 Conflict 
    
    
            }else{
    
                $plan = $request->all();
                $plan['uuid'] = Str::uuid();
                $plan['auth_id'] = Auth::user()->uuid;
    
                $save_Plan = Plan::create($plan);
    
                if($save_Plan){ 
                    
                    return response()->json([
                            
                        'status_code' => Response::HTTP_CREATED,
                        'message' => 'Plan add successfully',
    
                    ], Response::HTTP_CREATED);
    
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


    public function edit_plan($uuid){

        $edit_plan = Plan::where('uuid', $uuid)->first();

        if($edit_plan)
        {

            return response()->json([

                'status_code' => Response::HTTP_OK,
                'data' => $edit_plan,

            ], Response::HTTP_OK);


        }else{

            return response()->json([

                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Record Not Found',

            ], Response::HTTP_NOT_FOUND);

        }


    }


    public function update_plan(Request $request){
        
        $validator = Validator::make($request->all(), [
             
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'amount' => 'required',
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
            
            $uuid = $request->header('uuid');
            $upd_plan = Plan::where('uuid', $uuid)->first();

            if (!$upd_plan) {

                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);

            }

            $upd_plan['auth_id'] = Auth::user()->uuid;
            $upd_plan['description'] = $request->description;
            $upd_plan['type'] = $request->type;
            $upd_plan['amount'] = $request->amount;
            $upd_plan['status'] = $request->status;


            $update_plan = $upd_plan->save();

            if($update_plan){
                
                return response()->json([
                    
                    'status_code' => Response::HTTP_OK,
                    'message' => 'Plan has been updated',
                
                ], Response::HTTP_OK);

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


    public function delete_plan($uuid){

        try{

            $del_plan = Plan::where('uuid', $uuid)->first();
            
            if(!$del_plan)
            {
                
                return response()->json([

                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Record not found'

                ], Response::HTTP_NOT_FOUND);


            }else{

                $delete_plan = Plan::destroy($del_plan->id);

                if($delete_plan){
                
                    return response()->json([
                        
                        'status_code' => Response::HTTP_OK,
                        'message' => 'Plan has been deleted',
                    
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


    
    public function get_plan(){

        $get_all_plans = Plan::all();
    
        if($get_all_plans){
            
            return response()->json([
                    
                'status_code' => Response::HTTP_OK,
                'data' => $get_all_plans,
    
            ], Response::HTTP_OK);
    
        }
    
    }



}
