<?php

namespace App\Http\Controllers\MasterData\AdminCompany;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->maadmin_id) {
            $delete_res = std_delete([
                "table_name" => "maadmins",
                "where" => [
                    "maadmin_id" => $request->maadmin_id
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
