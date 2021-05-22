<?php

namespace App\Http\Controllers\Application\LegalVersion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $data = get_legal_version("*",[
            [
                "field_name" => "MALVR_ID",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        return view('application/legal_version/edit',[
            "data" => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MALVR_PRIVACY_POLICY_VERSION" => "required",
            "MALVR_TERM_SERVICE_VERSION" => "required",
        ]);

        $attributeNames = [
            "MALVR_PRIVACY_POLICY_VERSION" => "Privacy Policy Version",
            "MALVR_TERM_SERVICE_VERSION" => "Term Sercives Version",
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
            "MALVR_PRIVACY_POLICY_VERSION" => $request->MALVR_PRIVACY_POLICY_VERSION,
            "MALVR_TERM_SERVICE_VERSION" => $request->MALVR_TERM_SERVICE_VERSION,
            "MALVR_UPDATED_BY" => session("user_id"),
            "MALVR_UPDATED_TEXT" => session("user_name"),
            "MALVR_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "MALVR",
            "data" => $update_data,
            "where" => ["MALVR_ID" => $request->MALVR_ID]
        ]);

        if ($update_res != true) {
            return response()->json([
                'message' => "There is something wrong when updating data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
