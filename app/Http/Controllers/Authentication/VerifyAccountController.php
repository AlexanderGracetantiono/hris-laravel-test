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

class VerifyAccountController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "id" => "required",
        ]);

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
            return view("error_view");
        }

        $data = Crypt::decrypt($request->id);
        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_CODE",
                    "operator" => "=",
                    "value" =>  $data['id'],
                ],
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" =>  "0",
                ],
            ],
            "first_row" => true
        ]);

        if ($user_data == NULL || $user_data["MAEMP_ACTIVATION_STATUS"] == "1") {
            Log::critical("Email verfication reset password : User data not found / already activated");
            return view("error_view");
        }; 

        $update = std_update([
            "table_name" => "MAEMP",
            "where" => [ "MAEMP_CODE" => $data['id']],
            "data" => [
                "MAEMP_ACTIVATION_STATUS" => 1,
                "MAEMP_STATUS" => 1,
            ]
        ]);

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
            Log::critical("Success on send email verfication reset password".$to_name);
        } catch (\Exception $e) {
            Log::critical("Email verfication reset password : ".json_encode($e->getMessage()));
            return view("error_view");
        }

        $limit_forgot_password = 5;
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
        
        return view('success_view');
    }
}
