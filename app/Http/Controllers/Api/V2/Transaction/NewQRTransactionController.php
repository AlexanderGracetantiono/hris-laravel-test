<?php

namespace App\Http\Controllers\Api\V2\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Count;

class NewQRTransactionController extends Controller
{
    public function post(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "qr_code_alpha" => "required",
            "qr_code_zeta" => "required",
            "partial_type" => "required",
            "customer_code" => "required",
            "customer_name" => "required",
            "latitude" => "required",
            "longitude" => "required",
            // "country" => "required",
			// "province" => "required",
			// "regency" => "required",
			// "district" => "required",
			// "address" => "required",
            "app_version" => "required",
            "device_id" => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->all(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        } 

        if ($request->latitude == null || $request->longitude == null) {
            return response()->json([
                "message" => "Location required",
                "data" => $request->all(),
                "err_code" => "E6"
            ], 400);
        }

        $data_alpha = std_get([
            "table_name" => "TRQRA",
            "select" => ["*"],
            "where" => [
                [
                    "field_name" => "TRQRA_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_alpha
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
                    "value" => $request->qr_code_zeta
                ],
            ],
            "first_row" => true
        ]);

        if ($data_alpha == null || $data_zeta == null) {
            $this->check_type_qr($request->all());

            return response()->json([
                'message' => "QR Not Found",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        if ($data_alpha["TRQRA_ACCEPTED_BY_STORE"] != 1 || $data_zeta["TRQRZ_ACCEPTED_BY_STORE"] != 1) {
            return response()->json([
                'message' => "QR Not Recognized",
                'data' => $request->all(),
                'err_code' => "E2"
            ], 400);
        }

        if ($data_alpha["TRQRA_MASCO_CODE"] != $data_zeta["TRQRZ_MASCO_CODE"]) {
            $this->check_type_qr($request->all());
            
            return response()->json([
                'message' => "Fake QR",
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        $get_company_brand_detail = std_get([
            "table_name" => "MASCO",
            "select" => [
                "MASCO_MCOMP_CODE",
                "MASCO_MCOMP_TEXT",
                "MASCO_MBRAN_CODE",
                "MASCO_MBRAN_TEXT",
            ],
            "where" => [
                [
                    "field_name" => "MASCO_CODE",
                    "operator" => "=",
                    "value" => $data_alpha["TRQRA_MASCO_CODE"],
                ]
            ],
            "first_row" => true
        ]);
        if ($get_company_brand_detail == null) {
            return response()->json([
                'message' => "Sticker code doesn't have any company or brand integrated",
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        //Brand Checking
        $check_brand_is_partner = std_get([
            "table_name" => "MBRAN",
            "select" => [
                "MBRAN_CODE",
                "MBRAN_NAME",
                "MBRAN_IS_DELETED",
                "MBRAN_STATUS"
            ],
            "where" => [
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" =>  $get_company_brand_detail["MASCO_MBRAN_CODE"],
                ]
            ],
            "first_row" => true
        ]);
        if ($check_brand_is_partner["MBRAN_IS_DELETED"] == 1) {
            return response()->json([
                'message' => "Brand no longer part of cekori",
                'brand' => $get_company_brand_detail["MASCO_MBRAN_TEXT"],
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }
        if ($check_brand_is_partner["MBRAN_STATUS"] != 1) {
            return response()->json([
                'message' => "CekOri no longer manage brand",
                'brand' => $check_brand_is_partner["MBRAN_NAME"],
                'data' => $request->all(),
                'err_code' => "E5"
            ], 400);
        }

        $update_transaction_alpha = std_update([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_alpha
                ],
            ],
            "data" => [
                "TRQRA_COUNTER" => $data_alpha["TRQRA_COUNTER"] + 1,
                "TRQRA_CST_SCAN_BY" => $request->customer_code,
                "TRQRA_CST_SCAN_TEXT" => $request->customer_name,
                "TRQRA_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TRQRA_CST_SCAN_LAT" => $request->latitude,
                "TRQRA_CST_SCAN_LNG" => $request->longitude,
                "TRQRA_CST_SCAN_DEVICE_ID" => $request->device_id,
                "TRQRA_CST_SCAN_APP_VERSION" => $request->app_version,
            ]
        ]);

        $update_transaction_zeta = std_update([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request->qr_code_zeta
                ],
            ],
            "data" => [
                "TRQRZ_COUNTER" => $data_zeta["TRQRZ_COUNTER"] + 1,
                "TRQRZ_CST_SCAN_BY" => $request->customer_code,
                "TRQRZ_CST_SCAN_TEXT" => $request->customer_name,
                "TRQRZ_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TRQRZ_CST_SCAN_LAT" => $request->latitude,
                "TRQRZ_CST_SCAN_LNG" => $request->longitude,
                "TRQRZ_CST_SCAN_DEVICE_ID" => $request->device_id,
                "TRQRZ_CST_SCAN_APP_VERSION" => $request->app_version,
            ]
        ]);

        $scan_header = std_get([
            "table_name" => "SCHED",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "SCHED_TRQRA_CODE",
                    "operator" => "=",
                    "value" => $request["qr_code_alpha"]
                ],
                [
                    "field_name" => "SCHED_TRQRZ_CODE",
                    "operator" => "=",
                    "value" => $request["qr_code_zeta"]
                ],
            ],
            "order_by" => [
                [
                    "field" => "SCHED_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => true
        ]);

		std_update([
			"table_name" => "SCHED",
			"where" => [
				"SCHED_ID" => $scan_header["SCHED_ID"]
			],
			"data" => [
				"SCHED_COUNTER" => $scan_header["SCHED_COUNTER"] + 1
			]
		]);
		
		std_insert([
			"table_name" => "SCLOG",
			"data" => [
				"SCLOG_SCHED_ID" => $scan_header["SCHED_ID"],
				"SCLOG_CST_SCAN_BY" => $request["customer_code"],
				"SCLOG_CST_SCAN_TEXT" => $request["customer_name"],
				"SCLOG_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
				"SCLOG_CST_SCAN_LAT" => $request["latitude"],
				"SCLOG_CST_SCAN_LNG" => $request["longitude"],
				// "SCLOG_CST_SCAN_COUNTRY" => $request["country"],
				// "SCLOG_CST_SCAN_PROVINCE" => $request["province"],
				// "SCLOG_CST_SCAN_REGENCY" => $request["regency"],
				// "SCLOG_CST_SCAN_DISTRICT" => $request["district"],
				// "SCLOG_CST_SCAN_ADDRESS" => $request["address"],
				"SCLOG_CST_SCAN_DEVICE_ID" => $request["device_id"],
				"SCLOG_CST_SCAN_APP_VERSION" => $request["app_version"],
				"SCLOG_CREATED_BY" => $request["customer_code"],
				"SCLOG_CREATED_TEXT" => $request["customer_name"],
				"SCLOG_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
			]
		]);

        $pic_brand = get_master_employee(["MAEMP_TEXT","MAEMP_EMAIL"],[
            [
                "field_name" => "MAEMP_MBRAN_CODE",
                "operator" => "=",
                "value" => $check_brand_is_partner["MBRAN_CODE"]
            ],
            [
                "field_name" => "MAEMP_ROLE",
                "operator" => "=",
                "value" => "3"
            ],
        ]);

        $repot_scan = $this->report_scan($scan_header,$request->all());

        return response()->json([
            "report_scan" => $repot_scan,
            "email_pic_brand" => $pic_brand[0]["MAEMP_EMAIL"] 
        ], 200);
    }

    public function report_scan($scan_header,$request)
    {
        $current_scan = $this->current_scan($scan_header,$request);
        
        $first_scan = null;
        $first_scan = $this->first_scan($scan_header,$request);

        $latest_scan = null;
        if ($scan_header["SCHED_COUNTER"] + 1 > 2) {
            $latest_scan = $this->latest_scan($scan_header,$request);
        }

        $report_scan_en = [
            "paragraph_1" => $current_scan["EN"]["paragraph_1"],
            "paragraph_1_lat" => $current_scan["EN"]["paragraph_1_lat"],
            "paragraph_1_long" => $current_scan["EN"]["paragraph_1_long"],
            "paragraph_2_part_1" => "",
            "paragraph_2_part_1_lat" => "",
            "paragraph_2_part_1_long" => "",
            "paragraph_2_part_2" => "",
            "paragraph_2_part_2_lat" => "",
            "paragraph_2_part_2_long" => "",
        ];

        $report_scan_id = [
            "paragraph_1" => $current_scan["ID"]["paragraph_1"],
            "paragraph_1_lat" => $current_scan["ID"]["paragraph_1_lat"],
            "paragraph_1_long" => $current_scan["ID"]["paragraph_1_long"],
            "paragraph_2_part_1" => "",
            "paragraph_2_part_1_lat" => "",
            "paragraph_2_part_1_long" => "",
            "paragraph_2_part_2" => "",
            "paragraph_2_part_2_lat" => "",
            "paragraph_2_part_2_long" => "",
        ];

        $report_scan_en["paragraph_2_part_1"] = $first_scan["EN"]["paragraph_2_part_1"];
        $report_scan_en["paragraph_2_part_1_lat"] = $first_scan["EN"]["paragraph_2_part_1_lat"];
        $report_scan_en["paragraph_2_part_1_long"] = $first_scan["EN"]["paragraph_2_part_1_long"];

        $report_scan_id["paragraph_2_part_1"] = $first_scan["ID"]["paragraph_2_part_1"];
        $report_scan_id["paragraph_2_part_1_lat"] = $first_scan["ID"]["paragraph_2_part_1_lat"];
        $report_scan_id["paragraph_2_part_1_long"] = $first_scan["ID"]["paragraph_2_part_1_long"];

        if ($latest_scan != null) {
            $report_scan_en["paragraph_2_part_2"] = $latest_scan["EN"]["paragraph_2_part_2"];
            $report_scan_en["paragraph_2_part_2_lat"] = $latest_scan["EN"]["paragraph_2_part_2_lat"];
            $report_scan_en["paragraph_2_part_2_long"] = $latest_scan["EN"]["paragraph_2_part_2_long"];

            $report_scan_id["paragraph_2_part_2"] = $latest_scan["ID"]["paragraph_2_part_2"];
            $report_scan_id["paragraph_2_part_2_lat"] = $latest_scan["ID"]["paragraph_2_part_2_lat"];
            $report_scan_id["paragraph_2_part_2_long"] = $latest_scan["ID"]["paragraph_2_part_2_long"];
        }

		return ([
			"EN" => $report_scan_en,
			"ID" => $report_scan_id
        ]);
    }

    public function current_scan($scan_header,$request)
    {
        $total_scanned = $scan_header["SCHED_COUNTER"] + 1;
        $ordinal_total_scanned = $this->ordinal_number($total_scanned);
        
        $paragraph_en = [
            "paragraph_1" => "Currently this is the {$ordinal_total_scanned} Scan of this product which is done at ".date("jS F Y").", ".date("H:i")." and located in",
            "paragraph_1_lat" => $request["latitude"],
            "paragraph_1_long" => $request["longitude"],
        ];

        setlocale(LC_ALL, 'IND');
        $paragraph_id = [
            "paragraph_1" => "Produk ini telah dipasangkan dan didaftarkan dalam database kami sebagai produk Asli! Ini merupakan scan ke {$total_scanned} Scan produk ini dilakukan pada ".strftime("%e %B %Y").", ".strftime("%H:%M")." dan berlokasi di",
            "paragraph_1_lat" => $request["latitude"],
            "paragraph_1_long" => $request["longitude"],
        ];

        return ([
            "EN" => $paragraph_en,
            "ID" => $paragraph_id,
        ]);
    }

    public function first_scan($scan_header,$request)
    {
        $data_log = std_get([
            "table_name" => "SCLOG",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header["SCHED_ID"]
                ],
            ],
            "order_by" => [
                [
                    "field" => "SCLOG_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => true
        ]);

        $is_first_scanner = [
            "EN" => "other",
            "ID" => "lain"
        ];
        if ($request["customer_code"] == $data_log["SCLOG_CST_SCAN_BY"]) {
            $is_first_scanner = [
                "EN" => "your",
                "ID" => "Anda"
            ];
        }

        $paragraph_en = [
            "paragraph_2_part_1" => "The First Authentication Scan product was by {$is_first_scanner['EN']} CekOri Account ID which is done at ". date('jS F Y, H:i', strtotime($data_log['SCLOG_CST_SCAN_TIMESTAMP']))." and located in",
            "paragraph_2_part_1_lat" => $data_log['SCLOG_CST_SCAN_LAT'],
            "paragraph_2_part_1_long" => $data_log['SCLOG_CST_SCAN_LNG'],
        ];

        setlocale(LC_ALL, 'IND');
        $paragraph_id = [
            "paragraph_2_part_1" => "Scan Keaslian Pertama produk dilakukan oleh CekOri Account ID {$is_first_scanner['ID']} yang dilakukan pada ". strftime('%e %B %Y, %H:%M', strtotime($data_log['SCLOG_CST_SCAN_TIMESTAMP']))." dan berlokasi di",
            "paragraph_2_part_1_lat" => $data_log['SCLOG_CST_SCAN_LAT'],
            "paragraph_2_part_1_long" => $data_log['SCLOG_CST_SCAN_LNG'],
        ];

        return ([
            "EN" => $paragraph_en,
            "ID" => $paragraph_id,
        ]);
    }

    public function latest_scan($scan_header,$request)
    {
        $latest_scan = $scan_header["SCHED_COUNTER"];

        $data_log = std_get([
            "table_name" => "SCLOG",
            "select" => "*",
            "order_by" => [
                [
                    "field" => "SCLOG_ID",
                    "type" => "ASC",
                ]
            ],
			"offset" => $latest_scan - 1,
            "first_row" => true
        ]);

        $ordinal_latest_scan = $this->ordinal_number($latest_scan);

        $is_latest_scanner = [
            "EN" => "different",
            "ID" => "berbeda"
        ];
        if ($request["customer_code"] == $data_log["SCLOG_CST_SCAN_BY"]) {
            $is_latest_scanner = [
                "EN" => "same",
                "ID" => "sama"
            ];
        }
        
        $paragraph_en["paragraph_2_part_2"] = "The latest scan which is the ".($ordinal_latest_scan)." scan was done by the {$is_latest_scanner['EN']} CekOri Account ID as the first scan at ". date('jS F Y, H:i', strtotime($data_log['SCLOG_CST_SCAN_TIMESTAMP']))." and located in";
        $paragraph_en["paragraph_2_part_2_lat"] = $data_log['SCLOG_CST_SCAN_LAT'];
        $paragraph_en["paragraph_2_part_2_long"] = $data_log['SCLOG_CST_SCAN_LNG'];

        setlocale(LC_ALL, 'IND');
        $paragraph_id["paragraph_2_part_2"] = "Scan terbaru yang dilakukan yaitu scan ke ".($latest_scan)." dilakukan oleh CekOri Account ID {$is_latest_scanner['ID']} dengan scan pertama, diakukan pada ". strftime('%e %B %Y, %H:%M', strtotime($data_log['SCLOG_CST_SCAN_TIMESTAMP']))." dan berlokasi di";
        $paragraph_id["paragraph_2_part_2_lat"] = $data_log['SCLOG_CST_SCAN_LAT'];
        $paragraph_id["paragraph_2_part_2_long"] = $data_log['SCLOG_CST_SCAN_LNG'];

        return ([
            "EN" => $paragraph_en,
            "ID" => $paragraph_id,
        ]);
    }

    public function ordinal_number($number)
    {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if (($number %100) >= 11 && ($number%100) <= 13) {
            $abbreviation = $number. 'th';
        }
        else {
            $abbreviation = $number. $ends[$number % 10];
        }

        return $abbreviation;
    }

    public function get(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "app_version" => "required|max:255",
            "app_type" => "required|max:255",
            "os_type" => "required|max:255",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors(),
                "data" => $request->all(),
            ], 400);
        } else {

            $data = std_get([
                "table_name" => "MAVER",
                "select" => ["*"],
                "where" => [
                    [
                        "field_name" => "MAVER_APP_VERSION",
                        "operator" => "=",
                        "value" => $request->app_version
                    ],
                    [
                        "field_name" => "MAVER_APP_TYPE",
                        "operator" => "=",
                        "value" => $request->app_type
                    ],
                    [
                        "field_name" => "MAVER_OS_TYPE",
                        "operator" => "=",
                        "value" => $request->os_type
                    ],
                ],
                "first_row" => true
            ]);

            if ($data == null) {
                return response()->json([
                    'message' => "Update your application to latest version"
                ], 200);
            } else {
                return response()->json([
                    'message' => "Your application has the latest version"
                ], 404);
            }
        }
    }

    public function check_type_qr($request)
	{
		$reponse = null;

		$scan_header_alpha = std_get([
			"table_name" => "SCHED",
			"select" => ["SCHED_ID","SCHED_MBRAN_CODE","SCHED_MBRAN_NAME","SCHED_MCOMP_CODE","SCHED_MCOMP_NAME"],
			"where" => [
				[
					"field_name" => "SCHED_TRQRA_CODE",
					"operator" => "=",
					"value" => $request["partial_type"],
				],
			],
			"first_row" => true
		]);

		$scan_header_zeta = std_get([
			"table_name" => "SCHED",
			"select" => ["SCHED_ID","SCHED_MBRAN_CODE","SCHED_MBRAN_NAME","SCHED_MCOMP_CODE","SCHED_MCOMP_NAME"],
			"where" => [
				[
					"field_name" => "SCHED_TRQRZ_CODE",
					"operator" => "=",
					"value" => $request["partial_type"],
				],
			],
			"first_row" => true
		]);

		if ($scan_header_alpha != null) {
            $company_code = $scan_header_alpha["SCHED_MCOMP_CODE"];
            $company_name = $scan_header_alpha["SCHED_MCOMP_NAME"];

            $brand_code = $scan_header_alpha["SCHED_MBRAN_CODE"];
            $brand_name = $scan_header_alpha["SCHED_MBRAN_NAME"];

            $scan_detail = get_scan_detail("*",[
                [
                    "field_name" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header_alpha["SCHED_ID"],
                ]
            ],true);

		} elseif ($scan_header_zeta != null) {
            $company_code = $scan_header_zeta["SCHED_MCOMP_CODE"];
            $company_name = $scan_header_zeta["SCHED_MCOMP_NAME"];

            $brand_code = $scan_header_zeta["SCHED_MBRAN_CODE"];
            $brand_name = $scan_header_zeta["SCHED_MBRAN_NAME"];

            $scan_detail = get_scan_detail("*",[
                [
                    "field_name" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header_zeta["SCHED_ID"],
                ]
            ],true);
		}

        std_insert([
            "table_name" => "LGSCN",
            "data" => [
                "LGSCN_TRQRA_CODE" => $request["qr_code_alpha"],
                "LGSCN_TRQRZ_CODE" => $request["qr_code_zeta"],
                "LGSCN_MPRCA_CODE" => $scan_detail["SCDET_MPRCA_CODE"],
                "LGSCN_MPRCA_TEXT" => $scan_detail["SCDET_MPRCA_TEXT"],
                "LGSCN_MPRDT_CODE" => $scan_detail["SCDET_MPRDT_CODE"],
                "LGSCN_MPRDT_TEXT" => $scan_detail["SCDET_MPRDT_TEXT"],
                "LGSCN_MPRMO_CODE" => $scan_detail["SCDET_MPRMO_CODE"],
                "LGSCN_MPRMO_TEXT" => $scan_detail["SCDET_MPRMO_TEXT"],
                "LGSCN_MPRVE_CODE" => $scan_detail["SCDET_MPRVE_CODE"],
                "LGSCN_MPRVE_TEXT" => $scan_detail["SCDET_MPRVE_TEXT"],
                "LGSCN_MPRVE_SKU" => $scan_detail["SCDET_MPRVE_SKU"],
                "LGSCN_MPRVE_NOTES" => $scan_detail["SCDET_MPRVE_NOTES"],
                "LGSCN_CST_SCAN_BY" => $request["customer_code"],
                "LGSCN_CST_SCAN_TEXT" => $request["customer_name"],
                "LGSCN_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
                "LGSCN_CST_SCAN_LAT" => $request["latitude"],
                "LGSCN_CST_SCAN_LNG" => $request["longitude"],
                // "LGSCN_CST_SCAN_COUNTRY" => ,
                // "LGSCN_CST_SCAN_PROVINCE" => ,
                // "LGSCN_CST_SCAN_REGENCY" => ,
                // "LGSCN_CST_SCAN_DISTRICT" => ,
                // "LGSCN_CST_SCAN_ADDRESS" => ,
                "LGSCN_CST_SCAN_DEVICE_ID" => $request["device_id"],
                "LGSCN_CST_SCAN_APP_VERSION" => $request["app_version"],
                "LGSCN_MCOMP_CODE" => $company_code,
                "LGSCN_MCOMP_NAME" => $company_name,
                "LGSCN_MBRAN_CODE" => $brand_code,
                "LGSCN_MBRAN_NAME" => $brand_name,
            ]
        ]);

		return;
	}

}
