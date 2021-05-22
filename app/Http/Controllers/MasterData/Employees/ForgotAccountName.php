<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class ForgotAccountName extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5,8]);
    }
    
    public function index(Request $request)
    {
        $employee_data = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_ID",
                    "operator" => "=",
                    "value" => $request->maemp_id
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
            ],
            "first_row" => true,
        ]);
        if ($employee_data == NULL) {
            abort(404);
        }
        return view('master_data/master_employees/forgot_account', [
            "employee_data" => $employee_data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "account_name" => "required|unique:MAEMP,MAEMP_TEXT"
        ]);
        $attributeNames = [
            "account_name" => "Username"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
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
            ], 400);
        }
        $employee_data = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "=",
                    "value" => $request->employee_code
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
            ],
            "first_row" => true,
        ]);
        $employee_log_trfan = std_get([
            "select" => "*",
            "table_name" => "TRFAN",
            "where" => [
                [
                    "field_name" => "TRFAN_EMP_CODE",
                    "operator" => "=",
                    "value" => $request->employee_code
                ],
            ],
            "first_row" => true,
        ]);
        $update_data = [
            "MAEMP_USER_NAME" =>$request->account_name,
            "MAEMP_UPDATED_BY" => session("user_id"),
            "MAEMP_UPDATED_TEXT" => session("user_name"),
            "MAEMP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lgema_data = [
            "LGEMA_EMP_CODE" =>$request->employee_code,
            "LGEMA_EMP_NAME" =>$request->account_name,
            "LGEMA_EMP_EMAIL" =>$employee_data["MAEMP_EMAIL"],
            "LGEMA_COMP_CODE" =>$employee_data["MAEMP_MCOMP_CODE"],
            "LGEMA_COMP_NAME" =>$employee_data["MAEMP_MCOMP_NAME"],
            "LGEMA_STATUS" =>0,
            "LGEMA_CREATED_BY" => session("user_id"),
            "LGEMA_CREATED_TEXT" => session("user_name"),
            "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lgema = std_insert([
            "table_name" => "LGEMA",
            "data" => $insert_lgema_data
        ]);
        if($employee_log_trfan==null){
            $insert_trfan_data = [
                "TRFAN_EMP_CODE" =>$request->employee_code,
                "TRFAN_EMP_NAME" =>$request->account_name,
                "TRFAN_EMP_EMAIL" =>$employee_data["MAEMP_EMAIL"],
                "TRFAN_COMP_CODE" =>$employee_data["MAEMP_MCOMP_CODE"],
                "TRFAN_COMP_NAME" =>$employee_data["MAEMP_MCOMP_NAME"],
                "TRFAN_CREATED_BY" => session("user_id"),
                "TRFAN_CREATED_TEXT" => session("user_name"),
                "TRFAN_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_trfan = std_insert([
                "table_name" => "TRFAN",
                "data" => $insert_trfan_data
            ]);
        }else{
            $update_trfan_data = [
                "TRFAN_EMP_NAME" =>$request->account_name,
                "TRFAN_EMP_EMAIL" =>$employee_data["MAEMP_EMAIL"],
                "TRFAN_COMP_CODE" =>$employee_data["MAEMP_MCOMP_CODE"],
                "TRFAN_COMP_NAME" =>$employee_data["MAEMP_MCOMP_NAME"],
                "TRFAN_UPDATED_BY" => session("user_id"),
                "TRFAN_UPDATED_TEXT" => session("user_name"),
                "TRFAN_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $update_trfan = std_update([
                "table_name" => "TRFAN",
                "where" => ["TRFAN_EMP_CODE" => $request->employee_code],
                "data" => $update_trfan_data
            ]);
        }

        $update_res = std_update([
            "table_name" => "MAEMP",
            "where" => ["MAEMP_ID" => $request->employee_id],
            "data" => $update_data
        ]);
      

        if ($update_res === false) {
            return response()->json([
                'message' => "Something wrong when updating data, please try again"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }
}
