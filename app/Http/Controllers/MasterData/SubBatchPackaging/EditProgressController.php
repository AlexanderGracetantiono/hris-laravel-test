<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditProgressController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_sub_batch_packaging("*", [
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        $staff = get_staff_packaging("*",[
            [
                "field_name" => "STBPA_SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ]);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "count" => true,
            "first_row" => true
        ]);

        $selected_batch_packaging = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_MABPA_CODE"],
            ],
        ],true);

        $selected_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_MABPR_CODE"],
            ],
        ],true);

        return view('master_data/sub_batch_packaging/edit_closed',[
            'data' => $data,
            'staff' => $staff,
            'count_paired_qr' => $count_paired_qr,
            'selected_batch_packaging' => $selected_batch_packaging,
            'selected_batch_production' => $selected_batch_production,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "SUBPA_QTY" => "required|numeric",
        ]);

        $attributeNames = [
            "SUBPA_QTY" => "Sub Batch Quantity",
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

        $data = get_master_sub_batch_packaging(["SUBPA_QTY"], [
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->SUBPA_CODE,
            ]
        ],true);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "count" => true,
            "first_row" => true
        ]);

        if ($request->SUBPA_QTY < $count_paired_qr) {
            return response()->json([
                'message' => "New quantity must be bigger than paired QR"
            ],500);
        }

        if ($request->SUBPA_QTY > $data["SUBPA_QTY"]) {
            return response()->json([
                'message' => "New quantity must be smaller than current sub batch quantity"
            ],500);
        }

        $update_data = [
            "SUBPA_QTY" => $request->SUBPA_QTY,
            "SUBPA_UPDATED_BY" => session("user_id"),
            "SUBPA_UPDATED_TEXT" => session("user_name"),
            "SUBPA_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $request->SUBPA_CODE],
            "data" => $update_data
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "There was an error saving brand production, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
