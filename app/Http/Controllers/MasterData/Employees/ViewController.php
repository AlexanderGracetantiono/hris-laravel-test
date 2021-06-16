<?php

namespace App\Http\Controllers\MasterData\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LengthException;
use Illuminate\Support\Str;

class ViewController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1,3,4,5,8]);
    }
    
    public function index()
    {
        $accessible_roles = [];
        $accessible_roles[] = 1;
            $accessible_roles[] = 2;
        // if (session('user_role') == 1) {
        //     $accessible_roles[] = 1;
        //     $accessible_roles[] = 2;
        //     // $accessible_roles[] = 3;
        // }
        // if (session('user_role') == 2) {
        //     $accessible_roles[] = 2;
        // }
        // if (session('user_role') == 3) {
        //     $accessible_roles[] = 4;
        //     $accessible_roles[] = 5;
        //     $accessible_roles[] = 8;
        // }
        // if (session('user_role') == 4) {
        //     $accessible_roles[] = 6;
        // }
        // if (session('user_role') == 5) {
        //     $accessible_roles[] = 7;
        // }
        // if (session('user_role') == 8) {
        //     $accessible_roles[] = 9;
        // }

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
                ]
            ],
            "where_in" => [
                "field_name" => "MAEMP_ROLE",
                "ids" => $accessible_roles
            ]
        ]);
// dd($master_employee_data);
        // if (session("brand_type") == 1) {
            for ($i=0; $i < count($master_employee_data); $i++) {
                // $master_employee_data[$i]["MAEMP_ROLE"] = "Staff";
                if ($master_employee_data[$i]["MAEMP_ROLE"] == 1) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "HRIS Administrator";
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 2) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Staff Admin";
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
                } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 9) {
                    $master_employee_data[$i]["MAEMP_ROLE"] = "Store Staff";
                }
            }
        // }
        // elseif (session("brand_type") == 2) {
        //     for ($i=0; $i < count($master_employee_data); $i++) {
        //         if ($master_employee_data[$i]["MAEMP_ROLE"] == 1) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "CekOri Administrator";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 2) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "QR Approver";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 3) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Lab PIC Brand";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 4) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Lab Testing Doctor";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 5) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Laboratorium Doctor";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 6) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Lab Testing Doctor Staff";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 7) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Laboratorium Staff";
        //         } elseif ($master_employee_data[$i]["MAEMP_ROLE"] == 8) {
        //             $master_employee_data[$i]["MAEMP_ROLE"] = "Result Doctor";
        //         }
        //     }
        // }

        return view('master_data/master_employees/view', ["master_employee_data" => $master_employee_data]);
    }
}
