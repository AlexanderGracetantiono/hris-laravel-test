<?php

namespace App\Http\Controllers\About\MengapaPerluGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $gnrm_data = std_get([
            "select" => ["*"],
            "table_name" => "mengapa_perlu_gnrm",
            "where" => [
                [
                    "field_name" => "mengapa_perlu_gnrm_id",
                    "operator" => "=",
                    "value" => "1",
                ]
            ],
            "first_row" => true
        ]);
        if ($gnrm_data == NULL) {
            abort(404);
        }
        $gnrm_data['mengapa_perlu_gnrm_meta_keywords'] = json_decode($gnrm_data['mengapa_perlu_gnrm_meta_keywords'],true);
        
        if (is_array($gnrm_data['mengapa_perlu_gnrm_meta_keywords'])) {
            $gnrm_data['mengapa_perlu_gnrm_meta_keywords'] = implode(', ',$gnrm_data['mengapa_perlu_gnrm_meta_keywords']);
        }

        return view('about/mengapa_perlu_gnrm/view', ['gnrm_data' => $gnrm_data]);
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
