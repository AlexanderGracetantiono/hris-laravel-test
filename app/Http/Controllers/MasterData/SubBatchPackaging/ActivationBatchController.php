<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ActivationBatchController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }

    public function activate(Request $request)
    {
        std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $request->SUBPA_CODE],
            "data" => [
                "SUBPA_ACTIVATION_STATUS" => 1,
                "SUBPA_ACTIVATION_TIMESTAMP" => date("Y-m-d H:i:s"),
                "SUBPA_NOTES" => $request->SUBPA_NOTES
            ]
        ]);
        
        return response()->json([
            'message' => "Data succesfully activate"
        ],200);
    }

    public function close(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "SUBPA_STORE_ADMIN_CODE" => "required|exists:MAEMP,MAEMP_CODE",
            "SUBPA_DISCREPANCY_PRODUCT" => "required|numeric",
            "SUBPA_DISCREPANCY_TRQRZ" => "required|numeric",
            "SUBPA_DISCREPANCY_MASCO" => "required|numeric",
            "SUBPA_DISCREPANCY_NOTES" => "required",
            ]);
            
        $attributeNames = [
            "SUBPA_STORE_ADMIN_CODE" => "Store Admin",
            "SUBPA_DISCREPANCY_PRODUCT" => "Discreparancy Product",
            "SUBPA_DISCREPANCY_TRQRZ" => "Discreparancy QR Zeta",
            "SUBPA_DISCREPANCY_MASCO" => "Discreparancy sticker code",
            "SUBPA_DISCREPANCY_NOTES" => "Discreparancy notes",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->all()
            ], 400);
        }

        if ($request->SUBPA_DISCREPANCY_TRQRZ != "0" || $request->SUBPA_DISCREPANCY_MASCO != "0" || $request->SUBPA_DISCREPANCY_PRODUCT != "0") {
            if ($request->SUBPA_DISCREPANCY_NOTES == null || $request->SUBPA_DISCREPANCY_NOTES == "") {
                return response()->json([
                    'message' => "Please insert discrepancy notes"
                ], 400);
            }
        }

        $data = std_get([
            "table_name" => "SUBPA",
            "select" => ["*"],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "POPRD",
                    "on1" => "POPRD_CODE",
                    "operator" => "=",
                    "on2" => "SUBPA_POPRD_CODE",
                ]
            ],
            "where" => [
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->SUBPA_CODE,
                ]
            ],
            "first_row" => true,
        ]);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->SUBPA_CODE,
                ],
                [
                    "field_name" => "TRQRZ_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        if ($count_paired_qr != $data["SUBPA_QTY"]) {
            if ($request->SUBPA_NOTES == null) {
                return response()->json([
                    'message' => "Please insert batch packaging notes"
                ], 500);
            }
        }

        $unpaired_product = $data["SUBPA_QTY"] - $count_paired_qr;
        $total_discrepancy_product = $request->SUBPA_DISCREPANCY_PRODUCT + ($unpaired_product - $request->SUBPA_DISCREPANCY_PRODUCT);
        $total_discrepancy_qr = $request->SUBPA_DISCREPANCY_TRQRZ + ($unpaired_product - $request->SUBPA_DISCREPANCY_TRQRZ);
        $total_discrepancy_bridge = $request->SUBPA_DISCREPANCY_MASCO + ($unpaired_product - $request->SUBPA_DISCREPANCY_MASCO);

        if ($unpaired_product != $total_discrepancy_product) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy product report not match with not paired product"
            ], 500);
        }

        if ($unpaired_product != $total_discrepancy_qr) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy qr report not match with not paired product"
            ], 500);
        }

        if ($unpaired_product != $total_discrepancy_bridge) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy qr bridge not match with not paired product"
            ], 500);
        }

        $store_admin = get_master_employee("*",[
            [
                "field_name" => "MAEMP_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_STORE_ADMIN_CODE
            ],
        ],true);

        // $to_name = $data_store_admin[$i]['MAEMP_TEXT'];
        // $to_email = $data_store_admin[$i]['MAEMP_EMAIL'];
        // try {
        //     Mail::send("mail.information_sub_batch", ['data' => $data], function ($message) use ($to_name, $to_email) {
        //         $message
        //             ->to($to_email, $to_name)
        //             ->subject("Package ready to ship");
        //         $message->from("admin@cekori.com", "Package ready to ship");
        //     });
            
        // } catch (\Exception $e) {
        //     Log::critical("Error when send email via sendinblue");
        // }

        std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $request->SUBPA_CODE],
            "data" => [
                "SUBPA_ACTIVATION_STATUS" => 2,
                "SUBPA_CLOSED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "SUBPA_NOTES" => $request->SUBPA_NOTES,
                "SUBPA_STORE_ADMIN_CODE" => $store_admin["MAEMP_CODE"],
                "SUBPA_STORE_ADMIN_TEXT" => $store_admin["MAEMP_TEXT"],
                "SUBPA_DISCREPANCY_PRODUCT" => $request->SUBPA_DISCREPANCY_PRODUCT,
                "SUBPA_RETURNED_PRODUCT" => $unpaired_product - $request->SUBPA_DISCREPANCY_PRODUCT,
                "SUBPA_DISCREPANCY_TRQRZ" => $request->SUBPA_DISCREPANCY_TRQRZ,
                "SUBPA_RETURNED_TRQRZ" => $unpaired_product - $request->SUBPA_DISCREPANCY_TRQRZ,
                "SUBPA_DISCREPANCY_MASCO" => $request->SUBPA_DISCREPANCY_MASCO,
                "SUBPA_RETURNED_MASCO" => $unpaired_product - $request->SUBPA_DISCREPANCY_MASCO,
                "SUBPA_DISCREPANCY_NOTES" => $request->SUBPA_DISCREPANCY_NOTES,
                "SUBPA_IS_REPORTED" => "1",
            ]
        ]);

        std_update([
            "table_name" => "STBPA",
            "where" => ["STBPA_SUBPA_CODE" => $request->SUBPA_CODE],
            "data" => ["STBPA_SUBPA_STATUS" => "2",]
        ]);

        $code = generate_code(session('company_code'),5,"MBSTR");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_batch_store = [
            "MBSTR_CODE" => strtoupper($code["data"]),
            // "MBSTR_TEXT" => $request->MBSTR_TEXT,
            // "MBSTR_DATE" => $request->MBSTR_DATE,
            "MBSTR_SUBPA_QTY" => $count_paired_qr,
            "MBSTR_MAPLA_CODE" => $data["SUBPA_MAPLA_CODE"],
            "MBSTR_MAPLA_TEXT" => $data["SUBPA_MAPLA_TEXT"],
            "MBSTR_MCOMP_CODE" => session('company_code'),
            "MBSTR_MCOMP_TEXT" => session('company_name'),
            "MBSTR_MBRAN_CODE" => session('brand_code'),
            "MBSTR_MBRAN_TEXT" => session('brand_name'),

            // "MBSTR_MABPR_CODE" => $data["SUBPA_MABPR_CODE"],
            // "MBSTR_MABPR_TEXT" => $data["SUBPA_MABPR_TEXT"],
            // "MBSTR_MABPA_CODE" => $data["SUBPA_MABPA_CODE"],
            // "MBSTR_MABPA_TEXT" => $data["SUBPA_MABPA_TEXT"],
            "MBSTR_SUBPA_CODE" => $data["SUBPA_CODE"],
            "MBSTR_SUBPA_TEXT" => $data["SUBPA_TEXT"],

            "MBSTR_MBRAN_CODE" => $data["POPRD_MBRAN_CODE"],
            "MBSTR_MBRAN_TEXT" => $data["POPRD_MBRAN_TEXT"],
            "MBSTR_MPRCA_CODE" => $data["POPRD_MPRCA_CODE"],
            "MBSTR_MPRCA_TEXT" => $data["POPRD_MPRCA_TEXT"],
            "MBSTR_MPRDT_CODE" => $data["POPRD_MPRDT_CODE"],
            "MBSTR_MPRDT_TEXT" => $data["POPRD_MPRDT_TEXT"],
            "MBSTR_MPRMO_CODE" => $data["POPRD_MPRMO_CODE"],
            "MBSTR_MPRMO_TEXT" => $data["POPRD_MPRMO_TEXT"],
            "MBSTR_MPRVE_CODE" => $data["POPRD_MPRVE_CODE"],
            "MBSTR_MPRVE_TEXT" => $data["POPRD_MPRVE_TEXT"],
            "MBSTR_MPRVE_SKU" => $data["POPRD_MPRVE_SKU"],
            "MBSTR_MPRVE_NOTES" => $data["POPRD_MPRVE_NOTES"],

            "MBSTR_STORE_ADMIN_CODE" => $store_admin["MAEMP_CODE"],
            "MBSTR_STORE_ADMIN_TEXT" => $store_admin["MAEMP_TEXT"],

            "MBSTR_ACTIVATION_STATUS" => 1,
            "MBSTR_CREATED_BY" => session("user_id"),
            "MBSTR_CREATED_TEXT" => session("user_name"),
            "MBSTR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MBSTR",
            "data" => $insert_batch_store
        ]);

        return response()->json([
            'message' => "Data succesfully closed"
        ],200);
    }

    public function reject_qr(Request $request)
    {
        std_update([
            "table_name" => "MASCO",
            "where" => ["MASCO_CODE" => $request->code],
            "data" => [
                "MASCO_STATUS" => 3,
            ]
        ]);
        
        return response()->json([
            'message' => "QR succesfully rejected"
        ],200);
    }
}
