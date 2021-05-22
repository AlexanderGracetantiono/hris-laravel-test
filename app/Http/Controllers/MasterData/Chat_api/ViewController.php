<?php

namespace App\Http\Controllers\MasterData\Chat_api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }

    public function index()
    {
        $data = get_log_chat("*",[
            [
                "field_name" => "is_deleted",
                "operator" => "=",
                "value" => "0"
            ]
        ]);
        return view('master_data/chat_api/view', ["data" => $data]);
    }
}
