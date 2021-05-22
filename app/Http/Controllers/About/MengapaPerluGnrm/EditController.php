<?php

namespace App\Http\Controllers\About\MengapaPerluGnrm;
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
            "table_name" => "mengapa_perlu_gnrm",
            "where" => [
                [
                    "field_name" => "mengapa_perlu_gnrm_id",
                    "operator" => "=",
                    "value" => 1
                ]
            ],
            "first_row" => true
        ]);

        if ($gnrm_data == NULL) {
            abort(404);
        }
        return view('about/mengapa_perlu_gnrm/edit', ['gnrm_data' => $gnrm_data]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "mengapa_perlu_gnrm_title" => "required|max:255",
            "mengapa_perlu_gnrm_text" => "required|max:1000000",
            "mengapa_perlu_gnrm_image" => "image|mimes:jpeg,png,jpg,png|max:2048|dimensions:width=1366,height=768",
            "mengapa_perlu_gnrm_image_alt" => "required|max:255",
            "mengapa_perlu_gnrm_meta_keywords" => "required|max:255",
            "mengapa_perlu_gnrm_meta_description" => "required|max:500"
        ]);

        $attributeNames = [
            "mengapa_perlu_gnrm_title" => "Judul",
            "mengapa_perlu_gnrm_text" => "Text",
            "mengapa_perlu_gnrm_image" => "Gambar",
            "mengapa_perlu_gnrm_image_alt" => "Gambar Alt",
            "mengapa_perlu_gnrm_meta_keywords" => "Keywords",
            "mengapa_perlu_gnrm_meta_description" => "Deskripsi"
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

        $keyword = json_decode($request->mengapa_perlu_gnrm_meta_keywords,true);

        for($i = 0;$i < count($keyword);$i++)
        {
            $keyword_value[$i] = $keyword[$i]['value'];
        }

        $keyword_value = json_encode($keyword_value);

        if($request->file('mengapa_perlu_gnrm_image') != NULL || $request->file('mengapa_perlu_gnrm_image') != "")
        {
            $mengapa_perlu_gnrm_pic = $request->file('mengapa_perlu_gnrm_image');
            $mengapa_perlu_gnrm_pic_extension = $mengapa_perlu_gnrm_pic->getClientOriginalExtension();
            $mengapa_perlu_gnrm_pic_name = time().'.'.$mengapa_perlu_gnrm_pic_extension;
            $mengapa_perlu_gnrm_pic->move('storage/images/mengapa_perlu_gnrm_pic', $mengapa_perlu_gnrm_pic_name);

            $update_data = [
                "mengapa_perlu_gnrm_title" => $request->mengapa_perlu_gnrm_title,
                "mengapa_perlu_gnrm_text" => $request->mengapa_perlu_gnrm_text,
                "mengapa_perlu_gnrm_image" => $mengapa_perlu_gnrm_pic_name,
                "mengapa_perlu_gnrm_image_alt" => $request->mengapa_perlu_gnrm_image_alt,
                "mengapa_perlu_gnrm_meta_keywords" => $keyword_value,
                "mengapa_perlu_gnrm_meta_description" => $request->mengapa_perlu_gnrm_meta_description,
                "mengapa_perlu_gnrm_changed_by" => session("user_code"),
                "mengapa_perlu_gnrm_changed_by_name" => session("user_name"),
                "mengapa_perlu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        } else {
            $update_data = [
                "mengapa_perlu_gnrm_title" => $request->mengapa_perlu_gnrm_title,
                "mengapa_perlu_gnrm_text" => $request->mengapa_perlu_gnrm_text,
                "mengapa_perlu_gnrm_image_alt" => $request->mengapa_perlu_gnrm_image_alt,
                "mengapa_perlu_gnrm_meta_keywords" => $keyword_value,
                "mengapa_perlu_gnrm_meta_description" => $request->mengapa_perlu_gnrm_meta_description,
                "mengapa_perlu_gnrm_changed_by" => session("user_code"),
                "mengapa_perlu_gnrm_changed_by_name" => session("user_name"),
                "mengapa_perlu_gnrm_changed_time" => date("Y-m-d H:i:s")
            ];
        }

        $update_res = std_update([
            "table_name" => "mengapa_perlu_gnrm",
            "where" => ["mengapa_perlu_gnrm_id" => 1],
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
