<?php

namespace App\Http\Controllers\MasterData\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $data = get_master_vendor("*",[
            [
                "field_name" => "MVNDR_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
        ],true);

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

        return view('master_data/companies/vendor/edit',[
            "company" => $company,
            "data" => $data,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MVNDR_MCOMP_CODE" => "required|max:255|exists:MCOMP,MCOMP_CODE",
            "MVNDR_NAME" => "required",
            "MVNDR_EMAIL" => "required",
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

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $company = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->MVNDR_MCOMP_CODE
            ]
        ],true);

        $update_res = std_update([
            "table_name" => "MVNDR",
            "where" => [
                "MVNDR_CODE" => $request->MVNDR_CODE
            ],
            "data" => [
                "MVNDR_NAME" => $request->MVNDR_NAME,
                "MVNDR_EMAIL" => $request->MVNDR_EMAIL,
                "MVNDR_ADDRESS" => $request->MVNDR_ADDRESS,
                "MVNDR_MCOMP_CODE" => $request->MVNDR_MCOMP_CODE,
                "MVNDR_STATUS" => $request->MVNDR_STATUS,
                "MVNDR_MCOMP_NAME" => $company["MCOMP_NAME"],
                "MVNDR_UPDATED_BY" => session("user_id"),
                "MVNDR_UPDATED_TEXT" => date("H:i:s"),
                "MVNDR_UPDATED_TIMESTAMP" => date("Y-m-d"),
            ]
        ]);

        if ($update_res != true) {
            return response()->json([
                'message' => "Something wrong when updating data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
