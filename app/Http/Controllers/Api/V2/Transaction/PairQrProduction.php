<?php

namespace App\Http\Controllers\Api\V2\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PairQrProduction extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "qr_code_alpha" => "required|exists:TRQRA,TRQRA_CODE|max:255",
            "sticker_code" => "required|exists:MASCO,MASCO_CODE|max:255",
            "batch_code" => "required|exists:MABPR,MABPR_CODE",
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
                ]
            ],
            "first_row" => true
        ]);

        if ($check_sticker_paired["MASCO_TRQAH_CODE"] != NULL) {
            return response()->json([
                'message' => "Sticker Code Production already paired",
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_STATUS"] == "3") {
            return response()->json([
                'message' => "QR Code Production already paired",
                'data' => $request->all(),
                'err_code' => "E8"
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
                'err_code' => "E7"
            ], 400);
        }

        if ($check_qr_paired["TRQRA_STATUS"] == "3") {
            return response()->json([
                'message' => "QR Code Production already paired",
                'data' => $request->all(),
                'err_code' => "E8"
            ], 400);
        }

        $data_batch = std_get([
            "table_name" => "MABPR",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->batch_code,
                ]
            ],
            "first_row" => true
        ]);

        if (($check_sticker_paired["MASCO_MBRAN_CODE"] != $check_qr_paired["TRQRA_MBRAN_CODE"]) || ($check_sticker_paired["MASCO_MBRAN_CODE"] != $data_batch["MABPR_MBRAN_CODE"])) {
            return response()->json([
                'message' => "Please scan QR with same brand",
                'data' => $request->all(),
                'err_code' => "E10"
            ], 400);
        }

        $data_employee = std_get([
            "table_name" => "STBPR",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "STBPR_EMP_CODE",
                    "operator" => "=",
                    "value" => $request->scan_by,
                ],
                [
                    "field_name" => "STBPR_MABPR_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "order_by" => [
                [
                    "field" => "STBPR_ID",
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

        
        if ($data_batch == null || $data_batch["MABPR_ACTIVATION_STATUS"] != "1") {
            return response()->json([
                'message' => "Batch inactive",
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }

        if ($data_batch["MABPR_IS_REPORTED"] == "1") {
            return response()->json([
                'message' => "Batch already reported",
                'data' => $request->all(),
                'err_code' => "E9"
            ], 400);
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" =>  $request->batch_code,
                ]
            ],
            "count" => true,
            "first_row" => true
        ]);

        if ($data_batch["MABPR_EXPECTED_QTY"] <= $count_paired_qr ) {
            return response()->json([
                'message' => "Target quantity batch reached",
                'data' => $request->all(),
                'err_code' => "E6"
            ], 400);
        }

        $update_data_sticker_code = [
            "MASCO_TRQAH_CODE" => $request->qr_code_alpha,
            "MASCO_STATUS" => 1, // 1 mean pair
            "MASCO_MABPR_CODE" => $data_batch["MABPR_CODE"],
            "MASCO_MABPR_TEXT" => $data_batch["MABPR_TEXT"],
            "MASCO_UPDATED_BY" => $request->scan_by,
            "MASCO_UPDATED_TEXT" => $request->scan_by_text,
            "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            "MASCO_TYPE" => 1,
        ];
        
        $update_qr_alpha = [
            "TRQRA_MASCO_CODE" => $request->sticker_code,
            "TRQRA_MABPR_CODE" => $data_batch["MABPR_CODE"],
            "TRQRA_MABPR_TEXT" => $data_batch["MABPR_TEXT"],
            "TRQRA_MCOMP_CODE" => $data_batch["MABPR_MCOMP_CODE"],
            "TRQRA_MCOMP_TEXT" => $data_batch["MABPR_MCOMP_TEXT"],
            "TRQRA_MBRAN_CODE" => $data_batch["MABPR_MBRAN_CODE"],
            "TRQRA_MBRAN_TEXT" => $data_batch["MABPR_MBRAN_TEXT"],
            "TRQRA_MPRCA_CODE" => $data_batch["MABPR_MPRCA_CODE"],
            "TRQRA_MPRCA_TEXT" => $data_batch["MABPR_MPRCA_TEXT"],
            "TRQRA_MPRDT_CODE" => $data_batch["MABPR_MPRDT_CODE"],
            "TRQRA_MPRDT_TEXT" => $data_batch["MABPR_MPRDT_TEXT"],
            "TRQRA_MPRMO_CODE" => $data_batch["MABPR_MPRMO_CODE"],
            "TRQRA_MPRMO_TEXT" => $data_batch["MABPR_MPRMO_TEXT"],
            "TRQRA_MPRVE_CODE" => $data_batch["MABPR_MPRVE_CODE"],
            "TRQRA_MPRVE_SKU" => $data_batch["MABPR_MPRVE_SKU"],
            "TRQRA_MPRVE_TEXT" => $data_batch["MABPR_MPRVE_TEXT"],
            "TRQRA_MPRVE_NOTES" => $data_batch["MABPR_MPRVE_NOTES"],
            "TRQRA_MAPLA_CODE" => $data_batch["MABPR_MAPLA_CODE"],
            "TRQRA_MAPLA_TEXT" => $data_batch["MABPR_MAPLA_TEXT"],
            "TRQRA_STATUS" => "1",
            "TRQRA_EMP_SCAN_BY" => $request->scan_by,
            "TRQRA_EMP_SCAN_TEXT" => $request->scan_by_text,
            "TRQRA_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
            "TRQRA_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
            "TRQRA_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
            "TRQRA_TYPE" => 1,
        ];

        if ($request->scan_lat != null || $request->scan_lat != "" || $request->scan_lng != null || $request->scan_lng != "") {
            $update_qr_alpha["TRQRA_EMP_SCAN_LAT"] = $request->scan_lat;
            $update_qr_alpha["TRQRA_EMP_SCAN_LNG"] = $request->scan_lng;
        }

        $update_sticker_code_table = std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->sticker_code],
            "data" => $update_data_sticker_code
        ]);

        $update_qr_alpha_table = std_update([
            "table_name" => "TRQRA",
            "where" => ["TRQRA_CODE" => $request->qr_code_alpha],
            "data" => $update_qr_alpha
        ]);

        $update_batch_production = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $data_batch["MABPR_CODE"]],
            "data" => [
                "MABPR_PAIRED_QTY" => $data_batch["MABPR_PAIRED_QTY"] + 1
            ]
        ]);

        if ($update_sticker_code_table == false || $update_qr_alpha_table == false || $update_batch_production == false) {
            return response()->json([
                'message' => "Error occured when update data",
                'data' => $request->all()
            ], 500);
        }

        if ($update_sticker_code_table == false || $update_qr_alpha_table == false) {
            return response()->json([
                'message' => "Error occured when update data",
                'data' => $request->all(),
            ], 500);
        }

        if (($data_batch["MABPR_EXPECTED_QTY"]-1) == $count_paired_qr ) {
            return response()->json([
                "response" => "Update code success with last paired",
                "batch_target" => $data_batch["MABPR_EXPECTED_QTY"],
                "paired_qr" => $count_paired_qr+1,
            ], 201);
        }

        return response()->json([
            "response" => "Update code success"
        ], 200);
    }
}
