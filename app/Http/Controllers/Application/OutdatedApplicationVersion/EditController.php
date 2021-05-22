<?php

namespace App\Http\Controllers\Application\OutdatedApplicationVersion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $data = get_master_version("*",[
            [
                "field_name" => "MAVER_ID",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        return view('application/outdated_application_version/edit',[
            "data" => $data
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MAVER_TEXT" => "required",
            "MAVER_APP_VERSION" => "required",
            "MAVER_APP_TYPE" => "required",
            "MAVER_OS_TYPE" => "required",
            "MAVER_IS_PRIORITY" => "required",
        ]);

        $attributeNames = [
            "MAVER_TEXT" => "Version Text",
            "MAVER_APP_VERSION" => "Application Version",
            "MAVER_APP_TYPE" => "Application Type",
            "MAVER_OS_TYPE" => "Operating System",
            "MAVER_IS_PRIORITY" => "Priority",
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
            "MAVER_TEXT" => $request->MAVER_TEXT,
            "MAVER_APP_VERSION" => $request->MAVER_APP_VERSION,
            "MAVER_APP_TYPE" => $request->MAVER_APP_TYPE,
            "MAVER_OS_TYPE" => $request->MAVER_OS_TYPE,
            "MAVER_IS_PRIORITY" => $request->MAVER_IS_PRIORITY,
            "MAVER_NOTES" => $request->MAVER_NOTES,
            "MAVER_CREATED_BY" => session("user_id"),
            "MAVER_CREATED_TEXT" => session("user_name"),
            "MAVER_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "MAVER",
            "data" => $update_data,
            "where" => ["MAVER_ID" => $request->MAVER_ID]
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
