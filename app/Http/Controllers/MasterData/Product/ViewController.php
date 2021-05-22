<?php

namespace App\Http\Controllers\MasterData\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index()
    {
        $data = get_master_product("*",[
            [
                "field_name" => "MPRDT_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code')
            ],
        ]);

        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
            $check_access = true;
        }

        return view('master_data/product/view', [
            'data' => $data,
            "check_access" => $check_access,
        ]);
    }
}
