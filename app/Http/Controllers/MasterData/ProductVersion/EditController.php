<?php

namespace App\Http\Controllers\MasterData\ProductVersion;
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
        $data = get_master_product_version("*",[
            [
                "field_name" => "MPRVE_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        return view('master_data/product_version/edit', [
            "data" => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MPRVE_CODE" => "required|max:255|exists:MPRVE,MPRVE_CODE",
            "MPRVE_MPRCA_CODE" => "required|exists:MPRCA,MPRCA_CODE",
            "MPRVE_MPRDT_CODE" => "required|exists:MPRDT,MPRDT_CODE",
            "MPRVE_MPRMO_CODE" => "required|exists:MPRMO,MPRMO_CODE",
            "MPRVE_TEXT" => "required",
            "MPRVE_SKU" => "required",
            "MPRVE_NOTES" => "max:255",
        ]);

        $attributeNames = [
            "MPRVE_CODE" => "Product Model Code",
            "MPRVE_TEXT" => "Product Model Text",
            "MPRVE_MPRCA_CODE" => "Product Category",
            "MPRVE_MPRDT_CODE" => "Product",
            "MPRVE_SKU" => "Product Version SKU",
            "MPRVE_NOTES" => "Product Version Notes",
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
                "value" => $request->MPRVE_MPRCA_CODE
            ]
        ],true);

        $product = get_master_product("*",[
            [
                "field_name" => "MPRDT_CODE",
                "operator" => "=",
                "value" => $request->MPRVE_MPRDT_CODE
            ]
        ],true);

        $model = get_master_product_model("*",[
            [
                "field_name" => "MPRMO_CODE",
                "operator" => "=",
                "value" => $request->MPRVE_MPRMO_CODE
            ]
        ],true);

        $update_data = [
            "MPRVE_TEXT" => $request->MPRVE_TEXT,
            "MPRVE_MPRCA_CODE" => $request->MPRVE_MPRCA_CODE,
            "MPRVE_MPRCA_TEXT" => $category["MPRCA_TEXT"],
            "MPRVE_MPRDT_CODE" => $request->MPRVE_MPRDT_CODE,
            "MPRVE_MPRDT_TEXT" => $product["MPRDT_TEXT"],
            "MPRVE_MPRMO_CODE" => $request->MPRVE_MPRMO_CODE,
            "MPRVE_MPRMO_TEXT" => $model["MPRMO_TEXT"],
            "MPRVE_STATUS" => $request->MPRVE_STATUS,
            "MPRVE_SKU" => $request->MPRVE_SKU,
            "MPRVE_NOTES" => $request->MPRVE_NOTES,
            "MPRVE_CREATED_BY" => session("user_id"),
            "MPRVE_CREATED_TEXT" => session("user_name"),
            "MPRVE_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_update([
            "table_name" => "MPRVE",
            "data" => $update_data,
            "where" => ["MPRVE_CODE" => $request->MPRVE_CODE]
        ]);

        if ($insert_res != true) {
            return response()->json([
                'message' => "There is something wrong when updating data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
