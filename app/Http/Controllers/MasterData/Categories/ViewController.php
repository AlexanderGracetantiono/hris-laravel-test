<?php

namespace App\Http\Controllers\MasterData\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }

    public function index()
    {
        $product_categories_data = get_master_product_category("*",[
            [
                "field_name" => "MPRCA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
            [
                "field_name" => "MPRCA_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
            ],
        ]);

        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
            $check_access = true;
        }

        return view('master_data/product_categories/view', [
            "product_categories_data" => $product_categories_data,
            "check_access" => $check_access,
        ]);
    }
}
