<?php

namespace App\Http\Controllers\MasterData\EmployeesBrand;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LengthException;
use Illuminate\Support\Str;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index(Request $request)
    {
        if ($request->code == NULL) {
            abort(404);
        }

        $brand = get_master_product_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
        ],true);

        $master_employee_data = std_get([
            "select" => "*",
            "table_name" => "MAEMP",
            "order_by" => [
                [
                    "field" => "MAEMP_ID",
                    "type" => "DESC",
                ]
            ],
            "where" => [
                [
                    "field_name" => "MAEMP_IS_DELETED",
                    "operator" => "=",
                    "value" => "0"
                ],
                [
                    "field_name" => "MAEMP_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request->code
                ],
                // [
                //     "field_name" => "MAEMP_ROLE",
                //     "operator" => "=",
                //     "value" => "3"
                // ],
            ],
        ]);

        if ($brand["MBRAN_TYPE"] == 1) {
            for ($i=0; $i < count($master_employee_data); $i++) {
                if ($master_employee_data[$i]["MAEMP_ROLE"] == 1) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "CekOri Administrator";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 2) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "QR Approver";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 3) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "PIC Brand";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 4) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Production Administrator";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 5) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Packaging Administrator";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 6) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Production Staff";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 7) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Packaging Staff";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 8) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Store Inventory Administrator";
                }
            }
        } elseif ($brand["MBRAN_TYPE"] == 2) {
            for ($i=0; $i < count($master_employee_data); $i++) {
                if ($master_employee_data[$i]["MAEMP_ROLE"] == 1) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "CekOri Administrator";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 2) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "QR Approver";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 3) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "PIC Brand";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 4) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Test Lab Doctor";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 5) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Laboratorium Doctor";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 6) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Test Lab Staff";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 7) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Laboratorium Staff";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 8) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Result Doctor";
                }
            }
        }

        return view('master_data/brand_employees/view', [
            "master_employee_data" => $master_employee_data,
            "brand" => $brand,
        ]);
    }
}
