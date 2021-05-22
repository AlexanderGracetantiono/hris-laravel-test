<?php

namespace App\Http\Controllers\Transaction\OrderQr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class QrSetting extends Controller
{
    public function __construct() {
        check_is_role_allowed([3]);
    }

    public function index(Request $request)
    {
        $code = $request->code;
        $qr_route = $request->qr_route;
        return view('transaction/order_qr/qr_setting', ["code" => $code,"qr_route" => $qr_route]);
    }
}
