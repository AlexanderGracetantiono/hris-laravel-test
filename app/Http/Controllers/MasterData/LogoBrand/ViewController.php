<?php

namespace App\Http\Controllers\MasterData\LogoBrand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function index()
    {
        $data = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        return view('master_data/logo_brand/edit', ['data' => $data]);
    }
}
