<?php

namespace App\Http\Controllers\About\HimpunanPeraturanGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    public function index()
    {
        return view('about/himpunan_peraturan_gnrm/add');
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "himpunan_peraturan_gnrm_no" => "required|numeric|unique:himpunan_peraturan_gnrm,himpunan_peraturan_gnrm_no",
            "himpunan_peraturan_gnrm_peraturan" => "required|max:500",
            "himpunan_peraturan_gnrm_tentang" => "required|max:255",
            "himpunan_peraturan_gnrm_attachment" => "required|file|mimes:pdf|max:30720"
        ]);

        $attributeNames = [
            "himpunan_peraturan_gnrm_no" => "Urutan Ke",
            "himpunan_peraturan_gnrm_peraturan" => "Peraturan",
            "himpunan_peraturan_gnrm_tentang" => "Tentang",
            "himpunan_peraturan_gnrm_attachment" => "Lampiran",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $file = $request->file('himpunan_peraturan_gnrm_attachment');
        $filename = date("Ymd")."_".uniqid().".".$file->getClientOriginalExtension();
        $upload_dir = "storage/files/himpunan_peraturan_gnrm/";
        
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam proses upload files, silahkan coba berberapa saat lagi"
            ],500);
        }
        
        $file->move($upload_dir, $filename);

        $insert_res = std_insert([
            "table_name" => "himpunan_peraturan_gnrm",
            "data" => [
                "himpunan_peraturan_gnrm_no" => $request->himpunan_peraturan_gnrm_no,
                "himpunan_peraturan_gnrm_peraturan" => $request->himpunan_peraturan_gnrm_peraturan,
                "himpunan_peraturan_gnrm_tentang" => $request->himpunan_peraturan_gnrm_tentang,
                "himpunan_peraturan_gnrm_attachment" => $filename,
                "himpunan_peraturan_gnrm_created_by" => session("user_code"),
                "himpunan_peraturan_gnrm_created_by_name" => session("user_name"),
                "himpunan_peraturan_gnrm_changed_by" => NULL,
                "himpunan_peraturan_gnrm_changed_by_name" => NULL,
                "himpunan_peraturan_gnrm_created_time" => date("Y-m-d H:i:s"),
                "himpunan_peraturan_gnrm_changed_time" => NULL
            ]
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam menyimpan data, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
