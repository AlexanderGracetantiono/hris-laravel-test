<?php

namespace App\Http\Controllers\MasterData\BatchProduction;
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
            "table_name" => "MABPR",
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ]
            ],
            "first_row" => true,
        ]);

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
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code'),
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
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code'),
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
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code'),
            ],
            [
                "field_name" => "MPRMO_MPRCA_CODE",
                "operator" => "=",
                "value" => $data["MABPR_MPRCA_CODE"],
            ],
            [
                "field_name" => "MPRMO_MPRDT_CODE",
                "operator" => "=",
                "value" => $data["MABPR_MPRDT_CODE"],
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
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code'),
            ],
            [
                "field_name" => "MPRVE_MPRCA_CODE",
                "operator" => "=",
                "value" => $data["MABPR_MPRCA_CODE"],
            ],
            [
                "field_name" => "MPRVE_MPRDT_CODE",
                "operator" => "=",
                "value" => $data["MABPR_MPRDT_CODE"],
            ],
            [
                "field_name" => "MPRVE_MPRMO_CODE",
                "operator" => "=",
                "value" => $data["MABPR_MPRMO_CODE"],
            ],
        ]);
        $master_plant =  get_master_plant("*", [
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

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

        $start_timestamp = $data["MABPR_START_TIMESTAMP"]; 
        $end_timestamp = $data["MABPR_END_TIMESTAMP"]; 
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
                ],
                "order_by" => [
                    [
                        "field" => "STBPR_ID",
                        "type" => "DESC"
                    ]
                ],
                "first_row" => true,
            ]);

            if ($temp_master_employee_2 == null) {
                $master_employee[] = $temp_master_employee[$i];
            }
            else {
                if ($temp_master_employee_2["STBPR_MABPR_STATUS"] != 1 || $end_timestamp > $temp_master_employee_2["STBPR_MABPR_END_TIMESTAMP"] || $start_timestamp < $temp_master_employee_2["STBPR_MABPR_START_TIMESTAMP"]) {
                    $master_employee[] = $temp_master_employee[$i];
                }
            }
        }

        $staff_production = get_staff_production("*",[
            [
                "field_name" => "STBPR_MABPR_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ]);

        return view('master_data/batch_production/edit', [
            'data' => $data,
            'master_product_category' => $master_product_category,
            'master_product' => $master_product,
            'master_product_model' => $master_product_model,
            'master_product_version' => $master_product_version,
            'master_plant' => $master_plant,
            'master_employee' => $master_employee,
            'staff_production' => $staff_production,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MABPR_TEXT" => "required|max:255",
            "MABPR_EXPECTED_QTY" => "required|numeric",
            "MABPR_MAPLA_CODE" => "required",
            "MABPR_MPRCA_CODE" => "required",
            "MABPR_MPRDT_CODE" => "required",
            "MABPR_MPRMO_CODE" => "required",
            "MABPR_MPRVE_CODE" => "required",
            "MABPR_MAPLA_CODE" => "required",
            "STAFF.*.MAEMP_CODE" => "exists:MAEMP,MAEMP_CODE",
            "OLD_MAEMP_CODE.*" => "exists:MAEMP,MAEMP_CODE",
        ]);

        $attributeNames = [
            "MABPR_TEXT" => "Batch Production Name",
            "MABPR_EXPECTED_QTY" => "Targeted Quantity",
            "MABPR_MAPLA_CODE" => "Plant",
            "MABPR_MPRCA_CODE" => "Product Category",
            "MABPR_MPRDT_CODE" => "Product",
            "MABPR_MPRMO_CODE" => "Product Model",
            "MABPR_MPRVE_CODE" => "Product Version",
            "MABPR_MAPLA_CODE" => "Plant",
            "STAFF.*.MAEMP_CODE" => "New Users",
            "OLD_MAEMP_CODE.*" => "Old Users",
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

        $data = std_get([
            "select" => ["*"],
            "table_name" => "MABPR",
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->MABPR_CODE
                ]
            ],
            "first_row" => true,
        ]);

        $old_staff = [];
        $new_staff = [];
        if (isset($request->OLD_MAEMP_CODE)) {
            $old_staff = $request->OLD_MAEMP_CODE;
        }
        if (isset($request->STAFF)) {
            $staff = $request->STAFF;
            for ($i=0; $i < count($staff); $i++) { 
                $new_staff[] = $staff[$i]["MAEMP_CODE"];
            }
        }

        $merge_staff = array_merge($old_staff,$new_staff);
        $unique_merge_staff = array_map("unserialize", array_unique(array_map("serialize", $merge_staff)));

        if (count($merge_staff) !== count($unique_merge_staff)) {
            return response()->json([
                'message' => "There are duplicated users, please check your user production"
            ], 400);
        }

        if (isset($request->OLD_MAEMP_CODE)) {
            $old_staff = $request->OLD_MAEMP_CODE;
            for ($i=0; $i < count($old_staff); $i++) { 
                $old_employee[] = get_master_employee("*", [
                    [
                        "field_name" => "MAEMP_CODE",
                        "operator" => "=",
                        "value" => $old_staff[$i],
                    ],
                ],true);
            }
    
            for ($i=0; $i < count($old_employee); $i++) { 
                $staff_production[] = [
                    "STBPR_MABPR_CODE" => $request->MABPR_CODE,
                    "STBPR_MABPR_TEXT" => $request->MABPR_TEXT,
                    "STBPR_MABPR_STATUS" => "1",
                    "STBPR_MABPR_START_TIMESTAMP" => $data["MABPR_START_TIMESTAMP"],
                    "STBPR_MABPR_END_TIMESTAMP" => $data["MABPR_END_TIMESTAMP"],
                    "STBPR_EMP_CODE" => $old_employee[$i]["MAEMP_CODE"],
                    "STBPR_EMP_TEXT" => $old_employee[$i]["MAEMP_TEXT"],
                    "STBPR_CREATED_BY" => session("user_code"),
                    "STBPR_CREATED_TEXT" => session("user_name"),
                    "STBPR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }

        if (isset($request->STAFF)) {
            $staff = $request->STAFF;
            for ($i=0; $i < count($staff); $i++) { 
                $new_staff[] = $staff[$i]["MAEMP_CODE"];
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
                    "STBPR_MABPR_CODE" => $request->MABPR_CODE,
                    "STBPR_MABPR_TEXT" => $request->MABPR_TEXT,
                    "STBPR_MABPR_STATUS" => "1",
                    "STBPR_MABPR_START_TIMESTAMP" => $data["MABPR_START_TIMESTAMP"],
                    "STBPR_MABPR_END_TIMESTAMP" => $data["MABPR_END_TIMESTAMP"],
                    "STBPR_EMP_CODE" => $employee[$i]["MAEMP_CODE"],
                    "STBPR_EMP_TEXT" => $employee[$i]["MAEMP_TEXT"],
                    "STBPR_CREATED_BY" => session("user_code"),
                    "STBPR_CREATED_TEXT" => session("user_name"),
                    "STBPR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }
        
        $update_data = [
            "MABPR_TEXT" => $request->MABPR_TEXT,
            "MABPR_EXPECTED_QTY" => $request->MABPR_EXPECTED_QTY,
            "MABPR_START_TIMESTAMP" => $data["MABPR_START_TIMESTAMP"],
            "MABPR_END_TIMESTAMP" => $data["MABPR_END_TIMESTAMP"],
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
            "MABPR_UPDATED_BY" => session("user_code"),
            "MABPR_UPDATED_TEXT" => session("user_name"),
            "MABPR_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s")
        ];

        $update_res = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $request->MABPR_CODE],
            "data" => $update_data
        ]);

        std_delete([
            "table_name" => "STBPR",
            "where" => ["STBPR_MABPR_CODE" => $request->MABPR_CODE],
        ]);

        $insert_staff = std_insert([
            "table_name" => "STBPR",
            "data" => $staff_production
        ]);

        if ($update_res == false || $insert_staff == false) {
            return response()->json([
                'message' => "There was an error saving brand production, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
