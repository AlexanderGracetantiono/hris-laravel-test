<?php

namespace App\Http\Controllers\About\SiapaItuGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $gnrm_data = std_get([
            "select" => ["*"],
            "table_name" => "siapa_itu_gnrm",
            "where" => [
                [
                    "field_name" => "siapa_itu_gnrm_id",
                    "operator" => "=",
                    "value" => "1",
                ]
            ],
            "first_row" => true
        ]);
        if ($gnrm_data == NULL) {
            abort(404);
        }
        $gnrm_data['siapa_itu_gnrm_meta_keywords'] = json_decode($gnrm_data['siapa_itu_gnrm_meta_keywords'],true);

        if (is_array($gnrm_data['siapa_itu_gnrm_meta_keywords'])) {
            $gnrm_data['siapa_itu_gnrm_meta_keywords'] = implode(', ',$gnrm_data['siapa_itu_gnrm_meta_keywords']);
        }
        return view('about/siapa_itu_gnrm/view', ['gnrm_data' => $gnrm_data]);
    }
}
