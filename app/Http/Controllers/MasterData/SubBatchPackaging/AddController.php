<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }
    
    public function index()
    {
        $pool_product = get_pool_product("*",[
            [
                "field_name" => "POPRD_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code")
            ],
            [
                "field_name" => "POPRD_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
            [
                "field_name" => "POPRD_QTY_LEFT",
                "operator" => ">",
                "value" => "0",
            ],
        ]);

        $master_plant = get_master_plant("*", [
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MAPLA_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        return view('master_data/sub_batch_packaging/add', [
            'pool_product' => $pool_product,
            'master_plant' => $master_plant,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "SUBPA_TEXT" => "required|max:255",
            "SUBPA_POPRD_CODE" => "required|exists:POPRD,POPRD_CODE",
            "SUBPA_DATE_START" => "required",
            "SUBPA_TIME_START" => "required",
            "SUBPA_TIME_END" => "required",
            "SUBPA_QTY" => "required|numeric",
            "SUBPA_TEXT" => "required",
            "SUBPA_MAPLA_CODE" => "required|exists:MAPLA,MAPLA_CODE",
            "STAFF" => "required|array",
            "STAFF.*.MAEMP_CODE" => "required|exists:MAEMP,MAEMP_CODE",
        ]);

        $attributeNames = [
            "SUBPA_POPRD_CODE" => "Pool Product",
            "SUBPA_DATE_START" => "Batch date",
            "SUBPA_TIME_START" => "Batch time start",
            "SUBPA_TIME_END" => "Batch time end",
            "SUBPA_MAPLA_CODE" => "Plant packaging",
            "SUBPA_QTY" => "Batch Packaging Quantity",
            "SUBPA_DATE" => "Batch Packaging Date",
            "SUBPA_TEXT" => "Batch Packaging Name",
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
                'message' => "There are duplicated users, please check your user packaging"
            ], 400);
        }
        
        $pool_product = get_pool_product("*", [
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_POPRD_CODE
            ],
        ],true);
        
        if ($pool_product["POPRD_QTY_LEFT"] < $request->SUBPA_QTY) {
            return response()->json([
                'message' => "Sub Packaging quantity exceed pool product quantity"
            ],500);
        }
        
        $master_plant = get_master_plant("*", [
            [
                "field_name" => "MAPLA_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_MAPLA_CODE
            ],
        ],true);        

        $code = generate_code(session('company_code'),5,"SUBPA");
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
            $staff_packaging[] = [
                "STBPA_POPRD_CODE" => $request->SUBPA_POPRD_CODE,
                "STBPA_SUBPA_CODE" => strtoupper($code["data"]),
                "STBPA_SUBPA_TEXT" => $request->SUBPA_TEXT,
                "STBPA_SUBPA_STATUS" => "1",
                "STBPA_SUBPA_START_TIMESTAMP" => $request->SUBPA_DATE_START." ".$request->SUBPA_TIME_START.":00",
                "STBPA_SUBPA_END_TIMESTAMP" => $request->SUBPA_DATE_START." ".$request->SUBPA_TIME_END.":59",
                "STBPA_EMP_CODE" => $employee[$i]["MAEMP_CODE"],
                "STBPA_EMP_TEXT" => $employee[$i]["MAEMP_TEXT"],
                "STBPA_CREATED_BY" => session("user_code"),
                "STBPA_CREATED_TEXT" => session("user_name"),
                "STBPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        $insert_res = std_insert([
            "table_name" => "SUBPA",
            "data" => [
                "SUBPA_CODE" => strtoupper($code["data"]),
                "SUBPA_TEXT" => $request->SUBPA_TEXT,
                "SUBPA_POPRD_CODE" => $request->SUBPA_POPRD_CODE,
                "SUBPA_MAPLA_CODE" => $request->SUBPA_MAPLA_CODE,
                "SUBPA_MAPLA_TEXT" => $master_plant["MAPLA_TEXT"],
                "SUBPA_QTY" => $request->SUBPA_QTY,
                "SUBPA_DATE" => $request->SUBPA_DATE,
                "SUBPA_START_TIMESTAMP" => $request->SUBPA_DATE_START." ".$request->SUBPA_TIME_START.":00",
                "SUBPA_END_TIMESTAMP" => $request->SUBPA_DATE_START." ".$request->SUBPA_TIME_END.":59",
                "SUBPA_MCOMP_CODE" => session('company_code'),
                "SUBPA_MCOMP_TEXT" => session('company_name'),
                "SUBPA_MBRAN_CODE" => session('brand_code'),
                "SUBPA_MBRAN_TEXT" => session('brand_name'),
                "SUBPA_STATUS" => 1,
                "SUBPA_PAIRED_QTY" => 0,
                "SUBPA_ACTIVATION_STATUS" => 0,
                "SUBPA_IS_DELETED" => 0,
                "SUBPA_CREATED_BY" => session("user_code"),
                "SUBPA_CREATED_TEXT" => session("user_name"),
                "SUBPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $insert_staff = std_insert([
            "table_name" => "STBPA",
            "data" => $staff_packaging
        ]);

        $update_batch = std_update([
            "table_name" => "POPRD",
            "where" => ["POPRD_CODE" => $request->SUBPA_POPRD_CODE],
            "data" => [
                "POPRD_QTY_LEFT" => $pool_product["POPRD_QTY_LEFT"] - $request->SUBPA_QTY
            ]
        ]);

        if ($insert_res != true && $insert_staff != true && $update_batch != true) {
            return response()->json([
                'message' => "There was an error saving data, please try again for a few moments"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }

    public function get_pool_product(Request $request)
    {
        $data = get_pool_product("*",[
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        return response()->json($data, 200);
    }

    public function get_staff_packaging(Request $request)
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
                    "value" => "7",
                ],
                [
                    "field_name" => "MAEMP_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session('brand_code'),
                ],
            ],
        ]);

        $master_employee = [];
        for ($i=0; $i < count($temp_master_employee); $i++) { 
            $temp_master_employee_2 = std_get([
                "table_name" => "STBPA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "STBPA_EMP_CODE",
                        "operator" => "=",
                        "value" => $temp_master_employee[$i]["MAEMP_CODE"],
                    ],
                    [
                        "field_name" => "STBPA_SUBPA_END_TIMESTAMP",
                        "operator" => ">",
                        "value" => $start_timestamp,
                    ],
                    [
                        "field_name" => "STBPA_SUBPA_START_TIMESTAMP",
                        "operator" => "<",
                        "value" => $end_timestamp,
                    ],
                ],
                "order_by" => [
                    [
                        "field" => "STBPA_ID",
                        "type" => "DESC"
                    ]
                ],
                "first_row" => true,
            ]);

            if ($temp_master_employee_2 == null || $temp_master_employee_2["STBPA_SUBPA_STATUS"] != 1) {
                $master_employee[] = $temp_master_employee[$i];
            }
        }

        $response = null;
        for ($i=0; $i < count($master_employee); $i++) { 
            $response[$i]["text"] = $master_employee[$i]["MAEMP_TEXT"]; 
            $response[$i]["id"] = $master_employee[$i]["MAEMP_CODE"]; 
        }

        echo json_encode($response);
    }
}
