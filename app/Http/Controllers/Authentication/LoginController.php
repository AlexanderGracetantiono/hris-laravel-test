<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use DateInterval;
use DateTime;

class LoginController extends Controller
{
    public function index()
    {
        return view('authentication.login');
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "username" => "required|max:255",
            "password" => "required|max:50"
        ]);

        $attributeNames = [
            "username" => "Account Name",
            "password" => "Password"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function process(Request $request)
    {
        // session(['is_blocked' => "false"]);
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ], 400);
        }

        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_USER_NAME",
                    "operator" => "=",
                    "value" => $request->username,
                ],
            ],
            "order_by" => [
                [
                    "field" => "MAEMP_ID",
                    "type" => "DESC"
                ]
            ],
            "first_row" => true
        ]);

        if ($user_data == NULL) {
            return response()->json([
                'message' => "Incorrect Password and Username"
            ], 500);
        }
        
        if ($user_data["MAEMP_IS_DELETED"] != 0) {
            return response()->json([
                'message' => "Incorrect Password and Username"
            ], 500);
        }

        if (!Hash::check($request->password, $user_data["MAEMP_PASSWORD"])) {
            return response()->json([
                'message' => "Incorrect Password and Username"
            ], 500);
        }
        $words = explode(" ", $user_data["MAEMP_TEXT"]);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        session(['user_initial_name' => strtoupper($acronym)]);
        session(['user_id' => $user_data["MAEMP_ID"]]);
        session(['user_code' => $user_data["MAEMP_CODE"]]);
        session(['user_name' => $user_data["MAEMP_USER_NAME"]]);
        session(['user_full_name' => $user_data["MAEMP_TEXT"]]);
        session(['user_role' => $user_data["MAEMP_ROLE"]]);
        // session(['company_code' => "A"]);
        // session(['company_name'=> "A"]);
        // session(['brand_code' => "A"]);
        // session(['brand_name' => "A"]);
        // session(['brand_type' => "A"]);

        return response()->json([
            'message' => "OK"
        ], 200);
    }
    // public function send_otp_email($user_data)
    // {
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
    //         $insert_lgema_data = [
    //             "LGEMA_EMP_CODE" => $user_data["MAEMP_CODE"],
    //             "LGEMA_EMP_NAME" => $user_data["MAEMP_TEXT"],
    //             "LGEMA_EMP_EMAIL" => $user_data["MAEMP_EMAIL"],
    //             "LGEMA_COMP_CODE" => $user_data["MAEMP_MCOMP_CODE"],
    //             "LGEMA_COMP_NAME" => $user_data["MAEMP_MCOMP_NAME"],
    //             "LGEMA_STATUS" => 4,
    //             "LGEMA_CREATED_BY" =>  $user_data["MAEMP_CODE"],
    //             "LGEMA_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
    //             "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
    //         ];
    //         $insert_lgema = std_insert([
    //             "table_name" => "LGEMA",
    //             "data" => $insert_lgema_data
    //         ]);
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
    //     return response()->json([
    //         'message' => "OK"
    //     ], 200);
    // }
}
