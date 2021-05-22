<?php

namespace App\Http\Controllers\Transaction\OrderQr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApprovalController extends Controller
{
    public function __construct() {
        check_is_role_allowed([2]);
    }

    public function index(Request $request)
    {
        $data = std_get([
            "select" => "*",
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "first_row" => true
        ]);

        return view('transaction/order_qr/approval', [
            "data" => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
             "TRORD_STATUS" => "required",
        ]);

        $attributeNames = [
             "TRORD_STATUS" => "Approval Status",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function approval(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        if ($request->TRORD_STATUS == "2") {
            $data_order = std_get([
                "select" => ["TRORD_QTY","TRORD_MCOMP_CODE","TRORD_MCOMP_TEXT","TRORD_MBRAN_CODE","TRORD_MBRAN_TEXT"],
                "table_name" => "TRORD",
                "where" => [
                    [
                        "field_name" => "TRORD_CODE",
                        "operator" => "=",
                        "value" => $request->TRORD_CODE,
                    ]
                ],
                "first_row" => true
            ]);

            $code_alpha = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 1);
            $code_zeta = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 2);
            $sticker_code = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 3);

            for ($i=0; $i < count($code_alpha["data"]); $i++) {
                $insert_qr_alpha[$i] = [
                    "TRQRA_CODE" => $code_alpha["data"][$i],
                    "TRQRA_TRORD_CODE" => $request->TRORD_CODE,
                    "TRQRA_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                    "TRQRA_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                    "TRQRA_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                    "TRQRA_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                    "TRQRA_CREATED_BY" => session("user_id"),
                    "TRQRA_CREATED_TEXT" => session("user_name"),
                    "TRQRA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }

            for ($i=0; $i < count($code_zeta["data"]); $i++) {
                $insert_qr_zeta[$i] = [
                    "TRQRZ_CODE" => $code_zeta["data"][$i],
                    "TRQRZ_TRORD_CODE" => $request->TRORD_CODE,
                    "TRQRZ_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                    "TRQRZ_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                    "TRQRZ_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                    "TRQRZ_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                    "TRQRZ_CREATED_BY" => session("user_id"),
                    "TRQRZ_CREATED_TEXT" => session("user_name"),
                    "TRQRZ_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }

            $count_order = std_get([
                "select" => ["TRORD_ID"],
                "table_name" => "TRORD",
                "where" => [
                    [
                        "field_name" => "TRORD_MCOMP_CODE",
                        "operator" => "=",
                        "value" => $data_order["TRORD_MCOMP_CODE"],
                    ]
                ],
                "first_row" => true,
                "count" => true
            ]);

            $counter = 0;
            for ($i=0; $i < $count_order; $i++) { 
                $counter++;
                if ($counter == 8) {
                    $counter = 1;
                }
            }

            for ($i=0; $i < count($sticker_code["data"]); $i++) {
                $insert_sticker_code[$i] = [
                    "MASCO_CODE" => $sticker_code["data"][$i],
                    "MASCO_TRORD_CODE" => $request->TRORD_CODE,
                    "MASCO_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                    "MASCO_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                    "MASCO_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                    "MASCO_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                    "MASCO_COUNTER" => $counter,
                    "MASCO_CREATED_BY" => session("user_id"),
                    "MASCO_CREATED_TEXT" => session("user_name"),
                    "MASCO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }

            $insert_alpha = std_insert([
                "table_name" => "TRQRA",
                "data" => $insert_qr_alpha
            ]);
            if ($insert_alpha != true) {
                return response()->json([
                    'message' => "There was an error on inserting QR alpha"
                ],500);
            }

            $insert_zeta = std_insert([
                "table_name" => "TRQRZ",
                "data" => $insert_qr_zeta
            ]);
            if ($insert_zeta != true) {
                return response()->json([
                    'message' => "There was an error on inserting QR zeta"
                ],500);
            }

            $insert_sticker = std_insert([
                "table_name" => "MASCO",
                "data" => $insert_sticker_code
            ]);
            if ($insert_sticker != true) {
                return response()->json([
                    'message' => "There was an error on inserting sticker code"
                ],500);
            }

            $update_res = std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_STATUS" => $request->TRORD_STATUS,
                    "TRORD_NOTES" => $request->TRORD_NOTES,
                    "TRORD_APPROVED_BY" => session("user_id"),
                    "TRORD_APPROVED_TEXT" => session("user_name"),
                    "TRORD_APPROVED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "TRORD_UPDATED_BY" => session("user_id"),
                    "TRORD_UPDATED_TEXT" => session("user_name"),
                    "TRORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ],
                "where" => ["TRORD_CODE" => $request->TRORD_CODE]
            ]);

            return response()->json([
                'message' => "OK"
            ],200);
        }
        else {
            $update_res = std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_STATUS" => $request->TRORD_STATUS,
                    "TRORD_APPROVED_BY" => session("user_id"),
                    "TRORD_APPROVED_TEXT" => session("user_name"),
                    "TRORD_APPROVED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "TRORD_UPDATED_BY" => session("user_id"),
                    "TRORD_UPDATED_TEXT" => session("user_name"),
                    "TRORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ],
                "where" => ["TRORD_CODE" => $request->TRORD_CODE]
            ]);

            if ($update_res != true) {
                return response()->json([
                    'message' => "There was an error on approving QR"
                ],500);
            }

            return response()->json([
                'message' => "OK"
            ],200);
        }
    }
}
