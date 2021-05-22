<?php

namespace App\Http\Controllers\Api\V1_Test_Lab;

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

        if ($check_qr_paired["TRQRZ_MASCO_CODE"] != null) {
            return response()->json([
                'message' => "QR Code Zeta already paired",
                'data' => $request->all(),
                'err_code' => "E2"
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

        if ($check_sticker_paired["MASCO_TRQZH_CODE"] != null) {
            return response()->json([
                'message' => "Sticker Code already paired",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_TRQAH_CODE"] == null) {
            return response()->json([
                'message' => "Sticker code has not been paired with QR alpha",
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_NOTES"] == null) {
            return response()->json([
                'message' => "Sticker code has not been paired with bridge",
                'data' => $request->all(),
                'err_code' => "E5"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_MBRAN_CODE"] != $check_qr_paired["TRQRZ_MBRAN_CODE"]) {
            return response()->json([
                'message' => "Please scan QR within same brand",
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        $update_data_sticker_code = [
            "MASCO_TRQZH_CODE" => $request->qr_code_zeta,
            "MASCO_STATUS" => 1, // 1 mean pair
            "MASCO_UPDATED_BY" => $request->scan_by,
            "MASCO_UPDATED_TEXT" => $request->scan_by_text,
            "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_sticker_code_table = std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->sticker_code],
            "data" => $update_data_sticker_code
        ]);

        $update_qr_zeta = [
            "TRQRZ_STATUS" => 1,
            "TRQRZ_ACCEPTED_BY_STORE" => 1,
            "TRQRZ_MASCO_CODE" => $request->sticker_code,
            "TRQRZ_EMP_SCAN_BY" => $request->scan_by,
            "TRQRZ_EMP_SCAN_TEXT" => $request->scan_by_text,
            "TRQRZ_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
            "TRQRZ_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
            "TRQRZ_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
            "TRQRZ_TYPE" => 2,
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
                    "value" => "2",
                ],
            ],true);

            if ($plant == null) {
                return response()->json([
                    'message' => "Plant not found",
                    'data' => $request->all(),
                    'err_code' => "E6"
                ], 400);
            }

            $update_qr_zeta["TRQRZ_MAPLA_CODE"] = $plant["MAPLA_CODE"];
            $update_qr_zeta["TRQRZ_MAPLA_TEXT"] = $plant["MAPLA_TEXT"];
        }
        else {
            $update_qr_zeta["TRQRZ_EMP_SCAN_LAT"] = $request->scan_lat;
            $update_qr_zeta["TRQRZ_EMP_SCAN_LNG"] = $request->scan_lng;
        }

        $update_qr_zeta_table = std_update([
            "table_name" => "TRQRZ",
            "where" => ["TRQRZ_CODE" => $request->qr_code_zeta],
            "data" => $update_qr_zeta
        ]);

        if ($update_sticker_code_table == false || $update_qr_zeta_table == false) {
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
