<?php

namespace App\Http\Controllers\MasterData\SuperAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    public function index()
    {
        return view('master_data/companies/super_admin/add');
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "maadmin_username" => "required|max:50|unique:maadmins,maadmin_username",
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

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $insert_res = std_insert([
            "table_name" => "maadmins",
            "data" => [
                "maadmin_username" => strtolower($request->maadmin_username),
                "maadmin_real_name" => $request->maadmin_real_name,
                "maadmin_email" => $request->maadmin_email,
                "maadmin_phone" => $request->maadmin_phone,
                "maadmin_sex" => $request->maadmin_sex,
                "maadmin_role" => "super_admin",
                "maadmin_status" => 1,
                "maadmin_password" => Hash::make("password", [
                    'rounds' => 12,
                ]),
                "maadmin_created_by" => session("user_id"),
                "maadmin_created_time" => date("H:i:s"),
                "maadmin_created_date" => date("Y-m-d"),
                "maadmin_updated_by" => NULL,
                "maadmin_updated_time" => NULL,
                "maadmin_updated_date" => NULL,
            ]
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam menyimpan data, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
