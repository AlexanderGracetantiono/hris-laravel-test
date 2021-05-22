<?php

namespace App\Http\Controllers\Api\V2\Version;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdatePriority extends Controller
{
    public function get(Request $request)
    {
        // desc
        // Apabila dia pake versi 1, tapi udah ada latest versi 2,
        // cek apakah prioriynya butuh untuk di update
        // apabila ya, maka kembalikan peringatan bahwa aplikasi butuh diupdate
        // kirim pakai param lulz
        $validate = Validator::make($request->all(), [
            "MAVER_APP_VERSION" => "required",
            "MAVER_APP_TYPE" => "required",
            "MAVER_OS_TYPE" => "required"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        }

        $data = std_get([
            "table_name" => "MAVER",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAVER_APP_VERSION",
                    "operator" => "=",
                    "value" => $request->MAVER_APP_VERSION
                ],
                [
                    "field_name" => "MAVER_APP_TYPE",
                    "operator" => "=",
                    "value" => $request->MAVER_APP_TYPE
                ],
                [
                    "field_name" => "MAVER_OS_TYPE",
                    "operator" => "=",
                    "value" => $request->MAVER_OS_TYPE
                ],
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            // return 2 to no need to update
            return response()->json([
                'MAVER_IS_PRIORITY'=>2,
                'MAVER_NOTES' => "App version is latest"
            ], 201);
        }else {
            return response()->json($data, 200);
        }

    }
}
