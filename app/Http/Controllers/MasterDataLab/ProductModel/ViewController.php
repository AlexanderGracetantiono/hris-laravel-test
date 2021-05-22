<?php

namespace App\Http\Controllers\MasterDataLab\ProductModel;
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
            "table_name" => "MPRMO",
            "where" => [
                [
                    "field_name" => "MPRMO_IS_DELETED",
                    "operator" => "=",
                    "value" => "0",
                ],
                [
                    "field_name" => "MPRMO_MCOMP_CODE",
                    "operator" => "=",
                    "value" => session('company_code'),
                ],
                [
                    "field_name" => "MPRMO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session('brand_code'),
                ],
            ]
        ]);

        return view('master_data_lab/product_model/view', ["data" => $data]);
    }
}
