<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "plant_type" => "required",
            "brand_code" => "required|exists:MBRAN,MBRAN_CODE",
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function index(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res,
                'data' => $request->all(),
                'err_code' => "E1"
            ], 400);
        }

        $data_plant = get_master_plant("*",[
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => $request->plant_type
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand_code
            ],
        ]);
      
        return response()->json($data_plant, 200);
    }
}
