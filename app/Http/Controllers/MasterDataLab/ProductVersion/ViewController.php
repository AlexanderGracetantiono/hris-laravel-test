<?php

namespace App\Http\Controllers\MasterDataLab\ProductVersion;
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

        return view('master_data_lab/product_version/view', ["data" => $data]);
    }
}
