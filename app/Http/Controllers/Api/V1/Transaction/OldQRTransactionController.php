<?php

namespace App\Http\Controllers\Api\V1\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OldQRTransactionController extends Controller
{
    public function get(Request $request){
        $validate = Validator::make($request->all(), [
            "qr_code" => "required|max:255"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
                "err_code" => "E01"
            ], 400);

        } else{

            $data_alpha = std_get([
                "table_name" => "TRQRA",
                "select" => ["*"],
                "where" => [
                    [
                        "field_name" => "TRQRA_CODE",
                        "operator" => "=",
                        "value" => $request->qr_code
                    ],
                ],
                "first_row" => true
            ]);

            $data_zeta = std_get([
                "table_name" => "TRQRZ",
                "select" => ["*"],
                "where" => [
                    [
                        "field_name" => "TRQRZ_CODE",
                        "operator" => "=",
                        "value" => $request->qr_code
                    ],
                ],
                "first_row" => true
            ]);

            if ($data_alpha == null && $data_zeta == null){
                return response()->json([
                    'message' => "QR Lama",
                    'data' => $request->all()
                ], 404);
            }else{
                if ($data_alpha != null){
                    return response()->json([
                        'message' => "QR Baru Alpha",
                        'data' => $request->all(),
                        'type' => "QRA"
                    ],200);
                }else{
                    return response()->json([
                        'message' => "QR Baru Zeta",
                        'data' => $request->all(),
                        'type' => "QRZ"
                    ],200);
                }
            }
        }
    }
}

