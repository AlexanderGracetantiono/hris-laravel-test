<?php

namespace App\Http\Controllers\MasterData\BatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivationBatchController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }

    public function check_exists_pool_product($batch_packaging_code)
    {
        $batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $batch_packaging_code["MABPA_MABPR_CODE"],
            ]
        ],true);

        $check_exist_pool = get_pool_product("*",[
            [
                "field_name" => "POPRD_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "POPRD_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "POPRD_MPRCA_CODE",
                "operator" => "=",
                "value" => $batch_production["MABPR_MPRCA_CODE"],
            ],
            [
                "field_name" => "POPRD_MPRDT_CODE",
                "operator" => "=",
                "value" => $batch_production["MABPR_MPRDT_CODE"],
            ],
            [
                "field_name" => "POPRD_MPRMO_CODE",
                "operator" => "=",
                "value" => $batch_production["MABPR_MPRMO_CODE"],
            ],
            [
                "field_name" => "POPRD_MPRVE_CODE",
                "operator" => "=",
                "value" => $batch_production["MABPR_MPRVE_CODE"],
            ],
        ],true);

        if ($check_exist_pool == null) {
            $temp_code = generate_code(session('company_code'),5,"POPRD");
            $code = strtoupper($temp_code["data"]);

            $status = std_insert([
                "table_name" => "POPRD",
                "data" => [
                    "POPRD_CODE" => $code,
                    "POPRD_QTY" => $batch_packaging_code["MABPA_QTY"],
                    "POPRD_QTY_LEFT" => $batch_packaging_code["MABPA_QTY"],
                    "POPRD_PAIRED_QTY" => "0",
                    "POPRD_MCOMP_CODE" => session("company_code"),
                    "POPRD_MCOMP_TEXT" => session("company_name"),
                    "POPRD_MBRAN_CODE" => session("brand_code"),
                    "POPRD_MBRAN_TEXT" => session("brand_name"),
                    "POPRD_MPRCA_CODE" => $batch_production["MABPR_MPRCA_CODE"],
                    "POPRD_MPRCA_TEXT" => $batch_production["MABPR_MPRCA_TEXT"],
                    "POPRD_MPRDT_CODE" => $batch_production["MABPR_MPRDT_CODE"],
                    "POPRD_MPRDT_TEXT" => $batch_production["MABPR_MPRDT_TEXT"],
                    "POPRD_MPRMO_CODE" => $batch_production["MABPR_MPRMO_CODE"],
                    "POPRD_MPRMO_TEXT" => $batch_production["MABPR_MPRMO_TEXT"],
                    "POPRD_MPRVE_CODE" => $batch_production["MABPR_MPRVE_CODE"],
                    "POPRD_MPRVE_TEXT" => $batch_production["MABPR_MPRVE_TEXT"],
                    "POPRD_MPRVE_SKU" => $batch_production["MABPR_MPRVE_SKU"],
                    "POPRD_MPRVE_NOTES" => $batch_production["MABPR_MPRVE_NOTES"],
                    "POPRD_CREATED_BY" => session("user_id"),
                    "POPRD_CREATED_TEXT" => session("user_name"),
                    "POPRD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ]
            ]);
        }
        else {
            $code = $check_exist_pool["POPRD_CODE"];

            $status = std_update([
                "table_name" => "POPRD",
                "where" => ["POPRD_ID" => $check_exist_pool["POPRD_ID"]],
                "data" => [
                    "POPRD_QTY" => $check_exist_pool["POPRD_QTY"] + $batch_packaging_code["MABPA_QTY"],
                    "POPRD_QTY_LEFT" => $check_exist_pool["POPRD_QTY_LEFT"] + $batch_packaging_code["MABPA_QTY"],
                    "POPRD_MPRVE_SKU" => $batch_production["MABPR_MPRVE_SKU"],
                    "POPRD_MPRVE_NOTES" => $batch_production["MABPR_MPRVE_NOTES"],
                    "POPRD_UPDATED_BY" => session("user_id"),
                    "POPRD_UPDATED_TEXT" => session("user_name"),
                    "POPRD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ]
            ]);
        }

        return ["status" => $status, "code" => $code];
    }

    public function activate(Request $request)
    {
        $batch_packaging_code = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $request->MABPA_CODE,
            ]
        ],true);

        $check_exist_pool = $this->check_exists_pool_product($batch_packaging_code);
        if ($check_exist_pool["status"] == false) {
            return response()->json([
                'message' => "Something wrog when adjusting pool product, please contact admin"
            ],500);
        }

        $update_res = std_update([
            "table_name" => "MABPA",
            "where" => ["MABPA_CODE" => $request->MABPA_CODE],
            "data" => [
                "MABPA_NOTES" => $request->MABPA_NOTES,
                "MABPA_ACTIVATION_STATUS" => 1,
                "MABPA_ACTIVATION_TIMESTAMP" => date("Y-m-d H:i:s"),
                "MABPA_POPRD_CODE" => $check_exist_pool["code"]
            ]
        ]);

        $update_batch_packaging = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $batch_packaging_code["MABPA_MABPR_CODE"]],
            "data" => [
                "MABPR_ACTIVATION_STATUS" => 3,
            ]
        ]);

        if ($update_batch_packaging == false) {
            return response()->json([
                'message' => "Failed to activate batch"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully activate"
        ],200);
    }

    public function close(Request $request)
    {
        $data = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $request->MABPA_CODE,
            ],
        ],true);

        if ($data["MABPA_QTY"] != $data["MABPA_PAIRED_QTY"]) {
            return response()->json([
                'message' => "Cannot close batch because paired QR Production not match with batch quantity"
            ],400);
        }

        std_update([
            "table_name" => "MABPA",
            "where" => ["MABPA_CODE" => $request->MABPA_CODE],
            "data" => [
                "MABPA_ACTIVATION_STATUS" => 2,
                "MABPA_CLOSED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);
        
        return response()->json([
            'message' => "Data succesfully closed"
        ],200);

    }
    
}
