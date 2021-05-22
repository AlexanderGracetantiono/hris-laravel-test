<?php

namespace App\Http\Controllers\MasterData\Users;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
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
            if ($user_data == NULL) {
                abort(404);
            }
            return view('master_data/users/edit', ['user_data' => $user_data]);
        }
        else{
            abort(404);
        }
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "user_code" => "required|max:255|exists:m_users,user_code",
            "user_role" => "required|in:1,2",
            "user_is_active" => "required|in:0,1"
        ]);

        $attributeNames = [
            "user_name" => "Nama",
            "user_email" => "Email",
            "user_phone_number" => "Nomor Telepon",
            "user_biography" => "Biografi",
            "user_role" => "Role",
            "user_is_active" => "Status",
            "user_profile_picture" => "Profile Picture"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "user_role" => $request->user_role,
            "user_is_active" => $request->user_is_active,
            "user_changed_by" => session("user_code"),
            "user_changed_by_name" => session("user_name"),
            "user_changed_time" => date("Y-m-d H:i:s")
        ];

        $update_res = std_update([
            "table_name" => "m_users",
            "where" => ["user_code" => $request->user_code],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data pengguna, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
