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

class ChatApiController extends Controller
{
    public function send_chat(Request $request){
        $validate = Validator::make($request->all(), [
            "text" => "required|max:100",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1",
            ], 400);
        } 
        $insert_log_login_data = [
            "text" =>$request->text,
            "send_date" => date("Y-m-d H:i:s"),
        ];
        $insert_log_chat = std_insert([
            "table_name" => "CAPI_LOG",
            "data" => $insert_log_login_data
        ]);
        if ($insert_log_chat != true) {
            return response()->json([
                "message" => "Error on chat",
                "data" => $request->all(),
                "err_code" => "E2",
            ], 400);
        }
        return response()->json([
            "message" => "Success",
        ], 200);

    }
    public function get_chat(Request $request){
        $data = get_log_chat("*",
        [
            [
                "field_name" => "is_deleted",
                "operator" => "=",
                "value" => "0"
            ]
            ],false,$request->limit);

        
        return response()->json($data, 200);

    }
   
}

