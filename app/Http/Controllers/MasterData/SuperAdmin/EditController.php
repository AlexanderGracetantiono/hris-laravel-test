<?php

namespace App\Http\Controllers\MasterData\SuperAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        if ($request->maadmin_username != NULL) {
            $maadmin_data = std_get([
                "select" => ["*"],
                "table_name" => "maadmins",
                "where" => [
                    [
                        "field_name" => "maadmin_username",
                        "operator" => "=",
                        "value" => $request->maadmin_username
                    ]
                ],
                "first_row" => true,
            ]);
            if ($maadmin_data == NULL) {
                abort(404);
            }
            return view('master_data/companies/super_admin/edit', ['maadmin_data' => $maadmin_data]);
        }
        else{
            abort(404);
        }
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "maadmin_username" => "required",
            "maadmin_real_name" => "required|max:255",
            "maadmin_email" => "required|max:255",
            "maadmin_phone" => "required|max:15",
            "maadmin_sex" => "required|in:1,2",
        ]);

        $attributeNames = [
            "maadmin_username" => "Username",
            "maadmin_real_name" => "Real Name",
            "maadmin_email" => "Email",
            "maadmin_phone" => "Phone Number",
            "maadmin_sex" => "Gender",
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
            "maadmin_real_name" => $request->maadmin_real_name,
            "maadmin_email" => $request->maadmin_email,
            "maadmin_phone" => $request->maadmin_phone,
            "maadmin_sex" => $request->maadmin_sex,
            "maadmin_status" => $request->maadmin_status,
            "maadmin_updated_by" => session("user_id"),
            "maadmin_updated_time" => date("H:i:s"),
            "maadmin_updated_date" => date("Y-m-d")
        ];

        $update_res = std_update([
            "table_name" => "maadmins",
            "where" => ["maadmin_username" => $request->maadmin_username],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
