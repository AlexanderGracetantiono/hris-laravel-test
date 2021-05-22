<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class UnblockController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5,8]);
    }
    
    public function view(Request $request)
    {
        return view("authentication.unblockAccountFormView");
    }
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "account_name_email" => "required|max:255|email",
        ]);

        $attributeNames = [
            "account_name_email" => "Email address",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }
    public function index(Request $request)
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
                    "field_name" => "MAEMP_EMAIL",
                    "operator" => "=",
                    "value" => $request->account_name_email,
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
            ],
            "first_row" => true,
        ]);
        $employeeRole = $employee_data["MAEMP_ROLE"];
        $send_email_role = 0;
        switch ($employeeRole) {
            case 1:
                //CekOri adm to CekOri adm
                $send_email_role = 1;
                break;
            case 2:
                //QRapp to CekOri adm
                $send_email_role = 1;
                break;
            case 3:
                //Vendor adm to cekoriadm
                $send_email_role = 1;
                break;
            case 4:
                //prod adm to vendor adm
                $send_email_role = 3;
                break;
            case 5:
                //pack adm to vendor adm
                $send_email_role = 3;
                break;
            case 8:
                //inven adm to vendor adm
                $send_email_role = 3;
                break;
            case 6:
                //prod staff to prod adm
                $send_email_role = 4;
                break;
            case 7:
                //pack staff to pack adm
                $send_email_role = 5;
                break;
            default:
                return response()->json([
                    'message' => "Something wrong when get employee rol data, please try again"
                ], 500);
                break;
        };
        $employee_send_to_data = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "!=",
                    "value" => $employee_data["MAEMP_CODE"]
                ],
                [
                    "field_name" => "MAEMP_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $employee_data["MAEMP_MCOMP_CODE"]
                ],
                [
                    "field_name" => "MAEMP_ROLE",
                    "operator" => "=",
                    "value" => $send_email_role
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
            ],
        ]);
        // return response()->json($employee_send_to_data, 500);
        $employee_log_trunb = std_get([
            "select" => "*",
            "table_name" => "TRUNB",
            "where" => [
                [
                    "field_name" => "TRUNB_EMP_CODE",
                    "operator" => "=",
                    "value" => $employee_data["MAEMP_CODE"]
                ],
            ],
            "first_row" => true,
        ]);
        // sending email
        try {
            for ($i = 0; $i < count($employee_send_to_data); $i++) {
                $to_name = $employee_send_to_data[$i]["MAEMP_USER_NAME"];
                $to_email = $employee_send_to_data[$i]["MAEMP_EMAIL"];
                $data = array(
                    "name" => $employee_send_to_data[$i]["MAEMP_TEXT"],
                    "username" => $employee_send_to_data[$i]["MAEMP_USER_NAME"],
                    "email" => $employee_send_to_data[$i]["MAEMP_EMAIL"],
                    "body" => "Your account have been unblock, please check the login"
                );
                Mail::send("mail.unblock_info_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                    $message
                        ->to($to_email, $to_name)
                        ->subject("Unblock Account Notification for CekOri User ".$to_name);
                    $message->from("admin@cekori.com", "Unblock Account Notification for CekOri User ".$to_name);
                });
            }
        } catch (\Exception $e) {
            Log::critical("Error when send email via sendinblue");
            return response()->json("Error when send email via sendinblue", 400);
        }
        $unban_res = std_update([
            "table_name" => "MAEMP",
            "where" => [
                "MAEMP_CODE" =>  $employee_data["MAEMP_CODE"]
            ],
            "data" => [
                "MAEMP_BLOCKED_STATUS" => 0
            ]
        ]);
        if ($unban_res === false) {
            return response()->json([
                'message' => "Something wrong when unban data, please try again"
            ], 500);
        }
        $counter_res = std_update([
            "table_name" => "TRLGN",
            "where" => [
                "TRLGN_EMP_CODE" =>  $employee_data["MAEMP_CODE"]
            ],
            "data" => [
                "TRLGN_COUNTER" => 0
            ]
        ]);
        if ($counter_res === false) {
            return response()->json([
                'message' => "Something wrong when counter data, please try again"
            ], 500);
        }
        $insert_lgema_data = [
            "LGEMA_EMP_CODE" => $employee_data["MAEMP_CODE"],
            "LGEMA_EMP_NAME" => $request->account_name,
            "LGEMA_EMP_EMAIL" => $employee_data["MAEMP_EMAIL"],
            "LGEMA_COMP_CODE" => $employee_data["MAEMP_MCOMP_CODE"],
            "LGEMA_COMP_NAME" => $employee_data["MAEMP_MCOMP_NAME"],
            "LGEMA_STATUS" => 0,
            "LGEMA_CREATED_BY" => session("user_id"),
            "LGEMA_CREATED_TEXT" => session("user_name"),
            "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lgema = std_insert([
            "table_name" => "LGEMA",
            "data" => $insert_lgema_data
        ]);
        if ($employee_log_trunb == null) {
            $insert_trunb_data = [
                "TRUNB_EMP_CODE" => $employee_data["MAEMP_CODE"],
                "TRUNB_EMP_NAME" => $employee_data["MAEMP_USER_NAME"],
                "TRUNB_EMP_EMAIL" => $employee_data["MAEMP_EMAIL"],
                "TRUNB_COMP_CODE" => $employee_data["MAEMP_MCOMP_CODE"],
                "TRUNB_COMP_NAME" => $employee_data["MAEMP_MCOMP_NAME"],
                "TRUNB_STATUS" => 0,
                "TRUNB_CREATED_BY" => session("user_id"),
                "TRUNB_CREATED_TEXT" => session("user_name"),
                "TRUNB_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_trunb = std_insert([
                "table_name" => "TRUNB",
                "data" => $insert_trunb_data
            ]);
        } else {
            $update_trunb_data = [
                "TRUNB_EMP_NAME" => $employee_data["MAEMP_USER_NAME"],
                "TRUNB_EMP_EMAIL" => $employee_data["MAEMP_EMAIL"],
                "TRUNB_COMP_CODE" => $employee_data["MAEMP_MCOMP_CODE"],
                "TRUNB_COMP_NAME" => $employee_data["MAEMP_MCOMP_NAME"],
                "TRUNB_STATUS" => 0,
                "TRUNB_UPDATED_BY" => session("user_id"),
                "TRUNB_UPDATED_TEXT" => session("user_name"),
                "TRUNB_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $update_trunb = std_update([
                "table_name" => "TRUNB",
                "where" => ["TRUNB_EMP_CODE" => $employee_data["MAEMP_CODE"]],
                "data" => $update_trunb_data
            ]);
        }

        return response()->json([
            'message' => "Account have been unblock"
        ], 200);
    }
}
