<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadController extends Controller
{

    public function add_lead_by_api(Request $request)
    {
        
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|nullable|string',
            'phone' => 'nullable|string',
            'message' => 'nullable|string',
            'ip' => 'nullable|string',
            'brand_name' => 'nullable|string',
            'page_url' => 'nullable|string',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new lead

        $data = array(

            'uuid' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'ip' => $request->ip,
            'brand_name' => $request->brand_name,
            'page_url' => $request->page_url,

        );

        

        $lead = Lead::create($data);

        // Return success response
        return response()->json([
            'status' => '201',
            'message' => 'Lead created successfully.',
            // 'data' => $lead
        ], 201);

    }


    public function get_lead()
    {

        $lead = Lead::all();

        return response()->json([
            'status' => '200',
            'data' => $lead
        ], 200);

    }


}
