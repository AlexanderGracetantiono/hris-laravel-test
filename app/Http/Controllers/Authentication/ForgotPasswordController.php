<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class ForgotPasswordController extends Controller
{
    public function view(Request $request)
    {
        return view("authentication.forgotPasswordFormView");
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

    public function send_email(Request $request)
    {
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
                    "field_name" => "MAEMP_EMAIL",
                    "operator" => "=",
                    "value" => $request->account_name_email,
                ],
            ],
            "first_row" => true
        ]);
        if ($user_data == null || $user_data["MAEMP_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "You don't have any account with this email"
            ], 404);
        }
        if ($user_data["MAEMP_BLOCKED_STATUS"] == 1) {
            return response()->json([
                'message' => "Your account is blocked, please contact admin"
            ], 404);
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

        $parameter = [
            'id' => $user_data["MAEMP_CODE"],
            'name' => $user_data["MAEMP_TEXT"]
        ];
        $parameter = Crypt::encrypt($parameter);
        // BEGIN::SEND EMAIL FOR FORGOT PASSWORD
        //LARAVEL send mail sample
        $to_name = $user_data["MAEMP_TEXT"];
        $to_email = $user_data["MAEMP_EMAIL"];
        $data = array(
            "name" => $user_data["MAEMP_TEXT"],
            "link" => url('/') . "/authentication/forgotPasswordView?id=" . $parameter,
            "user_account" => $user_data["MAEMP_USER_NAME"],
            "brand_logo"=>$brand_image,
        );

        try {
            Mail::send("mail.reset_password_email", ['data' => $data], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Create Or Reset Password for CekOri User ".$to_name);
                $message->from("admin@cekori.com", "Create Or Reset Password for CekOri User ".$to_name);
            });
            $insert_lgema_data = [
                "LGEMA_EMP_CODE" => $user_data["MAEMP_CODE"],
                "LGEMA_EMP_NAME" => $user_data["MAEMP_TEXT"],
                "LGEMA_EMP_EMAIL" => $user_data["MAEMP_EMAIL"],
                "LGEMA_COMP_CODE" => $user_data["MAEMP_MCOMP_CODE"],
                "LGEMA_COMP_NAME" => $user_data["MAEMP_MCOMP_NAME"],
                "LGEMA_STATUS" => 0,
                "LGEMA_CREATED_BY" =>  $user_data["MAEMP_CODE"],
                "LGEMA_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
                "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgema = std_insert([
                "table_name" => "LGEMA",
                "data" => $insert_lgema_data
            ]);
        } catch (\Exception $e) {
            Log::critical("Error when send email via sendinblue");
            return response()->json([
                "message" => "Error when send email via sendinblue"
            ], 400);
        }
        // END::SEND EMAIL FOR FORGOT PASSWORD
        $limit_forgot_password = 180;
        $time = new DateTime();
        $time->add(new DateInterval('PT' . $limit_forgot_password . 'M'));
        $stamp = $time->format('Y-m-d H:i:s');

        $insert_trfpw_data = [
            "TRFPW_EMP_CODE" => $user_data["MAEMP_CODE"],
            "TRFPW_EMP_NAME" => $user_data["MAEMP_TEXT"],
            "TRFPW_EMP_EMAIL" => $user_data["MAEMP_EMAIL"],
            "TRFPW_COMP_CODE" => $user_data["MAEMP_MCOMP_CODE"],
            "TRFPW_COMP_NAME" => $user_data["MAEMP_MCOMP_NAME"],
            "TRFPW_LIMIT_TIMESTAMP" =>  $stamp,
            "TRFPW_STATUS" =>  1,
            "TRFPW_CREATED_BY" =>  $user_data["MAEMP_CODE"],
            "TRFPW_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
            "TRFPW_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_trfpw = std_insert([
            "table_name" => "TRFPW",
            "data" => $insert_trfpw_data
        ]);
        if ($insert_trfpw == false) {
            return response()->json([
                'message' => "There is an error when request reset password"
            ], 500);
        }
        return response()->json([
            'message' => "OK"
        ], 200);
    }

    // FORGOT PASSWORD WEB VIEW
    public function validate_input_save($request)
    {
        $validate = Validator::make($request->all(), [
            "password_new" => "required",
            "password_new_confirmation" => "required|same:password_new",
        ]);

        $attributeNames = [
            "password_new" => "New password",
            "password_new_confirmation" => "New password confirmation"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }
    public function forgot_password_view(Request $request)
    {

        $decryptForgotPassData = Crypt::decrypt($request->id);
        // return response()->json($decryptForgotPassData['id'], 200);

        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "=",
                    "value" =>  $decryptForgotPassData['id'],
                ]
            ],
            "first_row" => true
        ]);
        $update_trfpw = std_update([
            "table_name" => "TRFPW",
            "where" => ["TRFPW_EMP_CODE" => $decryptForgotPassData['id']],
            "data" => [
                "TRFPW_STATUS" => 3,
                "TRFPW_UPDATED_BY" => $user_data["MAEMP_USER_NAME"],
                "TRFPW_UPDATED_TEXT" => $user_data["MAEMP_TEXT"],
                "TRFPW_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ],
        ]);

        if ($update_trfpw == false) {
            return response()->json([
                'message' => "There is an error when request reset password"
            ], 500);
        };
        if ($user_data != NULL) {
            return view("authentication.forgotPasswordView", [
                "MAEMP_CODE" => $decryptForgotPassData['id']
            ]);
        };
        abort(404);
    }

    public function forgot_password_save(Request $request)
    {
        $validation_res = $this->validate_input_save($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ], 400);
        }
        $user_data = std_get([
            "select" => ["MAEMP_EMAIL", "MAEMP_TEXT", "MAEMP_USER_NAME", "MAEMP_BLOCKED_STATUS", "MAEMP_IS_DELETED","MAEMP_ACTIVATION_STATUS"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "=",
                    "value" =>  $request->MAEMP_CODE,
                ]
            ],
            "first_row" => true
        ]);

        if ($user_data["MAEMP_ACTIVATION_STATUS"] != 1) {
            return view('error_custom_view',[
                "message" => "Please activate your account first !"
            ]);
        }

        $get_trfpw = std_get([
            "select" => ["*"],
            "table_name" => "TRFPW",
            "where" => [
                [
                    "field_name" => "TRFPW_EMP_CODE",
                    "operator" => "=",
                    "value" =>  $request->MAEMP_CODE,
                ]
            ],
            "order_by" => [
                [
                    "field" => "TRFPW_CREATED_TIMESTAMP",
                    "type" => "DESC",
                ]
            ],
            "first_row" => true
        ]);

        if ($user_data == null) {
            return view('error_custom_view',[
                "message" => "Invalid account !"
            ]);
        }
        if ($get_trfpw == null) {
            return view('error_custom_view',[
                "message" => "There are no request password for this account !"
            ]);
        }
        if ($get_trfpw["TRFPW_LIMIT_TIMESTAMP"] < date("Y-m-d H:i:s")) {
            $update_trfpw = std_update([
                "table_name" => "TRFPW",
                "where" => ["TRFPW_EMP_CODE" => $request->MAEMP_CODE],
                "data" => [
                    "TRFPW_STATUS" => 3,
                    "TRFPW_UPDATED_BY" => $user_data["MAEMP_USER_NAME"],
                    "TRFPW_UPDATED_TEXT" => $user_data["MAEMP_TEXT"],
                    "TRFPW_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ],
            ]);
            return view('error_custom_view',[
                "message" => "Reset password time expired, please re-do forget password !"
            ]);
        }

        $update_res = std_update([
            "table_name" => "MAEMP",
            "where" => ["MAEMP_CODE" => $request->MAEMP_CODE],
            "data" => [
                "MAEMP_PASSWORD" => Hash::make($request->password_new_confirmation),
                "MAEMP_CREATED_BY" => $user_data["MAEMP_USER_NAME"],
                "MAEMP_CREATED_TEXT" => $user_data["MAEMP_TEXT"],
                "MAEMP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ],
        ]);

        return view('success_password_view');
    }
}
