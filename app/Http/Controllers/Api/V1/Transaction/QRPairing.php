<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QRPairing extends Controller
{
    public function save(Request $request)
    {

        $validate = Validator::make($request->all(), [
            "qr_code" => "required|max:255",
            "sticker_code" => "required|max:255",
            "scan_by" => "required|max:255",
            "scan_by_text" => "required|max:255",
            // "scan_lat" => "required|max:255",
            // "scan_lng" => "required|max:255",
            "scan_device_id" => "required|max:255",
            "scan_app_version" => "required|max:255",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all()
            ], 400);
        } else {

            // Berat lols
            // cek is code registered, atau lu nemu kode di jalan raya kalimalang

            $data_TRQRA = std_get([
                "table_name" => "TRQRA",
                "select" => [
                    "TRQRA_MASCO_CODE"
                ],
                "where" => [
                    [
                        "field_name" => "TRQRA_CODE",
                        "operator" => "=",
                        "value" => $request->qr_code
                    ]
                ],
                "first_row" => true
            ]);

            $data_TRQRZ = std_get([
                "table_name" => "TRQRZ",
                "select" => [
                    "TRQRZ_MASCO_CODE"
                ],
                "where" => [
                    [
                        "field_name" => "TRQRZ_CODE",
                        "operator" => "=",
                        "value" => $request->qr_code
                    ]
                ],
                "first_row" => true
            ]);

            // if ($data_TRQRA != null) {
                // kode teregister
                // cek apakah pada Masco, kode yg dikirim itu udah pernah terpairing?
                // $checking_is_sticker_paired = std_get([
                //     "table_name" => "MASCO",
                //     "select" => [
                //         "MASCO_CODE"
                //     ],
                //     "where" => [
                //         [
                //             "field_name" => "MASCO_CODE",
                //             "operator" => "=",
                //             "value" => $request->sticker_code
                //         ],
                //         [
                //             "field_name" => "MASCO_TRQAH_CODE",
                //             "operator" => "!=",
                //             "value" => null
                //         ]

                //     ],
                //     "first_row" => true
                // ]);

                // $checking_is_qrcode_paired = std_get([
                //     "table_name" => "MASCO",
                //     "select" => [
                //         "MASCO_TRQAH_CODE"
                //     ],
                //     "where" => [
                //         [
                //             "field_name" => "MASCO_CODE",
                //             "operator" => "!=",
                //             "value" => null
                //         ],
                //         [
                //             "field_name" => "MASCO_TRQAH_CODE",
                //             "operator" => "=",
                //             "value" => $request->qr_code
                //         ]
                //     ],
                //     "first_row" => true
                // ]);
                // if ($checking_is_sticker_paired != null) {
                //     return response()->json([
                //         "response" => "Sticker code have been paired"
                //     ], 500);
                // } elseif ($checking_is_qrcode_paired != null) {
                //     return response()->json([
                //         "response" => "QR Code code have been paired"
                //     ], 500);
                // } else {
                    $update_masco = [
                        "MASCO_TRQAH_CODE" => $request->qr_code,
                        "MASCO_STATUS" => 1,// 1 mean pair
                        "MASCO_UPDATED_BY" => $request->scan_by,
                        "MASCO_UPDATED_TEXT" => $request->scan_by_text,
                        "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];

                    $update_res_masco = std_update([
                        "table_name" => "MASCO",
                        "where" => ["MASCO_CODE" => $request->sticker_code],
                        "data" => $update_masco
                    ]);
                    $update_table = [
                        "TRQRA_MASCO_CODE" => $request->sticker_code,
                        "TRQRA_EMP_SCAN_BY" => $request->scan_by,
                        "TRQRA_EMP_SCAN_TEXT" => $request->scan_by_text,
                        "TRQRA_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                        "TRQRA_EMP_SCAN_LAT" => $request->scan_lat,
                        "TRQRA_EMP_SCAN_LNG" => $request->scan_lng,
                        "TRQRA_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
                        "TRQRA_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
                    ];
                    $update_res_table = std_update([
                        "table_name" => "TRQRA",
                        "where" => ["TRQRA_CODE" => $request->qr_code],
                        "data" => $update_table
                    ]);

                    if ($update_res_masco === false || $update_res_table===false) {
                        return response()->json([
                            'message' => "Error occured when update data"
                        ], 500);
                    }
                    return response()->json([
                        "response" => "Update code success"
                    ], 200);
                // }
            // } else if ($data_TRQRZ != null) {
                // $checking_is_sticker_paired = std_get([
                //     "table_name" => "MASCO",
                //     "select" => [
                //         "MASCO_CODE"
                //     ],
                //     "where" => [
                //         [
                //             "field_name" => "MASCO_CODE",
                //             "operator" => "=",
                //             "value" => $request->sticker_code
                //         ],
                //         [
                //             "field_name" => "MASCO_TRQZH_CODE",
                //             "operator" => "!=",
                //             "value" => null
                //         ]

                //     ],
                //     "first_row" => true
                // ]);
                // $checking_is_qrcode_paired = std_get([
                //     "table_name" => "MASCO",
                //     "select" => [
                //         "MASCO_TRQZH_CODE"
                //     ],
                //     "where" => [
                //         [
                //             "field_name" => "MASCO_CODE",
                //             "operator" => "!=",
                //             "value" => null
                //         ],
                //         [
                //             "field_name" => "MASCO_TRQZH_CODE",
                //             "operator" => "=",
                //             "value" => $request->qr_code
                //         ]
                //     ],
                //     "first_row" => true
                // ]);
                // if ($checking_is_sticker_paired == null) {
                //     return response()->json([
                //         "response" => "Sticker code have been paired"
                //     ], 500);
                // } elseif ($checking_is_qrcode_paired == null) {
                //     return response()->json([
                //         "response" => "QR Code code have been paired"
                //     ], 500);
                // } else {
                    $update_masco = [
                        "MASCO_TRQZH_CODE" => $request->qr_code,
                        "MASCO_UPDATED_BY" => $request->scan_by,
                        "MASCO_STATUS" => 1,// 1 mean pair
                        "MASCO_UPDATED_TEXT" => $request->scan_by_text,
                        "MASCO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];

                    $update_res_masco = std_update([
                        "table_name" => "MASCO",
                        "where" => ["MASCO_CODE" => $request->sticker_code],
                        "data" => $update_masco
                    ]);
                    $update_table = [
                        "TRQRZ_MASCO_CODE" => $request->sticker_code,
                        "TRQRZ_EMP_SCAN_BY" => $request->scan_by,
                        "TRQRZ_EMP_SCAN_TEXT" => $request->scan_by_text,
                        "TRQRZ_EMP_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                        "TRQRZ_EMP_SCAN_LAT" => $request->scan_lat,
                        "TRQRZ_EMP_SCAN_LNG" => $request->scan_lng,
                        "TRQRZ_EMP_SCAN_DEVICE_ID" => $request->scan_device_id,
                        "TRQRZ_EMP_SCAN_APP_VERSION" => $request->scan_app_version,
                    ];
                    $update_res_table = std_update([
                        "table_name" => "TRQRZ",
                        "where" => ["TRQRZ_CODE" => $request->qr_code],
                        "data" => $update_table
                    ]);

                    if ($update_res_masco === false || $update_res_table===false) {
                        return response()->json([
                            'message' => "Error occured when update data"
                        ], 500);
                    }
                    return response()->json([
                        "response" => "Update code success"
                    ], 200);
                // }
            // } else {
            //     return response()->json([
            //         "response" => "QRCode is not registered"
            //     ], 500);
            // }
        }
    }

    public function get(Request $request){
        $validate = Validator::make($request->all(), [
            "batch_code" => "required|max:255",
            "role" => "required|max:255",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        }

        if($request->role == 6){
            $data_batch = get_master_batch_production("*",[
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->batch_code,
                ]
            ],true);

            $response = [
                // 0 waiting, 1 in progress
                "batch_status" => $data_batch["MABPR_ACTIVATION_STATUS"],
            ];
        } else{
            $data_batch = get_master_sub_batch_packaging("*",[
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->batch_code,
                ]
            ],true);

            $response = [
                // 0 waiting, 1 in progress
                "batch_status" => $data_batch["SUBPA_ACTIVATION_STATUS"],
            ];
        }

        return response()->json($response,200);
    }
}
