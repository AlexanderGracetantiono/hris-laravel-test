<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5,8]);
    }
    
    public function index(Request $request)
    {
        $reset = std_update([
            "table_name" => "MAEMP",
            "where" => [
                "MAEMP_ID" =>  $request->code
            ],
            "data" => [
                "MAEMP_PASSWORD" => Hash::make("password"),
            ]
        ]);
        if ($reset === false) {
            return response()->json([
                'message' => "Something wrong when deleting data, please try again"
            ], 500);
        }
        return response()->json([
            'message' => "Data succesfully deleted"
        ], 200);
    }
}
