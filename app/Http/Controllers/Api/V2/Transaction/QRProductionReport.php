<?php

namespace App\Http\Controllers\Api\V2\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QRProductionReport extends Controller
{
    public function report(Request $request)
    {

        $validate = Validator::make($request->all(), [
            "qr_code" => "required|max:255|exists:TRQRA,TRQRA_CODE",
            "sticker_code" => "required|max:255|exists:MASCO,MASCO_CODE",
            "batch_code" => "required|exists:MABPR,MABPR_CODE|max:255",
            "report_note" => "required|max:255",
            "scan_by" => "required|max:255",
            "scan_by_text" => "required|max:255",
            "scan_lat" => "max:255",
            "scan_lng" => "max:255",
            "plant_code" => "max:255",
            "scan_device_id" => "required|max:255",
            "scan_app_version" => "required|max:255",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->all(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        } else {
            $checking_sticker_exists = std_get([
                "table_name" => "MASCO",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MASCO_CODE",
                        "operator" => "=",
                        "value" => $request->sticker_code
                    ],
                ],
                "first_row" => true
            ]);

            if ($checking_sticker_exists["MASCO_TRQAH_CODE"] != NULL) {
				return response()->json([
                    "response" => "Sticker code have been paired, report canceled",
                    "data" => $request->all(),
                    "err_code" => "E2"
                ], 400);
			}

			if ($checking_sticker_exists["MASCO_STATUS"] == "3") {
				return response()->json([
                    "response" => "Sticker code have been rejected, report canceled",
                    "data" => $request->all(),
                    "err_code" => "E6"
                ], 400);
            }

            $checking_qr_exists = std_get([
                "table_name" => "TRQRA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "TRQRA_CODE",
                        "operator" => "=",
                        "value" => $request->qr_code
                    ],
                ],
                "first_row" => true
            ]);

            if ($checking_qr_exists["TRQRA_MASCO_CODE"] != NULL) {
				return response()->json([
                    "response" => "QR code have been paired, report canceled",
                    "data" => $request->all(),
                    "err_code" => "E7"
                ], 400);
			}

			if ($checking_qr_exists["TRQRA_STATUS"] == "3") {
				return response()->json([
                    "response" => "QR code have been rejected, report canceled",
                    "data" => $request->all(),
                    "err_code" => "E8"
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
                        "value" => "1",
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
            if ($data_batch["MABPR_ACTIVATION_STATUS"] != 1) {
                return response()->json([
                    'message' => "Batch inactive",
                    'data' => $request->all(),
                    'err_code' => "E4"
                ], 400);
            }

            $current_qty = $data_batch["MABPR_PAIRED_QTY"] + 1;

            if ($data_batch["MABPR_EXPECTED_QTY"] < $current_qty) {
                return response()->json([
                    'message' => "Target quantity batch reached",
                    'data' => $request->all(),
                    'err_code' => "E5"
                ], 400);
            }

            $update_qr_alpha = [
                "TRQRA_NOTES" => $request->report_note,
                "TRQRA_STATUS" => 3,// 3 for rejected
                "TRQRA_MABPR_CODE" => $data_batch["MABPR_CODE"],
                "TRQRA_MABPR_TEXT" => $data_batch["MABPR_TEXT"],
                "TRQRA_EMP_SCAN_BY" => $request->scan_by,
                "TRQRA_EMP_SCAN_TEXT" => $request->scan_by_text,
                "TRQRA_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $update_res_qr_alpha = std_update([
                "table_name" => "TRQRA",
                "where" => ["TRQRA_CODE" => $request->qr_code],
                "data" => $update_qr_alpha
            ]);

            $update_masco = [
                "MASCO_NOTES" => $request->report_note,
                "MASCO_STATUS" => 3,// 3 for rejected
                "MASCO_MABPR_CODE" => $data_batch["MABPR_CODE"],
                "MASCO_MABPR_TEXT" => $data_batch["MABPR_TEXT"],
                "MASCO_UPDATED_BY" => $request->scan_by,
                "MASCO_UPDATED_TEXT" => $request->scan_by_text,
                "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $update_res_masco = std_update([
                "table_name" => "MASCO",
                "where" => ["MASCO_CODE" => $request->sticker_code],
                "data" => $update_masco
            ]);

            $update_batch_production = std_update([
                "table_name" => "MABPR",
                "where" => ["MABPR_CODE" => $data_batch["MABPR_CODE"]],
                "data" => [
                    "MABPR_PAIRED_QTY" => $data_batch["MABPR_PAIRED_QTY"] + 1
                ]
            ]);

            return response()->json([
                "response" => "Update code success"
            ], 200);
        }
    }
}
