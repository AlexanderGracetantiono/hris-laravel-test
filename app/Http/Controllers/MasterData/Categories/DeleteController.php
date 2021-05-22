<?php

namespace App\Http\Controllers\MasterData\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }

    public function index(Request $request)
    {
        $code = get_master_product_category("*", [
            [
                "field_name" => "MPRCA_ID",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        $check_exist_product = get_master_product("*", [
            [
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRDT_MPRCA_CODE",
                "operator" => "=",
                "value" => $code["MPRCA_CODE"],
            ],
            [
                "field_name" => "MPRDT_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ],true);
        if ($check_exist_product != null) {
            return response()->json([
                'message' => "Cannot delete category because category still active in product"
            ], 500);
        }

        $check_exist_product_model = get_master_product_model("*", [
            [
                "field_name" => "MPRMO_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRMO_MPRCA_CODE",
                "operator" => "=",
                "value" => $code["MPRCA_CODE"],
            ],
            [
                "field_name" => "MPRMO_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ],true);
        if ($check_exist_product_model != null) {
            return response()->json([
                'message' => "Cannot delete category because category still active in model"
            ], 500);
        }

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
                "field_name" => "MPRVE_MPRCA_CODE",
                "operator" => "=",
                "value" => $code["MPRCA_CODE"],
            ],
            [
                "field_name" => "MPRVE_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ],true);
        if ($check_exist_product_version != null) {
            return response()->json([
                'message' => "Cannot delete category because category still active in version"
            ], 500);
        }

        $delete_res = std_update([
            "table_name" => "MPRCA",
            "where" => [
                "MPRCA_ID" =>  $request->code
            ],
            "data" => [
                "MPRCA_IS_DELETED" => 1
            ]
        ]);
        if ($delete_res === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ], 500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ], 200);
    }
}
