<?php

namespace App\Http\Controllers\MasterDataLab\Product;
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
        return view('master_data_lab/product/view', ['data' => $data]);
    }
}
