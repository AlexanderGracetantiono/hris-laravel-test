<?php

namespace App\Http\Controllers\MasterData\BatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }

    public function index()
    {
        $data = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MABPA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
        ]);
        return view('master_data/batch_packaging/view', ['data' => $data]);
    }

    public function detail(Request $request)
    {
        $data = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        $selected_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $data["MABPA_MABPR_CODE"],
            ],
        ],true);

        return view('master_data/batch_packaging/detail',[
            'data' => $data,
            'selected_batch_production' => $selected_batch_production,
        ]);
    }

    public function scanned_qr(Request $request)
    {
        $data_packaging = get_master_batch_packaging("*", [
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        $selected_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $data_packaging["MABPA_MABPR_CODE"],
            ],
        ],true);

        $data_qr = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_MABPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ]
        ]);

        return view('master_data/batch_packaging/scanned_qr', [
            'data_packaging' => $data_packaging,
            'selected_batch_production' => $selected_batch_production,
            'data_qr' => $data_qr,
        ]);
    }
}
