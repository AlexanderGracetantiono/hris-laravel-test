<?php

namespace App\Http\Controllers\MasterData\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }

    public function index(Request $request)
    {
        $category_data = std_get([
            "select" => [
                "MPRCA_ID",
                "MPRCA_CODE",
                "MPRCA_TEXT",
                "MPRCA_MCOMP_CODE",
                "MPRCA_MCOMP_TEXT",
                "MPRCA_MBRAN_CODE",
                "MPRCA_MBRAN_TEXT",
                "MPRCA_STATUS",
                "MPRCA_CREATED_BY",
                "MPRCA_CREATED_TEXT",
                "MPRCA_CREATED_TIMESTAMP",
                "MPRCA_UPDATED_BY",
                "MPRCA_UPDATED_TEXT",
                "MPRCA_UPDATED_TIMESTAMP"
            ],
            "table_name" => "MPRCA",
            "where" => [
                [
                    "field_name" => "MPRCA_ID",
                    "operator" => "=",
                    "value" => $request->maprca_id
                ],
                [
                    "field_name" => "MPRCA_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ]
            ],
            "first_row" => true,
        ]);
        
        return view('master_data/product_categories/edit', [
            "category_data" => $category_data,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "category_name" => "required|max:255",
            "category_status_is_active" => "required",
        ]);

        $attributeNames = [
            "category_name" => "category name",
            "category_status_is_active" => "Category status",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
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
            ], 400);
        }
        
        $update_data = [
            "MPRCA_TEXT" => $request->category_name,
            "MPRCA_STATUS" => $request->category_status_is_active,
            "MPRCA_UPDATED_BY" => session("user_id"),
            "MPRCA_UPDATED_TEXT" => session("user_name"),
            "MPRCA_UPDATED_TIMESTAMP" =>date("Y-m-d H:i:s")
        ];

        $update_res = std_update([
            "table_name" => "MPRCA",
            "where" => ["MPRCA_ID" => $request->category_id],
            "data" => $update_data
        ]);
        //update category in product
 $update_data = [
            "MPRDT_MPRCA_TEXT" => $request->category_name,
            "MPRDT_UPDATED_BY" => session("user_code"),
            "MPRDT_UPDATED_TEXT" => session("user_name"),
            "MPRDT_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "MPRDT",
            "where" => ["MPRDT_MPRCA_CODE" => $request->category_code],
            "data" => $update_data
        ]);
        // update category in model
        $update_data = [
            "MPRMO_MPRCA_TEXT" => $request->category_name,
            "MPRMO_UPDATED_BY" => session("user_code"),
            "MPRMO_UPDATED_TEXT" => session("user_name"),
            "MPRMO_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_res = std_update([
            "table_name" => "MPRMO",
            "where" => ["MPRMO_MPRCA_CODE" => $request->category_code],
            "data" => $update_data
        ]);
        // update category in Version
        $update_data = [
            "MPRVE_MPRCA_TEXT" => $request->category_name,
            "MPRVE_UPDATED_BY" => session("user_code"),
            "MPRVE_UPDATED_TEXT" => session("user_name"),
            "MPRVE_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $update_res = std_update([
            "table_name" => "MPRVE",
            "where" => ["MPRVE_MPRCA_CODE" => $request->category_code],
            "data" => $update_data
        ]);
        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data tag, silahkan coba berberapa saat lagi"
            ], 500);
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }
}
