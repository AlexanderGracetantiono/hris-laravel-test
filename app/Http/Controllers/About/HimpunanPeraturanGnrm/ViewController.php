<?php

namespace App\Http\Controllers\About\HimpunanPeraturanGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = std_get([
            "select" => ["himpunan_peraturan_gnrm_id", "himpunan_peraturan_gnrm_no", "himpunan_peraturan_gnrm_peraturan","himpunan_peraturan_gnrm_tentang", "himpunan_peraturan_gnrm_attachment"],
            "table_name" => "himpunan_peraturan_gnrm",
            "order_by" => [
                [
                    "field" => "himpunan_peraturan_gnrm_no",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('about/himpunan_peraturan_gnrm/view', ['data' => $data]);
    }
}
