<?php

namespace App\Http\Controllers\MasterData\AdminVendor;
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

            $mavendor_data = std_get([
                "select" => ["mavendor_id","mavendor_code","mavendor_name","macompany_name"],
                "table_name" => "mavendors",
                "where" => [
                    [
                        "field_name" => "mavendor_status",
                        "operator" => "=",
                        "value" => 1,
                    ]
                ],
                "join" => [
                    [
                        "join_type" => "INNER",
                        "table_name" => "macompanies",
                        "on1" => "macompanies.macompany_id",
                        "operator" => "=",
                        "on2" => "mavendors.mavendor_company_id",
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "mavendor_name",
                        "type" => "ASC",
                    ]
                ],
                "multiple_rows" => true,
            ]);
            if ($maadmin_data == NULL) {
                abort(404);
            }
            return view('master_data/companies/admin_vendor/edit', [
                'maadmin_data' => $maadmin_data,
                'mavendor_data' => $mavendor_data,
            ]);
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
            "maadmin_phone" => "required|numeric|max:15",
            "maadmin_sex" => "required|in:1,2",
            "maadmin_mavendor_id" => "required",
        ]);

        $attributeNames = [
            "maadmin_username" => "Username",
            "maadmin_real_name" => "Real Name",
            "maadmin_email" => "Email",
            "maadmin_phone" => "Phone Number",
            "maadmin_sex" => "Gender",
            "maadmin_mavendor_id" => "Vendor",
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

        $get_macompany_id = std_get([
            "select" => ["mavendor_id","mavendor_code","mavendor_name","macompany_id","macompany_code","macompany_name"],
            "table_name" => "mavendors",
            "where" => [
                [
                    "field_name" => "mavendor_id",
                    "operator" => "=",
                    "value" => $request->maadmin_mavendor_id,
                ]
            ],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "macompanies",
                    "on1" => "macompanies.macompany_id",
                    "operator" => "=",
                    "on2" => "mavendors.mavendor_company_id",
                ]
            ],
            "first_row" => true,
        ]);

        $update_data = [
            "maadmin_username" => strtolower($request->maadmin_username),
            "maadmin_real_name" => $request->maadmin_real_name,
            "maadmin_email" => $request->maadmin_email,
            "maadmin_phone" => $request->maadmin_phone,
            "maadmin_sex" => $request->maadmin_sex,
            "maadmin_role" => "admin_vendor",
            "maadmin_status" => $request->maadmin_status,
            "maadmin_mavendor_id" => $request->maadmin_mavendor_id,
            "maadmin_macompany_id" => $get_macompany_id["macompany_id"],
            "maadmin_updated_by" => session("user_id"),
            "maadmin_updated_time" => date("H:i:s"),
            "maadmin_updated_date" => date("Y-m-d"),
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
