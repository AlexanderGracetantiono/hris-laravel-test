<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $limit =10;
        if($request->limit!=null){
            $limit=$request->limit;
        }
if($request->log_type =="email"){
    $master_data_log_email = get_log_email("*",null,false,$limit);
    dd("Log Email:",$master_data_log_email);
}elseif($request->log_type =="download"){
    $master_data_log_download = get_log_download("*",null,false,$limit);
    dd("Log Download:",$master_data_log_download);
}elseif($request->log_type =="otp"){
    $master_data_log_otp = get_log_otp("*",null,false,$limit);
    dd("Log OTP:",$master_data_log_otp);
}elseif($request->log_type =="customer_scan"){
    $master_data_log_scan = get_log_scan("*",null,false,$limit);
    dd("Log Customer Scan:",$master_data_log_scan);
}elseif($request->log_type =="generate_qr"){
    $master_data_log_generate_qr = get_log_generate_qr("*",null,false,$limit);
    dd("Log Generated QR:",$master_data_log_generate_qr);
}elseif($request->log_type =="log_download_qr"){
    $master_data_log_generate_qr = get_log_download_qr("*",null,false,$limit);
    dd("Log Download QR:",$master_data_log_generate_qr);
}elseif($request->log_type =="log_chargin_map"){
    $master_data_log_generate_qr = get_log_map("*",null,false,$limit);
    dd("Log Chargin Map:",$master_data_log_generate_qr);
}elseif($request->log_type =="attribute"){
    $check_product_attribute = get_master_brand("*",[
        [
            "field_name" => "MBRAN_CODE",
            "operator" => "=",
            "value" => session("brand_code"),
        ]
    ],true);
    $data_attribute = get_product_attribute("*",[
        [
            "field_name" => "TRPAT_MBRAN_CODE",
            "operator" => "=",
            "value" => session("brand_code")
        ],
        [
            "field_name" => "TRPAT_KEY_TYPE",
            "operator" => "=",
            "value" => $check_product_attribute["MBRAN_TRPAT_TYPE"]
        ],
        // [
        //     "field_name" => "TRPAT_KEY_CODE",
        //     "operator" => "=",
        //     "value" => $request->code
        // ],
        [
            "field_name" => "TRPAT_TYPE",
            "operator" => "=",
            "value" => 2
        ],
    ]);
    dd("Log Attribut Per Brand:",$data_attribute);
}else{
    dd("
    http://localhost/cek_ori_v2/public/log?log_type=attribute
    http://localhost/cek_ori_v2/public/log?log_type=download
    http://localhost/cek_ori_v2/public/log?log_type=otp
    http://localhost/cek_ori_v2/public/log?log_type=email
    http://localhost/cek_ori_v2/public/log?log_type=customer_scan&limit=1
    http://localhost/cek_ori_v2/public/log?log_type=generate_qr&limit=1
    http://localhost/cek_ori_v2/public/log?log_type=log_download_qr&limit=1
    http://localhost/cek_ori_v2/public/log?log_type=log_chargin_map&limit=1
    ");
        // $check_product_attribute = get_master_brand("*",[
        //     [
        //         "field_name" => "MBRAN_CODE",
        //         "operator" => "=",
        //         "value" => session("brand_code"),
        //     ]
        // ],true);

        // $check_access = false;
        // if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
        //     $check_access = true;
        // }

        // return view('master_data/product_categories/view', [
        //     "product_categories_data" => $product_categories_data,
        //     "check_access" => $check_access,
        // ]);
}
    }
}
