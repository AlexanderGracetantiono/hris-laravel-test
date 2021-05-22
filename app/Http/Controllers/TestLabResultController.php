<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App;
use QrCode;

class TestLabResultController extends Controller
{
    public function view(Request $request)
    {
        return view('test_lab_result', [
            "brand" => "Test",
            "alpha" => base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate("MXX000100001A1")),
            "zeta" => base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate("MXX000100001Z1")),
        ]);
    }

    public function download(Request $request)
    {
        PDF::setOptions(['defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('test_lab_download', [
            "brand" => "Test",
            "alpha" => base64_encode(QrCode::format('svg')->size(40)->errorCorrection('H')->generate("MXX000100001A1")),
            "zeta" => base64_encode(QrCode::format('svg')->size(40)->errorCorrection('H')->generate("MXX000100001Z1")),
            // "color" => "#05747e"
        ]);
        return $pdf->download('qr_zeta.pdf');
    }

    public function test(Type $var = null)
    {
        return view('mail.account_verification_lab_result', [
            'data_brand' => "Brand Test",
            'brand_logo ' => null,
            'test_date' => "2020-04-10 12:00:00",
            'data' => [
                "patient" => "test",
                "customer_phone_number" => "+621",
                "customer_email" => "a@mail.com",
                "user_token" => "A",
                "qr_zeta" => "Test Z",
                "qr_alpha" => "Test A",
            ],
        ]);
    }
}
