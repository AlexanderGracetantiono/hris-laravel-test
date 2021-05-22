<?php

namespace App\Http\Controllers\Application\OutdatedApplicationVersion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    public function index(Request $request)
    {
        return view('application/outdated_application_version/add');
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MAVER_TEXT" => "required",
            "MAVER_APP_VERSION" => "required",
            "MAVER_APP_TYPE" => "required",
            "MAVER_OS_TYPE" => "required",
            "MAVER_IS_PRIORITY" => "required"
        ]);

        $attributeNames = [
            "MAVER_TEXT" => "Application Text",
            "MAVER_APP_VERSION" => "Application Version",
            "MAVER_APP_TYPE" => "Application Type",
            "MAVER_OS_TYPE" => "Operating System",
            "MAVER_IS_PRIORITY" => "Priority",
            "MAVER_NOTES" => "Note",
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

        $insert_data = [
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

        $insert_res = std_insert([
            "table_name" => "MAVER",
            "data" => $insert_data
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "There is something wrong when saving data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
