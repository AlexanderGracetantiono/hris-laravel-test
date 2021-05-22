<?php

namespace App\Http\Controllers\About\HimpunanPeraturanGnrm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user_code) {
            $delete_res = std_delete([
                "table_name" => "himpunan_peraturan_gnrm",
                "where" => [
                    "himpunan_peraturan_gnrm_id" => $request->user_code
                ]
            ]);

            if ($delete_res === false) {
                return response()->json([
                    'message' => "Terjadi kesalahan dalam menghapus data"
                ],500);
            }
            return response()->json([
                'message' => "Data berhasil dihapus"
            ],200);
        }
        return response()->json([
            'message' => "Terjadi kesalahan dalam menghapus data"
        ],500);
    }
}
