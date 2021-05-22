<?php

namespace App\Http\Controllers\Report\CustomerReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function index()
    {
        $data = get_report_qr_customer("*",[
            [
                "field_name" => "REPQR_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
            [
                "field_name" => "REPQR_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code')
            ],
        ]);
        return view('report/customer_qr_report/view', ['data' => $data]);
    }

    public function detail(Request $request)
    {
        $data_customer = get_report_qr_customer("*",[
            [
                "field_name" => "REPQR_ID",
                "operator" => "=",
                "value" => $request->code
            ],
        ],true);

        $data_scan_head = get_scan_header(["SCHED_ID"],[
            [
                "field_name" => "SCHED_TRQRA_CODE",
                "operator" => "=",
                "value" => $data_customer["REPQR_TRQRA"]
            ],
        ],true);

        $data_product =  get_scan_detail("*",[
            [
                "field_name" => "SCDET_SCHED_ID",
                "operator" => "=",
                "value" => $data_scan_head["SCHED_ID"]
            ],
        ],true);
        $log_scan = std_get([
            "table_name" => "SCHED",
            "select" => "SCLOG.*",
            "where" => [
                [
                    "field_name" => "SCHED_TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $data_customer['REPQR_TRQRZ']
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
        // dd($log_scan)
        return view('report/customer_qr_report/detail',[
            'data_customer' => $data_customer,
            'data_product' => $data_product,
            'log_scan' => $log_scan,
        ]);
    }
}
