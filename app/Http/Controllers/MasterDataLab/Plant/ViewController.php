<?php

namespace App\Http\Controllers\MasterDataLab\Plant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function index()
    {
        $data = get_master_product_plant("*",[
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MAPLA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code')
            ],
        ]);
        
        return view('master_data_lab/plant/view', ['data' => $data]);
    }
}
