<?php

namespace App\Http\Controllers\MasterData\AdminVendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $maadmin_data = std_get([
            "select" => ["maadmin_id","maadmin_username","maadmin_email","maadmin_real_name","maadmin_phone","maadmin_status","maadmin_sex","mavendor_name","macompany_name"],
            "table_name" => "maadmins",
            "where" => [
                [
                    "field_name" => "maadmin_role",
                    "operator" => "=",
                    "value" => "admin_vendor",
                ]
            ],
            "join" => [
                [
                    "join_type" => "INNER",
                    "table_name" => "macompanies",
                    "on1" => "macompanies.macompany_id",
                    "operator" => "=",
                    "on2" => "maadmins.maadmin_macompany_id",
                ],
                [
                    "join_type" => "INNER",
                    "table_name" => "mavendors",
                    "on1" => "mavendors.mavendor_id",
                    "operator" => "=",
                    "on2" => "maadmins.maadmin_mavendor_id",
                ]
            ],
            "order_by" => [
                [
                    "field" => "maadmin_real_name",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('master_data/companies/admin_vendor/view', ["maadmin_data" => $maadmin_data]);
    }
}
