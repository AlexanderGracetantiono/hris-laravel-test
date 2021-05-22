<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PartialQrController extends Controller
{
	public function index(Request $request)
	{
		$validate = Validator::make($request->all(), [
			"code" => "required",
			"scan_by" => "required",
			"scan_text" => "required",
			"scan_email" => "required",
			"lat" => "required",
			"long" => "required",
			"country" => "required",
			"province" => "required",
			"regency" => "required",
			"district" => "required",
			"address" => "required",
			"device_id" => "required",
			"app_version" => "required",
		]);

		if ($validate->fails()) {
			return response()->json([
				"message" => $validate->errors()->all(),
				"data" => $request->all(),
				"err_code" => "E1"
			], 400);
		}

		$qr_type = $this->check_type_qr($request);
		if ($qr_type == null) {
			return response()->json([
				"message" => "Old QR",
				"data" => $request->all(),
				"err_code" => "E2"
			], 400);
		}

		if (isset($qr_type["err_code"])) {
			if ($qr_type["err_code"] == "E3") {
				return response()->json([
					"message" => $qr_type["brand_name"],
					"data" => $request->all(),
					"err_code" => "E3"
				], 400);
			}

			if ($qr_type["err_code"] == "E5") {
				return response()->json([
					"message" =>"QR alpha not paired",
					"data" => $request->all(),
					"err_code" => "E5"
				], 400);
			}

			if ($qr_type["err_code"] == "E6") {
				return response()->json([
					"message" =>"QR zeta not paired",
					"data" => $request->all(),
					"err_code" => "E6"
				], 400);
			}

			if ($qr_type["err_code"] == "E7") {
				return response()->json([
					"message" =>"QR alpha not recognized",
					"data" => $request->all(),
					"err_code" => "E7"
				], 400);
			}

			if ($qr_type["err_code"] == "E8") {
				return response()->json([
					"message" =>"QR zeta not recognized",
					"data" => $request->all(),
					"err_code" => "E8"
				], 400);
			}
		}

		if (isset($qr_type["qr_type"])) {
			if ($qr_type["qr_type"] == "A2") {
				$brand_logo = $this->get_brand_logo($request->all(),1);
				$data = $this->scan_lab_qr($request->all(),1);

				return response()->json([
					"message" => "Success on scan alpha test lab",
					"request" => $request->all(),
					"type" => "2",
					"data" => $data,
					"authenticity" => null,
					"brand_logo" => $brand_logo,
				], 200);
			}

			if ($qr_type["qr_type"] == "Z2") {
				$brand_logo = $this->get_brand_logo($request->all(),2);
				$data = $this->scan_lab_qr($request->all(),2);
				$authenticity = $this->authenticty_lab_zeta($request->all());

				return response()->json([
					"message" => "Success on scan zeta test lab",
					"request" => $request->all(),
					"type" => "2",
					"data" => $data,
					"authenticity" => $authenticity,
					"brand_logo" => $brand_logo,
				], 200);
			}
		}

		$data = null;
		if ($qr_type["type"] == 1) {
			if ($request->lat == null || $request->long == null) {
				return response()->json([
					"message" => "Location required",
					"request" => $request->all(),
					"err_code" => "E4"
				], 400);
			}
			$type = "alpha";
			
			$brand_logo = $this->get_brand_logo($qr_type,1);
			$data = $this->check_production_qr($qr_type);
			// $insert_data["LGPRT_TYPE"] = "1";
		}
		elseif ($qr_type["type"] == 2) {
			$type = "zeta";
			$data = $this->check_packaging_qr($qr_type);
			$brand_logo = $this->get_brand_logo($qr_type,2);
			// $insert_data["LGPRT_TYPE"] = "2";
		}

		return response()->json([
			"message" => "Success on scan ".$type,
			"request" => $request->all(),
			"type" => $qr_type["type"],
			"data" => $data,
			"brand_logo" => $brand_logo,
		], 200);
	}

	public function check_production_qr($request)
	{
		$scan_header = get_scan_header("*",[
            [
                "field_name" => "SCHED_TRQRA_CODE",
                "operator" => "=",
                "value" => $request["code"],
            ]
        ],true);

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

	public function check_packaging_qr($request)
	{
		$scan_header = get_scan_header("*",[
            [
                "field_name" => "SCHED_TRQRZ_CODE",
                "operator" => "=",
                "value" => $request["code"],
            ]
        ],true);

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

	public function check_type_qr($request)
	{
		$reponse = null;

		$check_exists_alpha = std_get([
			"table_name" => "TRQRA",
			"select" => "*",
			"where" => [
				[
					"field_name" => "TRQRA_CODE",
					"operator" => "=",
					"value" => $request["code"],
				],
			],
			"first_row" => true
		]);

		$check_exists_zeta = std_get([
			"table_name" => "TRQRZ",
			"select" => "*",
			"where" => [
				[
					"field_name" => "TRQRZ_CODE",
					"operator" => "=",
					"value" => $request["code"],
				],
			],
			"first_row" => true
		]);

		if ($check_exists_alpha != null) {
			if ($check_exists_alpha["TRQRA_MASCO_CODE"] == null) {
				$reponse = [
					"err_code" => "E5",
				];

				return $reponse;
			}

			if ($check_exists_alpha["TRQRA_ACCEPTED_BY_STORE"] != 1) {
				$reponse = [
					"err_code" => "E7",
				];

				return $reponse;
			}

			

			$scan_header = get_scan_header("*",[
				[
					"field_name" => "SCHED_TRQRA_CODE",
					"operator" => "=",
					"value" => $request["code"],
				]
			],true);

			if ($scan_header == null) {
				$reponse = [
					"err_code" => "E7",
				];

				return $reponse;
			}

			$check_brand = get_master_product_brand("*",[
				[
					"field_name" => "MBRAN_CODE",
					"operator" => "=",
					"value" => $check_exists_alpha["TRQRA_MBRAN_CODE"],
				]
			],true);

			if ($check_brand["MBRAN_STATUS"] != "1") {
				$reponse = [
					"err_code" => "E3",
					"message" => "brand inactive",
					"brand_name" => $check_brand["MBRAN_NAME"]
				];
			}

			if ($check_exists_alpha["TRQRA_TYPE"] == "2") {
				$reponse = [
					"qr_type" => "A2",
				];

				return $reponse;
			} else {
				$reponse = [
					"type" =>  "1",
					"code" =>  $check_exists_alpha["TRQRA_CODE"],
				];
			}

		} elseif ($check_exists_zeta != null) {
			if ($check_exists_zeta["TRQRZ_MASCO_CODE"] == null) {
				$reponse = [
					"err_code" => "E6",
				];

				return $reponse;
			}

			

			$scan_header = get_scan_header("*",[
				[
					"field_name" => "SCHED_TRQRZ_CODE",
					"operator" => "=",
					"value" => $request["code"],
				]
			],true);

			if ($scan_header == null) {
				$reponse = [
					"err_code" => "E8",
				];

				return $reponse;
			}

			if ($check_exists_zeta["TRQRZ_ACCEPTED_BY_STORE"] != 1) {
				$reponse = [
					"err_code" => "E8",
				];

				return $reponse;
			}

			$check_brand = get_master_product_brand("*",[
				[
					"field_name" => "MBRAN_CODE",
					"operator" => "=",
					"value" => $check_exists_zeta["TRQRZ_MBRAN_CODE"],
				]
			],true);

			if ($check_brand["MBRAN_STATUS"] != "1") {
				$reponse = [
					"err_code" => "E3",
					"message" => "brand inactive",
					"brand_name" => $check_brand["MBRAN_NAME"]
				];
			}

			if ($check_exists_zeta["TRQRZ_TYPE"] == "2") {
				$reponse = [
					"qr_type" => "Z2",
				];

				return $reponse;
			} else {
				$reponse = [
					"type" =>  "2",
					"code" =>  $check_exists_zeta["TRQRZ_CODE"],
				];
			}
		}

		return $reponse;
	}

	public function get_brand_logo($request,$type)
	{
		if ($type == 1) {
			$temp = std_get([
				"table_name" => "TRQRA",
				"select" => ["*"],
				"where" => [
					[
						"field_name" => "TRQRA_CODE",
						"operator" => "=",
						"value" => $request["code"],
					]
				],
				"first_row" => true
			]);
	
			$check_brand = get_master_brand("*",[
				[
					"field_name" => "MBRAN_CODE",
					"operator" => "=",
					"value" => $temp["TRQRA_MBRAN_CODE"],
				]
			],true);

		} else {
			$temp = std_get([
				"table_name" => "TRQRZ",
				"select" => ["*"],
				"where" => [
					[
						"field_name" => "TRQRZ_CODE",
						"operator" => "=",
						"value" => $request["code"],
					]
				],
				"first_row" => true
			]);
	
			$check_brand = get_master_brand("*",[
				[
					"field_name" => "MBRAN_CODE",
					"operator" => "=",
					"value" => $temp["TRQRZ_MBRAN_CODE"],
				]
			],true);
		}

		$image = asset('storage/images/brand_logo/').'/'.$check_brand["MBRAN_IMAGE"];

		return $image;
	}

	public function scan_lab_qr($request,$type)
	{
		if ($type == 1) {
			$scan_header = get_scan_header("*",[
				[
					"field_name" => "SCHED_TRQRA_CODE",
					"operator" => "=",
					"value" => $request["code"],
				]
			],true);
		} else {
			$scan_header = get_scan_header("*",[
				[
					"field_name" => "SCHED_TRQRZ_CODE",
					"operator" => "=",
					"value" => $request["code"],
				]
			],true);
		}

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
			// [
			// 	"label" => $get_general_attribute[3]["TRPAT_MASKING"],
			// 	"value" => $temp["TRQRA_MPRCA_TEXT"],
			// 	"type" => "category",
			// 	"status" => $get_general_attribute[3]["TRPAT_ACTIVE_STATUS"],
			// ],
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
			// [
			// 	"label" => $get_general_attribute[12]["TRPAT_MASKING"],
			// 	"value" => $temp["TRQRA_MPRVE_NOTES"],
			// 	"type" => "description",
			// 	"status" => $get_general_attribute[12]["TRPAT_ACTIVE_STATUS"],
			// ],
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

	public function authenticty_lab_zeta($request)
	{
		$scan_header = get_scan_header("*",[
            [
                "field_name" => "SCHED_TRQRZ_CODE",
                "operator" => "=",
                "value" => $request["code"],
            ]
        ],true);

		if ($scan_header["SCHED_CUST_EMAIL"] != $request["scan_email"]) {
			return null;
		}

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
				"SCLOG_CST_SCAN_BY" => $request["scan_by"],
				"SCLOG_CST_SCAN_TEXT" => $request["scan_text"],
				"SCLOG_CST_SCAN_TIMESTAMP" => date("Y-m-d H:i:s"),
				"SCLOG_CST_SCAN_LAT" => $request["lat"],
				"SCLOG_CST_SCAN_LNG" => $request["long"],
				// "SCLOG_CST_SCAN_COUNTRY" => $request["country"],
				// "SCLOG_CST_SCAN_PROVINCE" => $request["province"],
				// "SCLOG_CST_SCAN_REGENCY" => $request["regency"],
				// "SCLOG_CST_SCAN_DISTRICT" => $request["district"],
				// "SCLOG_CST_SCAN_ADDRESS" => $request["address"],
				"SCLOG_CST_SCAN_DEVICE_ID" => $request["device_id"],
				"SCLOG_CST_SCAN_APP_VERSION" => $request["app_version"],
				"SCLOG_CREATED_BY" => $request["scan_by"],
				"SCLOG_CREATED_TEXT" => $request["scan_text"],
				"SCLOG_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
			]
		]);

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
            "paragraph_1_lat" => $request["lat"],
            "paragraph_1_long" => $request["long"],
        ];

        setlocale(LC_ALL, 'IND');
        $paragraph_id = [
            "paragraph_1" => "Produk ini telah dipasangkan dan didaftarkan dalam database kami sebagai produk Asli! Ini merupakan scan ke {$total_scanned} Scan produk ini dilakukan pada ".strftime("%e %B %Y").", ".strftime("%H:%M")." dan berlokasi di",
            "paragraph_1_lat" => $request["lat"],
            "paragraph_1_long" => $request["long"],
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
        if ($request["scan_by"] == $data_log["SCLOG_CST_SCAN_BY"]) {
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
        if ($request["scan_by"] == $data_log["SCLOG_CST_SCAN_BY"]) {
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
}