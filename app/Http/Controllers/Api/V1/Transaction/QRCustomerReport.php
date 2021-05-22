<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class QRCustomerReport extends Controller
{
    public function report(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "qr_code_alpha" => "required",
            "qr_code_zeta" => "required|exists:SCHED,SCHED_TRQRZ_CODE",
            "report_note" => "required",
            "scan_by" => "required",
            "scan_by_text" => "required",
            "scan_by_email" => "required",
            "scan_by_phone_number" => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->all(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        }

        $data_brand = std_get([
            "table_name" => "SCHED",
            "select" => ["*"],
            "where" => [
                [
                    "field_name" => "SCHED_TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_zeta
                ],
            ],
            "join" => [
                [
                    "table_name" => "SCDET",
                    "join_type" => "INNER",
                    "on1" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCHED_ID"
                ]
            ],
            "first_row" => true
        ]);

        $brand_logo = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $data_brand["SCHED_MBRAN_CODE"],
            ]
        ],true);
        

        $log_scan = std_get([
            "table_name" => "SCHED",
            "select" => "SCLOG.*",
            "where" => [
                [
                    "field_name" => "SCHED_TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request["qr_code_zeta"]
                ],
            ],
            "join" => [
                [
                    "table_name" => "SCLOG",
                    "join_type" => "INNER",
                    "on1" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCHED_ID"
                ]
            ],
            "order_by" => [
                [
                    "field" => "SCLOG_ID",
                    "type" => "DESC",
                ]
            ],
        ]);

        std_insert([
            "table_name" => "REPQR",
            "data" => [
                "REPQR_TRQRA" => $data_brand["SCHED_TRQRA_CODE"],
                "REPQR_TRQRZ" => $data_brand["SCHED_TRQRZ_CODE"],
                "REPQR_CST_SCAN_BY" => $request->scan_by,
                "REPQR_CST_SCAN_TEXT" => $request->scan_by_text,
                "REPQR_CST_SCAN_EMAIL" => $request->scan_by_email,
                "REPQR_CST_SCAN_PHONE_NUMBER" => $request->scan_by_phone_number,
                "REPQR_CST_SCAN_NOTES" => $request->report_note,
                "REPQR_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                "REPQR_STATUS" => 1,
                "REPQR_MCOMP_CODE" => $data_brand["SCHED_MCOMP_CODE"],
                "REPQR_MCOMP_TEXT" => $data_brand["SCHED_MCOMP_NAME"],
                "REPQR_MBRAN_CODE" => $data_brand["SCHED_MBRAN_CODE"],
                "REPQR_MBRAN_TEXT" => $data_brand["SCHED_MBRAN_NAME"],
                "REPQR_CREATED_BY" => $request->scan_by,
                "REPQR_CREATED_TEXT" => $request->scan_by_text,
                "REPQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $email_list_pic_brand = get_master_employee(["MAEMP_EMAIL","MAEMP_TEXT"],[
            [
                "field_name" => "MAEMP_MBRAN_CODE",
                "operator" => "=",
                "value" => $data_brand["SCHED_MBRAN_CODE"],
            ],
            [
                "field_name" => "MAEMP_ROLE",
                "operator" => "=",
                "value" => "3",
            ],
        ]);

        
        for ($i=0; $i < count($email_list_pic_brand); $i++) { 
            $email_body[$i] = [
                "brand" => $data_brand["SCHED_MBRAN_NAME"],
                "type" => $brand_logo["MBRAN_TYPE"],
                "SKU" => $data_brand["SCDET_MPRVE_SKU"],
                "report_note" => $request->report_note,
                "scan_by_text" => $request->scan_by_text,
                "scan_by_email" => $request->scan_by_email,
                "alpha_code" => $request->qr_code_alpha,
                "zeta_code" => $request->qr_code_zeta,
                "scan_by_phone_number" => $request->scan_by_phone_number,
                "log_scan" => $log_scan,
                "brand_logo" => $brand_logo["MBRAN_IMAGE"],
            ];

            try {
                $to_name = $email_list_pic_brand[$i]['MAEMP_TEXT'];
                $to_email = $email_list_pic_brand[$i]['MAEMP_EMAIL'];
    
                Mail::send("mail.customer_report", ['data' => $email_body[$i]], function ($message) use ($to_name, $to_email) {
                    $message
                        ->to($to_email, $to_name)
                        ->subject("Customer report");
                    $message->from("admin@cekori.com", "Customer report");
                });
            } catch (\Exception $e) {
                Log::critical("Error on send customer at counter : ".$i." date : ".date("Y-m-d H:i:s")." error description : ".json_encode($e->all())." data : ".json_encode($email_body));
            }
        }

        try {
            $email_body_customer = [
                "brand" => $data_brand["SCHED_MBRAN_NAME"],
                "type" => $brand_logo["MBRAN_TYPE"],
                "SKU" => $data_brand["SCDET_MPRVE_SKU"],
                "report_note" => $request->report_note,
                "scan_by_text" => $request->scan_by_text,
                "scan_by_email" => $request->scan_by_email,
                "alpha_code" => $request->qr_code_alpha,
                "zeta_code" => $request->qr_code_zeta,
                "scan_by_phone_number" => $request->scan_by_phone_number,
                "log_scan" => $log_scan,
                "brand_logo" => $brand_logo["MBRAN_IMAGE"],
            ];

            $to_name = $request->scan_by_text;
            $to_email = $request->scan_by_email;

            Mail::send("mail.customer_report", ['data' => $email_body_customer], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Customer report");
                $message->from("admin@cekori.com", "Customer report");
            });
        } catch (\Exception $f) {
            Log::critical("Error on send customer at counter : ".$i." date : ".date("Y-m-d H:i:s")." error description : ".$f->all()." data : ".json_encode($email_body));
        }
            
        return response()->json([
            "response" => "Report QR success"
        ], 200);
    }
}
