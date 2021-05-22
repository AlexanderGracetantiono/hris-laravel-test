<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5]);
    }
    
    public function index(Request $request)
    {
        $delete_res = std_update([
            "table_name" => "MAEMP",
            "where" => [
                "MAEMP_ID" =>  $request->code
            ],
            "data" => [
                "MAEMP_IS_DELETED" => 1
            ]
        ]);
        if ($delete_res === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ], 500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ], 200);
    }
}
