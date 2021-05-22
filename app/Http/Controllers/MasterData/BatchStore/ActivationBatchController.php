<?php

namespace App\Http\Controllers\MasterData\BatchStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivationBatchController extends Controller
{
    public function __construct() {
        check_is_role_allowed([8]);
    }
    
    public function activate(Request $request)
    {
        $update_res = std_update([
            "table_name" => "MBSTR",
            "where" => ["MBSTR_CODE" => $request->MBSTR_CODE],
            "data" => [
                "MBSTR_ACTIVATION_STATUS" => 2,
                "MBSTR_NOTES" => $request->MBSTR_NOTES,
            ]
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "Failed to activate batch"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully activate"
        ],200);
    }

    public function ready_sale(Request $request)
    {
        
        $update_res = std_update([
            "table_name" => "MBSTR",
            "where" => ["MBSTR_CODE" => $request->MBSTR_CODE],
            "data" => [
                "MBSTR_ACTIVATION_STATUS" => 2,
                "MBSTR_NOTES" => $request->MBSTR_NOTES,
            ]
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "Failed to activate batch"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully closed"
        ],200);
    }

    public function close(Request $request)
    {
        $update_res = std_update([
            "table_name" => "MBSTR",
            "where" => ["MBSTR_CODE" => $request->MBSTR_CODE],
            "data" => [
                "MBSTR_ACTIVATION_STATUS" => 3,
                "MBSTR_NOTES" => $request->MBSTR_NOTES,
            ]
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "Failed to activate batch"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully closed"
        ],200);
    }
}
