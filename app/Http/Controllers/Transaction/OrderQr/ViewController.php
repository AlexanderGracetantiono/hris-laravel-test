<?php

namespace App\Http\Controllers\Transaction\OrderQr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3]);
    }

    public function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public function index()
    {
        $where = [];
        if (session('company_code') != "ORI0001") {
            $where[] = [
                "field_name" => "TRORD_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];
        }

        $data = std_get([
            "field_name" => "*",
            "table_name" => "TRORD",
            "where" => $where,
            "order_by" => [
                [
                    "field" => "TRORD_ID",
                    "type" => "DESC",
                ]
            ],
        ]);

        if ($data != null) {
            $counter = 0;
            $start =count($data)-1;

            for ($i=$start; $i >= 0; $i--) { 
                if ($data[$i]["TRORD_STATUS"] != "2") {
                if ($data[$i]["TRORD_GENERATE_STATUS"] == "0") {
                    $data[$i]["TRORD_ESTIMATED_TIME"] = $counter+=1;
                } elseif ($data[$i]["TRORD_GENERATE_STATUS"] == "1") {
                    $data[$i]["TRORD_ESTIMATED_TIME"] = "In Progress";
                } elseif ($data[$i]["TRORD_GENERATE_STATUS"] == "2") {
                    $data[$i]["TRORD_ESTIMATED_TIME"] = "Done Generate";
                }
                }else{
                    $data[$i]["TRORD_ESTIMATED_TIME"] = "Done Generate";
                }
                // echo "DATA".$i."/".$start."/".$counter."\n";
            }

            for ($i=$start; $i >=0; $i--) { 
                if ($data[$i]["TRORD_ESTIMATED_TIME"] != "Done Generate" && $data[$i]["TRORD_ESTIMATED_TIME"] != "In Progress") {
                    $current_time = $this->convertToHoursMins($data[$i]["TRORD_ESTIMATED_TIME"] * 10,'%02d hours %02d minutes');
                    // $data[$i]["TRORD_ESTIMATED_TIME"] = date('Y-m-d H:i:s', strtotime(sprintf('- %d second', $current_time * 60)));
                    $data[$i]["TRORD_ESTIMATED_TIME"] = "Estimated : ".$current_time;
                }
            }
        }

        return view('transaction/order_qr/view', ["data" => $data]);
    }
}
