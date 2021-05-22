<?php

namespace App\Http\Controllers\MasterData\BatchStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([8]);
    }
    
    public function index()
    {
        $data = get_master_batch_store("*",[
            [
                "field_name" => "MBSTR_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
        ]);
        return view('master_data/batch_store/view', ['data' => $data]);
    }

    public function detail(Request $request)
    {
        $data = get_master_batch_store("*",[
            [
                "field_name" => "MBSTR_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
        ],true);

        $sub_batch_packaging = get_master_sub_batch_packaging("SUBPA_NOTES",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $data["MBSTR_SUBPA_CODE"],
            ],
        ],true);

        return view('master_data/batch_store/detail',[
            'data' => $data,
            'sub_batch_packaging' => $sub_batch_packaging,
        ]);
    }
}
