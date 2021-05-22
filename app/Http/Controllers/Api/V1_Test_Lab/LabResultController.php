<?php

namespace App\Http\Controllers\Api\V1_Test_Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDF;
use App;
use QrCode;

class LabResultController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(), [
            "lab_result" => "required|array",
            "lab_result.*.chain_code" => "required",
            "lab_result.*.result" => "required|array",
            "lab_result.*.result.*.test_lab_type" => "required",
            "lab_result.*.result.*.result_lab" => "required",
            "lab_result.*.gender" => "required",
            "lab_result.*.date_of_birth" => "required|date_format:Y-m-d",
            "lab_result.*.patient" => "required",
            "lab_result.*.customer_email" => "required",
            "lab_result.*.customer_phone_number" => "required",
            "lab_result.*.nik" => "required",
            "lab_result.*.testing_center" => "required",
            "lab_result.*.testing_center_doctor" => "required",
            "lab_result.*.laboratorium" => "required",
            "lab_result.*.laboratorium_doctor" => "required",
            "lab_result.*.brand_hosital" => "required",
            "lab_result.*.company_hosital" => "required",
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function find_company($request)
    {
        $return = [];

        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            $check_exists = std_get([
                "table_name" => "MCOMP",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MCOMP_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["company_hosital"],
                    ],
                ],
                "first_row" => true
            ]);
            
            if ($check_exists == null) {
                $return[] = $i;
            } 
        }

        return $return;
    }

    public function find_brand($request)
    {
        $return = [];

        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            $check_exists = std_get([
                "table_name" => "MBRAN",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["brand_hosital"],
                    ],
                ],
                "first_row" => true
            ]);
            
            if ($check_exists == null) {
                $return[] = $i;
            } 
        }

        return $return;
    }

    public function find_bridge($request)
    {
        $return = [];
        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            $check_exists = std_get([
                "table_name" => "MASCO",
                "select" => "*",
                "where_raw" => [
                    [
                        "field_name" => "MASCO_NOTES",
                        "value" => $request["lab_result"][$i]["chain_code"],
                    ],
                    [
                        "field_name" => "MASCO_MBRAN_CODE",
                        "value" => $request["lab_result"][$i]["brand_hosital"],
                    ],
                ],
                // "where" => [
                //     [
                //         "field_name" => "MASCO_NOTES",
                //         "operator" => "=",
                //         "value" => $request["lab_result"][$i]["chain_code"],
                //     ],
                //     [
                //         "field_name" => "MASCO_MBRAN_CODE",
                //         "operator" => "=",
                //         "value" => $request["lab_result"][$i]["brand_hosital"],
                //     ],
                // ],
                "first_row" => true
            ]);
        
            if ($check_exists == null) {
                $return[] = $i;
            } 
        } 
        return $return;
    }

    public function find_testing_lab_center($request)
    {
        $return = [];
        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            $check_exists = std_get([
                "table_name" => "MAPLA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MAPLA_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["testing_center"],
                    ],
                    [
                        "field_name" => "MAPLA_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["brand_hosital"],
                    ],
                    [
                        "field_name" => "MAPLA_TYPE",
                        "operator" => "=",
                        "value" => 1,
                    ],
                ],
                "first_row" => true
            ]);
        
            if ($check_exists == null) {
                $return[] = $i;
            } 
        } 
        return $return;
    }

    public function find_laboratorium_center($request)
    {
        $return = [];
        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            $check_exists = std_get([
                "table_name" => "MAPLA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MAPLA_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["laboratorium"],
                    ],
                    [
                        "field_name" => "MAPLA_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["lab_result"][$i]["brand_hosital"],
                    ],
                    [
                        "field_name" => "MAPLA_TYPE",
                        "operator" => "=",
                        "value" => 2,
                    ],
                ],
                "first_row" => true
            ]);
        
            if ($check_exists == null) {
                $return[] = $i;
            } 
        } 
        return $return;
    }

    public function find_test_lab_type($request)
    {
        $return = [];

        for ($i=0; $i < count($request["lab_result"]); $i++) { 
            for ($j=0; $j < count($request["lab_result"][$i]["result"]); $j++) { 
                $check_exists = std_get([
                    "table_name" => "MPRCA",
                    "select" => "*",
                    "where" => [
                        [
                            "field_name" => "MPRCA_CODE",
                            "operator" => "=",
                            "value" => $request["lab_result"][$i]["result"][$j]["test_lab_type"],
                        ],
                        [
                            "field_name" => "MPRCA_MBRAN_CODE",
                            "operator" => "=",
                            "value" => $request["lab_result"][$i]["brand_hosital"],
                        ],
                    ],
                    "first_row" => true
                ]);
                
                if ($check_exists == null) {
                    $return[] = [
                        "header" => $i,
                        "detail" => $j
                    ];
                } 
            }
        }

        return $return;
    }

    public function insert_scan_log($request,$sticker_code,$patient_code)
    {
        $company_data = std_get([
            "table_name" => "MCOMP",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MCOMP_CODE",
                    "operator" => "=",
                    "value" => $request["company_hosital"]
                ],
            ],
            "first_row" => true
        ]);

        $brand_data = std_get([
            "table_name" => "MBRAN",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"]
                ],
                [
                    "field_name" => "MBRAN_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $request["company_hosital"]
                ],
            ],
            "first_row" => true
        ]);

        $testing_lab_center = std_get([
            "table_name" => "MAPLA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAPLA_CODE",
                    "operator" => "=",
                    "value" => $request["testing_center"],
                ],
                [
                    "field_name" => "MAPLA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
                [
                    "field_name" => "MAPLA_TYPE",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true
        ]);

        $laboratorium_center = std_get([
            "table_name" => "MAPLA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAPLA_CODE",
                    "operator" => "=",
                    "value" => $request["laboratorium"],
                ],
                [
                    "field_name" => "MAPLA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
                [
                    "field_name" => "MAPLA_TYPE",
                    "operator" => "=",
                    "value" => 2,
                ],
            ],
            "first_row" => true
        ]);

        $check_exists_gender = std_get([
            "table_name" => "MPRDT",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MPRDT_TEXT",
                    "operator" => "=",
                    "value" => $request["gender"],
                ],
                [
                    "field_name" => "MPRDT_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
            ],
            "first_row" => true
        ]);

        $check_exists_dob = std_get([
            "table_name" => "MPRMO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MPRMO_TEXT",
                    "operator" => "=",
                    "value" => $request["date_of_birth"],
                ],
                [
                    "field_name" => "MPRMO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
            ],
            "first_row" => true
        ]);

        $check_exists_patient = std_get([
            "table_name" => "MPRMO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MPRMO_TEXT",
                    "operator" => "=",
                    "value" => $request["date_of_birth"],
                ],
                [
                    "field_name" => "MPRMO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
            ],
            "first_row" => true
        ]);

        $get_alpha_data = std_get([
            "table_name" => "TRQRA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRA_MASCO_CODE",
                    "operator" => "=",
                    "value" => $sticker_code
                ]
            ],
            "first_row" => true
        ]);

        $get_zeta_data = std_get([
            "table_name" => "TRQRZ",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "TRQRZ_MASCO_CODE",
                    "operator" => "=",
                    "value" => $sticker_code
                ]
            ],
            "first_row" => true
        ]);

        $data_header = [
            "SCHED_TRQRA_CODE" => $get_alpha_data["TRQRA_CODE"],
            "SCHED_TRQRZ_CODE" => $get_zeta_data["TRQRZ_CODE"],
            "SCHED_MASCO_CODE" => $sticker_code,
            "SCHED_CHAIN_CODE" => $request["chain_code"],
            "SCHED_COUNTER" => 0,
            "SCHED_MBRAN_CODE" => $brand_data["MBRAN_CODE"],
            "SCHED_MBRAN_NAME" => $brand_data["MBRAN_NAME"],
            "SCHED_MCOMP_CODE" => $company_data["MCOMP_CODE"],
            "SCHED_MCOMP_NAME" => $company_data["MCOMP_NAME"],
            "SCHED_CUST_EMAIL" => $request["customer_email"],
            "SCHED_CUST_PHONE_NUMBER" => $request["customer_phone_number"],
            "SCHED_CUST_NAME" => $request["patient"],
        ];

        $id = std_insert_get_id([
            "table_name" => "SCHED",
            "data" => $data_header
        ]);

        for ($i=0; $i < count($request["result"]); $i++) {
            $check_exists_lab_type[$i] = std_get([
                "table_name" => "MPRCA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request["result"][$i]["test_lab_type"],
                    ],
                    [
                        "field_name" => "MPRCA_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["brand_hosital"],
                    ],
                ],
                "first_row" => true
            ]);
            
            $data_detail[$i] = [
                "SCDET_SCHED_ID" => $id,
                "SCDET_MPRCA_CODE" => $check_exists_lab_type[$i]["MPRCA_CODE"],
                "SCDET_MPRCA_TEXT" => $check_exists_lab_type[$i]["MPRCA_TEXT"],
                "SCDET_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                "SCDET_MPRDT_TEXT" => $request["gender"],
                "SCDET_MPRMO_CODE" => $check_exists_dob["MPRMO_CODE"],
                "SCDET_MPRMO_TEXT" => $request["date_of_birth"],
                "SCDET_MPRVE_CODE" => $patient_code,
                "SCDET_MPRVE_TEXT" => $request["patient"],
                "SCDET_MPRVE_SKU" => $request["nik"],
                "SCDET_MPRVE_NOTES" => $request["result"][$i]["result_lab"],
                "SCDET_MABPR_STAFF_CODE" => $get_alpha_data["TRQRA_EMP_SCAN_BY"],
                "SCDET_MABPR_STAFF_TEXT" => $get_alpha_data["TRQRA_EMP_SCAN_TEXT"],
                "SCDET_MABPR_SCAN_TIMESTAMP" => $get_alpha_data["TRQRA_EMP_SCAN_TIMESTAMP"],
                "SCDET_MABPR_ADMIN_TEXT" => $request["testing_center_doctor"],
                "SCDET_MABPR_MAPLA_CODE" => $request["testing_center"],
                "SCDET_MABPR_MAPLA_TEXT" => $testing_lab_center["MAPLA_TEXT"],
                "SCDET_SUBPA_STAFF_CODE" => $get_zeta_data["TRQRZ_EMP_SCAN_BY"],
                "SCDET_SUBPA_STAFF_TEXT" => $get_zeta_data["TRQRZ_EMP_SCAN_TEXT"],
                "SCDET_SUBPA_SCAN_TIMESTAMP" => $get_zeta_data["TRQRZ_EMP_SCAN_TIMESTAMP"],
                "SCDET_SUBPA_ADMIN_TEXT" => $request["laboratorium_doctor"],
                "SCDET_SUBPA_MAPLA_CODE" => $request["laboratorium"],
                "SCDET_SUBPA_MAPLA_TEXT" => $laboratorium_center["MAPLA_TEXT"],
            ];

        }
        std_insert([
            "table_name" => "SCDET",
            "data" => $data_detail
        ]);
    }

    public function insert_master_data($request)
    {
        $company_data = std_get([
            "table_name" => "MCOMP",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MCOMP_CODE",
                    "operator" => "=",
                    "value" => $request["company_hosital"]
                ],
            ],
            "first_row" => true
        ]);

        $brand_data = std_get([
            "table_name" => "MBRAN",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"]
                ],
                [
                    "field_name" => "MBRAN_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $request["company_hosital"]
                ],
            ],
            "first_row" => true
        ]);

        $check_sticker_code = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request["chain_code"],
                ],
            ],
            "first_row" => true
        ]);

        $testing_lab_center = std_get([
            "table_name" => "MAPLA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAPLA_CODE",
                    "operator" => "=",
                    "value" => $request["testing_center"],
                ],
                [
                    "field_name" => "MAPLA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
                [
                    "field_name" => "MAPLA_TYPE",
                    "operator" => "=",
                    "value" => 1,
                ],
            ],
            "first_row" => true
        ]);

        $laboratorium_center = std_get([
            "table_name" => "MAPLA",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MAPLA_CODE",
                    "operator" => "=",
                    "value" => $request["laboratorium"],
                ],
                [
                    "field_name" => "MAPLA_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
                [
                    "field_name" => "MAPLA_TYPE",
                    "operator" => "=",
                    "value" => 2,
                ],
            ],
            "first_row" => true
        ]);
        
        for ($i=0; $i < count($request["result"]); $i++) { 
            $check_exists_lab_type = std_get([
                "table_name" => "MPRCA",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request["result"][$i]["test_lab_type"],
                    ],
                    [
                        "field_name" => "MPRCA_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["brand_hosital"],
                    ],
                ],
                "first_row" => true
            ]);

            $check_exists_gender = std_get([
                "table_name" => "MPRDT",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MPRDT_TEXT",
                        "operator" => "=",
                        "value" => $request["gender"],
                    ],
                    [
                        "field_name" => "MPRDT_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["brand_hosital"],
                    ],
                ],
                "first_row" => true
            ]);
    
            // Insert from gender, dob
                if ($check_exists_gender == null) {
                    $gender_code = generate_code($request["company_hosital"], 5, "MPRDT");
                    $insert_data_gender = [
                        "MPRDT_CODE" => $gender_code["data"],
                        "MPRDT_TEXT" => $request["gender"],
                        "MPRDT_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        "MPRDT_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        "MPRDT_MCOMP_CODE" => $request["company_hosital"],
                        "MPRDT_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                        "MPRDT_MBRAN_CODE" => $request["brand_hosital"],
                        "MPRDT_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                        "MPRDT_STATUS" => 1,
                        "MPRDT_IS_DELETED" => 0,
                        "MPRDT_CREATED_BY" => $request["brand_hosital"],
                        "MPRDT_CREATED_TEXT" => $request["laboratorium_doctor"],
                        "MPRDT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
    
                    $dob_code = generate_code($request["company_hosital"], 5, "MPRMO");
                    $insert_data_dob = [
                        "MPRMO_CODE" => $dob_code["data"],
                        "MPRMO_TEXT" => $request["date_of_birth"],
                        "MPRMO_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        "MPRMO_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        "MPRMO_MPRDT_CODE" => $gender_code["data"],
                        "MPRMO_MPRDT_TEXT" => $request["gender"],
                        "MPRMO_MCOMP_CODE" => $request["company_hosital"],
                        "MPRMO_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                        "MPRMO_MBRAN_CODE" => $request["brand_hosital"],
                        "MPRMO_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                        "MPRMO_STATUS" => 1,
                        "MPRMO_IS_DELETED" => 0,
                        "MPRMO_CREATED_BY" => $request["brand_hosital"],
                        "MPRMO_CREATED_TEXT" => $request["laboratorium_doctor"],
                        "MPRMO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
    
                    $patient_code = generate_code($request["company_hosital"], 5, "MPRVE");
                   
                    $insert_data_patient = [
                        "MPRVE_CODE" => $patient_code["data"],
                        "MPRVE_TEXT" => $request["patient"],
                        "MPRVE_SKU" => $request["nik"],
                        "MPRVE_NOTES" => $request["result"][$i]["result_lab"],
                        "MPRVE_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        "MPRVE_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        "MPRVE_MPRDT_CODE" => $gender_code["data"],
                        "MPRVE_MPRDT_TEXT" => $request["gender"],
                        "MPRVE_MPRMO_CODE" => $dob_code["data"],
                        "MPRVE_MPRMO_TEXT" => $request["date_of_birth"],
                        "MPRVE_MCOMP_CODE" => $request["company_hosital"],
                        "MPRVE_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                        "MPRVE_MBRAN_CODE" => $request["brand_hosital"],
                        "MPRVE_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                        "MPRVE_STATUS" => 1,
                        "MPRVE_IS_DELETED" => 0,
                        "MPRVE_CREATED_BY" => $request["brand_hosital"],
                        "MPRVE_CREATED_TEXT" => $request["laboratorium_doctor"],
                        "MPRVE_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
    
                    try {
                        $insert_res_gender = std_insert([
                            "table_name" => "MPRDT",
                            "data" => $insert_data_gender
                        ]);
                        $insert_res_dob = std_insert([
                            "table_name" => "MPRMO",
                            "data" => $insert_data_dob
                        ]);
                        $insert_res_patient = std_insert([
                            "table_name" => "MPRVE",
                            "data" => $insert_data_patient
                        ]);
                        
                        $log = [
                            $insert_data_gender,
                            $insert_data_dob,
                            $insert_data_patient
                        ];
    
                        // $data_alpha = [
                        //     "TRQRA_MAPLA_CODE" => $request["testing_center"],
                        //     "TRQRA_MAPLA_TEXT" => $testing_lab_center["MAPLA_TEXT"],
                        //     "TRQRA_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        //     "TRQRA_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        //     "TRQRA_MPRDT_CODE" => $gender_code["data"],
                        //     "TRQRA_MPRDT_TEXT" => $request["gender"],
                        //     "TRQRA_MPRMO_CODE" => $dob_code["data"],
                        //     "TRQRA_MPRMO_TEXT" => $request["date_of_birth"],
                        //     "TRQRA_MPRVE_CODE" => $patient_code["data"],
                        //     "TRQRA_MPRVE_TEXT" => $request["patient"],
                        //     "TRQRA_MPRVE_SKU" => $request["nik"],
                        //     "TRQRA_MPRVE_NOTES" => $request["result"],
                        //     "TRQRA_NOTES" => $request["testing_center_doctor"],
                        // ];
    
                        // $update_alpha = std_update([
                        //     "table_name" => "TRQRA",
                        //     "where" => ["TRQRA_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                        //     "data" => $data_alpha
                        // ]);
    
                        // $data_zeta = [
                        //     "TRQRZ_MAPLA_CODE" => $request["laboratorium"],
                        //     "TRQRZ_MAPLA_TEXT" => $laboratorium_center["MAPLA_TEXT"],
                        //     "TRQRZ_NOTES" => $request["laboratorium_doctor"],
                        // ];
    
                        // $update_zeta = std_update([
                        //     "table_name" => "TRQRZ",
                        //     "where" => ["TRQRZ_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                        //     "data" => $data_zeta
                        // ]);
                            
                        if ($i == 0) {
                            $this->insert_scan_log($request,$check_sticker_code["MASCO_CODE"],$patient_code["data"]);
                        }
    
                        Log::info("Success on create gender date : ".date("Y-m-d")." data : ".json_encode($log));
                    } catch (\Exception $e) {
                        Log::critical("Fail on create gender date : ".date("Y-m-d")." message : ".json_encode($e->getMessage()));
                    }
    
                    return;
                }
            // 
    
            $check_exists_dob = std_get([
                "table_name" => "MPRMO",
                "select" => "*",
                "where" => [
                    [
                        "field_name" => "MPRMO_TEXT",
                        "operator" => "=",
                        "value" => $request["date_of_birth"],
                    ],
                    [
                        "field_name" => "MPRMO_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request["brand_hosital"],
                    ],
                ],
                "first_row" => true
            ]);
            
            // Insert from dob
                if ($check_exists_dob == null) {
                    $dob_code = generate_code($request["company_hosital"], 5, "MPRMO");
                    $insert_data_dob = [
                        "MPRMO_CODE" => $dob_code["data"],
                        "MPRMO_TEXT" => $request["date_of_birth"],
                        "MPRMO_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        "MPRMO_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        "MPRMO_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                        "MPRMO_MPRDT_TEXT" => $request["gender"],
                        "MPRMO_MCOMP_CODE" => $request["company_hosital"],
                        "MPRMO_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                        "MPRMO_MBRAN_CODE" => $request["brand_hosital"],
                        "MPRMO_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                        "MPRMO_STATUS" => 1,
                        "MPRMO_IS_DELETED" => 0,
                        "MPRMO_CREATED_BY" => $request["brand_hosital"],
                        "MPRMO_CREATED_TEXT" => $request["laboratorium_doctor"],
                        "MPRMO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
                    
                    $patient_code = generate_code($request["company_hosital"], 5, "MPRVE");
                   
                    $insert_data_patient = [
                        "MPRVE_CODE" => $patient_code["data"],
                        "MPRVE_TEXT" => $request["patient"],
                        "MPRVE_SKU" => $request["nik"],
                        "MPRVE_NOTES" => $request["result"][$i]["result_lab"],
                        "MPRVE_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        "MPRVE_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        "MPRVE_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                        "MPRVE_MPRDT_TEXT" => $request["gender"],
                        "MPRVE_MPRMO_CODE" => $dob_code["data"],
                        "MPRVE_MPRMO_TEXT" => $request["date_of_birth"],
                        "MPRVE_MCOMP_CODE" => $request["company_hosital"],
                        "MPRVE_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                        "MPRVE_MBRAN_CODE" => $request["brand_hosital"],
                        "MPRVE_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                        "MPRVE_STATUS" => 1,
                        "MPRVE_IS_DELETED" => 0,
                        "MPRVE_CREATED_BY" => $request["brand_hosital"],
                        "MPRVE_CREATED_TEXT" => $request["laboratorium_doctor"],
                        "MPRVE_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ];
    
                    try {
                        $insert_res_dob = std_insert([
                            "table_name" => "MPRMO",
                            "data" => $insert_data_dob
                        ]);
                        $insert_res_patient = std_insert([
                            "table_name" => "MPRVE",
                            "data" => $insert_data_patient
                        ]);
                        
                        $log = [
                            $insert_data_dob,
                            $insert_data_patient
                        ];
    
                        // $data_alpha = [
                        //     "TRQRA_MAPLA_CODE" => $request["testing_center"],
                        //     "TRQRA_MAPLA_TEXT" => $testing_lab_center["MAPLA_TEXT"],
                        //     "TRQRA_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                        //     "TRQRA_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                        //     "TRQRA_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                        //     "TRQRA_MPRDT_TEXT" => $request["gender"],
                        //     "TRQRA_MPRMO_CODE" => $dob_code["data"],
                        //     "TRQRA_MPRMO_TEXT" => $request["date_of_birth"],
                        //     "TRQRA_MPRVE_CODE" => $patient_code["data"],
                        //     "TRQRA_MPRVE_TEXT" => $request["patient"],
                        //     "TRQRA_MPRVE_SKU" => $request["nik"],
                        //     "TRQRA_MPRVE_NOTES" => $request["result"],
                        //     "TRQRA_NOTES" => $request["testing_center_doctor"],
                        // ];
    
                        // $update_alpha = std_update([
                        //     "table_name" => "TRQRA",
                        //     "where" => ["TRQRA_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                        //     "data" => $data_alpha
                        // ]);
    
                        // $data_zeta = [
                        //     "TRQRZ_MAPLA_CODE" => $request["laboratorium"],
                        //     "TRQRZ_MAPLA_TEXT" => $laboratorium_center["MAPLA_TEXT"],
                        //     "TRQRZ_NOTES" => $request["laboratorium_doctor"],
                        // ];
    
                        // $update_zeta = std_update([
                        //     "table_name" => "TRQRZ",
                        //     "where" => ["TRQRZ_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                        //     "data" => $data_zeta
                        // ]);

                        if ($i == 0) {
                            $this->insert_scan_log($request,$check_sticker_code["MASCO_CODE"],$patient_code["data"]);
                        }
    
                        Log::info("Success on create dob date : ".date("Y-m-d")." data : ".json_encode($log));
                    } catch (\Exception $e) {
                        Log::critical("Fail on create dob date : ".date("Y-m-d")." message : ".json_encode($e->getMessage()));
                    }
    
                    return;
                }
            // 
    
            // insert version
                $patient_code = generate_code($request["company_hosital"], 5, "MPRVE");
               
                $insert_data_patient = [
                    "MPRVE_CODE" => $patient_code["data"],
                    "MPRVE_TEXT" => $request["patient"],
                    "MPRVE_SKU" => $request["nik"],
                    "MPRVE_NOTES" => $request["result"][$i]["result_lab"],
                    "MPRVE_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                    "MPRVE_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                    "MPRVE_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                    "MPRVE_MPRDT_TEXT" => $request["gender"],
                    "MPRVE_MPRMO_CODE" => $check_exists_dob["MPRMO_CODE"],
                    "MPRVE_MPRMO_TEXT" => $request["date_of_birth"],
                    "MPRVE_MCOMP_CODE" => $request["company_hosital"],
                    "MPRVE_MCOMP_TEXT" => $company_data["MCOMP_NAME"],
                    "MPRVE_MBRAN_CODE" => $request["brand_hosital"],
                    "MPRVE_MBRAN_TEXT" => $brand_data["MBRAN_NAME"],
                    "MPRVE_STATUS" => 1,
                    "MPRVE_IS_DELETED" => 0,
                    "MPRVE_CREATED_BY" => $request["brand_hosital"],
                    "MPRVE_CREATED_TEXT" => $request["laboratorium_doctor"],
                    "MPRVE_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
    
                try {
                    $insert_res_patient = std_insert([
                        "table_name" => "MPRVE",
                        "data" => $insert_data_patient
                    ]);
                    
                    $log = [
                        $insert_data_patient
                    ];
    
                    // $data_alpha = [
                    //     "TRQRA_MAPLA_CODE" => $request["testing_center"],
                    //     "TRQRA_MAPLA_TEXT" => $testing_lab_center["MAPLA_TEXT"],
                    //     "TRQRA_MPRCA_CODE" => $check_exists_lab_type["MPRCA_CODE"],
                    //     "TRQRA_MPRCA_TEXT" => $check_exists_lab_type["MPRCA_TEXT"],
                    //     "TRQRA_MPRDT_CODE" => $check_exists_gender["MPRDT_CODE"],
                    //     "TRQRA_MPRDT_TEXT" => $request["gender"],
                    //     "TRQRA_MPRMO_CODE" => $check_exists_dob["MPRMO_CODE"],
                    //     "TRQRA_MPRMO_TEXT" => $request["date_of_birth"],
                    //     "TRQRA_MPRVE_CODE" => $patient_code["data"],
                    //     "TRQRA_MPRVE_TEXT" => $request["patient"],
                    //     "TRQRA_MPRVE_SKU" => $request["nik"],
                    //     "TRQRA_MPRVE_NOTES" => $request["result"],
                    //     "TRQRA_NOTES" => $request["testing_center_doctor"],
                    // ];
    
                    // $update_alpha = std_update([
                    //     "table_name" => "TRQRA",
                    //     "where" => ["TRQRA_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                    //     "data" => $data_alpha
                    // ]);
    
                    // $data_zeta = [
                    //     "TRQRZ_MAPLA_CODE" => $request["laboratorium"],
                    //     "TRQRZ_MAPLA_TEXT" => $laboratorium_center["MAPLA_TEXT"],
                    //     "TRQRZ_NOTES" => $request["laboratorium_doctor"],
                    // ];
    
                    // $update_zeta = std_update([
                    //     "table_name" => "TRQRZ",
                    //     "where" => ["TRQRZ_MASCO_CODE" => $check_sticker_code["MASCO_CODE"]],
                    //     "data" => $data_zeta
                    // ]);
    
                    if ($i == 0) {
                        $this->insert_scan_log($request,$check_sticker_code["MASCO_CODE"],$patient_code["data"]);
                    }
    
                    Log::info("Success on create patient : ".date("Y-m-d")." data : ".json_encode($log));
                } catch (\Exception $e) {
                    Log::critical("Fail on create patient : ".date("Y-m-d")." message : ".json_encode($e->getMessage()));
                }
            // 
        }

        return;

    }

    public function insert_log($request)
    {
        for ($i=0; $i < count($request["result"]); $i++) { 
            $data[] = [
                "LGLAB_CHAIN_CODE" => $request["chain_code"], 
                "LGLAB_TEST_LAB_TYPE" => $request["result"][$i]["test_lab_type"],
                "LGLAB_GENDER" => $request["gender"],
                "LGLAB_DOB" => $request["date_of_birth"],
                "LGLAB_PATIENT" => $request["patient"],
                "LGLAB_CST_EMAIL" => $request["customer_email"],
                "LGLAB_CST_PHONE_NUMBER" => $request["customer_phone_number"],
                "LGLAB_NIK" => $request["nik"],
                "LGLAB_RESULT" => $request["result"][$i]["result_lab"],
                "LGLAB_TESTING_CENTER" => $request["testing_center"],
                "LGLAB_TESTING_CENTER_DOCTOR" => $request["testing_center_doctor"],
                "LGLAB_LABORATORIUM" => $request["laboratorium"],
                "LGLAB_LABORATORIUM_DOCTOR" => $request["laboratorium_doctor"],
                "LGLAB_BRAND_HOSPITAL" => $request["brand_hosital"],
                "LGLAB_COMPANY_HOSPITAL" => $request["company_hosital"],
                "LGLAB_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
            ];
        }
        try {
            std_insert([
                "table_name" => "LGLAB",
                "data" => $data
            ]);

            Log::critical("Success on insert log : ".date("Y-m-d")." data : ".json_encode($data));
        } catch (\Exception $e) {
            Log::critical("Fail on insert log : ".date("Y-m-d")." message : ".json_encode($e->getMessage()));
        }
    }

    public function register_test_lab_customer($request)
    {
        $data_bride = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request["chain_code"],
                ],
                [
                    "field_name" => "MASCO_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request["brand_hosital"],
                ],
            ],
            "first_row" => true
        ]);

        $data = [
            "customer_email" => $request["customer_email"],
            "customer_phone_number" => $request["customer_phone_number"],
            "customer_name" => $request["patient"],
        ];

        $request["qr_alpha"] = $data_bride["MASCO_TRQAH_CODE"];
        $request["qr_zeta"] = $data_bride["MASCO_TRQZH_CODE"];

        $token_registration = curl_post("http://134.209.124.184/api/v4/test_lab_register/register",$data);
        if (isset($token_registration["data"])) {
            $request["user_token"] = $token_registration["data"]["token"];
            
            $this->send_email_after_register($request);
        } else {

            $this->send_email_not_register($request);
        }

    }

    public function send_email_after_register($request)
    {
        $data_brand = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $request["brand_hosital"]
            ]
        ],true);

        $test_date = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request["chain_code"],
                ],
            ],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "TRQRA",
                    "on1" => "MASCO_TRQAH_CODE",
                    "operator" => "=",
                    "on2" => "TRQRA_CODE"
                ]
            ],
            "first_row" => true
        ]);

        try {
            $to_name = $request["patient"];
            $to_email = $request["customer_email"];
            Mail::send("mail.account_verification_lab_result", [
                'data' => $request,
                'data_brand' => $data_brand["MBRAN_NAME"],
                'brand_logo' => $data_brand["MBRAN_IMAGE"],
                'test_date' => $test_date["TRQRA_EMP_SCAN_TIMESTAMP"],
            ], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Activation account email confirmation for CekOri User ".$to_name);
                $message->from("admin@cekori.com", "Activation account for CekOri User ".$to_name);
            });

            Log::critical("Success on send email lab result");
        } catch (\Exception $e) {
            Log::critical("Email lab result : ".json_encode($e->getMessage()));
        }
    }

    public function send_email_not_register($request)
    {
        $data_brand = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $request["brand_hosital"]
            ]
        ],true);

        $test_date = std_get([
            "table_name" => "MASCO",
            "select" => "*",
            "where" => [
                [
                    "field_name" => "MASCO_NOTES",
                    "operator" => "=",
                    "value" => $request["chain_code"],
                ],
            ],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "TRQRA",
                    "on1" => "MASCO_TRQAH_CODE",
                    "operator" => "=",
                    "on2" => "TRQRA_CODE"
                ]
            ],
            "first_row" => true
        ]);

        try {
            $to_name = $request["patient"];
            $to_email = $request["customer_email"];
            Mail::send("mail.account_verification_lab_result_without_registered", [
                'data' => $request,
                'data_brand' => $data_brand["MBRAN_NAME"],
                'brand_logo' => $data_brand["MBRAN_IMAGE"],
                'test_date' => $test_date["TRQRA_EMP_SCAN_TIMESTAMP"],
            ], function ($message) use ($to_name, $to_email) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Test result email notification for CekOri User ".$to_name);
                $message->from("admin@cekori.com", "Test result for CekOri User ".$to_name);
            });

            Log::critical("Success on send email lab result");
        } catch (\Exception $e) {
            Log::critical("Email lab result : ".json_encode($e->getMessage()));
        }
    }

    public function index(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            // Log::critical("Fail on validate, date : ".date("Y-m-d")." message : ".json_encode($validation_res));
            return response()->json([
                'message' => $validation_res,
                'data' => $request->all(),
                'err_code' => "E1"
            ], 400);
        }

        $check_company = $this->find_company($request);
        if ($check_company != null) {
            // Log::critical("Company hospital not found at data position : ".date("Y-m-d")." message : ".json_encode($check_company));
            return response()->json([
                'message' => "Company hospital not found at data position : ".implode(", ",$check_company),
                "position" => $check_company,
                'data' => $request->all(),
                'err_code' => "E2"
            ], 400);
        }

        $check_brand = $this->find_brand($request);
        if ($check_brand != null) {
            // Log::critical("Brand hospital not found at data position : ".date("Y-m-d")." message : ".json_encode($check_brand));
            return response()->json([
                'message' => "Brand hospital not found at data position : ".implode(", ",$check_brand),
                "position" => $check_brand,
                'data' => $request->all(),
                'err_code' => "E3"
            ], 400);
        }

        $check_bridge = $this->find_bridge($request);
        if ($check_bridge != null) {
            // Log::critical("Chain not found at data position : ".date("Y-m-d")." message : ".json_encode($check_bridge));
            return response()->json([
                'message' => "Chain not found at data position : ".implode(", ",$check_bridge),
                "position" => $check_bridge,
                'data' => $request->all(),
                'err_code' => "E4"
            ], 400);
        }

        $check_testing_lab_center = $this->find_testing_lab_center($request);
        if ($check_testing_lab_center != null) {
            // Log::critical("Testing lab center not found at data position : ".date("Y-m-d")." message : ".json_encode($check_testing_lab_center));
            return response()->json([
                'message' => "Testing lab center not found at data position : ".implode(", ",$check_testing_lab_center),
                "position" => $check_testing_lab_center,
                'data' => $request->all(),
                'err_code' => "E5"
            ], 400);
        }

        $check_laboratorium_center = $this->find_laboratorium_center($request);
        if ($check_laboratorium_center != null) {
            // Log::critical("Laboratorium center not found at data position : ".date("Y-m-d")." message : ".json_encode($check_laboratorium_center));
            return response()->json([
                'message' => "Laboratorium center not found at data position : ".implode(", ",$check_laboratorium_center),
                "position" => $check_laboratorium_center,
                'data' => $request->all(),
                'err_code' => "E6"
            ], 400);
        }

        $check_test_lab_type = $this->find_test_lab_type($request);
        if ($check_test_lab_type != null) {
            // Log::critical("Test lab type data not found at position : ".date("Y-m-d")." message : ".json_encode($check_test_lab_type));
            for ($i=0; $i < count($check_test_lab_type); $i++) { 
                $message[] = "Test lab type data not found at result position : ".$check_test_lab_type[$i]["header"].", test lab type : ".$check_test_lab_type[$i]["detail"];
            }
            return response()->json([
                'message' => $message,
                'data' => $request->all(),
                'err_code' => "E7"
            ], 400);
        }

        for ($i=0; $i < count($request->lab_result); $i++) { 
            $this->insert_master_data($request->lab_result[$i]);
            $this->insert_log($request->lab_result[$i]);
            $this->register_test_lab_customer($request->lab_result[$i]);
        }
      
        return response()->json("Success on insert data lab result", 200);
    }
}
