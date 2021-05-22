<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5,8]);
    }

    public function index()
    {
        $code = std_get([
            "table_name" => "MACOP",
            "select" => "*",
        ]);
        return view('master_data/master_employees/add', [
            "code" => $code
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "employee_name" => "required",
            "role_select2" => "required",
            "employee_phone_code" => "required|exists:MACOP,MACOP_CODE",
            "employee_phone" => "required|numeric",
            "employee_username" => "required",
            "employee_email" => "required|email",
        ]);

        $attributeNames = [
            "employee_name" => "User Name",
            "role_select2" => "Role",
            "employee_phone_code" => "Phone Company Code",
            "employee_phone" => "Phone Number",
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

    public function save(Request $request)
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
        ],true);

        if ($check_phone_number != null) {
            return response()->json([
                'message' => "Phone number already exists"
            ],400);
        }

        $code = generate_code(session('company_code'),5,"MAEMP");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_data = [
            "MAEMP_CODE" => strtoupper($code["data"]),
            "MAEMP_TEXT" => $request->employee_name,
            "MAEMP_USER_NAME" => strtolower(str_replace(" ","_",$request->employee_username)),
            "MAEMP_MACOP_CODE" => $request->employee_phone_code,
            "MAEMP_PHONE_NUMBER" => $request->employee_phone,
            "MAEMP_EMAIL" => $request->employee_email,
            "MAEMP_MCOMP_CODE" => session("company_code"),
            "MAEMP_MCOMP_NAME" => session("company_name"),
            "MAEMP_MBRAN_CODE" => session("brand_code"),
            "MAEMP_MBRAN_NAME" => session("brand_name"),
            "MAEMP_ROLE" => $request->role_select2,
            "MAEMP_STATUS" => 1,
            "MAEMP_ACTIVATION_STATUS" => 0,
            "MAEMP_CREATED_BY" => session("user_id"),
            "MAEMP_CREATED_TEXT" => session("user_name"),
            "MAEMP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MAEMP",
            "data" => $insert_data
        ]);

        if ($insert_data["MAEMP_ROLE"] == 1) {
            $role = "CekOri Administrator";
        } elseif ($insert_data["MAEMP_ROLE"] == 2) {
            $role = "QR Approver";
        } elseif ($insert_data["MAEMP_ROLE"] == 3) {
            $role = "PIC Brand";
        } elseif ($insert_data["MAEMP_ROLE"] == 4) {
            $role = "Production Administrator";
        } elseif ($insert_data["MAEMP_ROLE"] == 5) {
            $role = "Packaging Administrator";
        } elseif ($insert_data["MAEMP_ROLE"] == 6) {
            $role = "Production Staff";
        } elseif ($insert_data["MAEMP_ROLE"] == 7) {
            $role = "Packaging Staff";
        } elseif ($insert_data["MAEMP_ROLE"] == 8) {
            $role = "Store Administrator";
        } elseif ($insert_data["MAEMP_ROLE"] == 9) {
            $role = "Store Staff";
        }

        $brand_data = std_get([
            "table_name" => "MBRAN",
            "where" => [
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ]
            ],
            "join" => [
                [
                    "table_name" => "MCOMP",
                    "join_type" => "INNER",
                    "on1" => "MCOMP_CODE",
                    "operator" => "=",
                    "on2" => "MBRAN_MCOMP_CODE"
                ]
            ],
            "first_row" => true
        ]);

        if ($brand_data == null) {
            $brand_image = null;
            $company_name = null;
            $brand_name = null;
        }
        else {
            $brand_image = $brand_data["MBRAN_IMAGE"];
            $company_name = $brand_data["MCOMP_TYPE"]." ".$brand_data["MBRAN_MCOMP_NAME"];
            $brand_name = $brand_data["MBRAN_NAME"];
        }

        if ($insert_data["MAEMP_ROLE"] == 1 || $insert_data["MAEMP_ROLE"] == 3) {
            $created_by = "admin@cekori.com";
        } else {
            $created_by = session("user_name");
        }

        $parameter = [
            'id' => strtoupper($code["data"]),
            'name' => $request->employee_name
        ];
        $parameter = Crypt::encrypt($parameter);
        $to_name = $request->employee_name;
        $to_email = $request->employee_email;
        $data =[
            "name" => $request->employee_name,
            "parameter" => $parameter,
            "company" => $company_name,
            "brand" => $brand_name,
            "created_by" => $created_by,
            "user_role" => $role,
            "brand_logo" => $brand_image,
            "user_account" => strtolower(str_replace(" ","_",$request->employee_username)),
        ];

        try {
            Mail::send("mail.account_verification", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Activation account email confirmation for CekOri User ".$to_name);
                $message->from("admin@cekori.com", "Activation account for CekOri User ".$to_name);
            });

            $insert_lgema_data = [
                "LGEMA_EMP_CODE" => strtoupper($code["data"]),
                "LGEMA_EMP_NAME" => $request->employee_name,
                "LGEMA_EMP_EMAIL" => $request->employee_email,
                "LGEMA_COMP_CODE" => session("company_code"),
                "LGEMA_COMP_NAME" => session("company_name"),
                "LGEMA_BRAN_CODE" => session("brand_code"),
                "LGEMA_BRAN_NAME" => session("brand_name"),
                "LGEMA_CREATED_BY" =>  session("user_code"),
                "LGEMA_CREATED_TEXT" => session("user_name"),
                "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgema = std_insert([
                "table_name" => "LGEMA",
                "data" => $insert_lgema_data
            ]);
            Log::critical("Success on send email employee");
        } catch (\Exception $e) {
            Log::critical("Email Employee : ".json_encode($e->getMessage()));
            return response()->json([
                "message" => "Error when send email via sendinblue"
            ], 400);
        }

        if ($insert_res != true) {
            return response()->json([
                'message' => "Something wrong when saving data, please try again"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }
}
