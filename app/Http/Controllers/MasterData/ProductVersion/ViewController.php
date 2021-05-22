<?php

namespace App\Http\Controllers\MasterData\ProductVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index()
    {
        $data = std_get([
            "field_name" => "*",
            "table_name" => "MPRVE",
            "where" => [
                [
                    "field_name" => "MPRVE_IS_DELETED",
                    "operator" => "=",
                    "value" => "0",
                ],
                [
                    "field_name" => "MPRVE_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
            ]
        ]);

        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 4) {
            $check_access = true;
        }

        return view('master_data/product_version/view', [
            "data" => $data,
            "check_access" => $check_access,
        ]);
    }
}
