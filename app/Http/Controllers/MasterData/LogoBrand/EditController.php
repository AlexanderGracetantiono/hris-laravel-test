<?php

namespace App\Http\Controllers\MasterData\LogoBrand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MBRAN_IMAGE" => "required|image|mimes:jpeg,png,jpg,png|max:3072",
        ]);

        $attributeNames = [
            "MBRAN_IMAGE" => "Brand Logo",
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

        if (isset($request->MBRAN_IMAGE)) {
            $file = $request->file('MBRAN_IMAGE');
            $filename = date("Ymdhis").".".$file->getClientOriginalExtension();
            // $upload_dir = "public/storage/images/brand_logo/";
              $upload_dir = "storage/images/brand_logo/"; //localhostonly

            if (!is_writable($upload_dir)) {
                return response()->json([
                    'message' => "Storage error, please check existing location"
                ],500);
            }
            $file->move($upload_dir, $filename);

            $update_data["MBRAN_IMAGE"] = $filename;
        }

        $update_res = std_update([
            "table_name" => "MBRAN",
            "where" => ["MBRAN_CODE" => $request->MBRAN_CODE],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
