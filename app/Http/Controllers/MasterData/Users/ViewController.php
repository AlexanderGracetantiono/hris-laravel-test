<?php

namespace App\Http\Controllers\MasterData\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $user_data = std_get([
            "select" => ["user_code", "user_name","user_email", "user_phone_number","user_profile_picture", "user_role", "user_is_active","user_last_login","user_activation_status"],
            "table_name" => "m_users",
            "order_by" => [
                [
                    "field" => "user_name",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('master_data/users/view', ['user_data' => $user_data]);
    }

    public function detail(Request $request)
    {
        if ($request->user_code != NULL) {
            $user_data = std_get([
                "select" => ["user_code", "user_name","user_email", "user_phone_number","user_profile_picture", "user_biography", "user_role", "user_is_active","user_last_login", "user_last_login_ip_address", "user_created_by", "user_created_by_name", "user_changed_by", "user_changed_by_name", "user_created_time", "user_changed_time"],
                "table_name" => "m_users",
                "where" => [
                    [
                        "field_name" => "user_code",
                        "operator" => "=",
                        "value" => $request->user_code
                    ]
                ],
                "first_row" => true,
            ]);
            return view('master_data/users/detail', ['user_data' => $user_data]);
        }
    }
}
