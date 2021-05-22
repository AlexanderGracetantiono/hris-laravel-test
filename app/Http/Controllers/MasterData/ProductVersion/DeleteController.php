<?php

namespace App\Http\Controllers\MasterData\ProductVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index(Request $request)
    {
        $delete_res = std_update([
            "table_name" => "MPRVE",
            "where" => [
                "MPRVE_CODE" => $request->code,
                "MPRVE_MBRAN_CODE" => session("brand_code")
            ],
            "data" => [
                "MPRVE_IS_DELETED" => 1
            ]
        ]);

        $delete_attribute = std_delete([
            "table_name" => "TRPAT",
            "where" => [
                "TRPAT_KEY_CODE" => $request->code,
                "TRPAT_KEY_TYPE" => "4",
                "TRPAT_MBRAN_CODE" => session("brand_code"),
            ],
        ]);

        if ($delete_res === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ],500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ],200);
        
    }
}
