<?php

namespace App\Http\Controllers\MasterData\BatchProduction;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index()
    {
        $master_product_category =  get_master_product_category("*", [
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
        $master_product =  get_master_product("*", [
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
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);
        $master_product_model =  get_master_product_model("*", [
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
                "field_name" => "MPRMO_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);
        $master_product_version =  get_master_product_version("*", [
            [
                "field_name" => "MPRVE_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRVE_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MPRVE_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);
        $master_plant =  get_master_plant("*", [
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        return view('master_data/batch_production/add', [
            'master_product_category' => $master_product_category,
            'master_product' => $master_product,
            'master_product_model' => $master_product_model,
            'master_product_version' => $master_product_version,
            'master_plant' => $master_plant,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "MABPR_TEXT" => "required|max:255",
            "MABPR_EXPECTED_QTY" => "required|numeric",
            "MABPR_MAPLA_CODE" => "required",
            "MABPR_DATE_START" => "required",
            "MABPR_TIME_START" => "required",
            "MABPR_TIME_END" => "required",
            "MABPR_MPRCA_CODE" => "required",
            "MABPR_MPRDT_CODE" => "required",
            "MABPR_MPRMO_CODE" => "required",
            "MABPR_MPRVE_CODE" => "required",
            "STAFF" => "required|array",
            "STAFF.*.MAEMP_CODE" => "required",
        ]);

        $attributeNames = [
            "MABPR_TEXT" => "Batch Name",
            "MABPR_EXPECTED_QTY" => "Targeted Quantity",
            "MABPR_MAPLA_CODE" => "Production Center",
            "MABPR_DATE_START" => "Batch Start Date",
            "MABPR_TIME_START" => "Batch Start Time",
            "MABPR_TIME_END" => "Batch End Time",
            "MABPR_MPRCA_CODE" => "Product Category",
            "MABPR_MPRDT_CODE" => "Product",
            "MABPR_MPRMO_CODE" => "Product Model",
            "MABPR_MPRVE_CODE" => "Product Version",
            "STAFF" => "Users",
            "STAFF.*.MAEMP_CODE" => "Users",
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

        $staff = $request->STAFF;
        $unique_staff = array_map("unserialize", array_unique(array_map("serialize", $staff)));

        if (count($staff) !== count($unique_staff)) {
            return response()->json([
                'message' => "There are duplicated users, please check your user production"
            ], 400);
        }

        $master_product_category =  get_master_product_category("*", [
            [
                "field_name" => "MPRCA_CODE",
                "operator" => "=",
                "value" => $request->MABPR_MPRCA_CODE
            ],
        ],true);
        $master_product =  get_master_product("*", [
            [
                "field_name" => "MPRDT_CODE",
                "operator" => "=",
                "value" => $request->MABPR_MPRDT_CODE
            ],
        ],true);
        $master_product_model =  get_master_product_model("*", [
            [
                "field_name" => "MPRMO_CODE",
                "operator" => "=",
                "value" => $request->MABPR_MPRMO_CODE
            ],
        ],true);
        $master_product_version =  get_master_product_version("*", [
            [
                "field_name" => "MPRVE_CODE",
                "operator" => "=",
                "value" => $request->MABPR_MPRVE_CODE
            ],
        ],true);
        $master_plant =  get_master_plant("*", [
            [
                "field_name" => "MAPLA_CODE",
                "operator" => "=",
                "value" => $request->MABPR_MAPLA_CODE
            ],
        ],true);        

        $code = generate_code(session('company_code'),5,"MABPR");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $staff = $request->STAFF;
        
        for ($i=0; $i < count($staff); $i++) { 
            $employee[] = get_master_employee("*", [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "=",
                    "value" => $staff[$i]["MAEMP_CODE"],
                ],
            ],true);
        }

        for ($i=0; $i < count($employee); $i++) { 
            $staff_production[] = [
                "STBPR_MABPR_CODE" => strtoupper($code["data"]),
                "STBPR_MABPR_TEXT" => $request->MABPR_TEXT,
                "STBPR_MABPR_STATUS" => "1",
                "STBPR_MABPR_START_TIMESTAMP" => $request->MABPR_DATE_START." ".$request->MABPR_TIME_START.":00",
                "STBPR_MABPR_END_TIMESTAMP" => $request->MABPR_DATE_START." ".$request->MABPR_TIME_END.":59",
                "STBPR_EMP_CODE" => $employee[$i]["MAEMP_CODE"],
                "STBPR_EMP_TEXT" => $employee[$i]["MAEMP_TEXT"],
                "STBPR_CREATED_BY" => session("user_code"),
                "STBPR_CREATED_TEXT" => session("user_name"),
                "STBPR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        $insert_res = std_insert([
            "table_name" => "MABPR",
            "data" => [
                "MABPR_CODE" => strtoupper($code["data"]),
                "MABPR_TEXT" => $request->MABPR_TEXT,
                "MABPR_PAIRED_QTY" => "0",
                "MABPR_EXPECTED_QTY" => $request->MABPR_EXPECTED_QTY,
                "MABPR_START_TIMESTAMP" => $request->MABPR_DATE_START." ".$request->MABPR_TIME_START.":00",
                "MABPR_END_TIMESTAMP" => $request->MABPR_DATE_START." ".$request->MABPR_TIME_END.":59",
                "MABPR_MCOMP_CODE" => session('company_code'),
                "MABPR_MCOMP_TEXT" => session('company_name'),
                "MABPR_MBRAN_CODE" => session('brand_code'),
                "MABPR_MBRAN_TEXT" => session('brand_name'),
                "MABPR_MPRCA_CODE" => $request->MABPR_MPRCA_CODE,
                "MABPR_MPRCA_TEXT" => $master_product_category["MPRCA_TEXT"],
                "MABPR_MPRDT_CODE" => $request->MABPR_MPRDT_CODE,
                "MABPR_MPRDT_TEXT" => $master_product["MPRDT_TEXT"],
                "MABPR_MPRMO_CODE" => $request->MABPR_MPRMO_CODE,
                "MABPR_MPRMO_TEXT" => $master_product_model["MPRMO_TEXT"],
                "MABPR_MPRVE_CODE" => $request->MABPR_MPRVE_CODE,
                "MABPR_MPRVE_TEXT" => $master_product_version["MPRVE_TEXT"],
                "MABPR_MPRVE_SKU" => $master_product_version["MPRVE_SKU"],
                "MABPR_MPRVE_NOTES" => $master_product_version["MPRVE_NOTES"],
                "MABPR_MAPLA_CODE" => $request->MABPR_MAPLA_CODE,
                "MABPR_MAPLA_TEXT" => $master_plant["MAPLA_TEXT"],
                "MABPR_STATUS" => 1,
                "MABPR_IS_DELETED" => 0,
                "MABPR_CREATED_BY" => session("user_code"),
                "MABPR_CREATED_TEXT" => session("user_name"),
                "MABPR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $insert_staff = std_insert([
            "table_name" => "STBPR",
            "data" => $staff_production
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }
    public function category(Request $request)
    {
        $category = get_master_product_category(["MPRCA_CODE as id", "MPRCA_TEXT as text"], [
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ],
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

        echo json_encode($category);
    }

    public function product(Request $request)
    {
        $product = get_master_product(["MPRDT_CODE as id", "MPRDT_TEXT as text"], [
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
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        echo json_encode($product);
    }

    public function model(Request $request)
    {
        $model = get_master_product_model(["MPRMO_CODE as id", "MPRMO_TEXT as text"], [
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
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
                "field_name" => "MPRMO_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        echo json_encode($model);
    }

    public function version(Request $request)
    {
        $model = get_master_product_version(["MPRVE_CODE as id", "MPRVE_TEXT as text"], [
            [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ],
            [
                "field_name" => "MPRVE_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ],
            [
                "field_name" => "MPRVE_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ],
            [
                "field_name" => "MPRVE_MPRMO_CODE",
                "operator" => "=",
                "value" => $request->model,
            ],
            [
                "field_name" => "MPRVE_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRVE_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MPRVE_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        echo json_encode($model);
    }

    public function description(Request $request)
    {
        $data = get_master_product_version("*",[
            [
                "field_name" => "MPRVE_CODE",
                "operator" => "=",
                "value" => $request->version,
            ]
        ],true);

        return response()->json($data, 200);
    }

    public function get_staff_production(Request $request)
    {
        $start_timestamp = $request->date." ".$request->start_time.":00"; 
        $end_timestamp = $request->date." ".$request->end_time.":59"; 
        $temp_master_employee = std_get([
            "table_name" => "MAEMP",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAEMP_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => 0,
                ],
                [
                    "field_name" => "MAEMP_ROLE",
                    "operator" => "=",
                    "value" => "6",
                ],
                [
                    "field_name" => "MAEMP_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session('brand_code'),
                ],
            ],
        ]);

        $response = null;
        $master_employee = [];
        for ($i=0; $i < count($temp_master_employee); $i++) { 
            $temp_master_employee_2 = std_get([
                "table_name" => "STBPR",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "STBPR_EMP_CODE",
                        "operator" => "=",
                        "value" => $temp_master_employee[$i]["MAEMP_CODE"],
                    ],
                    [
                        "field_name" => "STBPR_MABPR_END_TIMESTAMP",
                        "operator" => ">=",
                        "value" => $start_timestamp,
                    ],
                    [
                        "field_name" => "STBPR_MABPR_START_TIMESTAMP",
                        "operator" => "<=",
                        "value" => $end_timestamp,
                    ],
                ],
                "order_by" => [
                    [
                        "field" => "STBPR_ID",
                        "type" => "DESC"
                    ]
                ],
                "first_row" => true,
            ]);

            if ($temp_master_employee_2 == null || $temp_master_employee_2["STBPR_MABPR_STATUS"] != 1) {
                $master_employee[] = $temp_master_employee[$i];
            }
            
        }

        for ($i=0; $i < count($master_employee); $i++) { 
            $response[$i]["text"] = $master_employee[$i]["MAEMP_TEXT"]; 
            $response[$i]["id"] = $master_employee[$i]["MAEMP_CODE"]; 
        }

        echo json_encode($response);
    }
}
