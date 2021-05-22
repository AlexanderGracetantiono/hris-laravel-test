<?php

namespace App\Http\Controllers\About\HimpunanPeraturanGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user_code != NULL) {
            $user_data = std_get([
                "select" => ["himpunan_peraturan_gnrm_id", "himpunan_peraturan_gnrm_no", "himpunan_peraturan_gnrm_peraturan","himpunan_peraturan_gnrm_tentang", "himpunan_peraturan_gnrm_attachment"],
                "table_name" => "himpunan_peraturan_gnrm",
                "where" => [
                    [
                        "field_name" => "himpunan_peraturan_gnrm_id",
                        "operator" => "=",
                        "value" => $request->user_code
                    ]
                ],
                "first_row" => true,
            ]);
            if ($user_data == NULL) {
                abort(404);
            }
            return view('about/himpunan_peraturan_gnrm/edit', ['user_data' => $user_data]);
        }
        else{
            abort(404);
        }
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "himpunan_peraturan_gnrm_no" => "required|numeric",
            "himpunan_peraturan_gnrm_peraturan" => "required|max:500",
            "himpunan_peraturan_gnrm_tentang" => "required|max:255",
            "himpunan_peraturan_gnrm_attachment" => "file|mimes:pdf|max:30720"
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

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "himpunan_peraturan_gnrm_no" => $request->himpunan_peraturan_gnrm_no,
                "himpunan_peraturan_gnrm_peraturan" => $request->himpunan_peraturan_gnrm_peraturan,
                "himpunan_peraturan_gnrm_tentang" => $request->himpunan_peraturan_gnrm_tentang,
                "himpunan_peraturan_gnrm_changed_by" => session("user_code"),
                "himpunan_peraturan_gnrm_changed_by_name" => session("user_name"),
                "himpunan_peraturan_gnrm_changed_time" => date("Y-m-d H:i:s")
        ];

        if($request->hasFile('himpunan_peraturan_gnrm_attachment')){
            $file = $request->file('himpunan_peraturan_gnrm_attachment');
            $filename = date("Ymd")."_".uniqid().".".$file->getClientOriginalExtension();
            $upload_dir = "storage/files/himpunan_peraturan_gnrm/";
            if (!is_writable($upload_dir)) {
                return response()->json([
                    'message' => "Terjadi kesalahan dalam proses upload data, silahkan coba berberapa saat lagi"
                ],500);
            }
            $file->move($upload_dir, $filename);
            $update_data["himpunan_peraturan_gnrm_attachment"] = $filename;
        }

        $update_res = std_update([
            "table_name" => "himpunan_peraturan_gnrm",
            "where" => ["himpunan_peraturan_gnrm_id" => $request->himpunan_peraturan_gnrm_id],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
