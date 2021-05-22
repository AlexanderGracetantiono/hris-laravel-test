<?php

namespace App\Http\Controllers\Api\V1\Version;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LegalVersion extends Controller
{
    public function index(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "pp_version" => "required",
            "ts_version" => "required",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->all(),
                "data" => $request->all(),
                "err_code" => "E1"
            ], 400);
        }

        $check_data = get_legal_version("*",[
            [
                "field_name" => "MALVR_ID",
                "operator" => "=",
                "value" => 1
            ]
        ],true);

        $check_latest_privacy_policy = true;
        if ($check_data["MALVR_PRIVACY_POLICY_VERSION"] != $request->pp_version) {
            $check_latest_privacy_policy = false;
        }

        $check_latest_term_services = true;
        if ($check_data["MALVR_TERM_SERVICE_VERSION"] != $request->ts_version) {
            $check_latest_term_services = false;
        }

        if ($check_latest_privacy_policy == true && $check_latest_term_services == true) {
            return response()->json([
                'message' => "Privacy policy & term service version is latest"
            ], 500);
        }else {
            return response()->json([
                "message" => "Privacy policy / term service version is not latest",
                "latest_privacy_policy_version" => $check_data["MALVR_PRIVACY_POLICY_VERSION"],
                "latest_term_services_version" => $check_data["MALVR_TERM_SERVICE_VERSION"],
                "data" => $request->all()
            ], 200);
        }

    }
}
