<?php

namespace App\Http\Controllers\Transaction\OrderQr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use PDF;
use QrCode;
use DateInterval;
use DateTime;
class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }

    public function index()
    {
        return view('transaction/order_qr/add', []);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
             "TRORD_QTY" => "required|numeric|max:1080",
             "TRORD_SIZE" => "required|numeric|max:24|min:12",
            //  "TRORD_ORIENTATION" => "required",
        ]);

        $attributeNames = [
             "TRORD_QTY" => "QR Quantity",
             "TRORD_SIZE" => "QR Size",
            //  "TRORD_ORIENTATION" => "QR Orientation",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    function old_save(Type $var = null)
    {
        $data_queue = std_get([
            "select" => ["*"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_GENERATE_STATUS",
                    "operator" => "=",
                    "value" => 0,
                ]
            ],
            "first_row" => true,
            "count" => true
        ]);

        $code_alpha = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 1);
        $code_zeta = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 2);
        $sticker_code = generate_qr_code($request->code, $data_order["TRORD_MBRAN_CODE"], $data_order["TRORD_QTY"], 3);

        for ($i=0; $i < count($code_alpha["data"]); $i++) {
            $insert_qr_alpha[$i] = [
                "TRQRA_CODE" => $code_alpha["data"][$i],
                "TRQRA_TRORD_CODE" => $data_order["TRORD_CODE"],
                "TRQRA_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                "TRQRA_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                "TRQRA_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                "TRQRA_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                "TRQRA_CREATED_BY" => session("user_id"),
                "TRQRA_CREATED_TEXT" => session("user_name"),
                "TRQRA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        for ($i=0; $i < count($code_zeta["data"]); $i++) {
            $insert_qr_zeta[$i] = [
                "TRQRZ_CODE" => $code_zeta["data"][$i],
                "TRQRZ_TRORD_CODE" => $data_order["TRORD_CODE"],
                "TRQRZ_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                "TRQRZ_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                "TRQRZ_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                "TRQRZ_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                "TRQRZ_CREATED_BY" => session("user_id"),
                "TRQRZ_CREATED_TEXT" => session("user_name"),
                "TRQRZ_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        $count_order = std_get([
            "select" => ["TRORD_ID"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $data_order["TRORD_MCOMP_CODE"],
                ]
            ],
            "first_row" => true,
            "count" => true
        ]);

        $counter = 0;
        for ($i=0; $i < $count_order; $i++) { 
            $counter++;
            if ($counter == 8) {
                $counter = 1;
            }
        }

        for ($i=0; $i < count($sticker_code["data"]); $i++) {
            $insert_sticker_code[$i] = [
                "MASCO_CODE" => $sticker_code["data"][$i],
                "MASCO_TRORD_CODE" => $data_order["TRORD_CODE"],
                "MASCO_MCOMP_CODE" => $data_order["TRORD_MCOMP_CODE"],
                "MASCO_MCOMP_TEXT" => $data_order["TRORD_MCOMP_TEXT"],
                "MASCO_MBRAN_CODE" => $data_order["TRORD_MBRAN_CODE"],
                "MASCO_MBRAN_TEXT" => $data_order["TRORD_MBRAN_TEXT"],
                "MASCO_COUNTER" => $counter,
                "MASCO_CREATED_BY" => session("user_id"),
                "MASCO_CREATED_TEXT" => session("user_name"),
                "MASCO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }
        DB::beginTransaction();
        try {
            $insert_alpha = std_insert([
                "table_name" => "TRQRA",
                "data" => $insert_qr_alpha
            ]);

            $insert_zeta = std_insert([
                "table_name" => "TRQRZ",
                "data" => $insert_qr_zeta
            ]);

            $insert_sticker = std_insert([
                "table_name" => "MASCO",
                "data" => $insert_sticker_code
            ]);

            $insert_res = std_insert([
                "table_name" => "TRORD",
                "data" => $data_order
            ]);

            $update_res = std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_STATUS" => "1",
                    "TRORD_NOTES" => "System Approved",
                    "TRORD_APPROVED_BY" => session("user_id"),
                    "TRORD_APPROVED_TEXT" => session("user_name"),
                    "TRORD_APPROVED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "TRORD_UPDATED_BY" => session("user_id"),
                    "TRORD_UPDATED_TEXT" => session("user_name"),
                    "TRORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ],
                "where" => ["TRORD_CODE" => $data_order["TRORD_CODE"]]
            ]);
            
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            if ($insert_alpha != true) {
                return response()->json([
                    'message' => "There was an error on inserting QR alpha"
                ],500);
            }

            if ($insert_zeta != true) {
                return response()->json([
                    'message' => "There was an error on inserting QR zeta"
                ],500);
            }

            if ($insert_sticker != true) {
                return response()->json([
                    'message' => "There was an error on inserting sticker code"
                ],500);
            }
        }
        $qr_orientation="portrait";
        $generate_qr_alpha = $this->generate_qr_alpha($request->TRORD_SIZE,$qr_orientation,$insert_qr_alpha);
        $generate_qr_zeta = $this->generate_qr_zeta($request->TRORD_SIZE,$qr_orientation,$insert_qr_zeta);
        $generate_sticker_code = $this->generate_sticker_code($request->TRORD_SIZE,$qr_orientation,$insert_sticker_code);
        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $code = generate_code_number(session('company_code'),3,"TRORD");
        // if ($request->TRORD_ORIENTATION == "portrait") {
            $orientation_code = "ptr";
        // } else {
            // $orientation_code = "lns";
        // }

        $data_order = [
            "TRORD_CODE" => $code["data"],
            "TRORD_QTY" => $request->TRORD_QTY,
            "TRORD_MCOMP_CODE" => session('company_code'),
            "TRORD_MCOMP_TEXT" => session('company_name'),
            "TRORD_MBRAN_CODE" => session('brand_code'),
            "TRORD_MBRAN_TEXT" => session('brand_name'),
            "TRORD_STATUS" => 1,
            "TRORD_UNIT" => "mm",
            "TRORD_SIZE" => $request->TRORD_SIZE,
            "TRORD_ORIENTATION" => $orientation_code,
            "TRORD_GENERATE_STATUS" => 0,
            "TRORD_CREATED_BY" => session("user_id"),
            "TRORD_CREATED_TEXT" => session("user_name"),
            "TRORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            "TRORD_APPROVED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_queue = std_insert([
            "table_name" => "TRORD",
            "data" => $data_order
        ]);

        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function generate_qr_alpha($TRORD_SIZE,$orientation,$data_qr_alpha)
    {
        $TRORD_SIZE = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $orientation_code = "ptr";
        $qr_type = "alpha";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly
        // if ($orientation == "portrait") {
        //     $orientation_code = "ptr";
        // } else {
        //     $orientation_code = "lns";
        // }
        PDF::setOptions(['defaultFont' => 'arial']);
        $qrcode=null;
        for ($i = 0; $i < count($data_qr_alpha); $i++) {
            $qrcode[$i]["qr_code"] = $data_qr_alpha[$i]["TRQRA_CODE"];
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($TRORD_SIZE)->errorCorrection('H')->color(145, 0, 4)->generate($data_qr_alpha[$i]["TRQRA_CODE"]));
        }
        shuffle($qrcode);
        if ($orientation == "portrait") {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha', [
                "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        } else {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha_landscape', [
                "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        }
        $pdf->setPaper('a3', 'portrait');
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            try {
                $pdf->save($upload_dir . '/' . $file_name);
                $insert_fiord_data = [
                    "FIORD_MCOMP_CODE" =>  session("company_code"),
                    "FIORD_MCOMP_NAME" =>  session("company_name"),
                    "FIORD_MBRAN_CODE" =>  session("brand_code"),
                    "FIORD_MBRAN_NAME" => session("brand_name"),
                    "FIORD_TRORD_CODE" => $data_qr_alpha[0]["TRQRA_TRORD_CODE"],
                    "FIORD_TRORD_QTY" => count($data_qr_alpha),
                    "FIORD_NAME" => $file_name,
                    "FIORD_ORIENTATION" => $orientation_code,
                    "FIORD_QR_TYPE" =>  $qr_type,
                    "FIORD_SIZE" => $TRORD_SIZE,
                    "FIORD_UNIT" => "mm",
                    "FIORD_STATUS" => 0, //0 ok, 1 deleted
                    "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                    "FIORD_CREATED_BY" => session("user_id"),
                    "FIORD_CREATED_TEXT" => session("user_name"),
                    "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_fiord = std_insert([
                    "table_name" => "FIORD",
                    "data" => $insert_fiord_data
                ]);
                if ($insert_fiord) {
                    // get id fiord
                    $data_fiord = std_get([
                        "select" => ["FIORD_ID"],
                        "table_name" => "FIORD",
                        "where" => [
                            [
                                "field_name" => "FIORD_NAME",
                                "operator" => "=",
                                "value" => $file_name,
                            ]
                        ],
                    ], true);
                    $insert_lgdqr_data = [
                        "LGDQR_MCOMP_CODE" =>  session("company_code"),
                        "LGDQR_MCOMP_NAME" =>  session("company_name"),
                        "LGDQR_MBRAN_CODE" =>  session("brand_code"),
                        "LGDQR_MBRAN_NAME" => session("brand_name"),
                        "LGDQR_TRORD_CODE" => $data_qr_alpha[0]["TRQRA_TRORD_CODE"],
                        "LGDQR_TRORD_QTY" => count($data_qr_alpha),
                        "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
                        "LGDQR_FIORD_NAME" => $file_name,
                        "LGDQR_FIORD_ORIENTATION" => $orientation_code,
                        "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
                        "LGDQR_FIORD_SIZE" => $TRORD_SIZE,
                        "LGDQR_FIORD_UNIT" => "mm",
                        "LGDQR_CREATED_BY" => session("user_id"),
                        "LGDQR_CREATED_TEXT" => session("user_name"),
                        "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
                    $insert_lgdqr = std_insert([
                        "table_name" => "LGDQR",
                        "data" => $insert_lgdqr_data
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => "Cannot save pdf in the location"
                ], 500);
            }
        }
        return true;
    }

    public function generate_qr_zeta($TRORD_SIZE,$orientation,$data_qr_zeta)
    {
        $TRORD_SIZE = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $orientation_code = "ptr";
        $qr_type = "zeta";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly
        // if ($orientation == "portrait") {
        //     $orientation_code = "ptr";
        // } else {
        //     $orientation_code = "lns";
        // }
        PDF::setOptions(['defaultFont' => 'arial']);
        $qrcode=null;
        for ($i = 0; $i < count($data_qr_zeta); $i++) {
            $qrcode[$i]["qr_code"] = $data_qr_zeta[$i]["TRQRZ_CODE"];
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($TRORD_SIZE)->errorCorrection('H')->color(145, 0, 4)->generate($data_qr_zeta[$i]["TRQRZ_CODE"]));
        }
        shuffle($qrcode);
        if ($orientation == "portrait") {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha', [
                "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        } else {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha_landscape', [
                "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        }
        $pdf->setPaper('a3', 'portrait');
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            try {
                $pdf->save($upload_dir . '/' . $file_name);
                $insert_fiord_data = [
                    "FIORD_MCOMP_CODE" =>  session("company_code"),
                    "FIORD_MCOMP_NAME" =>  session("company_name"),
                    "FIORD_MBRAN_CODE" =>  session("brand_code"),
                    "FIORD_MBRAN_NAME" => session("brand_name"),
                    "FIORD_TRORD_CODE" => $data_qr_zeta[0]["TRQRZ_TRORD_CODE"],
                    "FIORD_TRORD_QTY" => count($data_qr_zeta),
                    "FIORD_NAME" => $file_name,
                    "FIORD_ORIENTATION" => $orientation_code,
                    "FIORD_QR_TYPE" =>  $qr_type,
                    "FIORD_SIZE" => $TRORD_SIZE,
                    "FIORD_UNIT" => "mm",
                    "FIORD_STATUS" => 0, //0 ok, 1 deleted
                    "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                    "FIORD_CREATED_BY" => session("user_id"),
                    "FIORD_CREATED_TEXT" => session("user_name"),
                    "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_fiord = std_insert([
                    "table_name" => "FIORD",
                    "data" => $insert_fiord_data
                ]);
                if ($insert_fiord) {
                    // get id fiord
                    $data_fiord = std_get([
                        "select" => ["FIORD_ID"],
                        "table_name" => "FIORD",
                        "where" => [
                            [
                                "field_name" => "FIORD_NAME",
                                "operator" => "=",
                                "value" => $file_name,
                            ]
                        ],
                    ], true);
                    $insert_lgdqr_data = [
                        "LGDQR_MCOMP_CODE" =>  session("company_code"),
                        "LGDQR_MCOMP_NAME" =>  session("company_name"),
                        "LGDQR_MBRAN_CODE" =>  session("brand_code"),
                        "LGDQR_MBRAN_NAME" => session("brand_name"),
                        "LGDQR_TRORD_CODE" => $data_qr_zeta[0]["TRQRZ_TRORD_CODE"],
                        "LGDQR_TRORD_QTY" => count($data_qr_zeta),
                        "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
                        "LGDQR_FIORD_NAME" => $file_name,
                        "LGDQR_FIORD_ORIENTATION" => $orientation_code,
                        "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
                        "LGDQR_FIORD_SIZE" => $TRORD_SIZE,
                        "LGDQR_FIORD_UNIT" => "mm",
                        "LGDQR_CREATED_BY" => session("user_id"),
                        "LGDQR_CREATED_TEXT" => session("user_name"),
                        "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
                    $insert_lgdqr = std_insert([
                        "table_name" => "LGDQR",
                        "data" => $insert_lgdqr_data
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => "Cannot save pdf in the location"
                ], 500);
            }
        }
        return true;
    }
    
    public function generate_sticker_code($TRORD_SIZE,$orientation,$data_sticker_code)
    {
        $TRORD_SIZE = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $orientation_code = "ptr";
        $qr_type = "bridge";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly
        // if ($orientation == "portrait") {
        //     $orientation_code = "ptr";
        // } else {
        //     $orientation_code = "lns";
        // }
        PDF::setOptions(['defaultFont' => 'arial']);
        $qrcode=null;
        for ($i = 0; $i < count($data_sticker_code); $i++) {
            $qrcode[$i]["qr_code"] = $data_sticker_code[$i]["MASCO_CODE"];
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($TRORD_SIZE)->errorCorrection('H')->color(145, 0, 4)->generate($data_sticker_code[$i]["MASCO_CODE"]));
        }
        shuffle($qrcode);
        if ($orientation == "portrait") {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha', [
                "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        } else {
            $pdf = PDF::loadView('transaction/order_qr/qr_alpha_landscape', [
                "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "TRORD_SIZE" => $TRORD_SIZE,
            ]);
        }
        $pdf->setPaper('a3', 'portrait');
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            try {
                $pdf->save($upload_dir . '/' . $file_name);
                $insert_fiord_data = [
                    "FIORD_MCOMP_CODE" =>  session("company_code"),
                    "FIORD_MCOMP_NAME" =>  session("company_name"),
                    "FIORD_MBRAN_CODE" =>  session("brand_code"),
                    "FIORD_MBRAN_NAME" => session("brand_name"),
                    "FIORD_TRORD_CODE" => $data_sticker_code[0]["MASCO_TRORD_CODE"],
                    "FIORD_TRORD_QTY" => count($data_sticker_code),
                    "FIORD_NAME" => $file_name,
                    "FIORD_ORIENTATION" => $orientation_code,
                    "FIORD_QR_TYPE" =>  $qr_type,
                    "FIORD_SIZE" => $TRORD_SIZE,
                    "FIORD_UNIT" => "mm",
                    "FIORD_STATUS" => 0, //0 ok, 1 deleted
                    "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                    "FIORD_CREATED_BY" => session("user_id"),
                    "FIORD_CREATED_TEXT" => session("user_name"),
                    "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_fiord = std_insert([
                    "table_name" => "FIORD",
                    "data" => $insert_fiord_data
                ]);
                if ($insert_fiord) {
                    // get id fiord
                    $data_fiord = std_get([
                        "select" => ["FIORD_ID"],
                        "table_name" => "FIORD",
                        "where" => [
                            [
                                "field_name" => "FIORD_NAME",
                                "operator" => "=",
                                "value" => $file_name,
                            ]
                        ],
                    ], true);
                    $insert_lgdqr_data = [
                        "LGDQR_MCOMP_CODE" =>  session("company_code"),
                        "LGDQR_MCOMP_NAME" =>  session("company_name"),
                        "LGDQR_MBRAN_CODE" =>  session("brand_code"),
                        "LGDQR_MBRAN_NAME" => session("brand_name"),
                        "LGDQR_TRORD_CODE" => $data_sticker_code[0]["MASCO_TRORD_CODE"],
                        "LGDQR_TRORD_QTY" => count($data_sticker_code),
                        "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
                        "LGDQR_FIORD_NAME" => $file_name,
                        "LGDQR_FIORD_ORIENTATION" => $orientation_code,
                        "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
                        "LGDQR_FIORD_SIZE" => $TRORD_SIZE,
                        "LGDQR_FIORD_UNIT" => "mm",
                        "LGDQR_CREATED_BY" => session("user_id"),
                        "LGDQR_CREATED_TEXT" => session("user_name"),
                        "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
                    $insert_lgdqr = std_insert([
                        "table_name" => "LGDQR",
                        "data" => $insert_lgdqr_data
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => "Cannot save pdf in the location"
                ], 500);
            }
        }
        return true;
    }
}
