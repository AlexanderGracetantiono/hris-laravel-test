<?php

namespace App\Http\Controllers\MasterData\Plant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }
    
    public function index(Request $request)
    {
        $delete_res = std_update([
            "table_name" => "MAPLA",
            "where" => [
                "MAPLA_CODE" => $request->code
            ],
            "data" => [
                "MAPLA_IS_DELETED" => 1
            ]
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
