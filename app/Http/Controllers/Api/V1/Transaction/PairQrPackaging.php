<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PairQrPackaging extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "qr_code_zeta" => "required|exists:TRQRZ,TRQRZ_CODE|max:255",
            "sticker_code" => "required|exists:MASCO,MASCO_CODE|max:255",
            "batch_code" => "required|exists:SUBPA,SUBPA_CODE|max:255",
            "scan_by" => "required|max:255|exists:MAEMP,MAEMP_CODE",
            "scan_by_text" => "required|max:255",
            "scan_lat" => "max:255",
            "scan_lng" => "max:255",
            "plant_code" => "max:255",
            "scan_device_id" => "required|max:255",
            "scan_app_version" => "required|max:255",
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
                ],
            ],
            "first_row" => true
        ]);

        $check_qr_paired = std_get([
            "table_name" => "TRQRZ",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_zeta,
                ],
            ],
            "first_row" => true
        ]);

        if ($check_sticker_paired["MASCO_TRQZH_CODE"] != null || $check_qr_paired["TRQRZ_MASCO_CODE"] != null) {
            return response()->json([
                'message' => "QR Code / Sticker Code Packaging already paired",
                'data' => $request->all(),
                'err_code' => "E2"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_TYPE"] != 1) {
            return response()->json([
                'message' => "Only able to scan manufacture bridge",
                'data' => $request->all(),
                'err_code' => "E10"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_MBRAN_CODE"] != $check_qr_paired["TRQRZ_MBRAN_CODE"]) {
            return response()->json([
                'message' => "Please scan QR with same brand",
                'data' => $request->all(),
                'err_code' => "E11"
            ], 400);
        }

        $data_employee = std_get([
            "table_name" => "STBPA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "STBPA_EMP_CODE",
                    "operator" => "=",
                    "value" => $request->scan_by,
                ],
                [
                    "field_name" => "STBPA_SUBPA_STATUS",
                    "operator" => "=",
                    "value" => "1",
                ],
            ],
            "order_by" => [
                [
                    "field" => "STBPA_ID",
                    "type" => "DESC"
                ]
            ],
            "first_row" => true
        ]);
        if ($data_employee == null) {
            return response()->json([
                'message' => "Employee not assigned to any batch",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        $data_batch = std_get([
            "table_name" => "SUBPA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->batch_code,
                ]
            ],
            "first_row" => true
        ]);
        if ($data_batch == null|| $data_batch["SUBPA_ACTIVATION_STATUS"] != "1") {
            return response()->json([
                'message' => "Batch inactive",
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }

        if ($data_batch["SUBPA_IS_REPORTED"] == 1) {
            return response()->json([
                'message' => "Batch already reported",
                'data' => $request->all(),
                'err_code' => "E11"
            ], 400);
        }

        $batch_product_version = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $check_sticker_paired["MASCO_MABPR_CODE"]
            ]
        ],true);

        if ($batch_product_version == null) {
            return response()->json([
                'message' => "Sticker code not assigned to batch production",
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        $pool_product_version = get_pool_product("*",[
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $data_batch["SUBPA_POPRD_CODE"]
            ]
        ],true);

        if ($pool_product_version == null) {
            return response()->json([
                'message' => "Batch packaging not assigned to any pool product",
                'data' => $request->all(),
                'err_code' => "E8"
            ], 400);
        }

        if ($batch_product_version["MABPR_MPRVE_CODE"] != $pool_product_version["POPRD_MPRVE_CODE"]) {
            return response()->json([
                'message' => "Please scan production sticker code",
                'data' => $request->all(),
                'err_code' => "E9"
            ], 400);
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" =>  $request->batch_code,
                ]
            ],
            "count" => true,
            "first_row" => true
        ]);

        if ($data_batch["SUBPA_QTY"] <= $count_paired_qr) {
            return response()->json([
                'message' => "Target quantity batch reached",
                'data' => $request->all(),
                'err_code' => "E5"
            ], 400);
        }

        $update_data_sticker_code = [
            "MASCO_TRQZH_CODE" => $request->qr_code_zeta,
            "MASCO_STATUS" => 1, // 1 mean pair
            "MASCO_SUBPA_CODE" => $data_batch["SUBPA_CODE"],
            "MASCO_SUBPA_TEXT" => $data_batch["SUBPA_TEXT"],
            "MASCO_POPRD_CODE" => $data_batch["SUBPA_POPRD_CODE"],
            "MASCO_UPDATED_BY" => $request->scan_by,
            "MASCO_UPDATED_TEXT" => $request->scan_by_text,
            "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        

        $update_qr_zeta = [
            "TRQRZ_STATUS" => 1,
            "TRQRZ_MASCO_CODE" => $request->sticker_code,
            "TRQRZ_POPRD_CODE" => $data_batch["SUBPA_POPRD_CODE"],
            "TRQRZ_SUBPA_CODE" => $data_batch["SUBPA_CODE"],
            "TRQRZ_SUBPA_TEXT" => $data_batch["SUBPA_TEXT"],
            "TRQRZ_MAPLA_CODE" => $data_batch["SUBPA_MAPLA_CODE"],
            "TRQRZ_MAPLA_TEXT" => $data_batch["SUBPA_MAPLA_TEXT"],
            "TRQRZ_EMP_SCAN_BY" => $request->scan_by,
            "TRQRZ_EMP_SCAN_TEXT" => $request->scan_by_text,
            "TRQRZ_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
            "TRQRZ_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
            "TRQRZ_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
            "TRQRZ_TYPE" => 1,
        ];

        if ($request->scan_lat != null || $request->scan_lat != "" || $request->scan_lng != null || $request->scan_lng != "") {
            $update_qr_zeta["TRQRZ_EMP_SCAN_LAT"] = $request->scan_lat;
            $update_qr_zeta["TRQRZ_EMP_SCAN_LNG"] = $request->scan_lng;
        }

        $update_sticker_code_table = std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->sticker_code],
            "data" => $update_data_sticker_code
        ]);
        
        $update_qr_zeta_table = std_update([
            "table_name" => "TRQRZ",
            "where" => ["TRQRZ_CODE" => $request->qr_code_zeta],
            "data" => $update_qr_zeta
        ]);

        $update_sub_batch_packaging = std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $data_batch["SUBPA_CODE"]],
            "data" => [
                "SUBPA_PAIRED_QTY" => $data_batch["SUBPA_PAIRED_QTY"] + 1
            ]
        ]);

        if ($update_sticker_code_table == false || $update_qr_zeta_table == false || $update_sub_batch_packaging == false) {
            return response()->json([
                'message' => "Error occured when update data",
                'data' => $request->all()
            ], 500);
        }

        if (($data_batch["SUBPA_QTY"]-1) == $count_paired_qr ) {
            return response()->json([
                "response" => "Update code success with last paired",
                "batch_target" => $data_batch["SUBPA_QTY"],
                "paired_qr" => $count_paired_qr+1,
            ], 201);
        }

        return response()->json([
            "response" => "Update code success"
        ], 200);
    }
}
