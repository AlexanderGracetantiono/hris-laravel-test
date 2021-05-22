<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        $selected_pool_product = get_pool_product("*",[
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_POPRD_CODE"],
            ],
        ],true);

        $plants = get_master_plant("*", [
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MAPLA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code')
            ],
        ]);

        $staff_packaging = get_staff_packaging("*",[
            [
                "field_name" => "STBPA_SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
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
                    "value" => "7",
                ],
                [
                    "field_name" => "MAEMP_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session('brand_code'),
                ],
            ],
        ]);

        $start_timestamp = $data["SUBPA_START_TIMESTAMP"]; 
        $end_timestamp = $data["SUBPA_END_TIMESTAMP"]; 
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
                ],
                "order_by" => [
                    [
                        "field" => "STBPA_ID",
                        "type" => "DESC"
                    ]
                ],
                "first_row" => true,
            ]);

            if ($temp_master_employee_2 == null) {
                $master_employee[] = $temp_master_employee[$i];
            }
            else {
                if ($temp_master_employee_2["STBPA_SUBPA_STATUS"] != 1 || $end_timestamp > $temp_master_employee_2["STBPA_SUBPA_END_TIMESTAMP"] || $start_timestamp < $temp_master_employee_2["STBPA_SUBPA_START_TIMESTAMP"]) {
                    $master_employee[] = $temp_master_employee[$i];
                }
            }
        }

        return view('master_data/sub_batch_packaging/edit',[
            'plants' => $plants,
            'selected_pool_product' => $selected_pool_product,
            'data' => $data,
            'staff_packaging' => $staff_packaging,
            'master_employee' => $master_employee,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "SUBPA_TEXT" => "required|max:255",
            "SUBPA_POPRD_CODE" => "required|exists:POPRD,POPRD_CODE",
            "SUBPA_QTY" => "required|numeric",
            "SUBPA_MAPLA_CODE" => "required|exists:MAPLA,MAPLA_CODE",
            "STAFF.*.MAEMP_CODE" => "required|exists:MAEMP,MAEMP_CODE",
            "OLD_MAEMP_CODE.*" => "exists:MAEMP,MAEMP_CODE",
        ]);

        $attributeNames = [
            "SUBPA_TEXT" => "Sub Batch Packaging Name",
            "SUBPA_POPRD_CODE" => "Pool Product",
            "SUBPA_QTY" => "Sub Batch Quantity",
            "SUBPA_MAPLA_CODE" => "Plant",
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

        $pool_product = get_pool_product("*", [
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_POPRD_CODE
            ],
        ],true);

        $data = get_master_sub_batch_packaging("*", [
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_CODE,
            ]
        ],true);

        $old_quantity = $request->old_qty;
        $new_quantity = $request->SUBPA_QTY;
        $sub_total = $new_quantity - $old_quantity;

        if ($pool_product["POPRD_QTY_LEFT"] + $old_quantity < $new_quantity) {
            return response()->json([
                'message' => "Sub Packaging quantity exceed pool product quantity"
            ],500);
        }
        else {
            if ($sub_total > 0) {
                $total = $pool_product["POPRD_QTY_LEFT"] - $sub_total;
            }
            else {
                $total = $pool_product["POPRD_QTY_LEFT"] + abs($sub_total);
            }
            $update_batch = std_update([
                "table_name" => "POPRD",
                "where" => ["POPRD_CODE" => $request->SUBPA_POPRD_CODE],
                "data" => [
                    "POPRD_QTY_LEFT" => $total
                ]
            ]);
        }

        $master_plant = get_master_plant("*", [
            [
                "field_name" => "MAPLA_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_MAPLA_CODE
            ],
        ],true);

        $data = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_CODE,
            ],
        ],true);

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
                'message' => "There are duplicated users, please check your user packaging"
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
                $staff_packaging[] = [
                    "STBPA_POPRD_CODE" => $request->SUBPA_POPRD_CODE,
                    "STBPA_SUBPA_CODE" => $request->SUBPA_CODE,
                    "STBPA_SUBPA_TEXT" => $request->SUBPA_TEXT,
                    "STBPA_SUBPA_STATUS" => "1",
                    "STBPA_SUBPA_START_TIMESTAMP" => $data["SUBPA_START_TIMESTAMP"],
                    "STBPA_SUBPA_END_TIMESTAMP" => $data["SUBPA_END_TIMESTAMP"],
                    "STBPA_EMP_CODE" => $old_employee[$i]["MAEMP_CODE"],
                    "STBPA_EMP_TEXT" => $old_employee[$i]["MAEMP_TEXT"],
                    "STBPA_CREATED_BY" => session("user_code"),
                    "STBPA_CREATED_TEXT" => session("user_name"),
                    "STBPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }

        if (isset($request->STAFF)) {
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
                    "STBPA_SUBPA_CODE" => $request->SUBPA_CODE,
                    "STBPA_SUBPA_TEXT" => $request->SUBPA_TEXT,
                    "STBPA_SUBPA_STATUS" => "1",
                    "STBPA_SUBPA_START_TIMESTAMP" => $data["SUBPA_START_TIMESTAMP"],
                    "STBPA_SUBPA_END_TIMESTAMP" => $data["SUBPA_END_TIMESTAMP"],
                    "STBPA_EMP_CODE" => $employee[$i]["MAEMP_CODE"],
                    "STBPA_EMP_TEXT" => $employee[$i]["MAEMP_TEXT"],
                    "STBPA_CREATED_BY" => session("user_code"),
                    "STBPA_CREATED_TEXT" => session("user_name"),
                    "STBPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }

        $update_data = [
            "SUBPA_TEXT" => $request->SUBPA_TEXT,
            "SUBPA_POPRD_CODE" => $request->SUBPA_POPRD_CODE,
            "SUBPA_MAPLA_CODE" => $request->SUBPA_MAPLA_CODE,
            "SUBPA_MAPLA_TEXT" => $master_plant["MAPLA_TEXT"],
            "SUBPA_QTY" => $request->SUBPA_QTY,
            "SUBPA_DATE" => $request->SUBPA_DATE,
            "SUBPA_STATUS" => 1,
            "SUBPA_UPDATED_BY" => session("user_code"),
            "SUBPA_UPDATED_TEXT" => session("user_name"),
            "SUBPA_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $request->SUBPA_CODE],
            "data" => $update_data
        ]);

        std_delete([
            "table_name" => "STBPA",
            "where" => ["STBPA_SUBPA_CODE" => $request->SUBPA_CODE],
        ]);

        $insert_staff = std_insert([
            "table_name" => "STBPA",
            "data" => $staff_packaging
        ]);

        if ($update_res == false || $insert_staff == false) {
            return response()->json([
                'message' => "There was an error saving data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
