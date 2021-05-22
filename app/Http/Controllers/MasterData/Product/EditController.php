<?php

namespace App\Http\Controllers\MasterData\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "MPRDT",
            "where" => [
                [
                    "field_name" => "MPRDT_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ]
            ],
            "first_row" => true,
        ]);

        return view('master_data/product/edit', [
            'data' => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MPRDT_TEXT" => "required|max:255",
            "MPRDT_MPRCA_CODE" => "required|max:255",
            "MBRAN_STATUS" => "max:2",
        ]);

        $attributeNames = [
            "MPRDT_TEXT" => "Product Name",
            "MPRDT_MPRCA_CODE" => "Category Code",
            "MPRDT_STATUS" => "Product Status",
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

        $categories = std_get([
            "select" => ["*"],
            "table_name" => "MPRCA",
            "where"=> [
                [
                    "field_name" => "MPRCA_CODE",
                    "operator" => "=",
                    "value" => $request->MPRDT_MPRCA_CODE
                ],
            ],
            "first_row" => true
        ]);

        $update_data = [
            "MPRDT_CODE" => $request->MPRDT_CODE,
            "MPRDT_TEXT" => $request->MPRDT_TEXT,
            "MPRDT_MPRCA_CODE" => $request->MPRDT_MPRCA_CODE,
            "MPRDT_MPRCA_TEXT" => $categories["MPRCA_TEXT"],
            "MPRDT_STATUS" => $request->MPRDT_STATUS,
            "MPRDT_UPDATED_BY" => session("user_code"),
            "MPRDT_UPDATED_TEXT" => session("user_name"),
            "MPRDT_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "MPRDT",
            "where" => ["MPRDT_ID" => $request->MPRDT_ID],
            "data" => $update_data
        ]);
        // update MPRDT in model
        $update_data = [
            "MPRMO_MPRDT_CODE" => $request->MPRDT_CODE,
            "MPRMO_MPRDT_TEXT" => $request->MPRDT_TEXT,
            "MPRMO_UPDATED_BY" => session("user_code"),
            "MPRMO_UPDATED_TEXT" => session("user_name"),
            "MPRMO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_res = std_update([
            "table_name" => "MPRMO",
            "where" => ["MPRMO_MPRDT_CODE" => $request->MPRDT_CODE],
            "data" => $update_data
        ]);
        // update MPRDT in Version
        $update_data = [
            "MPRVE_MPRDT_CODE" => $request->MPRDT_CODE,
            "MPRVE_MPRDT_TEXT" => $request->MPRDT_TEXT,
            "MPRVE_UPDATED_BY" => session("user_code"),
            "MPRVE_UPDATED_TEXT" => session("user_name"),
            "MPRVE_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_res = std_update([
            "table_name" => "MPRVE",
            "where" => ["MPRVE_MPRDT_CODE" => $request->MPRDT_CODE],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "There was an error saving data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
