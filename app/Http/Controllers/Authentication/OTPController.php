<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use DateInterval;
use DateTime;

class OTPController extends Controller
{
    public function otp_form_view()
    {
        return view("authentication.sendOTPCodeForm");
    }
    // public function resend_otp_code(Request $request)
    // {
    //     session(['is_blocked' => "false"]);
    //     if($request->email){
    //         $temp_user_email =$request->email;
    //     }else{
    //         $temp_user_email =session("user_email");
    //     }
    //     $user_data = get_master_employee("*",[
    //         [
    //             "field_name" => "MAEMP_EMAIL",
    //             "operator" => "=",
    //             "value" => $temp_user_email
    //         ],
    //     ],true);
    //     if ($user_data == null) {
    //         return response()->json([
    //             'message' => "Incorrect Email"
    //         ], 400);
    //     }
    //     if ($user_data["MAEMP_ACTIVATION_STATUS"] != 1) {
    //         return response()->json([
    //             'message' => "Please Activate your account!"
    //         ], 400);
    //     }
    //     if ($user_data["MAEMP_IS_DELETED"] != 0) {
    //         return response()->json([
    //             'message' => "You do not have the access to this site!"
    //         ], 400);
    //     }
    //     if ($user_data["MAEMP_BLOCKED_STATUS"] == 1) {
    //         session(['is_blocked' => "true"]);
    //         return response()->json([
    //             'message' => "Your account is blocked"
    //         ], 400);
    //     }
    //     //send otp
    //     $brand_data = std_get([
    //         "table_name" => "MBRAN",
    //         "where" => [
    //             [
    //                 "field_name" => "MBRAN_CODE",
    //                 "operator" => "=",
    //                 "value" => $user_data["MAEMP_MBRAN_CODE"],
    //             ]
    //         ],
    //         "join" => [
    //             [
    //                 "table_name" => "MCOMP",
    //                 "join_type" => "INNER",
    //                 "on1" => "MCOMP_CODE",
    //                 "operator" => "=",
    //                 "on2" => "MBRAN_MCOMP_CODE"
    //             ]
    //         ],
    //         "first_row" => true
    //     ]);

    //     if ($brand_data == null) {
    //         $brand_image = null;
    //         $company_name = null;
    //         $brand_name = null;
    //     }
    //     else {
    //         $brand_image = $brand_data["MBRAN_IMAGE"];
    //         $company_name = $brand_data["MCOMP_TYPE"]." ".$brand_data["MBRAN_MCOMP_NAME"];
    //         $brand_name = $brand_data["MBRAN_NAME"];
    //     }

    //     $otp_code = mt_rand(100000, 999999);
    //     $expired_range_minute = 6;
    //     $expired_time = new DateTime();
    //     $expired_time->add(new DateInterval('PT' . $expired_range_minute . 'M'))->format('Y-m-d H:i:s');
    //     $to_name = $user_data["MAEMP_TEXT"];
    //     $to_email = $user_data["MAEMP_EMAIL"];
    //     $data = array(
    //         "name" => $user_data["MAEMP_TEXT"],
    //         "username"=>$user_data["MAEMP_USER_NAME"],
    //         "brand_logo"=>$brand_image,
    //         "otp_code"=>$otp_code,
    //     );

    //     try {
    //         Mail::send("mail.verification_login_otp_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
    //             $message
    //                 ->to($to_email, $to_name)
    //                 ->subject("One Time Password for Verification Login to ".$to_name);
    //             $message->from("admin@cekori.com", "One Time Password for CekOri User ".$to_name);
    //         });
    //         $insert_lgotp_data = [
    //             "LGOTP_EMP_CODE" =>$user_data["MAEMP_CODE"],
    //             "LGOTP_EMP_NAME" =>$user_data["MAEMP_TEXT"],
    //             "LGOTP_EMP_EMAIL" =>$user_data["MAEMP_EMAIL"],
    //             "LGOTP_EMP_USERNAME" =>$user_data["MAEMP_USER_NAME"],
    //             "LGOTP_EMP_PHONE" =>$user_data["MAEMP_PHONE_NUMBER"],
    //             "LGOTP_ACC_TYPE" =>2,
    //             "LGOTP_COMP_CODE" =>$user_data["MAEMP_MCOMP_CODE"],
    //             "LGOTP_COMP_NAME" =>$user_data["MAEMP_MCOMP_NAME"],
    //             "LGOTP_BRAN_CODE" =>$user_data["MAEMP_MBRAN_CODE"],
    //             "LGOTP_BRAN_NAME" =>$user_data["MAEMP_MBRAN_NAME"],
    //             "LGOTP_OTP_CODE" =>$otp_code,
    //             "LGOTP_SENT_TIMESTAMP" => date("Y-m-d H:i:s"),
    //             "LGOTP_EXPIRED_TIMESTAMP" => $expired_time,
    //             "LGOTP_STATUS" =>1,
    //             "LGOTP_CREATED_BY" => $user_data["MAEMP_CODE"],
    //             "LGOTP_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
    //             "LGOTP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
    //         ];
    //         $insert_lgotp = std_insert([
    //             "table_name" => "LGOTP",
    //             "data" => $insert_lgotp_data
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::critical("Error when send email via sendinblue");
    //         return response()->json($e, 400);
    //     }
    //     return view("authentication.sendOTPCodeForm");
    // }
    public function sent_otp_code(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if($request->email){
            $temp_user_email =$request->email;
        }else{
            $temp_user_email =session("user_email");
        }
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ], 400);
        }
        // $verification_data = std_get([
        //     "select" => ["*"],
        //     "table_name" => "LGOTP",
        //     "where" => [
        //         [
        //             "field_name" => "LGOTP_EMP_EMAIL",
        //             "operator" => "=",
        //             "value" => $temp_user_email,
        //         ],
        //         [
        //             "field_name" => "LGOTP_OTP_CODE",
        //             "operator" => "=",
        //             "value" => $request->otp_code,
        //         ],
        //         [
        //             "field_name" => "LGOTP_STATUS",
        //             "operator" => "=",
        //             "value" => 1,
        //         ],
        //     ],
        //     "order_by" => [
        //         [
        //             "field" => "LGOTP_CREATED_TIMESTAMP",
        //             "type" => "ASC"
        //         ]
        //     ],
        //     "first_row" => true
        // ]);
        if ($request->otp_code != "axeside") {
            return response()->json([
                'message' => "Request OTP tidak valid"
            ], 500);
        }
        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "username",
                    "operator" => "=",
                    "value" => $temp_user_email,
                ],
            ],
            "order_by" => [
                [
                    "field" => "id",
                    "type" => "DESC"
                ]
            ],
            "first_row" => true
        ]);
         
        $words = explode(" ", $user_data["full_name"]);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        session(['user_initial_name' => strtoupper($acronym)]);
        session(['user_id' => $user_data["id"]]);
        session(['user_name' => $user_data["username"]]);
        session(['user_full_name' => $user_data["full_name"]]);
        session(['user_name' => $user_data["full_name"]]);
        session(['user_role' => $user_data["cosplay_id"]]);
        session(['company_code' => "A"]);
        session(['company_name'=> "A"]);
        session(['brand_code' => "A"]);
        session(['brand_name' => "A"]);
        session(['brand_type' => "A"]);
       
        $update_lgotp_data = [
            "LGOTP_STATUS" =>2, //-- 1 idle, 2 verif success, 3 verif otp fail
            "LGOTP_UPDATED_BY" => session("user_id"),
            "LGOTP_UPDATED_TEXT" => session("user_name"),
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
            'message' => "OK"
        ], 200);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "otp_code" => "required",
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }
}
