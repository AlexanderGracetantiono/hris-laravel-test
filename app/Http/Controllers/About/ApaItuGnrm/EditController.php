<?php

namespace App\Http\Controllers\About\ApaItuGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $gnrm_data = std_get([
            "select" => ["*"],
            "table_name" => "apa_itu_gnrm",
            "where" => [
                [
                    "field_name" => "apa_itu_gnrm_id",
                    "operator" => "=",
                    "value" => 1
                ]
            ],
            "first_row" => true
        ]);
        if ($gnrm_data == NULL) {
            abort(404);
        }
        return view('about/apa_itu_gnrm/edit', ['gnrm_data' => $gnrm_data]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "apa_itu_gnrm_title" => "required|max:255",
            "apa_itu_gnrm_text" => "required|max:4000000000",
            "apa_itu_gnrm_image" => "image|mimes:jpeg,png,jpg,png|max:2048|dimensions:width=1366,height=768",
            "apa_itu_gnrm_image_alt" => "required|max:255",
            "apa_itu_gnrm_meta_keywords" => "required|max:255",
            "apa_itu_gnrm_meta_description" => "required|max:500"
        ]);

        $attributeNames = [
            "apa_itu_gnrm_title" => "Judul",
            "apa_itu_gnrm_text" => "Text",
            "apa_itu_gnrm_image" => "Gambar",
            "apa_itu_gnrm_image_alt" => "Gambar Alt",
            "apa_itu_gnrm_meta_keywords" => "Keywords",
            "apa_itu_gnrm_meta_description" => "Deskripsi"
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

        $keyword = json_decode($request->apa_itu_gnrm_meta_keywords,true);

        for($i = 0;$i < count($keyword);$i++)
        {
            $keyword_value[$i] = $keyword[$i]['value'];
        }

        $keyword_value = json_encode($keyword_value);

        if($request->file('apa_itu_gnrm_image') != null || $request->file('apa_itu_gnrm_image') != "")
        {
            $apa_itu_gnrm_pic = $request->file('apa_itu_gnrm_image');
            $apa_itu_gnrm_pic_extension = $apa_itu_gnrm_pic->getClientOriginalExtension();
            $apa_itu_gnrm_pic_name = time().'.'.$apa_itu_gnrm_pic_extension;
            $apa_itu_gnrm_pic->move('storage/images/apa_itu_gnrm_pic', $apa_itu_gnrm_pic_name);
            $update_data = [
                "apa_itu_gnrm_title" => $request->apa_itu_gnrm_title,
                "apa_itu_gnrm_text" => $request->apa_itu_gnrm_text,
                "apa_itu_gnrm_image" => $apa_itu_gnrm_pic_name,
                "apa_itu_gnrm_image_alt" => $request->apa_itu_gnrm_image_alt,
                "apa_itu_gnrm_meta_keywords" => $keyword_value,
                "apa_itu_gnrm_meta_description" => $request->apa_itu_gnrm_meta_description,
                "apa_itu_gnrm_changed_by" => session("user_code"),
                "apa_itu_gnrm_changed_by_name" => session("user_name"),
                "apa_itu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        } else {
            $update_data = [
                "apa_itu_gnrm_title" => $request->apa_itu_gnrm_title,
                "apa_itu_gnrm_text" => $request->apa_itu_gnrm_text,
                "apa_itu_gnrm_image_alt" => $request->apa_itu_gnrm_image_alt,
                "apa_itu_gnrm_meta_keywords" => $keyword_value,
                "apa_itu_gnrm_meta_description" => $request->apa_itu_gnrm_meta_description,
                "apa_itu_gnrm_changed_by" => session("user_code"),
                "apa_itu_gnrm_changed_by_name" => session("user_name"),
                "apa_itu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        }

        $update_res = std_update([
            "table_name" => "apa_itu_gnrm",
            "where" => ["apa_itu_gnrm_id" => 1],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data pengguna, silahkan coba berberapa saat lagi"
            ],500);
        }


        return response()->json([
            'message' => "OK"
        ],200);
    }
}
