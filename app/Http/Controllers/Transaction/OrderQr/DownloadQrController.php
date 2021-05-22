<?php

namespace App\Http\Controllers\Transaction\OrderQr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use QrCode;
use DateInterval;
use DateTime;

class DownloadQrController extends Controller
{
    public function __construct()
    {
        check_is_role_allowed([3]);
    }

    function hex_rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public function alpha(Request $request)
    {
        $orientation_code = "ptr";
        $qr_type = "alpha";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        // $upload_dir = "public/storage/file/qr_file/";
        $upload_dir = "storage/file/qr_file/"; //localhostonly
        $data_qr_alpha = std_get([
            "select" => ["TRQRA_CODE", "TRQRA_MBRAN_TEXT"],
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $data_qr_master = std_get([
            "select" => ["TRORD_SIZE","TRORD_ORIENTATION"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $qr_size = round($data_qr_master[0]["TRORD_SIZE"] * 3.333333333333333); //mm to pixel
        $orientation = $data_qr_master[0]["TRORD_ORIENTATION"];
        if ($orientation == "portrait") {
            $orientation_code = "ptr";
        } else {
            $orientation_code = "lns";
        }
        $data_qr_generated = std_get([
            "select" => ["*"],
            "table_name" => "FIORD",
            "where" => [
                [
                    "field_name" => "FIORD_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "FIORD_QR_TYPE",
                    "operator" => "=",
                    "value" => $qr_type,
                ],
                // [
                //     "field_name" => "FIORD_QR_TYPE",
                //     "operator" => "=",
                //     "value" => $qr_type,
                // ],
                // [
                //     "field_name" => "FIORD_SIZE",
                //     "operator" => "=",
                //     "value" => $data_qr_master[0]["TRORD_SIZE"],
                // ],
                // [
                //     "field_name" => "FIORD_STATUS",
                //     "operator" => "=",
                //     "value" => 0,
                // ]
            ],
        ]);
        if ($data_qr_generated != null) {
            $file_address = $upload_dir . $data_qr_generated[0]["FIORD_NAME"];
            if (file_exists($file_address)) {
                // fungsi download klo ada
                header("Content-Type: application/pdf");
                return readfile($file_address);
            }else{
                return response()->json([
                    'message' => "There is no QR generated, please generate qr"
                ], 500);
            }
        }else{
            return response()->json([
                'message' => "There is no QR generated, please generate qr"
            ], 500);
        }
        // PDF::setOptions(['defaultFont' => 'arial']);
        // for ($i = 0; $i < count($data_qr_alpha); $i++) {
        //     $qrcode[$i]["qr_code"] = $data_qr_alpha[$i]["TRQRA_CODE"];
        //     // $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->color(255, 10, 1)->generate($data_qr_alpha[$i]["TRQRA_CODE"]));
        //     $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->color(145, 0, 4)->generate($data_qr_alpha[$i]["TRQRA_CODE"]));
        // }

        // shuffle($qrcode);
        // if ($orientation == "portrait") {
        //     $pdf = PDF::loadView('transaction/order_qr/qr_alpha', [
        //         "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
        //         "qrcode" => $qrcode,
        //         "color" => "#63e2ff",
        //         "qr_size" => $qr_size,
        //     ]);
        // } else {
        //     $pdf = PDF::loadView('transaction/order_qr/qr_alpha_landscape', [
        //         "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
        //         "qrcode" => $qrcode,
        //         "color" => "#63e2ff",
        //         "qr_size" => $qr_size,
        //     ]);
        // }
        // $pdf->setPaper('a3', 'portrait');
        // if (!is_writable($upload_dir)) {
        //     return response()->json([
        //         'message' => "Storage error, please check existing location"
        //     ], 500);
        // } else {
        //     try {
        //         $pdf->save($upload_dir . '/' . $file_name);
        //         $insert_fiord_data = [
        //             "FIORD_MCOMP_CODE" =>  session("company_code"),
        //             "FIORD_MCOMP_NAME" =>  session("company_name"),
        //             "FIORD_MBRAN_CODE" =>  session("brand_code"),
        //             "FIORD_MBRAN_NAME" => session("brand_name"),
        //             "FIORD_TRORD_CODE" => $request->code,
        //             "FIORD_TRORD_QTY" => count($data_qr_alpha),
        //             "FIORD_NAME" => $file_name,
        //             "FIORD_ORIENTATION" => $orientation_code,
        //             "FIORD_QR_TYPE" =>  $qr_type,
        //             "FIORD_SIZE" => $request->QR_SIZE,
        //             "FIORD_UNIT" => "mm",
        //             "FIORD_STATUS" => 0, //0 ok, 1 deleted
        //             "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
        //             "FIORD_CREATED_BY" => session("user_id"),
        //             "FIORD_CREATED_TEXT" => session("user_name"),
        //             "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //         ];
        //         $insert_fiord = std_insert([
        //             "table_name" => "FIORD",
        //             "data" => $insert_fiord_data
        //         ]);
        //         if ($insert_fiord) {
        //             // get id fiord
        //             $data_fiord = std_get([
        //                 "select" => ["FIORD_ID"],
        //                 "table_name" => "FIORD",
        //                 "where" => [
        //                     [
        //                         "field_name" => "FIORD_NAME",
        //                         "operator" => "=",
        //                         "value" => $file_name,
        //                     ]
        //                 ],
        //             ], true);
        //             $insert_lgdqr_data = [
        //                 "LGDQR_MCOMP_CODE" =>  session("company_code"),
        //                 "LGDQR_MCOMP_NAME" =>  session("company_name"),
        //                 "LGDQR_MBRAN_CODE" =>  session("brand_code"),
        //                 "LGDQR_MBRAN_NAME" => session("brand_name"),
        //                 "LGDQR_TRORD_CODE" => $request->code,
        //                 "LGDQR_TRORD_QTY" => count($data_qr_alpha),
        //                 "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
        //                 "LGDQR_FIORD_NAME" => $file_name,
        //                 "LGDQR_FIORD_ORIENTATION" => $orientation_code,
        //                 "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
        //                 "LGDQR_FIORD_SIZE" => $request->QR_SIZE,
        //                 "LGDQR_FIORD_UNIT" => "mm",
        //                 "LGDQR_CREATED_BY" => session("user_id"),
        //                 "LGDQR_CREATED_TEXT" => session("user_name"),
        //                 "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             ];
        //             $insert_lgdqr = std_insert([
        //                 "table_name" => "LGDQR",
        //                 "data" => $insert_lgdqr_data
        //             ]);
        //         }
        //     } catch (\Throwable $th) {
        //         return response()->json([
        //             'message' => "Cannot save pdf in the location"
        //         ], 500);
        //     }
        // }
        // return $pdf->download($file_name);
    }
    public function zeta(Request $request)
    {
        // $qr_size = round($request->QR_SIZE * 3.333333333333333); //mm to pixel
        // $orientation = $request->orientation;
        $orientation_code = "ptr";
        $qr_type = "zeta";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        // $upload_dir = "public/storage/file/qr_file/";
        $upload_dir = "storage/file/qr_file/"; //localhostonly
        $data_qr_zeta = std_get([
            "select" => ["TRQRZ_CODE", "TRQRZ_MBRAN_TEXT"],
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $data_qr_master = std_get([
            "select" => ["TRORD_SIZE","TRORD_ORIENTATION"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $qr_size = round($data_qr_master[0]["TRORD_SIZE"] * 3.333333333333333); //mm to pixel
        $orientation = $data_qr_master[0]["TRORD_ORIENTATION"];
        if ($orientation == "portrait") {
            $orientation_code = "ptr";
        } else {
            $orientation_code = "lns";
        }
        $data_qr_generated = std_get([
            "select" => ["*"],
            "table_name" => "FIORD",
            "where" => [
                [
                    "field_name" => "FIORD_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "FIORD_QR_TYPE",
                    "operator" => "=",
                    "value" => $qr_type,
                ],
                // [
                //     "field_name" => "FIORD_QR_TYPE",
                //     "operator" => "=",
                //     "value" => $qr_type,
                // ],
                // [
                //     "field_name" => "FIORD_SIZE",
                //     "operator" => "=",
                //     "value" => $data_qr_master[0]["TRORD_SIZE"],
                // ],
                // [
                //     "field_name" => "FIORD_STATUS",
                //     "operator" => "=",
                //     "value" => 0,
                // ]
            ],
        ]);
        // dd($data_qr_generated);
        if ($data_qr_generated != null) {
            $file_address = $upload_dir . $data_qr_generated[0]["FIORD_NAME"];
            if (file_exists($file_address)) {
                // fungsi download klo ada
                header("Content-Type: application/pdf");
                return readfile($file_address);
            }else{
                return response()->json([
                    'message' => "There is no QR generated, please generate qr"
                ], 500);
            }
        }else{
            return response()->json([
                'message' => "There is no QR generated, please generate qr"
            ], 500);
        }
        // PDF::setOptions(['defaultFont' => 'arial']);
        // for ($i = 0; $i < count($data_qr_zeta); $i++) {
        //     $qrcode[$i]["qr_code"] = $data_qr_zeta[$i]["TRQRZ_CODE"];
        //     // $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->color(34, 255, 56)->generate($data_qr_zeta[$i]["TRQRZ_CODE"]));
        //     $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->color(5, 117, 127)->generate($data_qr_zeta[$i]["TRQRZ_CODE"]));
        // }
        // shuffle($qrcode);
        // if ($orientation == "portrait") {
        //     $orientation_code = "ptr";
        //     $pdf = PDF::loadView('transaction/order_qr/qr_zeta', [
        //         "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
        //         "qrcode" => $qrcode,
        //         // "color" => "#22ff38"
        //         "color" => "#05747e",
        //         "qr_size" => $qr_size
        //     ]);
        // } else {
        //     $orientation_code = "lns";
        //     $pdf = PDF::loadView('transaction/order_qr/qr_zeta_landscape', [
        //         "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
        //         "qrcode" => $qrcode,
        //         // "color" => "#22ff38"
        //         "color" => "#05747e",
        //         "qr_size" => $qr_size
        //     ]);
        // }
        // $pdf->setPaper('a3', 'portrait');
        // if (!is_writable($upload_dir)) {
        //     return response()->json([
        //         'message' => "Storage error, please check existing location"
        //     ], 500);
        // } else {
        //     try {
        //         $pdf->save($upload_dir . '/' . $file_name);
        //         $insert_fiord_data = [
        //             "FIORD_MCOMP_CODE" =>  session("company_code"),
        //             "FIORD_MCOMP_NAME" =>  session("company_name"),
        //             "FIORD_MBRAN_CODE" =>  session("brand_code"),
        //             "FIORD_MBRAN_NAME" => session("brand_name"),
        //             "FIORD_TRORD_CODE" => $request->code,
        //             "FIORD_TRORD_QTY" => count($data_qr_zeta),
        //             "FIORD_NAME" => $file_name,
        //             "FIORD_ORIENTATION" => $orientation_code,
        //             "FIORD_QR_TYPE" =>  $qr_type,
        //             "FIORD_SIZE" => $request->QR_SIZE,
        //             "FIORD_UNIT" => "mm",
        //             "FIORD_STATUS" => 0, //0 ok, 1 deleted
        //             "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
        //             "FIORD_CREATED_BY" => session("user_id"),
        //             "FIORD_CREATED_TEXT" => session("user_name"),
        //             "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //         ];
        //         $insert_fiord = std_insert([
        //             "table_name" => "FIORD",
        //             "data" => $insert_fiord_data
        //         ]);
        //         if ($insert_fiord) {
        //             // get id fiord
        //             $data_fiord = std_get([
        //                 "select" => ["FIORD_ID"],
        //                 "table_name" => "FIORD",
        //                 "where" => [
        //                     [
        //                         "field_name" => "FIORD_NAME",
        //                         "operator" => "=",
        //                         "value" => $file_name,
        //                     ]
        //                 ],
        //             ], true);
        //             $insert_lgdqr_data = [
        //                 "LGDQR_MCOMP_CODE" =>  session("company_code"),
        //                 "LGDQR_MCOMP_NAME" =>  session("company_name"),
        //                 "LGDQR_MBRAN_CODE" =>  session("brand_code"),
        //                 "LGDQR_MBRAN_NAME" => session("brand_name"),
        //                 "LGDQR_TRORD_CODE" => $request->code,
        //                 "LGDQR_TRORD_QTY" => count($data_qr_zeta),
        //                 "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
        //                 "LGDQR_FIORD_NAME" => $file_name,
        //                 "LGDQR_FIORD_ORIENTATION" => $orientation_code,
        //                 "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
        //                 "LGDQR_FIORD_SIZE" => $request->QR_SIZE,
        //                 "LGDQR_FIORD_UNIT" => "mm",
        //                 "LGDQR_CREATED_BY" => session("user_id"),
        //                 "LGDQR_CREATED_TEXT" => session("user_name"),
        //                 "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             ];
        //             $insert_lgdqr = std_insert([
        //                 "table_name" => "LGDQR",
        //                 "data" => $insert_lgdqr_data
        //             ]);
        //         }
        //     } catch (\Throwable $th) {
        //         return response()->json([
        //             'message' => "Cannot save pdf in the location"
        //         ], 500);
        //     }
        // }
        // return $pdf->download($file_name);
    }

    public function sticker_code(Request $request)
    {
        $orientation_code = "ptr";
        $qr_type = "bridge";
        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . session("brand_code") . "_" . $orientation_code . "_" . $qr_type . ".pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
         // $upload_dir = "public/storage/file/qr_file/";
         $upload_dir = "storage/file/qr_file/"; //localhostonly
        $data_sticker_code = std_get([
            "select" => ["MASCO_CODE", "MASCO_MBRAN_TEXT", "MASCO_COUNTER"],
            "table_name" => "MASCO",
            "where" => [
                [
                    "field_name" => "MASCO_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $data_qr_master = std_get([
            "select" => ["TRORD_SIZE","TRORD_ORIENTATION"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ],
        ]);
        $qr_size = round($data_qr_master[0]["TRORD_SIZE"] * 3.333333333333333); //mm to pixel
        $orientation = $data_qr_master[0]["TRORD_ORIENTATION"];
        if ($orientation == "portrait") {
            $orientation_code = "ptr";
        } else {
            $orientation_code = "lns";
        }
        $data_qr_generated = std_get([
            "select" => ["*"],
            "table_name" => "FIORD",
            "where" => [
                [
                    "field_name" => "FIORD_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "FIORD_QR_TYPE",
                    "operator" => "=",
                    "value" => $qr_type,
                ],
                // [
                //     "field_name" => "FIORD_QR_TYPE",
                //     "operator" => "=",
                //     "value" => $qr_type,
                // ],
                // [
                //     "field_name" => "FIORD_SIZE",
                //     "operator" => "=",
                //     "value" => $data_qr_master[0]["TRORD_SIZE"],
                // ],
                // [
                //     "field_name" => "FIORD_STATUS",
                //     "operator" => "=",
                //     "value" => 0,
                // ]
            ],
        ]);
        // dd($data_qr_generated);
        if ($data_qr_generated != null) {
            $file_address = $upload_dir . $data_qr_generated[0]["FIORD_NAME"];
            if (file_exists($file_address)) {
                // fungsi download klo ada
                header("Content-Type: application/pdf");
                return readfile($file_address);
            }else{
                return response()->json([
                    'message' => "There is no QR generated, please generate qr"
                ], 500);
            }
        }else{
            return response()->json([
                'message' => "There is no QR generated, please generate qr"
            ], 500);
        }
        // if ($data_sticker_code[0]["MASCO_COUNTER"] == 1) {
        //     $raw_color = '#0d19ff';
        //     $color = $this->hex_rgb('#0d19ff');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 2) {
        //     $raw_color = '#02bfe4';
        //     $color = $this->hex_rgb('#02bfe4');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 3) {
        //     $raw_color = '#397274';
        //     $color = $this->hex_rgb('#397274');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 4) {
        //     $raw_color = '#22ff38';
        //     $color = $this->hex_rgb('#22ff38');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 5) {
        //     $raw_color = '#ff9100';
        //     $color = $this->hex_rgb('#ff9100');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 6) {
        //     $raw_color = '#02e1e4';
        //     $color = $this->hex_rgb('#02e1e4');
        // } elseif ($data_sticker_code[0]["MASCO_COUNTER"] == 7) {
        //     $raw_color = '#d4af37';
        //     $color = $this->hex_rgb('#d4af37');
        // }

        // PDF::setOptions(['defaultFont' => 'arial']);
        // for ($i = 0; $i < count($data_sticker_code); $i++) {
        //     $sticker_code[$i]["qr_code"] = substr($data_sticker_code[$i]["MASCO_CODE"], 13);
        //     // $sticker_code[$i]["image"] = base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->color($color[0],$color[1],$color[2])->generate($data_sticker_code[$i]["MASCO_CODE"]));
        //     $sticker_code[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->generate($data_sticker_code[$i]["MASCO_CODE"]));
        // }
        // shuffle($sticker_code);
        // if ($orientation == "portrait") {
        //     $orientation_code = "ptr";
        //     $pdf = PDF::loadView('transaction/order_qr/sticker_code', [
        //         "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
        //         "sticker_code" => $sticker_code,
        //         // "color" => $raw_color,
        //         "color" => "#000000",
        //         "qr_size" => $qr_size
        //     ]);
        // } else {
        //     $orientation_code = "lns";
        //     $pdf = PDF::loadView('transaction/order_qr/qr_sticker_code_landscape', [
        //         "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
        //         "sticker_code" => $sticker_code,
        //         // "color" => $raw_color,
        //         "color" => "#000000",
        //         "qr_size" => $qr_size
        //     ]);
        // }
        // $pdf->setPaper('a3', 'portrait');
        // if (!is_writable($upload_dir)) {
        //     return response()->json([
        //         'message' => "Storage error, please check existing location"
        //     ], 500);
        // } else {
        //     try {
        //         $pdf->save($upload_dir . '/' . $file_name);
        //         $insert_fiord_data = [
        //             "FIORD_MCOMP_CODE" =>  session("company_code"),
        //             "FIORD_MCOMP_NAME" =>  session("company_name"),
        //             "FIORD_MBRAN_CODE" =>  session("brand_code"),
        //             "FIORD_MBRAN_NAME" => session("brand_name"),
        //             "FIORD_TRORD_CODE" => $request->code,
        //             "FIORD_TRORD_QTY" => count($data_sticker_code),
        //             "FIORD_NAME" => $file_name,
        //             "FIORD_ORIENTATION" => $orientation_code,
        //             "FIORD_QR_TYPE" =>  $qr_type,
        //             "FIORD_SIZE" => $request->QR_SIZE,
        //             "FIORD_UNIT" => "mm",
        //             "FIORD_STATUS" => 0, //0 ok, 1 deleted
        //             "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
        //             "FIORD_CREATED_BY" => session("user_id"),
        //             "FIORD_CREATED_TEXT" => session("user_name"),
        //             "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //         ];
        //         $insert_fiord = std_insert([
        //             "table_name" => "FIORD",
        //             "data" => $insert_fiord_data
        //         ]);
        //         if ($insert_fiord) {
        //             // get id fiord
        //             $data_fiord = std_get([
        //                 "select" => ["FIORD_ID"],
        //                 "table_name" => "FIORD",
        //                 "where" => [
        //                     [
        //                         "field_name" => "FIORD_NAME",
        //                         "operator" => "=",
        //                         "value" => $file_name,
        //                     ]
        //                 ],
        //             ], true);
        //             $insert_lgdqr_data = [
        //                 "LGDQR_MCOMP_CODE" =>  session("company_code"),
        //                 "LGDQR_MCOMP_NAME" =>  session("company_name"),
        //                 "LGDQR_MBRAN_CODE" =>  session("brand_code"),
        //                 "LGDQR_MBRAN_NAME" => session("brand_name"),
        //                 "LGDQR_TRORD_CODE" => $request->code,
        //                 "LGDQR_TRORD_QTY" => count($data_sticker_code),
        //                 "LGDQR_FIORD_ID" => $data_fiord[0]["FIORD_ID"],
        //                 "LGDQR_FIORD_NAME" => $file_name,
        //                 "LGDQR_FIORD_ORIENTATION" => $orientation_code,
        //                 "LGDQR_FIORD_QR_TYPE" =>  $qr_type,
        //                 "LGDQR_FIORD_SIZE" => $request->QR_SIZE,
        //                 "LGDQR_FIORD_UNIT" => "mm",
        //                 "LGDQR_CREATED_BY" => session("user_id"),
        //                 "LGDQR_CREATED_TEXT" => session("user_name"),
        //                 "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        //             ];
        //             $insert_lgdqr = std_insert([
        //                 "table_name" => "LGDQR",
        //                 "data" => $insert_lgdqr_data
        //             ]);
        //         }
        //     } catch (\Throwable $th) {
        //         return response()->json([
        //             'message' => "Cannot save pdf in the location"
        //         ], 500);
        //     }
        // }
        // return $pdf->download($file_name);
    }

    public function download_qr(Request $request)
    {
        $check_exist = std_get([
            "select" => ["*"],
            "table_name" => "FIORD",
            "where" => [
                [
                    "field_name" => "FIORD_TRORD_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "FIORD_QR_TYPE",
                    "operator" => "=",
                    "value" => $request->qr_type,
                ]
            ],
            "first_row" => true
        ]);

        if ($check_exist == null) {
            return response()->json([
                "message"=>"File not ready"
            ], 500);
        }
        if ($check_exist["FIORD_STATUS"] == "1") {
            return response()->json([
                "message"=>"File deleted"
            ], 500);
        }

        $insert_lgdqr_data = [
            "LGDQR_MCOMP_CODE" =>  session("company_code"),
            "LGDQR_MCOMP_NAME" =>  session("company_name"),
            "LGDQR_MBRAN_CODE" =>  session("brand_code"),
            "LGDQR_MBRAN_NAME" => session("brand_name"),
            "LGDQR_TRORD_CODE" => $check_exist["FIORD_TRORD_CODE"],
            "LGDQR_TRORD_QTY" => $check_exist["FIORD_TRORD_QTY"],
            "LGDQR_FIORD_ID" => $check_exist["FIORD_ID"],
            "LGDQR_FIORD_NAME" => $check_exist["FIORD_NAME"],
            "LGDQR_FIORD_ORIENTATION" => $check_exist["FIORD_ORIENTATION"],
            "LGDQR_FIORD_QR_TYPE" =>  $check_exist["FIORD_QR_TYPE"],
            "LGDQR_FIORD_SIZE" => $check_exist["FIORD_SIZE"],
            "LGDQR_FIORD_UNIT" => $check_exist["FIORD_UNIT"],
            "LGDQR_CREATED_BY" => session("user_id"),
            "LGDQR_CREATED_TEXT" => session("user_name"),
            "LGDQR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lgdqr = std_insert([
            "table_name" => "LGDQR",
            "data" => $insert_lgdqr_data
        ]);

        return response()->json([
            "file" => url("storage/file/qr_file/".$check_exist["FIORD_NAME"])
        ], 200);
    }
}
