<?php

namespace App\Http\Controllers\MasterData\BatchStore;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([8]);
    }
    
    public function index()
    {
        $closed_sub_batch_packaging = get_master_sub_batch_packaging(["SUBPA_CODE","SUBPA_TEXT"],[
            [
                "field_name" => "SUBPA_ACTIVATION_STATUS",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "SUBPA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code'),
            ],
        ]);

        $plants = get_master_plant("*", [
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MAPLA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
        ]);

        return view('master_data/batch_store/add',[
            'plants' => $plants,
            'closed_sub_batch_packaging' => $closed_sub_batch_packaging,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MBSTR_SUBPA_CODE" => "required|exists:SUBPA,SUBPA_CODE|unique:MBSTR,MBSTR_SUBPA_CODE",
            "MBSTR_DATE" => "required",
            "MBSTR_TEXT" => "required",
        ]);

        $attributeNames = [
            "MBSTR_SUBPA_CODE" => "Sub Batch",
            "MBSTR_DATE" => "Date",
            "MBSTR_TEXT" => "Batch Name",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $sub_batch_packaging = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->MBSTR_SUBPA_CODE,
            ],
        ],true);

        $batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $sub_batch_packaging["SUBPA_MABPR_CODE"],
            ],
        ],true);

        $code = generate_code(session('company_code'),5,"MBSTR");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_res = std_insert([
            "table_name" => "MBSTR",
            "data" => [
                "MBSTR_CODE" => strtoupper($code["data"]),
                "MBSTR_TEXT" => $request->MBSTR_TEXT,
                "MBSTR_DATE" => $request->MBSTR_DATE,
                "MBSTR_SUBPA_QTY" => $sub_batch_packaging["SUBPA_QTY"],
                "MBSTR_MAPLA_CODE" => $sub_batch_packaging["SUBPA_MAPLA_CODE"],
                "MBSTR_MAPLA_TEXT" => $sub_batch_packaging["SUBPA_MAPLA_TEXT"],
                "MBSTR_MCOMP_CODE" => session('company_code'),
                "MBSTR_MCOMP_TEXT" => session('company_name'),
                "MBSTR_MBRAN_CODE" => session('brand_code'),
                "MBSTR_MBRAN_TEXT" => session('brand_name'),

                "MBSTR_MABPR_CODE" => $sub_batch_packaging["SUBPA_MABPR_CODE"],
                "MBSTR_MABPR_TEXT" => $sub_batch_packaging["SUBPA_MABPR_TEXT"],
                "MBSTR_MABPA_CODE" => $sub_batch_packaging["SUBPA_MABPA_CODE"],
                "MBSTR_MABPA_TEXT" => $sub_batch_packaging["SUBPA_MABPA_TEXT"],
                "MBSTR_SUBPA_CODE" => $sub_batch_packaging["SUBPA_CODE"],
                "MBSTR_SUBPA_TEXT" => $sub_batch_packaging["SUBPA_TEXT"],

                "MBSTR_MBRAN_CODE" => $batch_production["MABPR_MBRAN_CODE"],
                "MBSTR_MBRAN_TEXT" => $batch_production["MABPR_MBRAN_TEXT"],
                "MBSTR_MPRCA_CODE" => $batch_production["MABPR_MPRCA_CODE"],
                "MBSTR_MPRCA_TEXT" => $batch_production["MABPR_MPRCA_TEXT"],
                "MBSTR_MPRDT_CODE" => $batch_production["MABPR_MPRDT_CODE"],
                "MBSTR_MPRDT_TEXT" => $batch_production["MABPR_MPRDT_TEXT"],
                "MBSTR_MPRMO_CODE" => $batch_production["MABPR_MPRMO_CODE"],
                "MBSTR_MPRMO_TEXT" => $batch_production["MABPR_MPRMO_TEXT"],
                "MBSTR_MPRVE_CODE" => $batch_production["MABPR_MPRVE_CODE"],
                "MBSTR_MPRVE_TEXT" => $batch_production["MABPR_MPRVE_TEXT"],

                "MBSTR_ACTIVATION_STATUS" => 2,
                "MBSTR_CREATED_BY" => session("user_id"),
                "MBSTR_CREATED_TEXT" => session("user_name"),
                "MBSTR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $update_res = std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $sub_batch_packaging["SUBPA_CODE"]],
            "data" => ["SUBPA_ACTIVATION_STATUS" => "3"],
        ]);

        if ($insert_res != true || $update_res != true) {
            return response()->json([
                'message' => "There was an error saving the data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function sub_batch_packaging(Request $request)
    {
        $sub_batch_packaging = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        $batch_acceptance = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $sub_batch_packaging["SUBPA_MABPA_CODE"],
            ],
        ],true);

        $batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $sub_batch_packaging["SUBPA_MABPR_CODE"],
            ],
        ],true);

        return response()->json([
            "sub_batch_packaging" => $sub_batch_packaging,
            "batch_acceptance" => $batch_acceptance,
            "batch_production" => $batch_production
        ], 200);
    }
}
