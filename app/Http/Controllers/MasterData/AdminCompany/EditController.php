<?php

namespace App\Http\Controllers\MasterData\AdminCompany;
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
            if ($maadmin_data == NULL) {
                abort(404);
            }
            return view('master_data/companies/admin_company/edit', [
                'maadmin_data' => $maadmin_data,
                'macompanies_data' => $macompanies_data,
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
            "maadmin_phone" => "required|max:15|numeric",
            "maadmin_sex" => "required|in:1,2",
            "maadmin_macompany_id" => "required",
            "maadmin_status" => "required",
        ]);

        $attributeNames = [
            "maadmin_username" => "Username",
            "maadmin_real_name" => "Real Name",
            "maadmin_email" => "Email",
            "maadmin_phone" => "Phone Number",
            "maadmin_sex" => "Gender",
            "maadmin_macompany_id" => "Company",
            "maadmin_status" => "Status",
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
            "maadmin_macompany_id" => $request->maadmin_macompany_id,
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
                'message' => "Terjadi kesalahan dalam update data pengguna, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
