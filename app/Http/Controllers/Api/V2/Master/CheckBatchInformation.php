<?php

namespace App\Http\Controllers\Api\V2\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CheckBatchInformation extends Controller
{
	public function index(Request $request)
	{
		$validate = Validator::make($request->all(), [
			"employee_code" => "required|max:255|exists:MAEMP,MAEMP_CODE",
			"role" => "required",
		]);

		if ($validate->fails()) {
			return response()->json([
				"message" => $validate->errors(),
				"data" => $request->all(),
				"err_code" => "E1"
			], 400);
        }
        
        $response = null;
        if ($request->role == "6") {
            $batch_code = std_get([
                "table_name" => "STBPR",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "STBPR_EMP_CODE",
                        "operator" => "=",
                        "value" => $request->employee_code,
                    ],
                    [
                        "field_name" => "STBPR_MABPR_STATUS",
                        "operator" => "=",
                        "value" => "1",
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "STBPR_ID",
                        "type" => "ASC"
                    ]
                ],
            ]);
            if ($batch_code == null) {
                return response()->json([
                    "message" => "Employee not assigned to any batch",
                    "data" => $request->all(),
                    "err_code" => "E2"
                ], 400);
            }

            for ($i=0; $i < count($batch_code); $i++) { 
                $count_batch_paired_qr[$i] = std_get([
                    "table_name" => "TRQRA",
                    "where" => [
                        [
                            "field_name" => "TRQRA_MABPR_CODE",
                            "operator" => "=",
                            "value" => $batch_code[$i]["STBPR_MABPR_CODE"],
                        ]
                    ],
                    "count" => true,
                    "first_row" => true
                ]);

                $count_employee_paired_qr[$i] = std_get([
                    "table_name" => "TRQRA",
                    "where" => [
                        [
                            "field_name" => "TRQRA_MABPR_CODE",
                            "operator" => "=",
                            "value" => $batch_code[$i]["STBPR_MABPR_CODE"],
                        ],
                        [
                            "field_name" => "TRQRA_EMP_SCAN_BY",
                            "operator" => "=",
                            "value" => $request->employee_code,
                        ],
                    ],
                    "count" => true,
                    "first_row" => true
                ]);

                $data_batch[$i] = get_master_batch_production("*",[
                    [
                        "field_name" => "MABPR_CODE",
                        "operator" => "=",
                        "value" => $batch_code[$i]["STBPR_MABPR_CODE"],
                    ]
                ],true);

                $response[$i] = [
                    // 0 waiting, 1 in progress
                    "batch_status" => $data_batch[$i]["MABPR_ACTIVATION_STATUS"],
                    "batch_code" => $data_batch[$i]["MABPR_CODE"],
                    "batch_name" => $data_batch[$i]["MABPR_TEXT"],
                    "batch_company_code" => $data_batch[$i]["MABPR_MCOMP_CODE"],
                    "batch_company_text" => $data_batch[$i]["MABPR_MCOMP_TEXT"],
                    "batch_brand_code" => $data_batch[$i]["MABPR_MBRAN_CODE"],
                    "batch_brand_text" => $data_batch[$i]["MABPR_MBRAN_TEXT"],
                    "batch_category_code" => $data_batch[$i]["MABPR_MPRCA_CODE"],
                    "batch_category_text" => $data_batch[$i]["MABPR_MPRCA_TEXT"],
                    "batch_product_code" => $data_batch[$i]["MABPR_MPRDT_CODE"],
                    "batch_product_text" => $data_batch[$i]["MABPR_MPRDT_TEXT"],
                    "batch_model_code" => $data_batch[$i]["MABPR_MPRMO_CODE"],
                    "batch_model_text" => $data_batch[$i]["MABPR_MPRMO_TEXT"],
                    "batch_variant_code" => $data_batch[$i]["MABPR_MPRVE_CODE"],
                    "batch_variant_text" => $data_batch[$i]["MABPR_MPRVE_TEXT"],
                    "batch_variant_sku" => $data_batch[$i]["MABPR_MPRVE_SKU"],
                    "batch_plant_code" => $data_batch[$i]["MABPR_MAPLA_CODE"],
                    "batch_plant_text" => $data_batch[$i]["MABPR_MAPLA_TEXT"],
                    "batch_start" => $data_batch[$i]["MABPR_START_TIMESTAMP"],
                    "batch_end" => $data_batch[$i]["MABPR_END_TIMESTAMP"],
                    "batch_targeted_quantity" => $data_batch[$i]["MABPR_EXPECTED_QTY"],
                    "total_paired_batch_qr_quantity" => $count_batch_paired_qr[$i],
                    "employee_paired_qr_quantity" => $count_employee_paired_qr[$i],
                ];
            }

        } elseif ($request->role == "7") {
            $batch_code = std_get([
                "table_name" => "STBPA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "STBPA_EMP_CODE",
                        "operator" => "=",
                        "value" => $request->employee_code,
                    ],
                ],
                "order_by" => [
                    [
                        "field" => "STBPA_ID",
                        "type" => "DESC"
                    ]
                ],
            ]);
            if ($batch_code == null) {
                return response()->json([
                    "message" => "Employee not assigned to any batch",
                    "data" => $request->all(),
                    "err_code" => "E2"
                ], 400);
            }

            for ($i=0; $i < count($batch_code); $i++) { 
                $batch_production[$i] = std_get([
                    "table_name" => "POPRD",
                    "select" => "*",
                    "where" => [
                        [
                            "field_name" => "POPRD_CODE",
                            "operator" => "=",
                            "value" => $batch_code[$i]["STBPA_POPRD_CODE"],
                        ]
                    ],
                    "first_row" => true
                ]);
    
                $data_batch[$i] = get_master_sub_batch_packaging("*",[
                    [
                        "field_name" => "SUBPA_CODE",
                        "operator" => "=",
                        "value" => $batch_code[$i]["STBPA_SUBPA_CODE"],
                    ]
                ],true);
    
                $count_employee_paired_qr[$i] = std_get([
                    "table_name" => "MASCO",
                    "where" => [
                        [
                            "field_name" => "MASCO_SUBPA_CODE",
                            "operator" => "=",
                            "value" => $batch_code[$i]["STBPA_SUBPA_CODE"],
                        ],
                        [
                            "field_name" => "MASCO_UPDATED_BY",
                            "operator" => "=",
                            "value" => $request->employee_code,
                        ],
                    ],
                    "count" => true,
                    "first_row" => true
                ]);
    
                $response[$i] = [
                    // 0 waiting, 1 in progress
                    "batch_status" => $data_batch[$i]["SUBPA_ACTIVATION_STATUS"],
                    "batch_code" => $data_batch[$i]["SUBPA_CODE"],
                    "batch_name" => $data_batch[$i]["SUBPA_TEXT"],
                    "batch_company_code" => $data_batch[$i]["SUBPA_MCOMP_CODE"],
                    "batch_company_text" => $data_batch[$i]["SUBPA_MCOMP_TEXT"],
                    "batch_brand_code" => $data_batch[$i]["SUBPA_MBRAN_CODE"],
                    "batch_brand_text" => $data_batch[$i]["SUBPA_MBRAN_TEXT"],
                    "batch_category_code" => $batch_production[$i]["POPRD_MPRCA_CODE"],
                    "batch_category_text" => $batch_production[$i]["POPRD_MPRCA_TEXT"],
                    "batch_product_code" => $batch_production[$i]["POPRD_MPRDT_CODE"],
                    "batch_product_text" => $batch_production[$i]["POPRD_MPRDT_TEXT"],
                    "batch_model_code" => $batch_production[$i]["POPRD_MPRMO_CODE"],
                    "batch_model_text" => $batch_production[$i]["POPRD_MPRMO_TEXT"],
                    "batch_variant_code" => $batch_production[$i]["POPRD_MPRVE_CODE"],
                    "batch_variant_text" => $batch_production[$i]["POPRD_MPRVE_TEXT"],
                    "batch_variant_sku" => $batch_production[$i]["POPRD_MPRVE_SKU"],
                    "batch_plant_code" => $data_batch[$i]["SUBPA_MAPLA_CODE"],
                    "batch_plant_text" => $data_batch[$i]["SUBPA_MAPLA_TEXT"],
                    "batch_start" => $data_batch[$i]["SUBPA_START_TIMESTAMP"],
                    "batch_end" => $data_batch[$i]["SUBPA_END_TIMESTAMP"],
                    "batch_targeted_quantity" => $data_batch[$i]["SUBPA_QTY"],
                    "total_paired_batch_qr_quantity" => $data_batch[$i]["SUBPA_PAIRED_QTY"],
                    "employee_paired_qr_quantity" => $count_employee_paired_qr[$i],
                ];
            }
        }
		
        return response()->json($response, 200);
	}
}
