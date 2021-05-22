<?php

namespace App\Http\Controllers\MasterData\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index(Request $request)
    {
        $delete_employee = std_update([
            "table_name" => "MAEMP",
            "where" => [
                "MAEMP_MBRAN_CODE" => $request->code
            ],
            "data" => [
                "MAEMP_IS_DELETED" => 1
            ]
        ]);

        $delete_res = std_update([
            "table_name" => "MBRAN",
            "where" => [
                "MBRAN_CODE" => $request->code
            ],
            "data" => [
                "MBRAN_IS_DELETED" => 1
            ]
        ]);
        if ($delete_res === false || $delete_employee === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ],500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ],200);

    }
}
