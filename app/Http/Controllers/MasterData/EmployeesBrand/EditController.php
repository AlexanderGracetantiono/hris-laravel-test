<?php

namespace App\Http\Controllers\MasterData\EmployeesBrand;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index(Request $request)
    {
        $brand = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand_code
            ],
        ],true);

        $employee_data = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_ID",
                    "operator" => "=",
                    "value" => $request->maemp_id
                ],
            ],
            "first_row" => true,
        ]);

        if ($employee_data["MAEMP_ROLE"] != 3) {
            abort(404);
        }

        $code = std_get([
            "table_name" => "MACOP",
            "select" => "*",
        ]);
        
        return view('master_data/brand_employees/edit', [
            "employee_data" => $employee_data,
            "brand" => $brand,
            "code" => $code,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "role_select2" => "required",
            "employee_phone_code" => "required|exists:MACOP,MACOP_CODE",
            "employee_phone" => "required",
            "employee_status_is_active" => "required",
            "employee_username" => "required",
            "employee_email" => "required|email",
        ]);
        $attributeNames = [
            "role_select2" => "User Role",
            "employee_phone_code" => "Phone Country Code",
            "employee_phone" => "Phone",
            "employee_status_is_active" => "User Status",
            "employee_username" => "Account Name",
            "employee_email" => "Email",
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

        $check_email = get_master_employee("*",[
            [
                "field_name" => "MAEMP_IS_DELETED",
                "operator" => "!=",
                "value" => 1
            ],
            [
                "field_name" => "MAEMP_EMAIL",
                "operator" => "=",
                "value" => $request->employee_email
            ],
            [
                "field_name" => "MAEMP_ID",
                "operator" => "!=",
                "value" => $request->employee_id
            ],
        ],true);

        if ($check_email != null) {
            return response()->json([
                'message' => "Email already exists"
            ],400);
        }

        $check_account_name = get_master_employee("*",[
            [
                "field_name" => "MAEMP_IS_DELETED",
                "operator" => "!=",
                "value" => 1
            ],
            [
                "field_name" => "MAEMP_USER_NAME",
                "operator" => "=",
                "value" => strtolower(str_replace(" ","_",$request->employee_username))
            ],
            [
                "field_name" => "MAEMP_ID",
                "operator" => "!=",
                "value" => $request->employee_id
            ],
        ],true);

        if ($check_account_name != null) {
            return response()->json([
                'message' => "Account name already exists"
            ],400);
        }

        $check_phone_number = get_master_employee("*",[
            [
                "field_name" => "MAEMP_IS_DELETED",
                "operator" => "!=",
                "value" => 1
            ],
            [
                "field_name" => "MAEMP_MACOP_CODE",
                "operator" => "=",
                "value" => $request->employee_phone_code
            ],
            [
                "field_name" => "MAEMP_PHONE_NUMBER",
                "operator" => "=",
                "value" => $request->employee_phone
            ],
            [
                "field_name" => "MAEMP_ID",
                "operator" => "!=",
                "value" => $request->employee_id
            ],
        ],true);

        if ($check_phone_number != null) {
            return response()->json([
                'message' => "Phone number already exists"
            ],400);
        }

        $old_email = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_ID",
                    "operator" => "=",
                    "value" => $request->employee_id
                ],
            ],
            "first_row" => true,
        ]);
        if ($old_email["MAEMP_EMAIL"] != $request->employee_email) {
            $name = $old_email["MAEMP_TEXT"];
            $old_email = $old_email["MAEMP_EMAIL"];
            $new_email = $request->employee_email;
            try {
                Mail::send("mail.confirmation_email", [], function ($message) use ($name, $old_email) {
                    $message
                        ->to($old_email, $name)
                        ->subject("Email Change Notification for CekOri User ".$name);
                    $message->from("admin@cekori.com", "Email Change Notification for CekOri User ".$name);
                });

                Mail::send("mail.confirmation_email", [], function ($message) use ($name, $new_email) {
                    $message
                        ->to($new_email, $name)
                        ->subject("Email Change Notification for CekOri User ".$name);
                    $message->from("admin@cekori.com", "Email Change Notification for CekOri User ".$name);
                });
    
            } catch (\Exception $e) {
                Log::critical("Error when send email via sendinblue");
                return response()->json([
                    "message" => "Error when send email via sendinblue"
                ], 400);
            }
        }

        $update_data = [
            "MAEMP_EMAIL" =>$request->employee_email,
            "MAEMP_MACOP_CODE" =>$request->employee_phone_code,
            "MAEMP_PHONE_NUMBER" =>$request->employee_phone,
            "MAEMP_USER_NAME" => strtolower(str_replace(" ","_",$request->employee_username)),
            "MAEMP_ROLE" => $request->role_select2,
            "MAEMP_STATUS" => $request->employee_status_is_active,
            "MAEMP_UPDATED_BY" => session("user_id"),
            "MAEMP_UPDATED_TEXT" => session("user_name"),
            "MAEMP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

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
