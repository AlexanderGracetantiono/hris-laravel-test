<?php

namespace App\Http\Controllers\MasterData\ProductModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_product_model("*",[
            [
                "field_name" => "MPRMO_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        return view('master_data/product_model/edit', [
            "data" => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MPRMO_CODE" => "required|max:255|exists:MPRMO,MPRMO_CODE",
            "MPRMO_TEXT" => "required",
            "MPRMO_MPRCA_CODE" => "required|exists:MPRCA,MPRCA_CODE",
            "MPRMO_MPRDT_CODE" => "required|exists:MPRDT,MPRDT_CODE",
        ]);

        $attributeNames = [
            "MPRMO_CODE" => "Product Model Code",
            "MPRMO_TEXT" => "Product Model Text",
            "MPRMO_MPRCA_CODE" => "Product category",
            "MPRMO_MPRDT_CODE" => "Product",
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

        $category = get_master_product_category("*",[
            [
                "field_name" => "MPRCA_CODE",
                "operator" => "=",
                "value" => $request->MPRMO_MPRCA_CODE
            ]
        ],true);

        $product = get_master_product("*",[
            [
                "field_name" => "MPRDT_CODE",
                "operator" => "=",
                "value" => $request->MPRMO_MPRDT_CODE
            ]
        ],true);

        $update_data = [
            "MPRMO_TEXT" => $request->MPRMO_TEXT,
            "MPRMO_MPRCA_CODE" => $request->MPRMO_MPRCA_CODE,
            "MPRMO_MPRCA_TEXT" => $category["MPRCA_TEXT"],
            "MPRMO_MPRDT_CODE" => $request->MPRMO_MPRDT_CODE,
            "MPRMO_MPRDT_TEXT" => $product["MPRDT_TEXT"],
            "MPRMO_STATUS" => $request->MPRMO_STATUS,
            "MPRMO_CREATED_BY" => session("user_id"),
            "MPRMO_CREATED_TEXT" => session("user_name"),
            "MPRMO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_update([
            "table_name" => "MPRMO",
            "data" => $update_data,
            "where" => ["MPRMO_CODE" => $request->MPRMO_CODE]
        ]);
        if ($insert_res != true) {
            return response()->json([
                'message' => "There is something wrong when updating data, please try again"
            ],500);
        }
         // update model in Version
         $update_data = [
            "MPRVE_MPRMO_TEXT" => $request->MPRMO_TEXT,
            "MPRVE_UPDATED_BY" => session("user_code"),
            "MPRVE_UPDATED_TEXT" => session("user_name"),
            "MPRVE_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_res = std_update([
            "table_name" => "MPRVE",
            "where" => ["MPRVE_MPRMO_CODE" => $request->MPRMO_CODE],
            "data" => $update_data
        ]);

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
