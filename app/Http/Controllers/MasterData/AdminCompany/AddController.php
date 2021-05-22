<?php

namespace App\Http\Controllers\MasterData\AdminCompany;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    //Sample
    public function __construct() {
        check_is_role_allowed([1,2,3]);
    }

    public function index()
    {
        $macompanies_data = std_get([
            "select" => ["*"],
            "table_name" => "macompanies",
            "where" => [
                [
                    "field_name" => "macompany_status",
                    "operator" => "=",
                    "value" => 1,
                ]
            ],
            "order_by" => [
                [
                    "field" => "macompany_name",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('master_data/companies/admin_company/add',["macompanies_data" => $macompanies_data]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "maadmin_username" => "required|max:50|unique:maadmins,maadmin_username",
            "maadmin_real_name" => "required|max:255",
            "maadmin_email" => "required|max:255",
            "maadmin_phone" => "required|max:15|numeric",
            "maadmin_sex" => "required|in:1,2",
            "maadmin_macompany_id" => "required",
        ]);

        $attributeNames = [
            "maadmin_username" => "Username",
            "maadmin_real_name" => "Real Name",
            "maadmin_email" => "Email",
            "maadmin_phone" => "Phone Number",
            "maadmin_sex" => "Gender",
            "maadmin_macompany_id" => "Company",
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
                "maadmin_role" => "admin_company",
                "maadmin_status" => 1,
                "maadmin_password" => Hash::make("password", [
                    'rounds' => 12,
                ]),
                "maadmin_macompany_id" => $request->maadmin_macompany_id,
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
