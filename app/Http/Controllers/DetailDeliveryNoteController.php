<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use QrCode;

class DetailDeliveryNoteController extends Controller
{
    public function index(Request $request)
    {
        if (!isset($request->id)) {
            return view('error_view');
        }

        // if (session("user_id") != null) {
        //     # code...
        // }

        return view('authentication/delivery_note_login', ['data' => $request->id]);
    }

    public function login_validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "username" => "required|max:255",
            "password" => "required|max:50"
        ]);

        $attributeNames = [
            "username" => "Account Name",
            "password" => "Password"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function login(Request $request)
    {
        $validation_res = $this->login_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ], 400);
        }

        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MAEMP",
            "where" => [
                [
                    "field_name" => "MAEMP_USER_NAME",
                    "operator" => "=",
                    "value" => $request->username,
                ],
            ],
            "first_row" => true
        ]);

        if ($user_data == NULL) {
            return response()->json([
                'message' => "Incorrect User Name"
            ], 500);
        }
        
        if ($user_data["MAEMP_IS_DELETED"] != 0) {
            return response()->json([
                'message' => "Unable to access this site!"
            ], 500);
        }
        
        if ($user_data["MAEMP_BLOCKED_STATUS"] == 1) {
            session(['is_blocked' => "true"]);
            return response()->json([
                'message' => "Your account is blocked"
            ], 400);
        }

        if ($user_data["MAEMP_ROLE"] == "6" || $user_data["MAEMP_ROLE"] == "7") {
            return response()->json([
                'message' => "You don't have previlage to access this site"
            ], 500);
        }

        if ($user_data["MAEMP_STATUS"] != 1) {
            return response()->json([
                'message' => "Inactive Account!"
            ], 500);
        }

        if ($user_data["MAEMP_ACTIVATION_STATUS"] != 1) {
            return response()->json([
                'message' => "Please Activate your account!"
            ], 500);
        }

        if (!Hash::check($request->password, $user_data["MAEMP_PASSWORD"])) {
            return response()->json([
                'message' => "Wrong Password!"
            ], 500);
        }

        $decrypted = Crypt::decrypt($request->data);
        
        if ($decrypted["type"] == '1') {
            $brand_code = get_master_batch_production("*",[
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $decrypted["code"]
                ]
            ],true);

            if ($user_data["MAEMP_ROLE"] != "4" || $brand_code["MABPR_MCOMP_CODE"] != $user_data["MAEMP_MCOMP_CODE"] || $brand_code["MABPR_MBRAN_CODE"] != $user_data["MAEMP_MBRAN_CODE"]) {
                return response()->json([
                    'message' => "You don't have previlage to access this site"
                ], 500);
            }
        } elseif ($decrypted["type"] == '2') {
            $brand_code = get_master_sub_batch_packaging("*",[
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $decrypted["code"]
                ]
            ],true);

            // return response()->json($user_data, 400);

            if ($user_data["MAEMP_ROLE"] != "5" && $user_data["MAEMP_ROLE"] != "8" || $brand_code["SUBPA_MCOMP_CODE"] != $user_data["MAEMP_MCOMP_CODE"] || $brand_code["SUBPA_MBRAN_CODE"] != $user_data["MAEMP_MBRAN_CODE"]) {
                return response()->json([
                    'message' => "You don't have previlage to access this site"
                ], 500);
            }
        }

        return response()->json([
            'message' => "OK"
        ], 200);
    }

    public function detail_delivery_note(Request $request)
    {
        $decrypted = Crypt::decrypt($request->id);
        
        if ($decrypted["type"] == '1') {
            $data_production = get_master_batch_production("*", [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $decrypted["code"],
                ]
            ],true);
    
            $count_paired_qr = std_get([
                "table_name" => "TRQRA",
                "where" => [
                    [
                        "field_name" => "TRQRA_MABPR_CODE",
                        "operator" => "=",
                        "value" => $decrypted["code"],
                    ],
                    [
                        "field_name" => "TRQRA_STATUS",
                        "operator" => "=",
                        "value" => 1,
                    ],
                ],
                "count" => true,
                "first_row" => true
            ]);
    
            $data_qr = get_transaction_qr_alpha(
                [
                    "TRQRA_MASCO_CODE",
                    "TRQRA_EMP_SCAN_BY",
                    "TRQRA_EMP_SCAN_TEXT",
                    "TRQRA_EMP_SCAN_TIMESTAMP",
                    "TRQRA_EMP_SCAN_LAT",
                    "TRQRA_EMP_SCAN_LNG",
                    "TRQRA_EMP_SCAN_DEVICE_ID",
                    "TRQRA_EMP_SCAN_APP_VERSION"
                ],
                [
                    [
                        "field_name" => "TRQRA_MABPR_CODE",
                        "operator" => "=",
                        "value" => $decrypted["code"],
                    ]
                ]
            );

            $qr_code = Crypt::encrypt([
                "code" => $request->code,
                "type" => "1",
            ]);
            $link_qr = route("delivery_note",["id" => $qr_code]);
    
            return view('detail_delivery_note/detail_production', [
                'data_production' => $data_production,
                'data_qr' => $data_qr,
                'count_paired_qr' => $count_paired_qr,
                'link_qr' => $link_qr
            ]);

        } elseif ($decrypted["type"] == '2') {

            $data_sub_batch = get_master_sub_batch_packaging("*", [
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $decrypted["code"],
                ]
            ],true);

            $pool_product = get_pool_product("*",[
                [
                    "field_name" => "POPRD_CODE",
                    "operator" => "=",
                    "value" => $data_sub_batch["SUBPA_POPRD_CODE"],
                ],
            ],true);
    
            $data_qr = std_get([
                "table_name" => "MASCO",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MASCO_SUBPA_CODE",
                        "operator" => "=",
                        "value" => $decrypted["code"],
                    ]
                ]
            ]);

            $qr_code = Crypt::encrypt([
                "code" => $decrypted["code"],
                "type" => "2",
            ]);
            $link_qr = route("delivery_note",["id" => $qr_code]);

            return view('detail_delivery_note/detail_packaging', [
                'data_sub_batch' => $data_sub_batch,
                'pool_product' => $pool_product,
                'data_qr' => $data_qr,
                'link_qr' => $link_qr
            ]);
        }        
    }
}
