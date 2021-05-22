<?php

namespace App\Http\Controllers\Application\ApplicationVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user_code) {
            $delete_res = std_delete([
                "table_name" => "m_users",
                "where" => [
                    "user_code" => $request->user_code
                ]
            ]);
            if ($delete_res === false) {
                return response()->json([
                    'message' => "Terjadi kesalahan dalam menghapus data pengguna"
                ],500);
            }
            return response()->json([
                'message' => "Data pengguna berhasil dihapus"
            ],200);
        }
        return response()->json([
            'message' => "Terjadi kesalahan dalam menghapus data pengguna"
        ],500);
    }
}
