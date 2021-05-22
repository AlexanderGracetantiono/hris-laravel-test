<?php

namespace App\Http\Controllers\About\SiapaItuGnrm;
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
            "table_name" => "siapa_itu_gnrm",
            "where" => [
                [
                    "field_name" => "siapa_itu_gnrm_id",
                    "operator" => "=",
                    "value" => 1
                ]
            ],
            "first_row" => true
        ]);
        if ($gnrm_data == NULL) {
            abort(404);
        }
        return view('about/siapa_itu_gnrm/edit', ['gnrm_data' => $gnrm_data]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "siapa_itu_gnrm_title" => "required|max:255",
            "siapa_itu_gnrm_text" => "required|max:1000000",
            "siapa_itu_gnrm_image" => "image|mimes:jpeg,png,jpg,png|max:2048|dimensions:width=1366,height=768",
            "siapa_itu_gnrm_image_alt" => "required|max:255",
            "siapa_itu_gnrm_meta_keywords" => "required|max:255",
            "siapa_itu_gnrm_meta_description" => "required|max:500"
        ]);

        $attributeNames = [
            "siapa_itu_gnrm_title" => "Judul",
            "siapa_itu_gnrm_text" => "Text",
            "siapa_itu_gnrm_image" => "Gambar",
            "siapa_itu_gnrm_image_alt" => "Gambar Alt",
            "siapa_itu_gnrm_meta_keywords" => "Keywords",
            "siapa_itu_gnrm_meta_description" => "Deskripsi"
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

        $keyword = json_decode($request->siapa_itu_gnrm_meta_keywords,true);

        for($i = 0;$i < count($keyword);$i++)
        {
            $keyword_value[$i] = $keyword[$i]['value'];
        }

        $keyword_value = json_encode($keyword_value);

        if($request->file('siapa_itu_gnrm_image') != null || $request->file('siapa_itu_gnrm_image') != "")
        {
            $siapa_itu_gnrm_pic = $request->file('siapa_itu_gnrm_image');
            $siapa_itu_gnrm_pic_extension = $siapa_itu_gnrm_pic->getClientOriginalExtension();
            $siapa_itu_gnrm_pic_name = time().'.'.$siapa_itu_gnrm_pic_extension;
            $siapa_itu_gnrm_pic->move('storage/images/siapa_itu_gnrm_pic', $siapa_itu_gnrm_pic_name);
            $update_data = [
                "siapa_itu_gnrm_title" => $request->siapa_itu_gnrm_title,
                "siapa_itu_gnrm_text" => $request->siapa_itu_gnrm_text,
                "siapa_itu_gnrm_image" => $siapa_itu_gnrm_pic_name,
                "siapa_itu_gnrm_image_alt" => $request->siapa_itu_gnrm_image_alt,
                "siapa_itu_gnrm_meta_keywords" => $keyword_value,
                "siapa_itu_gnrm_meta_description" => $request->siapa_itu_gnrm_meta_description,
                "siapa_itu_gnrm_changed_by" => session("user_code"),
                "siapa_itu_gnrm_changed_by_name" => session("user_name"),
                "siapa_itu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        } else {
            $update_data = [
                "siapa_itu_gnrm_title" => $request->siapa_itu_gnrm_title,
                "siapa_itu_gnrm_text" => $request->siapa_itu_gnrm_text,
                "siapa_itu_gnrm_image_alt" => $request->siapa_itu_gnrm_image_alt,
                "siapa_itu_gnrm_meta_keywords" => $keyword_value,
                "siapa_itu_gnrm_meta_description" => $request->siapa_itu_gnrm_meta_description,
                "siapa_itu_gnrm_changed_by" => session("user_code"),
                "siapa_itu_gnrm_changed_by_name" => session("user_name"),
                "siapa_itu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        }


        $update_res = std_update([
            "table_name" => "siapa_itu_gnrm",
            "where" => ["siapa_itu_gnrm_id" => 1],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam update data pengguna, silahkan coba berbersiapa saat lagi"
            ],500);
        }


        return response()->json([
            'message' => "OK"
        ],200);
    }
}
