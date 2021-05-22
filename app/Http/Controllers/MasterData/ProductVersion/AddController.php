<?php

namespace App\Http\Controllers\MasterData\ProductVersion;
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
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        $product = get_master_product("*",[
            [
                "field_name" => "MPRDT_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRDT_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        $model = get_master_product_model("*",[
            [
                "field_name" => "MPRMO_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRMO_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        return view('master_data/product_version/add', [
            "category" => $category,
            "product" => $product,
            "model" => $model,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MPRVE_MPRCA_CODE" => "required|exists:MPRCA,MPRCA_CODE",
            "MPRVE_MPRDT_CODE" => "required|exists:MPRDT,MPRDT_CODE",
            "MPRVE_MPRMO_CODE" => "required|exists:MPRMO,MPRMO_CODE",
            "MPRVE_TEXT" => "required",
            "MPRVE_SKU" => "required",
            "MPRVE_NOTES" => "max:255",
        ]);
            
        $attributeNames = [
            "MPRVE_TEXT" => "Product Version Text",
            "MPRVE_MPRCA_CODE" => "Product Category",
            "MPRVE_MPRDT_CODE" => "Product",
            "MPRVE_MPRMO_CODE" => "Product Model",
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

        $code = generate_code(session('company_code'),3,"MPRVE");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "There is something wrong when generating code, please try again"
            ],500);
        }

        $insert_data = [
            "MPRVE_CODE" => $code["data"],
            "MPRVE_TEXT" => $request->MPRVE_TEXT,
            "MPRVE_MCOMP_CODE" => session("company_code"),
            "MPRVE_MCOMP_TEXT" => session("company_name"),
            "MPRVE_MBRAN_CODE" => session("brand_code"),
            "MPRVE_MBRAN_TEXT" => session("brand_name"),
            "MPRVE_MPRCA_CODE" => $request->MPRVE_MPRCA_CODE,
            "MPRVE_MPRCA_TEXT" => $category["MPRCA_TEXT"],
            "MPRVE_MPRDT_CODE" => $request->MPRVE_MPRDT_CODE,
            "MPRVE_MPRDT_TEXT" => $product["MPRDT_TEXT"],
            "MPRVE_MPRMO_CODE" => $request->MPRVE_MPRMO_CODE,
            "MPRVE_MPRMO_TEXT" => $model["MPRMO_TEXT"],
            "MPRVE_SKU" => $request->MPRVE_SKU,
            "MPRVE_NOTES" => $request->MPRVE_NOTES,
            "MPRVE_STATUS" => 1,
            "MPRVE_IS_DELETED" => 0,
            "MPRVE_CREATED_BY" => session("user_id"),
            "MPRVE_CREATED_TEXT" => session("user_name"),
            "MPRVE_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MPRVE",
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
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 4) {
            $check_access = true;
        }

        if ($check_access == true) {
            $attribute = [
                "Brand Name",
                "Production Center",
                "Packaging Center",
                "Product Category Name",
                "Product Name",
                "Product Model Name",
                "Product Version Name",
                "SKU",
                "Production Date",
                "Packaging Date",
                "Production Staff",
                "Packaging Staff",
                "Description",
            ];

            for ($i=0; $i < count($attribute); $i++) { 
                $insert_data_attribute[] = [
                    "TRPAT_MCOMP_CODE" => session('company_code'),
                    "TRPAT_MCOMP_NAME" => session('company_name'),
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_MBRAN_NAME" => session('brand_name'),
                    "TRPAT_KEY_TYPE" => "4",
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
                'message' => "There is something wrong when saving data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
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

    public function model(Request $request)
    {
        $model = get_master_product_model(["MPRMO_CODE as id", "MPRMO_TEXT as text"],[
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRMO_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ],
            [
                "field_name" => "MPRMO_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ],
        ]);

        echo json_encode($model);
    }

    public function generate_sku(Request $request)
    {
        $sku_brand = $this->sku_formula(session("brand_name"));
        $sku_category = $this->sku_formula($request->category);
        $sku_product = $this->sku_formula($request->product);
        $sku_model = $this->sku_formula($request->model);
        $sku_version = $this->sku_formula($request->version);
        
        $temp_sku = $sku_brand.$sku_category.$sku_product.$sku_model.$sku_version;

        $count_sku = std_get([
            "table_name" => "MPRVE",
            "select" => ["MPRVE_ID"],
            "where" => [
                [
                    "field_name" => "MPRVE_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
                [
                    "field_name" => "MPRVE_SKU",
                    "operator" => "like",
                    "value" => "%".$temp_sku."%",
                ]
            ],
            "count" => true
        ]);

        $sku = $temp_sku.$count_sku;

        return response()->json($sku, 200);
    }

    public function sku_formula($request)
    {
        $separate_data = explode(" ",$request);

        $word;
        $process_word = [];
        $contain_number = false;

        for ($i=0; $i < count($separate_data); $i++) { 
            $process_word[] = [
                "clean_word" => $separate_data[$i],
                // ambil clean abjad dan angka "clean_word" => preg_replace('/[^A-Za-z0-9\-]/', '', $separate_data[$i]),
                "contain_number" => preg_match('~[0-9]+~', $separate_data[$i]),
            ];
        }

        for ($i=0; $i < count($process_word); $i++) { 
            if ($process_word[$i]["contain_number"] == true) {
                $contain_number = true;
                break;
            }
        }
        
        if ($contain_number == true) {
            if ($process_word[0]["clean_word"]) {
                $temp_word[] = $process_word[0]["clean_word"];
            }
            if (isset($process_word[1]["clean_word"])) {
                $temp_word[] = $process_word[1]["clean_word"];
            }
            if (isset($process_word[2]["clean_word"])) {
                $temp_word[] = $process_word[2]["clean_word"];
            }

            $word = implode("",$temp_word);
        } else {
            if ($process_word[0]["clean_word"]) {
                $temp_word[] = $process_word[0]["clean_word"][0];
            }
            if (isset($process_word[1]["clean_word"])) {
                $temp_word[] = $process_word[1]["clean_word"][0];
            }
            if (isset($process_word[2]["clean_word"])) {
                $temp_word[] = $process_word[2]["clean_word"][0];
            }

            $word = strtoupper(implode("",$temp_word));
        }

        return $word;
    }
}
