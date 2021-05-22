<?php

namespace App\Http\Controllers\MasterData\Plant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }

    public function index()
    {
        $companies = std_get([
            "select" => ["*"],
            "table_name" => "MCOMP",
            "where" => [
                [
                    "field_name" => "MCOMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
                [
                    "field_name" => "MCOMP_STATUS",
                    "operator" => "=",
                    "value" => "1"
                ],
            ],
            "multiple_rows" => true
        ]);

        $code = std_get([
            "table_name" => "MACOP",
            "select" => "*",
        ]);

        return view('master_data/plant/add',[
            'companies' => $companies,
            'code' => $code,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MAPLA_TEXT" => "required|max:255",
            "MAPLA_TYPE" => "required",
            "MAPLA_MACOP_CODE" => "max:255|required|exists:MACOP,MACOP_CODE",
            "MAPLA_AREA_NUMBER" => "max:255|required",
            "MAPLA_PHONE_NUMBER" => "max:255|required",
            "MAPLA_LAT" => "max:255",
            "MAPLA_LNG" => "max:255",
            "MAPLA_ADDRESS" => "required|max:255",
        ]);

        $attributeNames = [
            "MAPLA_TEXT" => "Production / Packaging Center Name",
            "MAPLA_TYPE" => "Production / Packaging Center Type",
            "MAPLA_MACOP_CODE" => "Phone Country Code",
            "MAPLA_AREA_NUMBER" => "Phone Area Code",
            "MAPLA_PHONE_NUMBER" => "Phone Number",
            "MAPLA_LAT" => "Production / Packaging Center Latitude",
            "MAPLA_LNG" => "Production / Packaging Center Longitude",
            "MAPLA_ADDRESS" => "Production / Packaging Center Address",
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

        $code = generate_code(session('company_code'),5,"MAPLA");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_res = std_insert([
            "table_name" => "MAPLA",
            "data" => [
                "MAPLA_CODE" => strtoupper($code["data"]),
                "MAPLA_TEXT" => $request->MAPLA_TEXT,
                "MAPLA_TYPE" => $request->MAPLA_TYPE,
                "MAPLA_MACOP_CODE" => $request->MAPLA_MACOP_CODE,
                "MAPLA_AREA_NUMBER" => $request->MAPLA_AREA_NUMBER,
                "MAPLA_PHONE_NUMBER" => $request->MAPLA_PHONE_NUMBER,
                "MAPLA_LAT" => $request->MAPLA_LAT,
                "MAPLA_LNG" => $request->MAPLA_LNG,
                "MAPLA_ADDRESS" => $request->MAPLA_ADDRESS,
                "MAPLA_MCOMP_CODE" => session('company_code'),
                "MAPLA_MCOMP_TEXT" => session('company_name'),
                "MAPLA_MBRAN_CODE" => session('brand_code'),
                "MAPLA_MBRAN_TEXT" => session('brand_name'),
                "MAPLA_STATUS" => 1,
                "MAPLA_CREATED_BY" => session("user_code"),
                "MAPLA_CREATED_TEXT" => session("user_name"),
                "MAPLA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
