<?php

namespace App\Http\Controllers\MasterData\ProductModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index(Request $request)
    {
        $check_exist_product_version = get_master_product_version("*", [
            [
                "field_name" => "MPRVE_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRVE_MPRMO_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
            [
                "field_name" => "MPRVE_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ],true);
        if ($check_exist_product_version != null) {
            return response()->json([
                'message' => "Cannot delete model because model still active in version"
            ], 500);
        }

        $delete_res = std_update([
            "table_name" => "MPRMO",
            "where" => [
                "MPRMO_CODE" => $request->code
            ],
            "data" => [
                "MPRMO_IS_DELETED" => 1
            ]
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
