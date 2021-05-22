<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScanHistoryDetail extends Controller
{
	public function index(Request $request){
        $validate = Validator::make($request->all(), [
			"log_scan_id" => "required",
		]);

		if ($validate->fails()) {
			return response()->json([
				"message" => $validate->errors()->all(),
				"data" => $request->all(),
				"err_code" => "E1"
			], 400);
        }

        $log_scan = std_get([
            "select" => "*",
            "table_name" => "SCLOG",
            "where" => [
                [
                    "field_name" => "SCLOG_ID",
                    "operator" => "=",
                    "value" => $request->log_scan_id,
                ]
            ],
            "first_row" => true
        ]);

        if ($log_scan == null) {
            return response()->json([
				"message" => "Invalid Log Scan",
				"data" => $request->all(),
				"err_code" => "E2"
			], 400);
        }

        $scan_header = get_scan_header("*",[
            [
                "field_name" => "SCHED_ID",
                "operator" => "=",
                "value" => $log_scan["SCLOG_SCHED_ID"],
            ]
        ],true);

        $current_scan = $this->current_scan($scan_header,$log_scan);
        
        $first_scan = null;
        $first_scan = $this->first_scan($scan_header,$log_scan);

        $temp_current_scan = 0;
        $total_scanned = std_get([
            "select" => "SCLOG_ID",
            "table_name" => "SCLOG",
            "order_by" => [
				[
					"field" => "SCLOG_ID",
					"type" => "ASC"
				]
			],
            "where" => [
                [
                    "field_name" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header["SCHED_ID"],
                ]
            ],
        ]);
        for ($i=0; $i < count($total_scanned); $i++) { 
            $temp_current_scan += 1;
            if ($log_scan["SCLOG_ID"] == $total_scanned[$i]["SCLOG_ID"]) {
                break;
            }
        }
        
        $latest_scan = null;
        if ($temp_current_scan > 2) {
            $latest_scan = $this->latest_scan($scan_header,$log_scan);
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

        $partial = $this->partial_information($scan_header);

        $check_brand = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $scan_header["SCHED_MBRAN_CODE"],
            ]
        ],true);

        $image = asset('storage/images/brand_logo/').'/'.$check_brand["MBRAN_IMAGE"];

        return response()->json([
            "report_scan" => [
                "EN" => $report_scan_en,
                "ID" => $report_scan_id
            ],
            "partial_information" => $partial,
            "brand_logo" => $image
        ], 200);
    }

    public function current_scan($scan_header,$log_scan)
    {
        $current_scan = 0;
        $total_scanned = std_get([
            "select" => "SCLOG_ID",
            "table_name" => "SCLOG",
            "order_by" => [
				[
					"field" => "SCLOG_ID",
					"type" => "ASC"
				]
			],
            "where" => [
                [
                    "field_name" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header["SCHED_ID"],
                ]
            ],
        ]);
        for ($i=0; $i < count($total_scanned); $i++) { 
            $current_scan += 1;
            if ($log_scan["SCLOG_ID"] == $total_scanned[$i]["SCLOG_ID"]) {
                break;
            }
        }

        $ordinal_total_scanned = $this->ordinal_number($current_scan);
        
        $paragraph_en = [
            "paragraph_1" => "Currently this is the {$ordinal_total_scanned} Scan of this product which is done at ".date('jS F Y, H:i', strtotime($log_scan['SCLOG_CST_SCAN_TIMESTAMP'])).", and located in",
            "paragraph_1_lat" => $log_scan["SCLOG_CST_SCAN_LAT"],
            "paragraph_1_long" => $log_scan["SCLOG_CST_SCAN_LNG"],
        ];

        setlocale(LC_ALL, 'IND');
        $paragraph_id = [
            "paragraph_1" => "Produk ini telah dipasangkan dan didaftarkan dalam database kami sebagai produk Asli! Ini merupakan scan ke {$current_scan} Scan produk ini dilakukan pada ".strftime('%e %B %Y, %H:%M', strtotime($log_scan['SCLOG_CST_SCAN_TIMESTAMP']))." dan berlokasi di",
            "paragraph_1_lat" => $log_scan["SCLOG_CST_SCAN_LAT"],
            "paragraph_1_long" => $log_scan["SCLOG_CST_SCAN_LNG"],
        ];

        return ([
            "EN" => $paragraph_en,
            "ID" => $paragraph_id,
        ]);
    }

    public function first_scan($scan_header,$log_scan)
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
        if ($log_scan["SCLOG_CST_SCAN_BY"] == $data_log["SCLOG_CST_SCAN_BY"]) {
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

    public function latest_scan($scan_header,$log_scan)
    {
        $current_scan = 0;
        $total_scanned = std_get([
            "select" => "SCLOG_ID",
            "table_name" => "SCLOG",
            "order_by" => [
				[
					"field" => "SCLOG_ID",
					"type" => "ASC"
				]
			],
            "where" => [
                [
                    "field_name" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "value" => $scan_header["SCHED_ID"],
                ]
            ],
        ]);
        for ($i=0; $i < count($total_scanned); $i++) { 
            $current_scan += 1;
            if ($log_scan["SCLOG_ID"] == $total_scanned[$i]["SCLOG_ID"]) {
                $latest_scan_id = $total_scanned[$i]["SCLOG_ID"];
                break;
            }
        }

        $data_log = std_get([
            "table_name" => "SCLOG",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "SCLOG_ID",
                    "operator" => "=",
                    "value" => $latest_scan_id,
                ]
            ],
            "order_by" => [
                [
                    "field" => "SCLOG_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => true
        ]);

        $latest_scan = ($current_scan - 1);
        $ordinal_latest_scan = $this->ordinal_number($current_scan - 1);

        $is_latest_scanner = [
            "EN" => "different",
            "ID" => "berbeda"
        ];
        if ($log_scan["SCLOG_CST_SCAN_BY"] == $data_log["SCLOG_CST_SCAN_BY"]) {
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

    public function partial_information($scan_header)
    {
        $check_qr = get_transaction_qr_zeta("*",[
            [
                "field_name" => "TRQRZ_CODE",
                "operator" => "=",
                "value" => $scan_header["SCHED_TRQRZ_CODE"]
            ]
        ],true);

        if ($check_qr["TRQRZ_TYPE"] == "2") {
            $attribute = $this->scan_lab_zeta($scan_header);
        } elseif ($check_qr["TRQRZ_TYPE"] == "1") {
            $attribute = $this->scan_manufacture_zeta($scan_header);
        }

        return $attribute;
    }

    public function scan_lab_zeta($scan_header)
	{
		$check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $scan_header["SCHED_MBRAN_CODE"],
            ]
        ],true);

		$scan_detail = get_scan_detail("*",[
            [
                "field_name" => "SCDET_SCHED_ID",
                "operator" => "=",
                "value" => $scan_header["SCHED_ID"],
            ]
        ]);

        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRCA_CODE"];
			}
        } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRDT_CODE"];
			}
		} elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 3) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRMO_CODE"];
			}
		} else {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRVE_CODE"];
			}
		}

		$get_general_attribute = get_product_attribute("*",[
			[
				"field_name" => "TRPAT_MBRAN_CODE",
				"operator" => "=",
				"value" => $scan_header["SCHED_MBRAN_CODE"],
			],
			[
				"field_name" => "TRPAT_KEY_TYPE",
				"operator" => "=",
				"value" => $check_product_attribute["MBRAN_TRPAT_TYPE"],
			],
			[
				"field_name" => "TRPAT_KEY_CODE",
				"operator" => "=",
				"value" => $key_code[0],
			],
			[
				"field_name" => "TRPAT_TYPE",
				"operator" => "=",
				"value" => 1,
			],
		]);

		if ($get_general_attribute == null) {
			return null;
		}

		$temp_general_attribute = [
			[
				"label" => $get_general_attribute[0]["TRPAT_MASKING"],
				"value" => $scan_header["SCHED_MBRAN_NAME"],
				"type" => "brand",
				"status" => $get_general_attribute[0]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" =>  $get_general_attribute[1]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_MAPLA_TEXT"],
				"type" => "production_center",
				"status" => $get_general_attribute[1]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" =>  $get_general_attribute[2]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_MAPLA_TEXT"],
				"type" => "packaging_center",
				"status" => $get_general_attribute[2]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[4]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRDT_TEXT"],
				"type" => "category",
				"status" => $get_general_attribute[4]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[5]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRMO_TEXT"],
				"type" => "model",
				"status" => $get_general_attribute[5]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[6]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRVE_TEXT"],
				"type" => "model",
				"status" => $get_general_attribute[6]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[7]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRVE_SKU"],
				"type" => "model",
				"status" => $get_general_attribute[7]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[8]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_SCAN_TIMESTAMP"],
				"type" => "production_date",
				"status" => $get_general_attribute[8]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[9]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_SCAN_TIMESTAMP"],
				"type" => "packaging_date",
				"status" => $get_general_attribute[9]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[10]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_STAFF_TEXT"],
				"type" => "production_staff",
				"status" => $get_general_attribute[10]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[11]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_STAFF_TEXT"],
				"type" => "packaging_staff",
				"status" => $get_general_attribute[11]["TRPAT_ACTIVE_STATUS"],
			],
		];

		for ($i=0; $i < count($temp_general_attribute); $i++) { 
			if ($temp_general_attribute[$i]["status"] == 1) {
				$general_attribute[] = $temp_general_attribute[$i];
			}
		}

		$general_attribute = array_values($general_attribute);

		for ($i=0; $i < count($scan_detail); $i++) { 
			$get_general_attribute[$i] = get_product_attribute("*",[
				[
					"field_name" => "TRPAT_MBRAN_CODE",
					"operator" => "=",
					"value" => $scan_header["SCHED_MBRAN_CODE"],
				],
				[
					"field_name" => "TRPAT_KEY_TYPE",
					"operator" => "=",
					"value" => $check_product_attribute["MBRAN_TRPAT_TYPE"],
				],
				[
					"field_name" => "TRPAT_KEY_CODE",
					"operator" => "=",
					"value" => $key_code[$i],
				],
				[
					"field_name" => "TRPAT_TYPE",
					"operator" => "=",
					"value" => 1,
				],
			]);

			$get_custom_attribute[$i] = get_product_attribute("*",[
				[
					"field_name" => "TRPAT_MBRAN_CODE",
					"operator" => "=",
					"value" => $scan_header["SCHED_MBRAN_CODE"],
				],
				[
					"field_name" => "TRPAT_KEY_TYPE",
					"operator" => "=",
					"value" => $check_product_attribute["MBRAN_TRPAT_TYPE"],
				],
				[
					"field_name" => "TRPAT_KEY_CODE",
					"operator" => "=",
					"value" => $key_code[$i],
				],
				[
					"field_name" => "TRPAT_TYPE",
					"operator" => "=",
					"value" => 2,
				],
			]);

			$general_attribute[] = [
				"label" => $get_general_attribute[$i][3]["TRPAT_MASKING"],
				"value" => $scan_detail[$i]["SCDET_MPRCA_TEXT"],
				"type" => "test_lab_type",
				"status" => $get_general_attribute[$i][3]["TRPAT_ACTIVE_STATUS"],
			];

			$general_attribute[] = [
				"label" => $get_general_attribute[$i][12]["TRPAT_MASKING"],
				"value" => $scan_detail[$i]["SCDET_MPRVE_NOTES"],
				"type" => "description",
				"status" => $get_general_attribute[$i][12]["TRPAT_ACTIVE_STATUS"],
			];
	
			for ($j=0; $j < count($get_custom_attribute[$i]); $j++) { 
				$general_attribute[] = [
					"label" => $get_custom_attribute[$i][$j]["TRPAT_MASKING"],
					"value" => $get_custom_attribute[$i][$j]["TRPAT_VALUE"],
					"type" => "custom_attribute",
					"status" => $get_custom_attribute[$i][$j]["TRPAT_ACTIVE_STATUS"],
				];
			}
		}

		$attribute = array_values($general_attribute);

		return $attribute;
	}

    public function scan_manufacture_zeta($scan_header)
	{
		$check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $scan_header["SCHED_MBRAN_CODE"],
            ]
        ],true);

		$scan_detail = get_scan_detail("*",[
            [
                "field_name" => "SCDET_SCHED_ID",
                "operator" => "=",
                "value" => $scan_header["SCHED_ID"],
            ]
        ]);

        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRCA_CODE"];
			}
        } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRDT_CODE"];
			}
		} elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 3) {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRMO_CODE"];
			}
		} else {
			for ($i=0; $i < count($scan_detail); $i++) { 
				$key_code[] = $scan_detail[$i]["SCDET_MPRVE_CODE"];
			}
		}

		$get_general_attribute = get_product_attribute("*",[
			[
				"field_name" => "TRPAT_MBRAN_CODE",
				"operator" => "=",
				"value" => $scan_header["SCHED_MBRAN_CODE"],
			],
			[
				"field_name" => "TRPAT_KEY_TYPE",
				"operator" => "=",
				"value" => $check_product_attribute["MBRAN_TRPAT_TYPE"],
			],
			[
				"field_name" => "TRPAT_KEY_CODE",
				"operator" => "=",
				"value" => $key_code[0],
			],
			[
				"field_name" => "TRPAT_TYPE",
				"operator" => "=",
				"value" => 1,
			],
		]);

		if ($get_general_attribute == null) {
			return null;
		}

		$temp_general_attribute = [
			[
				"label" => $get_general_attribute[0]["TRPAT_MASKING"],
				"value" => $scan_header["SCHED_MBRAN_NAME"],
				"type" => "brand",
				"status" => $get_general_attribute[0]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" =>  $get_general_attribute[1]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_MAPLA_TEXT"],
				"type" => "production_center",
				"status" => $get_general_attribute[1]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" =>  $get_general_attribute[2]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_MAPLA_TEXT"],
				"type" => "packaging_center",
				"status" => $get_general_attribute[2]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[3]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRCA_TEXT"],
				"type" => "category",
				"status" => $get_general_attribute[3]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[4]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRDT_TEXT"],
				"type" => "category",
				"status" => $get_general_attribute[4]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[5]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRMO_TEXT"],
				"type" => "model",
				"status" => $get_general_attribute[5]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[6]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRVE_TEXT"],
				"type" => "model",
				"status" => $get_general_attribute[6]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[7]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRVE_SKU"],
				"type" => "model",
				"status" => $get_general_attribute[7]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[8]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_SCAN_TIMESTAMP"],
				"type" => "production_date",
				"status" => $get_general_attribute[8]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[9]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_SCAN_TIMESTAMP"],
				"type" => "packaging_date",
				"status" => $get_general_attribute[9]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[10]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MABPR_STAFF_TEXT"],
				"type" => "production_staff",
				"status" => $get_general_attribute[10]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[11]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_SUBPA_STAFF_TEXT"],
				"type" => "packaging_staff",
				"status" => $get_general_attribute[11]["TRPAT_ACTIVE_STATUS"],
			],
			[
				"label" => $get_general_attribute[12]["TRPAT_MASKING"],
				"value" => $scan_detail[0]["SCDET_MPRVE_NOTES"],
				"type" => "description",
				"status" => $get_general_attribute[12]["TRPAT_ACTIVE_STATUS"],
			],
		];

		for ($i=0; $i < count($temp_general_attribute); $i++) { 
			if ($temp_general_attribute[$i]["status"] == 1) {
				$general_attribute[] = $temp_general_attribute[$i];
			}
		}

		$general_attribute = array_values($general_attribute);

		$get_custom_attribute = get_product_attribute("*",[
			[
				"field_name" => "TRPAT_MBRAN_CODE",
				"operator" => "=",
				"value" => $scan_header["SCHED_MBRAN_CODE"],
			],
			[
				"field_name" => "TRPAT_KEY_TYPE",
				"operator" => "=",
				"value" => $check_product_attribute["MBRAN_TRPAT_TYPE"],
			],
			[
				"field_name" => "TRPAT_KEY_CODE",
				"operator" => "=",
				"value" => $key_code,
			],
			[
				"field_name" => "TRPAT_TYPE",
				"operator" => "=",
				"value" => 2,
			],
		]);

		for ($i=0; $i < count($get_custom_attribute); $i++) { 
			if ($get_custom_attribute[$i]["TRPAT_ACTIVE_STATUS"] == 1) {
				$general_attribute[] = [
					"label" => $get_custom_attribute[$i]["TRPAT_MASKING"],
					"value" => $get_custom_attribute[$i]["TRPAT_VALUE"],
					"type" => "custom_attribute",
					"status" => $get_custom_attribute[$i]["TRPAT_ACTIVE_STATUS"],
				];
			}
		}

		$attribute = array_values($general_attribute);

		return $attribute;
	}
}