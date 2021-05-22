<?php

namespace App\Http\Controllers\Api\V2_Test_Lab;

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
			"type" => "required", // 1 Alpha, 2 Zeta, 3 Sticker Code, 4 Sticker Code & Bridge
			"employee_role" => "required",
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
		} elseif ($request->type == 3 || $request->type == 4) {
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

		} 
		elseif ($request->type == 3 || $request->type == 4) {
			$data = $this->check_sticker_code($request->all());

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
					'message' => "Sticker code already have bridge",
					"data" => $request->all(),
					"err_code" => "E8"
				], 400);
			}
			if ($data == "E9") {
				return response()->json([
					'message' => "Only able to scan test lab bridge",
					"data" => $request->all(),
					"err_code" => "E9"
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

		if ($check_exists["TRQRA_MASCO_CODE"] != null) {
			$reponse = "E3";
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

		if ($check_exists["TRQRZ_MASCO_CODE"] != null) {
			$reponse = "E5";
			return $reponse;
		}

		if ($check_exists["TRQRZ_TYPE"] != 2) {
			$reponse = "E9";
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

		if ($request["type"] == 4) {
			if ($check_exists["MASCO_NOTES"] != null) {
				$reponse = "E8";
				return $reponse;
			}
			
			$check_chain_is_bridge = std_get([
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
			$check_chain_is_alpha = std_get([
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
			$check_chain_is_zeta = std_get([
				"table_name" => "TRQRZ",
				"select" => "*",
				"where" => [
					[
						"field_name" => "TRQRZ_CODE",
						"operator" => "=",
						"value" => $request["code"],
					]
				],
				"first_row" => true
			]);
			if ($check_chain_is_alpha != null || $check_chain_is_zeta != null || $check_chain_is_bridge != null) {
				$reponse = "E10";
				return $reponse;
			}

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
				$reponse = "E7";
				return $reponse;
			}
		}
		elseif ($request["employee_role"] == "7"){
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
				$reponse = "E7";
				return $reponse;
			}

			if ($check_exists["MASCO_TYPE"] != "2") {
				$reponse = "E9";
				return $reponse;
			}
		}
	}
}
