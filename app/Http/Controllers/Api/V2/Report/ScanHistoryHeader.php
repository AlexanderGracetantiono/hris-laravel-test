<?php

namespace App\Http\Controllers\Api\V2\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScanHistoryHeader extends Controller
{
	public function index(Request $request){
        if (isset($request->limit)) {
            $limit = $request->limit;
        }
        else{
            $limit = NULL;
        }

        if (isset($request->offset)) {
            $offset = $request->offset;
        }
        else{
            $offset = NULL;
        }

        $conditions = NULL;
        if (isset($request->conditions) && is_array($request->conditions)) {
            $conditions = [];
            for ($i=0; $i < count($request->conditions); $i++) {
                if (isset($request->conditions[$i]["field_name"]) && isset($request->conditions[$i]["operator"]) && isset($request->conditions[$i]["value"])) {
                    $conditions[$i]["field_name"] = $request->conditions[$i]["field_name"];
                    $conditions[$i]["operator"] = $request->conditions[$i]["operator"];
                    $conditions[$i]["value"] = $request->conditions[$i]["value"];
                }
            }
        }

        $temp_data = std_get([
            "select" => ["SCHED_TRQRZ_CODE","SCDET_MPRVE_SKU","SCLOG_ID","MBRAN_IMAGE","SCLOG_CST_SCAN_TIMESTAMP"],
            "table_name" => "SCDET",
            "where" => $conditions,
            "order_by" => [
				[
					"field" => "SCLOG_ID",
					"type" => "DESC"
				]
			],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "SCHED",
                    "on1" => "SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCDET_SCHED_ID",
                ],
                [
                    "join_type" => "INNER",
                    "table_name" => "SCLOG",
                    "on1" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCDET_SCHED_ID",
                ],
                [
                    "join_type" => "INNER",
                    "table_name" => "MBRAN",
                    "on1" => "MBRAN_CODE",
                    "operator" => "=",
                    "on2" => "SCHED_MBRAN_CODE",
                ],
            ],
            // "limit" => $limit,
            // "offset" => $offset,
            "distinct" => true,
        ]);

        $real_data = [];
        if ($temp_data != null) {
            for ($i=0; $i < count($temp_data); $i++) { 
                $temp_data[$i]["brand_logo"] = asset('storage/images/brand_logo/').'/'.$temp_data[$i]["MBRAN_IMAGE"];
    
                $temp_unique[] = $temp_data[$i]['SCHED_TRQRZ_CODE'];
            }
            $unique_data = array_values(array_unique($temp_unique));
    
            for ($i=0; $i < count($unique_data); $i++) { 
                for ($j=0; $j < count($temp_data); $j++) { 
                    if ($unique_data[$i] == $temp_data[$j]["SCHED_TRQRZ_CODE"]) {
                        $data[] = $temp_data[$j];
                        break;
                    }
                }
            }
            
            for ($i = 0; $i < count($data); $i++) { 
                if ($i == $limit) {
                    break;
                }
                if (($i + $offset) >= count($data)) {
                    break;
                }
    
                $real_data[] = $data[($i + $offset)];
            }
        }

        return response()->json($real_data, 200);
    }
}