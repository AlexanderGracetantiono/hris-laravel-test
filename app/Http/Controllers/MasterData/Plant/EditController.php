<?php

namespace App\Http\Controllers\MasterData\Plant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function index(Request $request)
    {
        if ($request->code != NULL) {

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

            $data = std_get([
                "select" => ["*"],
                "table_name" => "MAPLA",
                "where" => [
                    [
                        "field_name" => "MAPLA_CODE",
                        "operator" => "=",
                        "value" => $request->code
                    ],
                ],
                "first_row" => true,
            ]);

            $code = std_get([
                "table_name" => "MACOP",
                "select" => "*",
            ]);

            if ($data == NULL) {
                abort(404);
            }

            return view('master_data/plant/edit', [
                'data' => $data,
                'companies' => $companies,
                'code' => $code,
            ]);
        }
        else{
            abort(404);
        }
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MAPLA_TEXT" => "required|max:255",
            "MAPLA_TYPE" => "required",
            "MAPLA_MACOP_CODE" => "max:255|required|exists:MACOP,MACOP_CODE",
            "MAPLA_AREA_NUMBER" => "max:255|required",
            "MAPLA_PHONE_NUMBER" => "max:255",
            "MAPLA_LAT" => "max:255",
            "MAPLA_LNG" => "max:255",
            "MAPLA_ADDRESS" => "required|max:255",
            "MAPLA_STATUS" => "required|max:255",
        ]);

        $attributeNames = [
            "MAPLA_TEXT" => "Production / Packaging Center Name",
            "MAPLA_TYPE" => "Production / Packaging Center Type",
            "MAPLA_MACOP_CODE" => "Phone Number - Code",
            "MAPLA_AREA_NUMBER" => "Phone Number - Area",
            "MAPLA_PHONE_NUMBER" => "Phone Number",
            "MAPLA_LAT" => "Production / Packaging Center Latitude",
            "MAPLA_LNG" => "Production / Packaging Center Longitude",
            "MAPLA_ADDRESS" => "Production / Packaging Center Address",
            "MAPLA_STATUS" => "Production / Packaging Center Status",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "MAPLA_TEXT" => $request->MAPLA_TEXT,
            "MAPLA_TYPE" => $request->MAPLA_TYPE,
            "MAPLA_MACOP_CODE" => $request->MAPLA_MACOP_CODE,
            "MAPLA_AREA_NUMBER" => $request->MAPLA_AREA_NUMBER,
            "MAPLA_PHONE_NUMBER" => $request->MAPLA_PHONE_NUMBER,
            "MAPLA_LAT" => $request->MAPLA_LAT,
            "MAPLA_LNG" => $request->MAPLA_LNG,
            "MAPLA_ADDRESS" => $request->MAPLA_ADDRESS,
            "MAPLA_UPDATED_BY" => session("user_code"),
            "MAPLA_UPDATED_TEXT" => session("user_name"),
            "MAPLA_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s")
        ];

        $update_res = std_update([
            "table_name" => "MAPLA",
            "where" => ["MAPLA_ID" => $request->MAPLA_ID],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
