<?php

namespace App\Http\Controllers\MasterData\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index()
    {
        $data = get_master_product_brand("*",[
            [
                "field_name" => "MBRAN_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
        ]);
        return view('master_data/brand/view', ['data' => $data]);
    }
}
