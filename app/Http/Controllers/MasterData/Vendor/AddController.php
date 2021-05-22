<?php

namespace App\Http\Controllers\MasterData\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function index()
    {
        $company = get_master_company("*",[
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MCOMP_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
        ]);

        return view('master_data/companies/vendor/add',["company" => $company]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MVNDR_MCOMP_CODE" => "required|max:255|exists:MCOMP,MCOMP_CODE",
            "MVNDR_NAME" => "required",
            "MVNDR_EMAIL" => "required|unique:MVNDR,MVNDR_EMAIL",
            "MVNDR_ADDRESS" => "required",
        ]);

        $attributeNames = [
            "MVNDR_MCOMP_CODE" => "Company",
            "MVNDR_NAME" => "Vendor Name",
            "MVNDR_EMAIL" => "Vendor Email",
            "MVNDR_ADDRESS" => "Company Address",
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

        $code = generate_code($request->MVNDR_MCOMP_CODE,4,'MVNDR');
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => $code["data"]
            ],500);
        }

        $company = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->MVNDR_MCOMP_CODE
            ]
        ],true);

        $insert_res = std_insert([
            "table_name" => "MVNDR",
            "data" => [
                "MVNDR_CODE" => $code["data"],
                "MVNDR_NAME" => $request->MVNDR_NAME,
                "MVNDR_EMAIL" => $request->MVNDR_EMAIL,
                "MVNDR_ADDRESS" => $request->MVNDR_ADDRESS,
                "MVNDR_MCOMP_CODE" => $request->MVNDR_MCOMP_CODE,
                "MVNDR_MCOMP_NAME" => $company["MCOMP_NAME"],
                "MVNDR_CREATED_BY" => session("user_id"),
                "MVNDR_CREATED_TEXT" => date("H:i:s"),
                "MVNDR_CREATED_TIMESTAMP" => date("Y-m-d"),
                "MVNDR_UPDATED_BY" => NULL,
                "MVNDR_UPDATED_TEXT" => NULL,
                "MVNDR_UPDATED_TIMESTAMP" => NULL,
            ]
        ]);

        if ($insert_res != true) {
            return response()->json([
                'message' => "Something wrong when saving data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
