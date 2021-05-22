<?php

namespace App\Http\Controllers\MasterData\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }

    public function index()
    {
        $company = get_master_company(["*"],[
            [
                "field_name" => "MCOMP_STATUS",
                "operator" => "=",
                "value" => 1
            ],
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "=",
                "value" => 0
            ],
        ]);

        return view('master_data/brand/add',[
            "company" => $company
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MBRAN_MCOMP_CODE" => "required",
            "MBRAN_NAME" => "required|max:255",
            "MBRAN_ADDRESS" => "required",
            "MBRAN_TYPE" => "required",
            // "MBRAN_IMAGE" => "|required|image|mimes:jpeg,png,jpg,png|max:3072",
            // "MBRAN_TRPAT_TYPE" => "required",
        ]);

        $attributeNames = [
            "MBRAN_NAME" => "Brand Name",
            "MBRAN_MCOMP_CODE" => "Company",
            "MBRAN_ADDRESS" => "Brand Address",
            "MBRAN_TYPE" => "Brand Type",
            // "MBRAN_IMAGE" => "Upload Brand Logo",
            // "MBRAN_TRPAT_TYPE" => "Level Attribute Product",
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
        $code = generate_code($request->MBRAN_MCOMP_CODE,5,"MBRAN");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $company = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->MBRAN_MCOMP_CODE,
            ]
        ],true);

        if ($request->MBRAN_TYPE == 1) {
            $type = 4;
        }
        if ($request->MBRAN_TYPE == 2) {
            $type = 1;
        }

        // $file = $request->file('MBRAN_IMAGE');
        // $filename = date("Ymdhis").".".$file->getClientOriginalExtension();
        // // $upload_dir = "public/storage/images/brand_logo/";
        // $upload_dir = "storage/images/brand_logo/";

        // if (!is_writable($upload_dir)) {
        //     return response()->json([
        //         'message' => "Storage error, please check existing location"
        //     ],500);
        // }
        // $file->move($upload_dir, $filename);

        $insert_res = std_insert([
            "table_name" => "MBRAN",
            "data" => [
                "MBRAN_CODE" => strtoupper($code["data"]),
                "MBRAN_NAME" => $request->MBRAN_NAME,
                "MBRAN_EMAIL" => $request->MBRAN_EMAIL,
                "MBRAN_TYPE" => $request->MBRAN_TYPE,
                "MBRAN_ADDRESS" => $request->MBRAN_ADDRESS,
                // "MBRAN_IMAGE" => $filename,
                "MBRAN_MCOMP_CODE" => $request->MBRAN_MCOMP_CODE,
                "MBRAN_TRPAT_TYPE" => $type,
                "MBRAN_MCOMP_NAME" => $company["MCOMP_NAME"],
                "MBRAN_STATUS" => 1,
                "MBRAN_CREATED_BY" => session("user_code"),
                "MBRAN_CREATED_TEXT" => session("user_name"),
                "MBRAN_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }

}
