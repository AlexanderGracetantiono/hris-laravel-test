<?php

namespace App\Http\Controllers\Api\V2\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Mail;

class CustomerAuthenticationController extends Controller
{
    public function send_otp_email(Request $request){
        $validate = Validator::make($request->all(), [
            "user_email" => "required",
            "user_username" => "required",
            "user_phone_number" => "required",
        ]);
        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 
        $otp_code = mt_rand(100000, 999999);
        $expired_range_minute = 6;
        $expired_time = new DateTime();
        $expired_time->add(new DateInterval('PT' . $expired_range_minute . 'M'))->format('Y-m-d H:i:s');
        $to_name = $request->user_username;
        $to_email = $request->user_email;
        $data = array(
            "name" => $request->user_username,
            "otp_code"=>$otp_code,
            "brand_logo"=>null,
            "username"=>null,
        );
        try {
            Mail::send("mail.verification_login_otp_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("One Time Password for Verification to ".$to_name);
                $message->from("admin@cekori.com", "One Time Password for Login CekOri User ".$to_name);
            });
            $insert_lgotp_data = [
                "LGOTP_EMP_NAME" =>$request->user_username,
                "LGOTP_EMP_EMAIL" =>$request->user_email,
                "LGOTP_EMP_USERNAME" =>$request->user_username,
                "LGOTP_EMP_PHONE" =>$request->user_phone_number,
                "LGOTP_ACC_TYPE" =>1,
                "LGOTP_OTP_CODE" =>$otp_code,
                "LGOTP_SENT_TIMESTAMP" => date("Y-m-d H:i:s"),
                "LGOTP_EXPIRED_TIMESTAMP" => $expired_time,
                "LGOTP_STATUS" =>1,
                "LGOTP_CREATED_BY" =>$request->user_username,
                "LGOTP_CREATED_TEXT" => $request->user_username,
                "LGOTP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgotp = std_insert([
                "table_name" => "LGOTP",
                "data" => $insert_lgotp_data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error when send email",
                "err_code" => "E2",
            ], 400);
        }
        return response()->json([
            'message' => "Success Send OTP",
            "data" => $request->all(),
        ], 200);
    }
    
    public function send_otp_account_change_email(Request $request){
        $validate = Validator::make($request->all(), [
            "user_email" => "required",
            "user_username" => "required",
            "user_phone_number" => "required",
        ]);
        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 
        $otp_code = mt_rand(100000, 999999);
        $expired_range_minute = 3;
        $expired_time = new DateTime();
        $expired_time->add(new DateInterval('PT' . $expired_range_minute . 'M'))->format('Y-m-d H:i:s');
        $to_name = $request->user_username;
        $to_email = $request->user_email;
        $data = array(
            "name" => $request->user_username,
            "otp_code"=>$otp_code,
            "brand_logo"=>null,
            "username"=>null,
        );
        try {
            Mail::send("mail.verification_account_change_otp_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("One Time Password for Verification to ".$to_name);
                $message->from("admin@cekori.com", "One Time Password for CekOri User ".$to_name);
            });
            $insert_lgotp_data = [
                "LGOTP_EMP_NAME" =>$request->user_username,
                "LGOTP_EMP_EMAIL" =>$request->user_email,
                "LGOTP_EMP_USERNAME" =>$request->user_username,
                "LGOTP_EMP_PHONE" =>$request->user_phone_number,
                "LGOTP_ACC_TYPE" =>1,
                "LGOTP_OTP_CODE" =>$otp_code,
                "LGOTP_SENT_TIMESTAMP" => date("Y-m-d H:i:s"),
                "LGOTP_EXPIRED_TIMESTAMP" => $expired_time,
                "LGOTP_STATUS" =>1,
                "LGOTP_CREATED_BY" =>$request->user_username,
                "LGOTP_CREATED_TEXT" => $request->user_username,
                "LGOTP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgotp = std_insert([
                "table_name" => "LGOTP",
                "data" => $insert_lgotp_data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error when send email",
                'error' => $e->getMessage(),
                "err_code" => "E2",
            ], 400);
        }
        return response()->json([
            'message' => "Success Send OTP",
            "data" => $request->all(),
        ], 200);
    }

    public function send_otp_code(Request $request)
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
                [
                    "field_name" => "LGOTP_ACC_TYPE",
                    "operator" => "=",
                    "value" => 1,
                ],
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
}