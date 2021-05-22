<?php

namespace App\Http\Controllers\MasterData\BatchStore;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([8]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_batch_store("*",[
            [
                "field_name" => "MBSTR_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
            [
                "field_name" => "MBSTR_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
        ],true);

        $sub_batch_packaging = get_master_sub_batch_packaging("SUBPA_NOTES",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $data["MBSTR_SUBPA_CODE"],
            ],
        ],true);

        return view('master_data/batch_store/edit',[
            'data' => $data,
            'sub_batch_packaging' => $sub_batch_packaging,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MBSTR_DATE" => "required",
            "MBSTR_TEXT" => "required",
            "MBSTR_NOTES" => "required",
        ]);
            
        $attributeNames = [
            "MBSTR_DATE" => "Batch Date",
            "MBSTR_TEXT" => "Batch Name",
            "MBSTR_NOTES" => "Batch Notes",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "MBSTR_TEXT" => $request->MBSTR_TEXT,
            "MBSTR_DATE" => $request->MBSTR_DATE,
            "MBSTR_NOTES" => $request->MBSTR_NOTES,
            "MBSTR_ACTIVATION_STATUS" => "2",
            "MBSTR_UPDATED_BY" => session("user_id"),
            "MBSTR_UPDATED_TEXT" => session("user_name"),
            "MBSTR_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $data = get_master_batch_store("*",[
            [
                "field_name" => "MBSTR_CODE",
                "operator" => "=",
                "value" => $request->MBSTR_CODE
            ],
        ],true);

        $update_zeta = std_update([
            "table_name" => "TRQRZ",
            "where" => [
                "TRQRZ_SUBPA_CODE" => $data["MBSTR_SUBPA_CODE"],
                "TRQRZ_STATUS" => "1"
            ],
            "data" => [
                "TRQRZ_ACCEPTED_BY_STORE" => "1"
            ]
        ]);

        $list_sticker_code = std_get([
            "select" => ["MASCO_CODE","MASCO_TRQAH_CODE","MASCO_TRQZH_CODE"],
            "table_name" => "MASCO",
            "where" => [
                [
                    "field_name" => "MASCO_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $data["MBSTR_SUBPA_CODE"],
                ],
                [
                    "field_name" => "MASCO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code")
                ],
            ]
        ]);

        for ($i=0; $i < count($list_sticker_code); $i++) { 
            $update_alpha = std_update([
                "table_name" => "TRQRA",
                "where" => [
                    "TRQRA_MASCO_CODE" => $list_sticker_code[$i]["MASCO_CODE"],
                ],
                "data" => [
                    "TRQRA_ACCEPTED_BY_STORE" => "1"
                ]
            ]);
        }

        $update = std_update([
            "table_name" => "MBSTR",
            "where" => ["MBSTR_CODE" => $request->MBSTR_CODE],
            "data" => $update_data
        ]);

        $get_alpha_data = std_get([
            "table_name" => "TRQRA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRA_CODE",
                    "operator" => "=",
                    "value" => $list_sticker_code[0]["MASCO_TRQAH_CODE"]
                ]
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "MABPR",
                    "on1" => "MABPR_CODE",
                    "operator" => "=",
                    "on2" => "TRQRA_MABPR_CODE"
                ]
            ],
            "first_row" => true
        ]);

        $get_zeta_data = std_get([
            "table_name" => "TRQRZ",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $list_sticker_code[0]["MASCO_TRQZH_CODE"]
                ]
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "SUBPA",
                    "on1" => "SUBPA_CODE",
                    "operator" => "=",
                    "on2" => "TRQRZ_SUBPA_CODE"
                ]
            ],
            "first_row" => true
        ]);

        for ($i=0; $i < count($list_sticker_code); $i++) { 
            $data_header[$i] = [
                "SCHED_TRQRA_CODE" => $list_sticker_code[$i]["MASCO_TRQAH_CODE"],
                "SCHED_TRQRZ_CODE" => $list_sticker_code[$i]["MASCO_TRQZH_CODE"],
                "SCHED_MASCO_CODE" => $list_sticker_code[$i]["MASCO_CODE"],
                "SCHED_COUNTER" => 0,
                "SCHED_MBRAN_CODE" => session("brand_code"),
                "SCHED_MBRAN_NAME" => session("brand_name"),
                "SCHED_MCOMP_CODE" => session("company_code"),
                "SCHED_MCOMP_NAME" => session("company_name"),
                "SCHED_CREATED_BY" => session("user_code"),
                "SCHED_CREATED_TEXT" => session("user_name"),
                "SCHED_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];

            $id[$i] = std_insert_get_id([
                "table_name" => "SCHED",
                "data" => $data_header[$i]
            ]);

            $data_detail[$i] = [
                "SCDET_SCHED_ID" => $id[$i],
                "SCDET_MPRCA_CODE" => $get_alpha_data["TRQRA_MPRCA_CODE"],
                "SCDET_MPRCA_TEXT" => $get_alpha_data["TRQRA_MPRCA_TEXT"],
                "SCDET_MPRDT_CODE" => $get_alpha_data["TRQRA_MPRDT_CODE"],
                "SCDET_MPRDT_TEXT" => $get_alpha_data["TRQRA_MPRDT_TEXT"],
                "SCDET_MPRMO_CODE" => $get_alpha_data["TRQRA_MPRMO_CODE"],
                "SCDET_MPRMO_TEXT" => $get_alpha_data["TRQRA_MPRMO_TEXT"],
                "SCDET_MPRVE_CODE" => $get_alpha_data["TRQRA_MPRVE_CODE"],
                "SCDET_MPRVE_TEXT" => $get_alpha_data["TRQRA_MPRVE_TEXT"],
                "SCDET_MPRVE_SKU" => $get_alpha_data["TRQRA_MPRVE_SKU"],
                "SCDET_MPRVE_NOTES" => $get_alpha_data["TRQRA_MPRVE_NOTES"],
                "SCDET_MABPR_CODE" => $get_alpha_data["TRQRA_MABPR_CODE"],
                "SCDET_MABPR_TEXT" => $get_alpha_data["TRQRA_MABPR_TEXT"],
                "SCDET_MABPR_ADMIN_CODE" => $get_alpha_data["MABPR_CREATED_BY"],
                "SCDET_MABPR_ADMIN_TEXT" => $get_alpha_data["MABPR_CREATED_TEXT"],
                "SCDET_MABPR_STAFF_CODE" => $get_alpha_data["TRQRA_EMP_SCAN_BY"],
                "SCDET_MABPR_STAFF_TEXT" => $get_alpha_data["TRQRA_EMP_SCAN_TEXT"],
                "SCDET_MABPR_SCAN_TIMESTAMP" => $get_alpha_data["TRQRA_EMP_SCAN_TIMESTAMP"],
                "SCDET_MABPR_MAPLA_CODE" => $get_alpha_data["TRQRA_MAPLA_CODE"],
                "SCDET_MABPR_MAPLA_TEXT" => $get_alpha_data["TRQRA_MAPLA_TEXT"],
                "SCDET_SUBPA_CODE" => $get_zeta_data["TRQRZ_SUBPA_CODE"],
                "SCDET_SUBPA_TEXT" => $get_zeta_data["TRQRZ_SUBPA_TEXT"],
                "SCDET_SUBPA_ADMIN_CODE" => $get_zeta_data["SUBPA_CREATED_BY"],
                "SCDET_SUBPA_ADMIN_TEXT" => $get_zeta_data["SUBPA_CREATED_TEXT"],
                "SCDET_SUBPA_STAFF_CODE" => $get_zeta_data["TRQRZ_EMP_SCAN_BY"],
                "SCDET_SUBPA_STAFF_TEXT" => $get_zeta_data["TRQRZ_EMP_SCAN_TEXT"],
                "SCDET_SUBPA_SCAN_TIMESTAMP" => $get_zeta_data["TRQRZ_EMP_SCAN_TIMESTAMP"],
                "SCDET_SUBPA_MAPLA_CODE" => $get_zeta_data["TRQRZ_MAPLA_CODE"],
                "SCDET_SUBPA_MAPLA_TEXT" => $get_zeta_data["TRQRZ_MAPLA_TEXT"],
                "SCDET_CREATED_BY" => session("user_code"),
                "SCDET_CREATED_TEXT" => session("user_name"),
                "SCDET_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];

            std_insert([
                "table_name" => "SCDET",
                "data" => $data_detail[$i]
            ]);
        }

        if ($update != true) {
            return response()->json([
                'message' => "There was an error updating data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
