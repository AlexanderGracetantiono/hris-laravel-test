<?php

namespace App\Http\Controllers\MasterData\BatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }

    public function index()
    {
        $closed_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_ACTIVATION_STATUS",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "MABPR_MCOMP_CODE",
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

        return view('master_data/batch_packaging/add',[
            'plants' => $plants,
            'closed_batch_production' => $closed_batch_production,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MABPA_MABPR_CODE" => "required|exists:MABPR,MABPR_CODE|unique:MABPA,MABPA_MABPR_CODE",
            "MABPA_NOTES" => "required",
        ]);

        $attributeNames = [
            "MABPA_MABPR_CODE" => "Batch Production",
            "MABPA_NOTES" => "Batch notes",
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

        $batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request->MABPA_MABPR_CODE,
            ],
        ],true);

        $code = generate_code(session('company_code'),5,"MABPA");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_res = std_insert([
            "table_name" => "MABPA",
            "data" => [
                "MABPA_CODE" => strtoupper($code["data"]),
                "MABPA_TEXT" => $batch_production["MABPR_TEXT"],
                "MABPA_QTY" => $batch_production["MABPR_PAIRED_QTY"],
                "MABPA_QTY_LEFT" => $batch_production["MABPR_PAIRED_QTY"],
                "MABPA_NOTES" => $request->MABPA_NOTES,
                "MABPA_MABPR_CODE" => $request->MABPA_MABPR_CODE,
                "MABPA_MABPR_TEXT" => $batch_production["MABPR_TEXT"],
                "MABPA_MAPLA_CODE" => $batch_production["MABPR_MAPLA_CODE"],
                "MABPA_MAPLA_TEXT" => $batch_production["MABPR_MAPLA_TEXT"],
                "MABPA_MCOMP_CODE" => session('company_code'),
                "MABPA_MCOMP_TEXT" => session('company_name'),
                "MABPA_MBRAN_CODE" => session('brand_code'),
                "MABPA_MBRAN_TEXT" => session('brand_name'),
                "MABPA_ACTIVATION_STATUS" => 1,
                "MABPA_STATUS" => 2,
                "MABPA_CREATED_BY" => session("user_id"),
                "MABPA_CREATED_TEXT" => session("user_name"),
                "MABPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $update_res = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $request->MABPA_MABPR_CODE],
            "data" => ["MABPR_ACTIVATION_STATUS" => 3],
        ]); 

        if ($insert_res != true && $update_res != true) {
            return response()->json([
                'message' => "There was an error saving the data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function get_batch_production(Request $request)
    {
        $batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        return response()->json($batch_production, 200);
    }
}
