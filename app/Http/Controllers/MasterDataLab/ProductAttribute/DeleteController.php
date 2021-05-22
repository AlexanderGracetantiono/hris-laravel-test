<?php

namespace App\Http\Controllers\MasterDataLab\ProductAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }

    public function delete(Request $request)
    {
        $data = std_get([
            "table_name" => "TRPAT",
            "where" => [
                [
                    "field_name" => "TRPAT_ID",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "first_row" => true
        ]);

        $delete_res = std_delete([
            "table_name" => "TRPAT",
            "where" => ["TRPAT_ID" => $request->code],
        ]);
       
        if ($delete_res == false && $update_res == false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ],500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ],200);
    }
}
