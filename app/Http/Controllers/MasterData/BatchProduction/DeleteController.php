<?php

namespace App\Http\Controllers\MasterData\BatchProduction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index(Request $request)
    {
        $data = std_get([
            "table_name" => "MABPR",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ]
            ],
            "first_row" => true
        ]);
        if ($data["MABPR_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "Data already deleted"
            ],500);
        }

        if ($data["MABPR_ACTIVATION_STATUS"] == 2 || $data["MABPR_ACTIVATION_STATUS"] == 3) {
            return response()->json([
                'message' => "Cannot delete active / accepted batch"
            ],500);
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ],
                [
                    "field_name" => "TRQRA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
            ],
            "count" => true,
            "first_row" => true
        ]);

        if ($count_paired_qr != 0) {
            return response()->json([
                'message' => "Cannot delete batch because batch already have paired QR"
            ],500);
        }

        $delete_res = std_update([
            "table_name" => "MABPR",
            "where" => [
                "MABPR_CODE" => $request->code
            ],
            "data" => [
                "MABPR_IS_DELETED" => 1
            ]
        ]);
        $delete_employee = std_delete([
            "table_name" => "STBPR",
            "where" => ["STBPR_MABPR_CODE" => $request->code],
        ]);
        if ($delete_res == false || $delete_employee == false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully deleted"
        ],200);

    }

    public function staff(Request $request)
    {
        $data = std_get([
            "table_name" => "STBPR",
            "where" => [
                [
                    "field_name" => "STBPR_ID",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "first_row" => true
        ]);

        $delete_res = std_delete([
            "table_name" => "STBPR",
            "where" => ["STBPR_ID" => $request->code],
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
