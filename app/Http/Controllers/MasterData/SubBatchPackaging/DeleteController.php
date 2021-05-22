<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);
        if ($data["SUBPA_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "Unable to delete because sub batch already deleted"
            ],500);
        }
        if ($data["SUBPA_ACTIVATION_STATUS"] == 2 || $data["SUBPA_ACTIVATION_STATUS"] == 3) {
            return response()->json([
                'message' => "Unable to delete because sub batch already closed / accepted"
            ],500);
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $data["SUBPA_CODE"],
                ],
                [
                    "field_name" => "TRQRZ_MBRAN_CODE",
                    "operator" => "=",
                    "value" => session("brand_code"),
                ],
            ],
            "count" => true,
            "first_row" => true
        ]);
        if ($count_paired_qr != 0) {
            return response()->json([
                'message' => "Unable to delete because sub batch already have paired QR"
            ],500);
        }

        $pool_product = get_pool_product("*", [
            [
                "field_name" => "POPRD_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_POPRD_CODE"],
            ],
        ],true);

        $update_batch = std_update([
            "table_name" => "SUBPA",
            "where" => [
                "SUBPA_CODE" => $request->code
            ],
            "data" => [
                "SUBPA_IS_DELETED" => 1
            ]
        ]);

        $update_pool_product = std_update([
            "table_name" => "POPRD",
            "where" => [
                "POPRD_CODE" => $data["SUBPA_POPRD_CODE"]
            ],
            "data" => [
                "POPRD_QTY_LEFT" => $pool_product["POPRD_QTY_LEFT"] + $data["SUBPA_QTY"]
            ]
        ]);

        $delete_staff = std_delete([
            "table_name" => "STBPA",
            "where" => ["STBPA_SUBPA_CODE" => $request->code],
        ]);

        if ($update_batch == false || $update_pool_product == false) {
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
            "table_name" => "STBPA",
            "where" => [
                [
                    "field_name" => "STBPA_ID",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
            "first_row" => true
        ]);

        $delete_res = std_delete([
            "table_name" => "STBPA",
            "where" => ["STBPA_ID" => $request->code],
        ]);
        if ($delete_res === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ],500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ],200);
    }
}
