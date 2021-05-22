<?php

namespace App\Http\Controllers\MasterData\ProductModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index()
    {
        $category = get_master_product_category("*",[
            [
                "field_name" => "MPRCA_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRCA_IS_DELETED",
                "operator" => "=",
                "value" => "0",
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
        ]);

        return view('master_data/product_model/add', [
            "category" => $category,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MPRMO_MPRCA_CODE" => "required|exists:MPRCA,MPRCA_CODE",
            "MPRMO_MPRDT_CODE" => "required|exists:MPRDT,MPRDT_CODE",
            "MPRMO_TEXT" => "required",
        ]);

        $attributeNames = [
            "MPRMO_MPRCA_CODE" => "Product Category",
            "MPRMO_MPRDT_CODE" => "Product",
            "MPRMO_TEXT" => "Product Model Text",
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

        $code = generate_code(session('company_code'),5,"MPRMO");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "There is something wrong when generating code, please try again"
            ],500);
        }

        $insert_data = [
            "MPRMO_CODE" => $code["data"],
            "MPRMO_TEXT" => $request->MPRMO_TEXT,
            "MPRMO_MCOMP_CODE" => session("company_code"),
            "MPRMO_MCOMP_TEXT" => session("company_name"),
            "MPRMO_MBRAN_CODE" => session("brand_code"),
            "MPRMO_MBRAN_TEXT" => session("brand_name"),
            "MPRMO_MPRCA_CODE" => $request->MPRMO_MPRCA_CODE,
            "MPRMO_MPRCA_TEXT" => $category["MPRCA_TEXT"],
            "MPRMO_MPRDT_CODE" => $request->MPRMO_MPRDT_CODE,
            "MPRMO_MPRDT_TEXT" => $product["MPRDT_TEXT"],
            "MPRMO_STATUS" => 1,
            "MPRMO_IS_DELETED" => 0,
            "MPRMO_CREATED_BY" => session("user_id"),
            "MPRMO_CREATED_TEXT" => session("user_name"),
            "MPRMO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MPRMO",
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
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 3) {
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
                    "TRPAT_KEY_TYPE" => "3",
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
                'message' => "There is something wrong when saving data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function product(Request $request)
    {
        $product = get_master_product(["MPRDT_CODE as id", "MPRDT_TEXT as text"],[
            [
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRDT_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ],
        ]);

        echo json_encode($product);
    }
}
