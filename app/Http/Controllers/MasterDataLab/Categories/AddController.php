<?php

namespace App\Http\Controllers\MasterDataLab\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }

    public function index()
    {
        return view('master_data_lab/product_categories/add', [
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "category_name" => "required|max:255",
        ]);

        $attributeNames = [
            "category_name" => "Category Name",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
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
            ], 400);
        }

        $code = generate_code(session('company_code'),5,"MPRCA");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_data = [
            "MPRCA_CODE" => strtoupper($code["data"]),
            "MPRCA_TEXT" => $request->category_name,
            "MPRCA_MCOMP_CODE" => session('company_code'),
            "MPRCA_MCOMP_TEXT" => session('company_name'),
            "MPRCA_MBRAN_CODE" => session('brand_code'),
            "MPRCA_MBRAN_TEXT" => session('brand_name'),
            "MPRCA_STATUS" => 1,
            "MPRCA_IS_DELETED" => 0,
            "MPRCA_CREATED_BY" => session("user_id"),
            "MPRCA_CREATED_TEXT" => session("user_name"),
            "MPRCA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MPRCA",
            "data" => $insert_data
        ]);
        
        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
            $check_access = true;
        }

        if ($check_access == true) {
            $attribute = [
                "Hospital Name",
                "Testing Center",
                "Laboratory",
                "Test Lab Type",
                "Gender",
                "Date Of Birth",
                "Patient",
                "NIK",
                "Testing Date",
                "Result Date",
                "Testing Staff",
                "Laboratory Staff",
                "Result",
            ];

            for ($i=0; $i < count($attribute); $i++) { 
                $insert_data_attribute[] = [
                    "TRPAT_MCOMP_CODE" => session('company_code'),
                    "TRPAT_MCOMP_NAME" => session('company_name'),
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_MBRAN_NAME" => session('brand_name'),
                    "TRPAT_KEY_TYPE" => "1",
                    "TRPAT_KEY_CODE" => strtoupper($code["data"]),
                    "TRPAT_LABEL" => $attribute[$i],
                    "TRPAT_MASKING" => $attribute[$i],
                    "TRPAT_ACTIVE_STATUS" => "1",
                    "TRPAT_TYPE" => "1",
                    "TRPAT_CREATED_BY" => session("user_id"),
                    "TRPAT_CREATED_TEXT" => session("user_name"),
                    "TRPAT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }

            $insert_res_attribute = std_insert([
                "table_name" => "TRPAT",
                "data" => $insert_data_attribute
            ]);
        }

        if ($insert_res !== true) {
            return response()->json([
                'message' => "Something wrong when saving data, please try again"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }
}
