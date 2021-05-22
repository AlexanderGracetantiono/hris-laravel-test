<?php

namespace App\Http\Controllers\Api\V2\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportBatch extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "batch_code" => "required",
            "employee_role" => "required",
            "discrepancy_qr" => "required|numeric",
            "discrepancy_bridge" => "required|numeric",
            "discrepancy_product" => "required|numeric",
            "discrepancy_notes" => "required",
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function report_batch_production($request)
    {
        $response = null;

        $check_batch = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request["batch_code"],
            ]
        ],true);

        if ($check_batch == null) {
            $response = "E2";
            return $response;
        }

        if ($check_batch["MABPR_IS_REPORTED"] == "1") {
            $response = "E3";
            return $response;
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request["batch_code"],
                ],
            ],
            "count" => true,
            "first_row" => true
        ]);
        
        $unpaired_product = $check_batch["MABPR_EXPECTED_QTY"] - $count_paired_qr;
        $total_discrepancy_product = $request["discrepancy_product"] + ($unpaired_product - $request["discrepancy_product"]);
        $total_discrepancy_qr = $request["discrepancy_qr"] + ($unpaired_product - $request["discrepancy_qr"]);
        $total_discrepancy_bridge = $request["discrepancy_bridge"] + ($unpaired_product - $request["discrepancy_bridge"]);

        if ($unpaired_product != $total_discrepancy_product || $unpaired_product != $total_discrepancy_qr || $unpaired_product != $total_discrepancy_bridge) {
            $response = "E4";
            return $response;
        }

        std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $request["batch_code"]],
            "data" => [
                "MABPR_DISCREPANCY_PRODUCT" => $request["discrepancy_product"],
                "MABPR_RETURNED_PRODUCT" => $unpaired_product - $request["discrepancy_product"],
                "MABPR_DISCREPANCY_TRQRA" => $request["discrepancy_qr"],
                "MABPR_RETURNED_TRQRA" => $unpaired_product - $request["discrepancy_qr"],
                "MABPR_DISCREPANCY_MASCO" => $request["discrepancy_bridge"],
                "MABPR_RETURNED_MASCO" => $unpaired_product - $request["discrepancy_bridge"],
                "MABPR_DISCREPANCY_NOTES" => $request["discrepancy_notes"],
                "MABPR_IS_REPORTED" => "1",
            ]
        ]);

        return $response;
    }

    public function report_batch_packaging($request)
    {
        $response = null;

        $check_batch = get_master_sub_batch_packaging("*",[
            [
                "field_name" => "SUBPA_CODE",
                "operator" => "=",
                "value" => $request["batch_code"],
            ]
        ],true);

        if ($check_batch == null) {
            $response = "E5";
            return $response;
        }

        if ($check_batch["SUBPA_IS_REPORTED"] == "1") {
            $response = "E6";
            return $response;
        }

        $count_paired_qr = std_get([
            "table_name" => "TRQRZ",
            "where" => [
                [
                    "field_name" => "TRQRZ_SUBPA_CODE",
                    "operator" => "=",
                    "value" => $request["batch_code"],
                ],
            ],
            "count" => true,
            "first_row" => true
        ]);
        
        $unpaired_product = $check_batch["SUBPA_QTY"] - $count_paired_qr;
        $total_discrepancy_product = $request["discrepancy_product"] + ($unpaired_product - $request["discrepancy_product"]);
        $total_discrepancy_qr = $request["discrepancy_qr"] + ($unpaired_product - $request["discrepancy_qr"]);
        $total_discrepancy_bridge = $request["discrepancy_bridge"] + ($unpaired_product - $request["discrepancy_bridge"]);

        if ($unpaired_product != $total_discrepancy_product || $unpaired_product != $total_discrepancy_qr || $unpaired_product != $total_discrepancy_bridge) {
            $response = "E7";
            return $response;
        }

        std_update([
            "table_name" => "SUBPA",
            "where" => ["SUBPA_CODE" => $request["batch_code"]],
            "data" => [
                "SUBPA_DISCREPANCY_PRODUCT" => $request["discrepancy_product"],
                "SUBPA_RETURNED_PRODUCT" => $unpaired_product - $request["discrepancy_product"],
                "SUBPA_DISCREPANCY_TRQRZ" => $request["discrepancy_qr"],
                "SUBPA_RETURNED_TRQRZ" => $unpaired_product - $request["discrepancy_qr"],
                "SUBPA_DISCREPANCY_MASCO" => $request["discrepancy_bridge"],
                "SUBPA_RETURNED_MASCO" => $unpaired_product - $request["discrepancy_bridge"],
                "SUBPA_DISCREPANCY_NOTES" => $request["discrepancy_notes"],
                "SUBPA_IS_REPORTED" => "1",
            ]
        ]);

        return $response;
    }

    public function index(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res,
                'data' => $request->all(),
                'err_code' => "E1"
            ], 400);
        }

        if ($request->employee_role == 6) {
            $report_batch_production = $this->report_batch_production($request->all()); 

            if ($report_batch_production == "E2") {
                return response()->json([
                    'message' => "Batch production not found",
                    'data' => $request->all(),
                    'err_code' => "E2"
                ], 400);
            }

            if ($report_batch_production == "E3") {
                return response()->json([
                    'message' => "Batch production already reported",
                    'data' => $request->all(),
                    'err_code' => "E3"
                ], 400);
            }

            if ($report_batch_production == "E4") {
                return response()->json([
                    'message' => "Total discrepancy report batch production not match with total not paired product",
                    'data' => $request->all(),
                    'err_code' => "E4"
                ], 400);
            }

            if ($report_batch_production == null) {
                return response()->json([
                    'message' => "Success on report batch production",
                    'data' => $request->all(),
                ], 200);
            }
        } else {
            $report_batch_packaging = $this->report_batch_packaging($request->all()); 

            if ($report_batch_packaging == "E5") {
                return response()->json([
                    'message' => "Batch packaging not found",
                    'data' => $request->all(),
                    'err_code' => "E5"
                ], 400);
            }

            if ($report_batch_packaging == "E6") {
                return response()->json([
                    'message' => "Batch packaging already reported",
                    'data' => $request->all(),
                    'err_code' => "E6"
                ], 400);
            }

            if ($report_batch_packaging == "E7") {
                return response()->json([
                    'message' => "Total discrepancy report batch packaging not match with total not paired product",
                    'data' => $request->all(),
                    'err_code' => "E7"
                ], 400);
            }

            if ($report_batch_packaging == null) {
                return response()->json([
                    'message' => "Success on report batch packaging",
                    'data' => $request->all(),
                ], 200);
            }
        }
    }
}
