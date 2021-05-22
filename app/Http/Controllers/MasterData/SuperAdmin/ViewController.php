<?php

namespace App\Http\Controllers\MasterData\SuperAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $maadmin_data = std_get([
            "select" => ["maadmin_id","maadmin_username","maadmin_email","maadmin_real_name","maadmin_phone","maadmin_status","maadmin_sex"],
            "table_name" => "maadmins",
            "where" => [
                [
                    "field_name" => "maadmin_role",
                    "operator" => "=",
                    "value" => "super_admin",
                ]
            ],
            "order_by" => [
                [
                    "field" => "maadmin_real_name",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('master_data/companies/super_admin/view', ["maadmin_data" => $maadmin_data]);
    }

    public function detail(Request $request)
    {
        if ($request->user_code != NULL) {
            $user_data = std_get([
                "select" => ["backend_user_code", "backend_user_name","backend_user_email", "backend_user_phone_number","backend_user_profile_picture", "backend_user_biography", "backend_user_role", "backend_user_is_active","backend_user_last_login", "backend_user_last_login_ip_address", "backend_user_created_by", "backend_user_created_by_name", "backend_user_changed_by", "backend_user_changed_by_name", "backend_user_created_time", "backend_user_changed_time"],
                "table_name" => "m_backend_users",
                "where" => [
                    [
                        "field_name" => "backend_user_code",
                        "operator" => "=",
                        "value" => $request->user_code
                    ]
                ],
                "first_row" => true,
            ]);
            return view('master_data/backend_users/detail', ['user_data' => $user_data]);
        }
    }
}
