<?php

namespace App\Http\Controllers\MasterData\Product;

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
        $categories = std_get([
            "select" => ["*"],
            "table_name" => "MPRCA",
            "where" => [
                [
                    "field_name" => "MPRCA_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
                [
                    "field_name" => "MPRCA_STATUS",
                    "operator" => "=",
                    "value" => "1"
                ],
                [
                    "field_name" => "MPRCA_MCOMP_CODE",
                    "operator" => "=",
                    "value" => session("company_code"),
                ],
                [
                    "field_name" => "MPRCA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
            ],
        ]);

        return view('master_data/product/add', ['categories' => $categories]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "MPRDT_MPRCA_CODE" => "required|max:255",
            "MPRDT_TEXT" => "required|max:255",
        ]);

        $attributeNames = [
            "MPRDT_TEXT" => "Product Name",
            "MPRDT_MPRCA_CODE" => "Category Name",
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

        $categories = std_get([
            "select" => ["*"],
            "table_name" => "MPRCA",
            "where" => [
                [
                    "field_name" => "MPRCA_CODE",
                    "operator" => "=",
                    "value" => $request->MPRDT_MPRCA_CODE
                ],
            ],
            "first_row" => true
        ]);

        $code = generate_code(session('company_code'), 5, "MPRDT");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_res = std_insert([
            "table_name" => "MPRDT",
            "data" => [
                "MPRDT_CODE" => strtoupper($code["data"]),
                "MPRDT_TEXT" => $request->MPRDT_TEXT,
                "MPRDT_MCOMP_CODE" => session('company_code'),
                "MPRDT_MCOMP_TEXT" => session('company_name'),
                "MPRDT_MBRAN_CODE" => session('brand_code'),
                "MPRDT_MBRAN_TEXT" => session('brand_name'),
                "MPRDT_MPRCA_CODE" => $request->MPRDT_MPRCA_CODE,
                "MPRDT_MPRCA_TEXT" => $categories["MPRCA_TEXT"],
                "MPRDT_STATUS" => 1,
                "MPRDT_CREATED_BY" => session("user_code"),
                "MPRDT_CREATED_TEXT" => session("user_name"),
                "MPRDT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
            $check_access = true;
        }

        if ($check_access == true) {
            $attribute = [
                "Brand Name",
                "Product Category Name",
                "Product Name",
                "Product Model Name",
                "Product Version Name",
                "SKU",
                "Production Date",
                "Packaging Date",
                "Description",
            ];

            for ($i=0; $i < count($attribute); $i++) { 
                $insert_data_attribute[] = [
                    "TRPAT_MCOMP_CODE" => session('company_code'),
                    "TRPAT_MCOMP_NAME" => session('company_name'),
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_MBRAN_NAME" => session('brand_name'),
                    "TRPAT_KEY_TYPE" => "2",
                    "TRPAT_KEY_CODE" => strtoupper($code["data"]),
                    "TRPAT_LABEL" => $attribute[$i],
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
                'message' => "There was an error saving the product data, please try again for a few moments"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }

    public function category(Request $request)
    {
        $category = get_master_product_category(["MPRCA_CODE as id", "MPRCA_TEXT as text"],[
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ],
            [
                "field_name" => "MPRCA_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
        ]);

        echo json_encode($category);
    }

    public function product(Request $request)
    {
        $product = get_master_product(["MPRDT_CODE as id", "MPRDT_TEXT as text"],[
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ],
            [
                "field_name" => "MPRDT_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ],
            [
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
        ]);

        echo json_encode($product);
    }
}
