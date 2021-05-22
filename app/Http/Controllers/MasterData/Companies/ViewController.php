<?php

namespace App\Http\Controllers\MasterData\Companies;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }

    public function index()
    {
        $data = get_master_company("*",[
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ]
        ]);
        for ($i=0; $i <count($data) ; $i++) { 
            # code...
            if($data[$i]["MCOMP_TEMP"]!=null){
                $data[$i]["MCOMP_STATUS"]=2;
            }
        }
        return view('master_data/companies/company/view', ["data" => $data]);
    }
}
