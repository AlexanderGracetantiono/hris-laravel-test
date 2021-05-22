<?php

namespace App\Http\Controllers\MasterData\SubBatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App;
use QrCode;
use Illuminate\Support\Facades\Crypt;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }
    
    public function index()
    {
        $data = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "SUBPA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
        ]);

        if ($data != null) {
            for ($i=0; $i < count($data); $i++) {
                $count_paired_qr[$i] = std_get([
                    "table_name" => "TRQRZ",
                    "where" => [
                        [
                            "field_name" => "TRQRZ_SUBPA_CODE",
                            "operator" => "=",
                            "value" => $data[$i]["SUBPA_CODE"],
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

                $data[$i]["paired_qr"] = $count_paired_qr[$i];
            }
        }
        
        return view('master_data/sub_batch_packaging/view', ['data' => $data]);
    }

    public function scanned_qr(Request $request)
    {
        $data_sub_batch = get_master_sub_batch_packaging("*", [
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        if ($data_sub_batch["SUBPA_DISCREPANCY_PRODUCT"] == null) {
            $data_sub_batch["SUBPA_DISCREPANCY_PRODUCT"] =0;
        }
        if ($data_sub_batch["SUBPA_DISCREPANCY_TRQRZ"] == null) {
            $data_sub_batch["SUBPA_DISCREPANCY_TRQRZ"] =0;
        }
        if ($data_sub_batch["SUBPA_DISCREPANCY_MASCO"] == null) {
            $data_sub_batch["SUBPA_DISCREPANCY_MASCO"] =0;
        }

        $data_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code")
            ],
            [
                "field_name" => "MAEMP_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
            [
                "field_name" => "MAEMP_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MAEMP_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MAEMP_ACTIVATION_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MAEMP_ROLE",
                "operator" => "=",
                "value" => "8"
            ],
        ]);

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
                    "value" => $request->code,
                ]
            ]
        ]);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TRQRZ_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $qr_code = Crypt::encrypt([
            "code" => $request->code,
            "type" => "2"
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        return view('master_data/sub_batch_packaging/scanned_qr', [
            'data_sub_batch' => $data_sub_batch,
            'data_employee' => $data_employee,
            'pool_product' => $pool_product,
            'data_qr' => $data_qr,
            'link_qr' => $link_qr,
            'count_paired_qr' => $count_paired_qr,
        ]);
    }

    public function scanned_qr_closed(Request $request)
    {
        $data_sub_batch = get_master_sub_batch_packaging("*", [
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request->code,
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
                    "value" => $request->code,
                ]
            ]
        ]);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TRQRZ_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $count_rejected_sticker = std_get([
            "table_name" => "MASCO",
            "where" => [
                [
                    "field_name" => "MASCO_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "MASCO_STATUS",
                    "operator" => "=",
                    "value" => 3,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $qr_code = Crypt::encrypt([
            "code" => $request->code,
            "type" => "2"
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        return view('master_data/sub_batch_packaging/scanned_qr_closed', [
            'data_sub_batch' => $data_sub_batch,
            'pool_product' => $pool_product,
            'data_qr' => $data_qr,
            'link_qr' => $link_qr,
            'count_paired_qr' => $count_paired_qr,
            'count_rejected_sticker' => $count_rejected_sticker,
        ]);
    }

    public function delivery_note(Request $request)
    {
        $data = std_get([
            "table_name" => "SUBPA",
            "select" => ["SUBPA.*","POPRD.*","MAPLA_ADDRESS"],
            "where" => [
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ]
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "POPRD",
                    "on1" => "SUBPA_POPRD_CODE",
                    "operator" => "=",
                    "on2" => "POPRD_CODE",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "MAPLA",
                    "on1" => "SUBPA_MAPLA_CODE",
                    "operator" => "=",
                    "on2" => "MAPLA_CODE",
                ],
            ],
            "first_row" => true
        ]);

        $brand_logo = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_MBRAN_CODE"],
            ]
        ],true);

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TRQRZ_STATUS",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $from_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_CODE",
                "operator" => "=",
                "value" => $data["SUBPA_CREATED_BY"],
            ]
        ],true);

        $qr_code = Crypt::encrypt([
            "code" => $data["SUBPA_CODE"],
            "type" => "2"
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        PDF::setOptions(['defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('master_data/sub_batch_packaging/delivery_note', [
            "data" => $data,
            "count_paired_qr" => $count_paired_qr,
            "from_employee" => $from_employee,
            "brand_logo" => $brand_logo["MBRAN_IMAGE"],
            "qr_image" => base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->generate($link_qr)),
        ]);
        return $pdf->download('delivery_note.pdf');
    }
}
