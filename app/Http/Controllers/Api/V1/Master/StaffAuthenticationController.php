<?php

namespace App\Http\Controllers\Api\V1\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StaffAuthenticationController extends Controller
{
    public function login(Request $request){
        $validate = Validator::make($request->all(), [
            "username" => "required|max:255",
            "password" => "required|max:255"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 

        $check_exists_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_USER_NAME",
                "operator" => "=",
                "value" => $request->username
            ],
        ],true);

        
        if ($check_exists_employee == null) {
            return response()->json([
                "message" => "Incorrect username",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 400);
        }

        if ($check_exists_employee["MAEMP_ACTIVATION_STATUS"] != 1) {
            return response()->json([
                'message' => "Please Activate your account!",
                "data" => $request->all(),
                "err_code" => "E5",
            ], 400);
        }

        if ($check_exists_employee["MAEMP_STATUS"] != 1) {
            return response()->json([
                'message' => "Account Inactive!",
                "data" => $request->all(),
                "err_code" => "E6",
            ], 400);
        }

        $check_password = Hash::check($request->password, $check_exists_employee["MAEMP_PASSWORD"]);
        if ($check_password == false) {
            return response()->json([
                "message" => "Incorrect password",
                "data" => $request->all(),
                "err_code" => "E3",
            ], 400);
        }

        if ($check_exists_employee["MAEMP_ROLE"] != "6" && $check_exists_employee["MAEMP_ROLE"] != "7") {
            return response()->json([
                "message" => "This account do not have credential to access this site",
                "data" => $request->all(),
                "err_code" => "E4",
            ], 400);
        }

        $token = Str::random(60);
        
        $update_token = std_update([
            "table_name" => "MAEMP",
            "where" => ["MAEMP_CODE" => $check_exists_employee["MAEMP_CODE"]],
            "data" => [
                "MAEMP_TOKEN" => $token,
            ]
        ]);
        if ($update_token != true) {
            return response()->json([
                "message" => "Error on update token",
                "data" => $request->all(),
            ], 500);
        }

        $brand_data = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $check_exists_employee["MAEMP_MBRAN_CODE"],
            ]
        ],true);

        if ($brand_data == null) {
            $brand_type = 0;
        } else {
            $brand_type = $brand_data["MBRAN_TYPE"];
        }

        $check_exists_employee["MAEMP_TOKEN"] = $token;
        $check_exists_employee["BRAND_TYPE"] = $brand_type;
        $send_otp_email = $this->send_otp_email($check_exists_employee);
        return response()->json($check_exists_employee, 200);

    }
    public function check_activity(Request $request){
        $validate = Validator::make($request->all(), [
            "username" => "required|max:255",
            "user_token" => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 

        $check_exists_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_USER_NAME",
                "operator" => "=",
                "value" => $request->username
            ],
        ],true);

        
        if ($check_exists_employee == null) {
            return response()->json([
                "message" => "Account is not available",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_TOKEN"] ==null) {
            return response()->json([
                'message' => "Account Is Not Login!",
                "data" => $request->all(),
                "err_code" => "E6",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_TOKEN"] != $request->user_token) {
            return response()->json([
                "message" => "Account is login on another device",
                "data" => $request->all(),
                "err_code" => "E7",
            ], 400);
        }

        if ($check_exists_employee["MAEMP_ACTIVATION_STATUS"] != 1) {
            return response()->json([
                'message' => "Account Inactive",
                "data" => $request->all(),
                "err_code" => "E3",
            ], 400);
        }

        if ($check_exists_employee["MAEMP_STATUS"] != 1) {
            return response()->json([
                'message' => "Account Inactive!",
                "data" => $request->all(),
                "err_code" => "E3",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "Account Is Deleted!",
                "data" => $request->all(),
                "err_code" => "E4",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_BLOCKED_STATUS"] == 1) {
            return response()->json([
                'message' => "Account Is Blocked!",
                "data" => $request->all(),
                "err_code" => "E5",
            ], 400);
        }
        $insert_log_login_data = [
            "LGLGN_EMP_CODE" =>$check_exists_employee["MAEMP_CODE"],
            "LGLGN_EMP_NAME" =>$check_exists_employee["MAEMP_TEXT"],
            "LGLGN_EMP_EMAIL" =>$check_exists_employee["MAEMP_EMAIL"],
            "LGLGN_COMP_CODE" =>$check_exists_employee["MAEMP_MCOMP_CODE"],
            "LGLGN_COMP_NAME" =>$check_exists_employee["MAEMP_MCOMP_NAME"],
            "LGLGN_BRAN_CODE" =>$check_exists_employee["MAEMP_MBRAN_CODE"],
            "LGLGN_BRAN_NAME" =>$check_exists_employee["MAEMP_MBRAN_NAME"],
            "LGLGN_LOGIN_STATUS" =>1,
            "LGLGN_LAST_ACTIVITY_TIMESTAMP" => date("Y-m-d H:i:s"),
            "LGLGN_CREATED_BY" => $check_exists_employee["MAEMP_CODE"],
            "LGLGN_CREATED_TEXT" => $check_exists_employee["MAEMP_TEXT"],
            "LGLGN_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lglgn = std_insert([
            "table_name" => "LGLGN",
            "data" => $insert_log_login_data
        ]);
        return response()->json([
            'message' => "Account Activity Approved!",
            "data" => $request->all(),
        ], 200);

    }
    public function resend_otp(Request $request){
        $validate = Validator::make($request->all(), [
            "email" => "required|max:255",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 

        $check_exists_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_EMAIL",
                "operator" => "=",
                "value" => $request->email
            ],
        ],true);
        if ($check_exists_employee == null) {
            return response()->json([
                "message" => "Incorrect Email",
                "data" => $request->all(),
                "err_code" => "E4",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_ACTIVATION_STATUS"] != 1) {
            return response()->json([
                'message' => "Please Activate your account!",
                "data" => $request->all(),
                "err_code" => "E5",
            ], 400);
        }
        if ($check_exists_employee["MAEMP_IS_DELETED"] != 0) {
            return response()->json([
                'message' => "You do not have the access to this site!",
                "data" => $request->all(),
                "err_code" => "E6",
            ], 400);
        }
        
        $send_otp_email = $this->send_otp_email($check_exists_employee);
        return response()->json($check_exists_employee, 200);

    }

    public function check_token(Request $request){
        $validate = Validator::make($request->all(), [
            "employee_code" => "required|exists:MAEMP,MAEMP_CODE",
            "token" => "required"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        }

        $check_token_exists = get_master_employee("*",[
            [
                "field_name" => "MAEMP_TOKEN",
                "operator" => "=",
                "value" => $request->token,
            ],
            [
                "field_name" => "MAEMP_CODE",
                "operator" => "=",
                "value" => $request->employee_code,
            ],
        ],true);

        if ($check_token_exists == null) {
            return response()->json([
                "message" => "Token not found",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 400);
        }
        else {
            return response()->json([
                "message" => "Token Exists",
                "data" => $request->all(),
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "employee_code" => "required|exists:MAEMP,MAEMP_CODE",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        }

        $update_token = std_update([
            "table_name" => "MAEMP",
            "where" => ["MAEMP_CODE" => $request->employee_code],
            "data" => [
                "MAEMP_TOKEN" => null,
            ]
        ]);
        if ($update_token != true) {
            return response()->json([
                "message" => "Error on update token",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 500);
        }

        return response()->json([
            "message" => "Logout Success",
            "data" => $request->all(),
        ], 200);
    }
    public function send_otp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "otp_code" => "required|max:6",
            "email" => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        }
        $verification_data = std_get([
            "select" => ["*"],
            "table_name" => "LGOTP",
            "where" => [
                [
                    "field_name" => "LGOTP_EMP_EMAIL",
                    "operator" => "=",
                    "value" => $request->email,
                ],
                // [
                //     "field_name" => "LGOTP_OTP_CODE",
                //     "operator" => "=",
                //     "value" => $request->otp_code,
                // ],
                // [
                //     "field_name" => "LGOTP_STATUS",
                //     "operator" => "=",
                //     "value" => 1,
                // ],
            ],
            "order_by" => [
                [
                    "field" => "LGOTP_CREATED_TIMESTAMP",
                    "type" => "DESC"
                ]
            ],
            "first_row" => true
        ]);
        if ($verification_data == NULL) {
            return response()->json([
                'message' => "OTP Request Invalid",
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        }
        if ($verification_data["LGOTP_OTP_CODE"] != $request->otp_code) {
            return response()->json([
                'message' => "OTP Code Invalid",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 400);
        }
        if ($verification_data["LGOTP_STATUS"] != 1) {
            return response()->json([
                'message' => "(OTP Request Invalid) No OTP request by this account",
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        }
        if ($verification_data["LGOTP_EXPIRED_TIMESTAMP"]<date("Y-m-d H:i:s")) {
            return response()->json([
                'message' => "OTP code Expired",
                "otp" => date("Y-m-d H:i:s"),
                "err_code" => "E3",
            ], 400);
        }
        $update_lgotp_data = [
            "LGOTP_STATUS" =>2, //-- 1 idle, 2 verif success, 3 verif otp fail
            "LGOTP_UPDATED_BY" =>$verification_data["LGOTP_EMP_CODE"],
            "LGOTP_UPDATED_TEXT" => $verification_data["LGOTP_EMP_NAME"],
            "LGOTP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        std_update([
			"table_name" => "LGOTP",
			"where" => [
				"LGOTP_ID" => $verification_data["LGOTP_ID"]
			],
			"data" => $update_lgotp_data
		]);
        return response()->json([
            "message" => "Verification OTP Success",
            "data" => $request->all(),
        ], 200);
    }
    public function send_otp_email($user_data)
    {
        //send otp
        $brand_data = std_get([
            "table_name" => "MBRAN",
            "where" => [
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => $user_data["MAEMP_MBRAN_CODE"],
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

        $otp_code = mt_rand(100000, 999999);
        $expired_range_minute = 6;
        $expired_time = new DateTime();
        $expired_time->add(new DateInterval('PT' . $expired_range_minute . 'M'))->format('Y-m-d H:i:s');
        $to_name = $user_data["MAEMP_TEXT"];
        $to_email = $user_data["MAEMP_EMAIL"];
        $data = array(
            "name" => $user_data["MAEMP_TEXT"],
            "username"=>$user_data["MAEMP_USER_NAME"],
            "brand_logo"=>$brand_image,
            "otp_code"=>$otp_code,
        );

        try {
            Mail::send("mail.verification_login_otp_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("One Time Password for Verification Login to ".$to_name);
                $message->from("admin@cekori.com", "One Time Password for CekOri User ".$to_name);
            });
            $insert_lgotp_data = [
                "LGOTP_EMP_CODE" =>$user_data["MAEMP_CODE"],
                "LGOTP_EMP_NAME" =>$user_data["MAEMP_TEXT"],
                "LGOTP_EMP_EMAIL" =>$user_data["MAEMP_EMAIL"],
                "LGOTP_EMP_USERNAME" =>$user_data["MAEMP_USER_NAME"],
                "LGOTP_EMP_PHONE" =>$user_data["MAEMP_PHONE_NUMBER"],
                "LGOTP_ACC_TYPE" =>2,
                "LGOTP_COMP_CODE" =>$user_data["MAEMP_MCOMP_CODE"],
                "LGOTP_COMP_NAME" =>$user_data["MAEMP_MCOMP_NAME"],
                "LGOTP_BRAN_CODE" =>$user_data["MAEMP_MBRAN_CODE"],
                "LGOTP_BRAN_NAME" =>$user_data["MAEMP_MBRAN_NAME"],
                "LGOTP_OTP_CODE" =>$otp_code,
                "LGOTP_SENT_TIMESTAMP" => date("Y-m-d H:i:s"),
                "LGOTP_EXPIRED_TIMESTAMP" => $expired_time,
                "LGOTP_STATUS" =>1,
                "LGOTP_CREATED_BY" => $user_data["MAEMP_CODE"],
                "LGOTP_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
                "LGOTP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgotp = std_insert([
                "table_name" => "LGOTP",
                "data" => $insert_lgotp_data
            ]);
        } catch (\Exception $e) {
            Log::critical("Error when send email via sendinblue");
            return response()->json("Failed send OTP", 400);
        }
        return response()->json([
            'message' => "Success Send OTP"
        ], 200);
    }
}

