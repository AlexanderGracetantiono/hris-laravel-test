<?php
    function generate_code($company_code = null, $digit = null, $table = null)
    {
        if ($table == "MCOMP") {
            $table_name = "MCOMP_CODE";
        }
        else {
            $table_name = $table."_MCOMP_CODE";
        }

        if ($company_code == "") {
            $company_code = null;
        }

        $counter = std_get([
            "select" => "*",
            "table_name" => $table,
            "where" => [
                [
                    "field_name" => $table_name,
                    "operator" => "=",
                    "value" => $company_code,
                ]
            ],
            "count" => true
        ]);

        // $alpha_numeric= base_convert( $counter+1 , 10, 36 );
        // $limit = strlen($alpha_numeric);
        $numeric= $counter+1;
        $limit = strlen($numeric);

        $return = [];

        if ($digit < $limit) {
            $return["data"] = "Limit Reached";
            $return["status_code"] = "ERR";
        }
        else {
            // $code = $company_code.str_pad( null, $digit - $limit, "0", STR_PAD_LEFT).$alpha_numeric;
            $code = $company_code.str_pad( null, $digit - $limit, "0", STR_PAD_LEFT).$numeric;
            $return["data"] = strtoupper($code);
            $return["status_code"] = "OK";
        }

        return $return;
    }

    function generate_code_number($company_code = null, $digit = null, $table = null)
    {
        if ($table == "MCOMP") {
            $table_name = "MCOMP_CODE";
        }
        else {
            $table_name = $table."_MCOMP_CODE";
        }

        if ($company_code == "") {
            $company_code = null;
        }

        $counter = std_get([
            "select" => "*",
            "table_name" => $table,
            "where" => [
                [
                    "field_name" => $table_name,
                    "operator" => "=",
                    "value" => $company_code,
                ]
            ],
            "count" => true
        ]);

        $return = [];

        $code = $company_code.date('/m/Y').'/'.str_pad($counter+1, $digit, "0", STR_PAD_LEFT);
        $return["data"] = strtoupper($code);
        $return["status_code"] = "OK";

        return $return;
    }

    function generate_qr_code($order_code = null, $company_code = null, $quantity = null, $type = null)
    {
        if ($type == 1) {
            $table = "TRQRA";
            $prefix = "A";
        } elseif ($type == 2) {
            $table = "TRQRZ";
            $prefix = "Z";
        } elseif ($type == 3) {
            $table = "MASCO";
            $prefix = "S";
        }

        $count = std_get([
            "select" => $table."_ID",
            "table_name" => $table,
            "where" => [
                // [
                //     "field_name" => $table."_TRORD_CODE",
                //     "operator" => "=",
                //     "value" => $order_code,
                // ],
                [
                    "field_name" => $table."_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $company_code,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);
        $counter = $count+1;
        for ($i=0; $i < $quantity; $i++) {
            // $qr_code[$i] = base_convert( $counter++ , 10, 36 );
            $qr_code[$i] = $counter++;
            $code[$i] = strtoupper($company_code.$prefix.$qr_code[$i]);
        }
        
        $return = [];
        // $return["data"] = array_slice($code, -$quantity, $quantity, true);
        $return["data"] = $code;
        $return["status_code"] = "OK";

        return $return;
    }

    function generate_sticker_code($order_code = null, $company_code = null, $quantity = null)
    {
        $count = std_get([
            "select" => "MASCO_ID",
            "table_name" =>  "MASCO",
            "where" => [
                // [
                //     "field_name" => "MASCO_TRORD_CODE",
                //     "operator" => "=",
                //     "value" => $order_code,
                // ],
                [
                    "field_name" => "MASCO_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $company_code,
                ],
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $counter = $count;
        for ($i=0; $i < $quantity; $i++) {
            $sticker_code[$i] = base_convert( $counter+1 , 10, 36 );
            $code[$i] = strtoupper($company_code."S".$sticker_code[$i]);
            $counter++;
        }

        $return = [];
        $return["data"] = $code;
        $return["status_code"] = "OK";

        return $return;
    }

    function dateConvertToString($data = NULL)
    {
        $date_temp = date("Y-m-d", strtotime($data));

        $date_comment=date_create(date("Y-m-d", strtotime($data)));
        $today=date_create(date("Y-m-d"));
        $diff=date_diff($date_comment,$today);

        if ($diff->format("%a") == 0) {
            $date = "Today";
        } else if ($diff->format("%a") == 1) {
            $date = "Yesterday";
        } else {
            $date = $diff->format("%a")." Days Ago";
        }
        return $date;
    }

    function get_master_company($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MCOMP",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MCOMP_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_brand($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MBRAN",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MBRAN_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product_brand($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MBRAN",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MBRAN_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product_plant($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MAPLA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MAPLA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product_category($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MPRCA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MPRCA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MPRDT",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MPRDT_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product_model($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MPRMO",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MPRMO_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_product_version($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MPRVE",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MPRVE_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_plant($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MAPLA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MAPLA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_batch_production($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MABPR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MABPR_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_batch_packaging($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MABPA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MABPA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_sub_batch_packaging($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "SUBPA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "SUBPA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_employee($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MAEMP",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MAEMP_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_version($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MAVER",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MAVER_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_staff_production($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "STBPR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "STBPR_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_staff_packaging($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "STBPA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "STBPA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_master_batch_store($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MBSTR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MBSTR_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_transaction_qr_alpha($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "TRQRA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "TRQRA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_transaction_qr_zeta($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "TRQRZ",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "TRQRZ_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_pool_product($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "POPRD",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "POPRD_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_report_qr_customer($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "REPQR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "REPQR_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_product_attribute($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "TRPAT",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "TRPAT_ID",
                    "type" => "AsC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_legal_version($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "MALVR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "MALVR_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_scan_header($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "SCHED",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "SCHED_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }

    function get_scan_detail($select = ["*"], $conditions = null, $first_row = false)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "SCDET",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "SCDET_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => $first_row
        ]);

        return $data;
    }
    function get_log_email($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGEMA",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGEMA_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
        ]);

        return $data;
    }
    function get_log_download($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGDLD",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGDLD_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_otp($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGOTP",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGOTP_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_scan($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGSCN",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGSCN_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_generate_qr($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "FIORD",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "FIORD_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_download_qr($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGDQR",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGDQR_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_map($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "LGMAP",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "LGMAP_ID",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
    function get_log_chat($select = ["*"], $conditions = null, $first_row = false,$limit_data = 10)
    {
        $data = std_get([
            "select" => $select,
            "table_name" => "CAPI_LOG",
            "where" => $conditions,
            "order_by" => [
                [
                    "field" => "send_date",
                    "type" => "DESC",
                ]
            ],
            "first_row" => $first_row,
            "limit"=>$limit_data
            
        ]);

        return $data;
    }
?>
