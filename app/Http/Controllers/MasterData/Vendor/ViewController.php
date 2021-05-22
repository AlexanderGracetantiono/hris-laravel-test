<?php

namespace App\Http\Controllers\MasterData\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = get_master_vendor("*",[
            [
                "field_name" => "MVNDR_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],

        ]);

        return view('master_data/companies/vendor/view', ["data" => $data]);
    }
}
