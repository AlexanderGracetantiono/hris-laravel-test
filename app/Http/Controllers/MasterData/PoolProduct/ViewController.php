<?php

namespace App\Http\Controllers\MasterData\PoolProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5,8]);
    }

    public function index()
    {
        $data = get_pool_product("*",[
            [
                "field_name" => "POPRD_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ]
        ]);

        return view('master_data/pool_product/view', [
            "data" => $data
        ]);
    }

    public function detail(Request $request)
    {
        $data = get_pool_product("*",[
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $request->code
            ]
        ],true);

        $batch_acceptance = std_get([
            "table_name" => "MABPA",
            "select" => ["MABPA_CODE","MABPA_QTY","MABPA_ACTIVATION_TIMESTAMP","MABPR_NOTES","MABPR_DISCREPANCY_NOTES"],
            "where" => [
                [
                    "field_name" => "MABPA_POPRD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "join" => [
                [
                    "table_name" => "MABPR",
                    "join_type" => "inner",
                    "on1" => "MABPR_CODE",
                    "operator" => "=",
                    "on2" => "MABPA_MABPR_CODE",
                ]
            ]
        ]);

        return view('master_data/pool_product/detail',[
            "data" => $data,
            "batch_acceptance" => $batch_acceptance
        ]);
    }
}
