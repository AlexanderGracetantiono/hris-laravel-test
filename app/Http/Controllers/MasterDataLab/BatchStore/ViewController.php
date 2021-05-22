<?php

namespace App\Http\Controllers\MasterDataLab\BatchStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App;
use QrCode;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([8]);
    }
    
    public function index()
    {
        $data = std_get([
            "table_name" => "MASCO",
            "select" => ["MASCO_NOTES","SCHED_CUST_NAME","SCHED_CUST_EMAIL","SCHED_CUST_PHONE_NUMBER","SCHED_ID"],
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "!=",
                    "value" => null,
                ],
                [
                    "field_name" => "MASCO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
            ],
            "join" => [
                [
                    "join_type" => "LEFT",
                    "table_name" => "SCHED",
                    "on1" => "SCHED_CHAIN_CODE",
                    "operator" => "=",
                    "on2" => "MASCO_NOTES",
                ],
            ]
        ]);

        return view('master_data_lab/batch_store/view', ['data' => $data]);
    }

    public function detail(Request $request)
    {
        $data = std_get([
            "table_name" => "SCDET",
            "select" => ["*"],
            "where" => [
                [
                    "field_name" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "value" => $request->code,
                ],
            ],
        ]);

        return view('master_data_lab/batch_store/detail',[
            'data' => $data,
        ]);
    }

    public function print_lab_report(Request $request)
    {
        $data = std_get([
            "table_name" => "SCDET",
            "select" => ["*"],
            "where" => [
                [
                    "field_name" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "value" => $request->code,
                ],
            ],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "SCHED",
                    "on1" => "SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCDET_SCHED_ID",
                ],
            ]
        ]);

        $brand_logo = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $data[0]["SCHED_MBRAN_CODE"],
            ]
        ],true);

        PDF::setOptions(['defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('master_data_lab/batch_store/print_lab_report', [
            "data" => $data,
            "brand_logo" => $brand_logo["MBRAN_IMAGE"],
            "qr_image_alpha" => base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->generate($data[0]["SCHED_TRQRA_CODE"])),
            "qr_image_zeta" => base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->generate($data[0]["SCHED_TRQRZ_CODE"])),
        ]);
        return $pdf->download('delivery_note.pdf');
    }
}
