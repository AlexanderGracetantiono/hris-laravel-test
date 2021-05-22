<?php

namespace App\Http\Controllers\MasterData\BatchProduction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;
use App;
use QrCode;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function index()
    {
        $data = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MABPR_MBRAN_CODE",
                "operator" => "=",
                "value" => session('brand_code')
            ],
        ]);

        if ($data != null) {
            for ($i=0; $i < count($data); $i++) {
                $count_paired_qr[$i] = std_get([
                    "table_name" => "TRQRA",
                    "where" => [
                        [
                            "field_name" => "TRQRA_MABPR_CODE",
                            "operator" => "=",
                            "value" => $data[$i]["MABPR_CODE"],
                        ],
                        [
                            "field_name" => "TRQRA_MBRAN_CODE",
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

        return view('master_data/batch_production/view', ['data' => $data]);
    }

    public function scanned_qr(Request $request)
    {
        $data_production = get_master_batch_production("*", [
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        if ($data_production["MABPR_DISCREPANCY_PRODUCT"] == null) {
            $data_production["MABPR_DISCREPANCY_PRODUCT"] =0;
        }
        if ($data_production["MABPR_DISCREPANCY_TRQRA"] == null) {
            $data_production["MABPR_DISCREPANCY_TRQRA"] =0;
        }
        if ($data_production["MABPR_DISCREPANCY_MASCO"] == null) {
            $data_production["MABPR_DISCREPANCY_MASCO"] =0;
        }

        $plant_packaging = get_master_product_plant("*", [
            [
                "field_name" => "MAPLA_TYPE",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "MAPLA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ]);

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code,
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
                "TRQRA_CODE",
                "TRQRA_NOTES",
                "TRQRA_MASCO_CODE",
                "TRQRA_STATUS",
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
                    "value" => $request->code,
                ]
            ]
        );

        $qr_code = Crypt::encrypt([
            "code" => $request->code,
            "type" => "1",
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        return view('master_data/batch_production/scanned_qr', [
            'data_production' => $data_production,
            'plant_packaging' => $plant_packaging,
            'data_qr' => $data_qr,
            'count_paired_qr' => $count_paired_qr,
            "link_qr" => $link_qr,
        ]);
    }

    public function scanned_qr_closed(Request $request)
    {
        $data_production = get_master_batch_production("*", [
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code,
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

        $count_rejected_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TRQRA_STATUS",
                    "operator" => "=",
                    "value" => 3,
                ],
            ],
            "count" => true,
            "first_row" => true
        ]);

        $data_qr = get_transaction_qr_alpha(
            [
                "TRQRA_CODE",
                "TRQRA_NOTES",
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
                    "value" => $request->code,
                ]
            ]
        );

        $qr_code = Crypt::encrypt([
            "code" => $request->code,
            "type" => "1",
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        return view('master_data/batch_production/scanned_qr_closed', [
            'data_production' => $data_production,
            'data_qr' => $data_qr,
            'count_paired_qr' => $count_paired_qr,
            'count_rejected_qr' => $count_rejected_qr,
            "link_qr" => $link_qr,
        ]);
    }

    public function delivery_note(Request $request)
    {
        $data = std_get([
            "table_name" => "MABPR",
            "select" => ["MABPR.*","MABPA_ASSIGNED_EMPLOYEE_CODE","MABPA_MAPLA_CODE","MABPA_ASSIGNED_EMPLOYEE_TEXT","MAEMP_USER_NAME as production_user_account","MAPLA_ADDRESS"],
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ]
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "MABPA",
                    "on1" => "MABPA_MABPR_CODE",
                    "operator" => "=",
                    "on2" => "MABPR_CODE",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "MAEMP",
                    "on1" => "MABPR_CREATED_BY",
                    "operator" => "=",
                    "on2" => "MAEMP_CODE",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "MAPLA",
                    "on1" => "MABPR_MAPLA_CODE",
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
                "value" => $data["MABPR_MBRAN_CODE"],
            ]
        ],true);

        $from_employee = get_master_employee("*",[
            [
                "field_name" => "MAEMP_CODE",
                "operator" => "=",
                "value" => $data["MABPR_CREATED_BY"],
            ]
        ],true);

        $plant_packaging = get_master_product_plant("*",[
            [
                "field_name" => "MAPLA_CODE",
                "operator" => "=",
                "value" => $data["MABPA_MAPLA_CODE"]
            ]
        ],true);

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->code,
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

        $qr_code = Crypt::encrypt([
            "code" => $data["MABPR_CODE"],
            "type" => "1",
        ]);
        $link_qr = route("delivery_note",["id" => $qr_code]);

        PDF::setOptions(['defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('master_data/batch_production/delivery_note', [
            "data" => $data,
            "count_paired_qr" => $count_paired_qr,
            "plant_packaging" => $plant_packaging,
            "from_employee" => $from_employee,
            "brand_logo" => $brand_logo["MBRAN_IMAGE"],
            "qr_image" => base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->generate($link_qr)),
        ]);
        return $pdf->download('delivery_note.pdf');
    }
}
