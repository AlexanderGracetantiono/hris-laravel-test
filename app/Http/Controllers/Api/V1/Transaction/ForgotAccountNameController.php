<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForgotAccountNameController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required|max:255|email",
        ]);

        $attributeNames = [
            "email" => "Email address",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function send_email(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res,
                'data' => $request->all(),
                'err_code' => "E1",
            ], 400);
        }

        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_EMAIL",
                    "operator" => "=",
                    "value" => $request->email,
                ]
            ],
            "first_row" => true
        ]);
        if ($user_data == null || $user_data["MAEMP_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "You don't have any account with this email",
                'data' => $request->all(),
                'err_code' => "E2",
            ], 400);
        }
        if ($user_data["MAEMP_BLOCKED_STATUS"] == 1) {
            return response()->json([
                'message' => "Your account is blocked, please contact admin",
                'data' => $request->all(),
                'err_code' => "E3",
            ], 400);
        }

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

        $to_name = $user_data["MAEMP_TEXT"];
        $to_email = $user_data["MAEMP_EMAIL"];
        $data = array(
            "name" => $user_data["MAEMP_TEXT"],
            "username"=>$user_data["MAEMP_USER_NAME"],
            "brand_logo"=>$brand_image,
        );

        try {
            Mail::send("mail.forgot_account_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Forgot Account Name request for CekOri User ".$to_name);
                $message->from("admin@cekori.com", "Forgot Account Name for CekOri User ".$to_name);
            });
            $insert_lgema_data = [
                "LGEMA_EMP_CODE" =>$user_data["MAEMP_CODE"],
                "LGEMA_EMP_NAME" =>$user_data["MAEMP_TEXT"],
                "LGEMA_EMP_EMAIL" =>$user_data["MAEMP_EMAIL"],
                "LGEMA_COMP_CODE" =>$user_data["MAEMP_MCOMP_CODE"],
                "LGEMA_COMP_NAME" =>$user_data["MAEMP_MCOMP_NAME"],
                "LGEMA_STATUS" =>0,
                "LGEMA_CREATED_BY" => session("user_id"),
                "LGEMA_CREATED_TEXT" => session("user_name"),
                "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgema = std_insert([
                "table_name" => "LGEMA",
                "data" => $insert_lgema_data
            ]);
        } catch (\Exception $e) {
            Log::critical("Error when send email via sendinblue");
            return response()->json("Error when send email via sendinblue", 400);
        }
        // END::SEND EMAIL FOR FORGOT PASSWORD

        return response()->json([
            'message' => "OK"
        ], 200);
    }
}
