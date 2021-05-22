<?php

namespace App\Http\Controllers\MasterData\BatchProduction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ActivationBatchController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function activate(Request $request)
    {
        $update_res = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $request->MABPR_CODE],
            "data" => [
                "MABPR_ACTIVATION_STATUS" => 1,
                "MABPR_NOTES" => $request->MABPR_NOTES,
                "MABPR_ACTIVATION_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "Failed to activate batch"
            ],500);
        }
        
        return response()->json([
            'message' => "Data succesfully activate"
        ],200);
    }

    public function close(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "MABPA_MAPLA_CODE" => "required|exists:MAPLA,MAPLA_CODE",
            "MABPR_DISCREPANCY_PRODUCT" => "required|numeric",
            "MABPR_DISCREPANCY_TRQRA" => "required|numeric",
            "MABPR_DISCREPANCY_MASCO" => "required|numeric",
            "MABPR_DISCREPANCY_NOTES" => "required",
        ]);

        $attributeNames = [
            "MABPA_MAPLA_CODE" => "Packaging center",
            "MABPR_DISCREPANCY_TRQRA" => "Discreparancy Product",
            "MABPR_DISCREPANCY_TRQRA" => "Discreparancy QR alpha",
            "MABPR_DISCREPANCY_MASCO" => "Discreparancy sticker code",
            "MABPR_DISCREPANCY_NOTES" => "Discreparancy notes",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->all()
            ], 400);
        }

        if ($request->MABPR_DISCREPANCY_TRQRA != "0" || $request->MABPR_DISCREPANCY_MASCO != "0" || $request->MABPR_DISCREPANCY_PRODUCT != "0") {
            if ($request->MABPR_DISCREPANCY_NOTES == null || $request->MABPR_DISCREPANCY_NOTES == "") {
                return response()->json([
                    'message' => "Please insert discrepancy notes"
                ], 400);
            }
        }

        $data_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $request->MABPR_CODE,
            ]
        ],true);

        $count_paired_qr = std_get([
            "table_name" => "TRQRA",
            "where" => [
                [
                    "field_name" => "TRQRA_MABPR_CODE",
                    "operator" => "=",
                    "value" => $request->MABPR_CODE,
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

        
        if ($count_paired_qr != $data_production["MABPR_EXPECTED_QTY"]) {
            if ($request->MABPR_NOTES == null) {
                return response()->json([
                    'message' => "Please insert batch production notes"
                ], 500);
            }
        }
        
        $unpaired_product = $data_production["MABPR_EXPECTED_QTY"] - $count_paired_qr;
        $total_discrepancy_product = $request->MABPR_DISCREPANCY_PRODUCT + ($unpaired_product - $request->MABPR_DISCREPANCY_PRODUCT);
        $total_discrepancy_qr = $request->MABPR_DISCREPANCY_TRQRA + ($unpaired_product - $request->MABPR_DISCREPANCY_TRQRA);
        $total_discrepancy_bridge = $request->MABPR_DISCREPANCY_MASCO + ($unpaired_product - $request->MABPR_DISCREPANCY_MASCO);

        if ($unpaired_product != $total_discrepancy_product) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy product report not match with not paired product"
            ], 500);
        }

        if ($unpaired_product != $total_discrepancy_qr) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy qr report not match with not paired product"
            ], 500);
        }

        if ($unpaired_product != $total_discrepancy_bridge) {
            return response()->json([
                'message' => "Cannot close batch because total discrepancy qr bridge not match with not paired product"
            ], 500);
        }

        $update_res = std_update([
            "table_name" => "MABPR",
            "where" => ["MABPR_CODE" => $request->MABPR_CODE],
            "data" => [
                "MABPR_ACTIVATION_STATUS" => 2,
                "MABPR_CLOSED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "MABPR_NOTES" => $request->MABPR_NOTES,
                "MABPR_DISCREPANCY_PRODUCT" => $request->MABPR_DISCREPANCY_PRODUCT,
                "MABPR_RETURNED_PRODUCT" => $unpaired_product - $request->MABPR_DISCREPANCY_PRODUCT,
                "MABPR_DISCREPANCY_TRQRA" => $request->MABPR_DISCREPANCY_TRQRA,
                "MABPR_RETURNED_TRQRA" => $unpaired_product - $request->MABPR_DISCREPANCY_TRQRA,
                "MABPR_DISCREPANCY_MASCO" => $request->MABPR_DISCREPANCY_MASCO,
                "MABPR_RETURNED_MASCO" => $unpaired_product - $request->MABPR_DISCREPANCY_MASCO,
                "MABPR_DISCREPANCY_NOTES" => $request->MABPR_DISCREPANCY_NOTES,
                "MABPR_CLOSED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "MABPR_IS_REPORTED" => "1",
            ]
        ]);

        $plant_packaging = get_master_product_plant("*", [
            [
                "field_name" => "MAPLA_CODE",
                "operator" => "=",
                "value" => $request->MABPA_MAPLA_CODE,
            ],
        ],true);

        $code = generate_code(session('company_code'),5,"MABPA");
        if ($code["status_code"] != "OK") {
            return response()->json([
                'message' => "Error on generating code, please try again"
            ], 500);
        }

        $insert_res = std_insert([
            "table_name" => "MABPA",
            "data" => [
                "MABPA_CODE" => strtoupper($code["data"]),
                "MABPA_TEXT" => $data_production["MABPR_TEXT"],
                "MABPA_QTY" => $count_paired_qr,
                "MABPA_QTY_LEFT" => $count_paired_qr,
                "MABPA_MABPR_CODE" => $request->MABPR_CODE,
                "MABPA_MABPR_TEXT" => $data_production["MABPR_TEXT"],
                "MABPA_MAPLA_CODE" => $plant_packaging["MAPLA_CODE"],
                "MABPA_MAPLA_TEXT" => $plant_packaging["MAPLA_TEXT"],
                "MABPA_MCOMP_CODE" => session('company_code'),
                "MABPA_MCOMP_TEXT" => session('company_name'),
                "MABPA_MBRAN_CODE" => session('brand_code'),
                "MABPA_MBRAN_TEXT" => session('brand_name'),
                "MABPA_ACTIVATION_STATUS" => 0,
                "MABPA_STATUS" => 2,
                "MABPA_CREATED_BY" => session("user_id"),
                "MABPA_CREATED_TEXT" => session("user_name"),
                "MABPA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ]
        ]);

        $update_employee = std_update([
            "table_name" => "STBPR",
            "where" => ["STBPR_MABPR_CODE" => $request->MABPR_CODE],
            "data" => ["STBPR_MABPR_STATUS" => "2",]
        ]);

        return response()->json([
            'message' => "Data succesfully closed"
        ],200);
    }
}
