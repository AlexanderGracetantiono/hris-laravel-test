<?php

namespace App\Http\Controllers\Api\V2_Test_Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PairBridge extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "sticker_code" => "required|exists:MASCO,MASCO_CODE|max:255",
            "bridge" => "required",
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

        if ($check_sticker_paired["MASCO_NOTES"] != null) {
            return response()->json([
                'message' => "Sticker Code already paired with bridge",
                'data' => $request->all(),
                'err_code' => "E2"
            ], 400);
        }

        if ($check_sticker_paired["MASCO_TRQAH_CODE"] == null) {
            return response()->json([
                'message' => "Sticker code has not been paired with QR alpha",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        $check_exists_bridge = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request->bridge,
                ],
            ],
            "first_row" => true
        ]);

        if ($check_exists_bridge != null) {
            return response()->json([
                'message' => "Bridge already in CekOri System",
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }

        $update_data_sticker_code = [
            "MASCO_NOTES" => $request->bridge,
            "MASCO_UPDATED_BY" => $request->scan_by,
            "MASCO_UPDATED_TEXT" => $request->scan_by_text,
            "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_sticker_code_table = std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->sticker_code],
            "data" => $update_data_sticker_code
        ]);


        if ($update_sticker_code_table == false) {
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
