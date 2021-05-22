<?php

namespace App\Http\Controllers\MasterData\BatchPackaging;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([5]);
    }

    public function index(Request $request)
    {
        $closed_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_ACTIVATION_STATUS",
                "operator" => "=",
                "value" => "2",
            ],
            [
                "field_name" => "MABPR_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code'),
            ],
        ]);

        $plants = get_master_plant("*", [
            [
                "field_name" => "MAPLA_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
            [
                "field_name" => "MAPLA_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MAPLA_MCOMP_CODE",
                "operator" => "=",
                "value" => session('company_code')
            ],
        ]);

        $data = get_master_batch_packaging("*",[
            [
                "field_name" => "MABPA_CODE",
                "operator" => "=",
                "value" => $request->code,
            ],
        ],true);

        $selected_batch_production = get_master_batch_production("*",[
            [
                "field_name" => "MABPR_CODE",
                "operator" => "=",
                "value" => $data["MABPA_MABPR_CODE"],
            ],
        ],true);

        return view('master_data/batch_packaging/edit',[
            'plants' => $plants,
            'closed_batch_production' => $closed_batch_production,
            'data' => $data,
            'selected_batch_production' => $selected_batch_production,
        ]);
    }

    public function update(Request $request)
    {
        $update_data = [
            "MABPA_NOTES" => $request->MABPA_NOTES,
            "MABPA_ACTIVATION_STATUS" => "1",
            "MABPA_ACTIVATION_TIMESTAMP" => date("Y-m-d H:i:s"),
            "MABPA_UPDATED_BY" => session("user_id"),
            "MABPA_UPDATED_TEXT" => session("user_name"),
            "MABPA_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $update_res = std_update([
            "table_name" => "MABPA",
            "where" => ["MABPA_CODE" => $request->MABPA_CODE],
            "data" => $update_data
        ]);

        if ($update_res == false) {
            return response()->json([
                'message' => "There was an error saving the batch data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
