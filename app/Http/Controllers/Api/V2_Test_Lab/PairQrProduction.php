<?php

namespace App\Http\Controllers\Api\V2_Test_Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PairQrProduction extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "qr_code_alpha" => "required|exists:TRQRA,TRQRA_CODE",
            "sticker_code" => "required|exists:MASCO,MASCO_CODE",
            "chain_code" => "required|unique:MASCO,MASCO_NOTES",
            "scan_by" => "required|exists:MAEMP,MAEMP_CODE",
            "scan_by_text" => "required",
            "scan_lat" => "max:255",
            "scan_lng" => "max:255",
            "plant_code" => "max:255",
            "scan_device_id" => "required",
            "scan_app_version" => "required",
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function index(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res,
                'data' => $request->all(),
                'err_code' => "E1"
            ], 400);
        }

        $check_sticker_paired = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_CODE",
                    "operator" => "=",
                    "value" => $request->sticker_code,
                ]
            ],
            "first_row" => true
        ]);
        if ($check_sticker_paired["MASCO_TRQAH_CODE"] != NULL) {
            return response()->json([
                'message' => "Sticker Code Production already paired",
                'data' => $request->all(),
                'err_code' => "E2"
            ], 400);
        }

        $check_bridge_exists = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request->chain_code,
                ]
            ],
            "first_row" => true
        ]);
        if ($check_bridge_exists != null) {
            return response()->json([
                'message' => "Chain already exists",
                'data' => $request->all(),
                'err_code' => "E5"
            ], 400);
        }

        $check_chain_is_bridge = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_CODE",
                    "operator" => "=",
                    "value" => $request->chain_code,
                ]
            ],
            "first_row" => true
        ]);
        $check_chain_is_alpha = std_get([
            "table_name" => "TRQRA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRA_CODE",
                    "operator" => "=",
                    "value" => $request->chain_code,
                ]
            ],
            "first_row" => true
        ]);
        $check_chain_is_zeta = std_get([
            "table_name" => "TRQRZ",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request->chain_code,
                ]
            ],
            "first_row" => true
        ]);
        if ($check_chain_is_alpha != null || $check_chain_is_zeta != null || $check_chain_is_bridge != null) {
            return response()->json([
                'message' => "Do not scan alpha / zeta / bridge on chain",
                'data' => $request->all(),
                'err_code' => "E6"
            ], 400);
        }
        
        $check_qr_paired = std_get([
            "table_name" => "TRQRA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRA_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_alpha,
                ]
            ],
            "first_row" => true
        ]);

        if ($check_qr_paired["TRQRA_MASCO_CODE"] != NULL) {
            return response()->json([
                'message' => "QR Code Production already paired",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        if ($check_qr_paired["TRQRA_MBRAN_CODE"] != $check_sticker_paired["MASCO_MBRAN_CODE"]) {
            return response()->json([
                'message' => "Please scan QR within same brand",
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        $update_data_sticker_code = [
            "MASCO_TRQAH_CODE" => $request->qr_code_alpha,
            "MASCO_STATUS" => 1, // 1 mean pair
            "MASCO_NOTES" => $request->chain_code,
            "MASCO_UPDATED_BY" => $request->scan_by,
            "MASCO_UPDATED_TEXT" => $request->scan_by_text,
            "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            "MASCO_TYPE" => 2,
        ];
        $update_sticker_code_table = std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->sticker_code],
            "data" => $update_data_sticker_code
        ]);

        $update_qr_alpha = [
            "TRQRA_STATUS" => "1",
            "TRQRA_ACCEPTED_BY_STORE" => 1,
            "TRQRA_MASCO_CODE" => $request->sticker_code,
            "TRQRA_EMP_SCAN_BY" => $request->scan_by,
            "TRQRA_EMP_SCAN_TEXT" => $request->scan_by_text,
            "TRQRA_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
            "TRQRA_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
            "TRQRA_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
            "TRQRA_TYPE" => 2,
        ];

        if ($request->scan_lat == null || $request->scan_lat == "" || $request->scan_lng == null || $request->scan_lng == "") {
            $plant = get_master_plant("*",[
                [
                    "field_name" => "MAPLA_CODE",
                    "operator" => "=",
                    "value" => $request->plant_code,
                ],
                [
                    "field_name" => "MAPLA_TYPE",
                    "operator" => "=",
                    "value" => "1",
                ],
            ],true);

            if ($plant == null) {
                return response()->json([
                    'message' => "Plant not found",
                    'data' => $request->all(),
                    'err_code' => "E4"
                ], 400);
            }

            $update_qr_alpha["TRQRA_MAPLA_CODE"] = $plant["MAPLA_CODE"];
            $update_qr_alpha["TRQRA_MAPLA_TEXT"] = $plant["MAPLA_TEXT"];
        }
        else {
            $update_qr_alpha["TRQRA_EMP_SCAN_LAT"] = $request->scan_lat;
            $update_qr_alpha["TRQRA_EMP_SCAN_LNG"] = $request->scan_lng;
        }

        $update_qr_alpha_table = std_update([
            "table_name" => "TRQRA",
            "where" => ["TRQRA_CODE" => $request->qr_code_alpha],
            "data" => $update_qr_alpha
        ]);

        if ($update_sticker_code_table == false || $update_qr_alpha_table == false) {
            return response()->json([
                'message' => "Error occured when update data",
                'data' => $request->all()
            ], 500);
        }

        return response()->json([
            "response" => "Update code success"
        ], 200);
    }
}
