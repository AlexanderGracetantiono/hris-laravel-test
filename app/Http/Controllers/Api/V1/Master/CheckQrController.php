<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CheckQrController extends Controller
{
	public function index(Request $request)
	{
		$validate = Validator::make($request->all(), [
			"code" => "required|max:255",
			"type" => "required", // 1 Alpha, 2 Zeta, 3 Sticker Code
			"employee_role" => "required",
			"batch_code" => "required",
		]);

		if ($validate->fails()) {
			return response()->json([
				"message" => $validate->errors(),
				"data" => $request->all(),
				"err_code" => "E1"
			], 400);
		}

		if ($request->type == 1) {
			$type = "alpha";
		} elseif ($request->type == 2) {
			$type = "zeta";
		} elseif ($request->type == 3) {
			$type = "sticker code";
		}
		
		$data = null;
		if ($request->type == 1) {
			$data = $this->check_production_qr($request->all());

			if ($data == "E2") {
				return response()->json([
					'message' => "QR alpha not found",
					"data" => $request->all(),
					"err_code" => "E2"
				], 400);
			}

			if ($data == "E3") {
				return response()->json([
					'message' => "QR alpha already paired",
					"data" => $request->all(),
					"err_code" => "E3"
				], 400);
			}

			if ($data == "E9") {
				return response()->json([
					'message' => "Target quantity batch production reached",
					"data" => $request->all(),
					"err_code" => "E9"
				], 400);
			}

			if ($data == "E12") {
				return response()->json([
					'message' => "Batch production inactive",
					"data" => $request->all(),
					"err_code" => "E12"
				], 400);
			}

			if ($data == "E17") {
				return response()->json([
					'message' => "QR alpha already rejected",
					"data" => $request->all(),
					"err_code" => "E17"
				], 400);
			}

			if ($data == "E19") {
				return response()->json([
					'message' => "Batch production already reported",
					"data" => $request->all(),
					"err_code" => "E19"
				], 400);
			}

		} 
		elseif ($request->type == 2) {
			$data = $this->check_packaging_qr($request->all());

			if ($data == "E4") {
				return response()->json([
					'message' => "QR zeta not found",
					"data" => $request->all(),
					"err_code" => "E4"
				], 400);
			}

			if ($data == "E5") {
				return response()->json([
					'message' => "QR zeta already paired",
					"data" => $request->all(),
					"err_code" => "E5"
				], 400);
			}

			if ($data == "E10") {
				return response()->json([
					'message' => "Target quantity batch packaging reached",
					"data" => $request->all(),
					"err_code" => "E10"
				], 400);
			}
			
			if ($data == "E13") {
				return response()->json([
					'message' => "Batch packaging inactive",
					"data" => $request->all(),
					"err_code" => "E13"
				], 400);
			}

			if ($data == "E20") {
				return response()->json([
					'message' => "Batch packaging already reported",
					"data" => $request->all(),
					"err_code" => "E20"
				], 400);
			}
		} 
		elseif ($request->type == 3) {
			$data = $this->check_sticker_code($request->all());

			// return response()->json($data, 400);
			if ($data == "E6") {
				return response()->json([
					'message' => "Sticker code not found",
					"data" => $request->all(),
					"err_code" => "E6"
				], 400);
			}
			if ($data == "E7") {
				return response()->json([
					'message' => "Sticker code already paired",
					"data" => $request->all(),
					"err_code" => "E7"
				], 400);
			}
			if ($data == "E8") {
				return response()->json([
					'message' => "Sticker code already paired",
					"data" => $request->all(),
					"err_code" => "E8"
				], 400);
			}
			if ($data == "E9") {
				return response()->json([
					'message' => "Target quantity batch production reached",
					"data" => $request->all(),
					"err_code" => "E9"
				], 400);
			}
			if ($data == "E10") {
				return response()->json([
					'message' => "Target quantity batch packaging reached",
					"data" => $request->all(),
					"err_code" => "E10"
				], 400);
			}
			if ($data == "E11") {
				return response()->json([
					'message' => "Sticker code already rejected",
					"data" => $request->all(),
					"err_code" => "E11"
				], 400);
			}
			if ($data == "E12") {
				return response()->json([
					'message' => "Batch production inactive",
					"data" => $request->all(),
					"err_code" => "E12"
				], 400);
			}
			if ($data == "E13") {
				return response()->json([
					'message' => "Batch packaging inactive",
					"data" => $request->all(),
					"err_code" => "E13"
				], 400);
			}
			if ($data == "E14") {
				return response()->json([
					'message' => "Please scan production sticker code",
					"data" => $request->all(),
					"err_code" => "E14"
				], 400);
			}
			if ($data == "E15") {
				return response()->json([
					'message' => "Sticker code not assigned to batch production",
					"data" => $request->all(),
					"err_code" => "E15"
				], 400);
			}
			if ($data == "E16") {
				return response()->json([
					'message' => "Batch packaging not assigned to any pool product",
					"data" => $request->all(),
					"err_code" => "E16"
				], 400);
			}
			if ($data == "E18") {
				return response()->json([
					'message' => "Only able to scan manufacture bridge",
					"data" => $request->all(),
					"err_code" => "E18"
				], 400);
			}
			if ($data == "E19") {
				return response()->json([
					'message' => "Batch production already reported",
					"data" => $request->all(),
					"err_code" => "E19"
				], 400);
			}
			if ($data == "E20") {
				return response()->json([
					'message' => "Batch packaging already reported",
					"data" => $request->all(),
					"err_code" => "E20"
				], 400);
			}
		}
		
		return response()->json([
			"message" => "Success on scan ".$type,
			"request" => $request->all(),
			"type" => $request->type,
		], 200);
	}

	public function check_production_qr($request)
	{
		$check_exists = std_get([
			"table_name" => "TRQRA",
			"select" => "*",
			"where" => [
				[
					"field_name" => "TRQRA_CODE",
					"operator" => "=",
					"value" => $request["code"],
				]
			],
			"first_row" => true
		]);

		$reponse = "OK";
		
		if ($check_exists == null) {
			$reponse = "E2";
			return $reponse;
		}

		if ($check_exists["TRQRA_MABPR_CODE"] != null) {
			$reponse = "E3";
			return $reponse;
		}

		if ($check_exists["TRQRA_STATUS"] == "3") {
			$reponse = "E17";
			return $reponse;
		}

		$data_batch = std_get([
            "table_name" => "MABPR",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MABPR_CODE",
                    "operator" => "=",
                    "value" => $request["batch_code"],
                ]
            ],
            "first_row" => true
        ]);
		if ($data_batch == null || $data_batch["MABPR_ACTIVATION_STATUS"] != "1") {
			$reponse = "E12";
			return $reponse;
		}

		if ($data_batch["MABPR_IS_REPORTED"] == 1) {
			$reponse = "E19";
			return $reponse;
		}

		$count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" =>  $request["batch_code"],
                ]
            ],
            "count" => true,
            "first_row" => true
		]);
		
		if ($data_batch["MABPR_EXPECTED_QTY"] <= $count_paired_qr ) {
			$reponse = "E9";
			return $reponse;
        }

		return $reponse;
	}

	public function check_packaging_qr($request)
	{
		$check_exists = std_get([
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

		$reponse = "OK";

		if ($check_exists == null) {
			$reponse = "E4";
			return $reponse;
		}

		if ($check_exists["TRQRZ_SUBPA_CODE"] != null) {
			$reponse = "E5";
			return $reponse;
		}

		$data_batch = std_get([
            "table_name" => "SUBPA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request["batch_code"],
                ]
            ],
            "first_row" => true
		]);
		if ($data_batch == null || $data_batch["SUBPA_ACTIVATION_STATUS"] != "1") {
			$reponse = "E13";
			return $reponse;
		}

		if ($data_batch["SUBPA_IS_REPORTED"] == 1) {
			$reponse = "E20";
			return $reponse;
		}

        $current_qty = $data_batch["SUBPA_PAIRED_QTY"] + 1;

        if ($data_batch["SUBPA_QTY"] < $current_qty) {
            $reponse = "E10";
			return $reponse;
        }

		return $reponse;
	}

	public function check_sticker_code($request)
	{
		$check_exists = std_get([
			"table_name" => "MASCO",
			"select" => "*",
			"where" => [
				[
					"field_name" => "MASCO_CODE",
					"operator" => "=",
					"value" => $request["code"],
				],
			],
			"first_row" => true
		]);

		$reponse = "OK";

		if ($check_exists == null) {
			$reponse = "E6";
			return $reponse;
		}

		if ($request["employee_role"] == "6") {

			$check_sticker_paired = std_get([
				"table_name" => "MASCO",
				"select" => "*",
				"where" => [
					[
						"field_name" => "MASCO_CODE",
						"operator" => "=",
						"value" => $request["code"],
					]
				],
				"first_row" => true
			]);

			if ($check_sticker_paired["MASCO_TRQAH_CODE"] != NULL) {
				$reponse = "E8";
				return $reponse;
			}

			if ($check_sticker_paired["MASCO_STATUS"] == "3") {
				$reponse = "E11";
				return $reponse;
			}

			$data_batch = std_get([
				"table_name" => "MABPR",
				"select" => "*",
				"where" => [
					[
						"field_name" => "MABPR_CODE",
						"operator" => "=",
						"value" => $request["batch_code"],
					]
				],
				"first_row" => true
			]);
			if ($data_batch == null || $data_batch["MABPR_ACTIVATION_STATUS"] != "1") {
				$reponse = "E12";
				return $reponse;
			}

			if ($data_batch["MABPR_IS_REPORTED"] == "1") {
				$reponse = "E19";
				return $reponse;
			}
			
			$count_paired_qr = std_get([
				"table_name" => "TRQRA",
				"where" => [
					[
						"field_name" => "TRQRA_MABPR_CODE",
						"operator" => "=",
						"value" =>  $request["batch_code"],
					]
				],
				"count" => true,
				"first_row" => true
			]);
			
			if ($data_batch["MABPR_EXPECTED_QTY"] <= $count_paired_qr ) {
				$reponse = "E10";
				return $reponse;
			}
		}
		else {
			$check_sticker_paired = std_get([
				"table_name" => "MASCO",
				"select" => "*",
				"where" => [
					[
						"field_name" => "MASCO_CODE",
						"operator" => "=",
						"value" => $request["code"],
					],
				],
				"first_row" => true
			]);

			if ($check_sticker_paired["MASCO_TRQZH_CODE"] != NULL) {
				$reponse = "E8";
				return $reponse;
			}

			if ($check_sticker_paired["MASCO_STATUS"] == "3") {
				$reponse = "E11";
				return $reponse;
			}

			if ($check_sticker_paired["MASCO_TYPE"] != "1") {
				$reponse = "E18";
				return $reponse;
			}

			$data_batch = std_get([
				"table_name" => "SUBPA",
				"select" => "*",
				"where" => [
					[
						"field_name" => "SUBPA_CODE",
						"operator" => "=",
						"value" => $request["batch_code"],
					]
				],
				"first_row" => true
			]);
			if ($data_batch == null || $data_batch["SUBPA_ACTIVATION_STATUS"] != "1") {
				$reponse = "E13";
				return $reponse;
			}

			if ($data_batch["SUBPA_IS_REPORTED"] == "1") {
				$reponse = "E20";
				return $reponse;
			}

			$batch_product_version = get_master_batch_production("*",[
				[
					"field_name" => "MABPR_CODE",
					"operator" => "=",
					"value" => $check_sticker_paired["MASCO_MABPR_CODE"]
				]
			],true);

			$pool_product_version = get_pool_product("*",[
				[
					"field_name" => "POPRD_CODE",
					"operator" => "=",
					"value" => $data_batch["SUBPA_POPRD_CODE"]
				]
			],true);
			
			if ($batch_product_version == null) {
				$reponse = "E15";
				return $reponse;
			}

			if ($pool_product_version == null) {
				$reponse = "E16";
				return $reponse;
			}

			if ($batch_product_version["MABPR_MPRVE_CODE"] != $pool_product_version["POPRD_MPRVE_CODE"]) {
				$reponse = "E14";
				return $reponse;
			}

			$current_qty = $data_batch["SUBPA_PAIRED_QTY"] + 1;
	
			if ($data_batch["SUBPA_QTY"] < $current_qty) {
				$reponse = "E10";
				return $reponse;
			}
			
		}
	}
}
