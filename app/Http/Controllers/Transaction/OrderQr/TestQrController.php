<?php

namespace App\Http\Controllers\Transaction\OrderQr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use QrCode;
use DateInterval;
use DateTime;

class TestQrController extends Controller
{
    public function __construct()
    {
        // check_is_role_allowed([3]);
    }

    function hex_rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public function alpha(Request $request)
    {
        
        $orientation_code = "ptr";
        $qr_type = "alpha";
        $qr_code_trq = "MXX0001/04/2021/011";
        $time = new DateTime();
        $name_time = $time->format('YmdHis'); 
        $file_name = $name_time . "_SAMPLE_BRAND_PTR_ALPHA.pdf";
        $time->add(new DateInterval('P1D'));
        $stamp = $time->format('Y-m-d H:i:s');
        // $upload_dir = "public/storage/file/qr_file/";
        $upload_dir = "storage/file/qr_file/"; //localhostonly

        $jumlah_qr =300;
        $size_mm =12;
        if($request->qr_size){
            $size_mm =$request->qr_size;
        }
        if($request->jumlah_qr){
            $jumlah_qr =$request->jumlah_qr;
        }
        $qr_size = round($size_mm * 3.333333333333333); //mm to pixel
        $dpi=96;
        $mm_divided_by=25.4;
        // $paper_width = 297;
        // $paper_height =	420;
        // $qr_size = round(((($data_qr_master[0]["TRORD_SIZE"]-1)/$mm_divided_by)*$dpi)-1);
        // $ratio_border_width=$qr_size/0.8695652173913043;
        // $margin_left_right=5.82;
        // $row_count = ($paper_width - 2*$margin_left_right)/$border_size;
        // echo nl2br($paper_width." * ".$paper_height."\n");
        // echo nl2br($dpi." _ ". $mm_divided_by."\n");
        // echo nl2br($data_qr_master[0]["TRORD_SIZE"]." => ".$border_size." _ ". $qr_size."\n");
        // echo nl2br($paper_width." _ ". $row_count ."\n");
       
        PDF::setOptions(['defaultFont' => 'arial']);
        for ($i = 0; $i < $jumlah_qr; $i++) {
            $qrcode[$i]["qr_code"] = "1";
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->color(145, 0, 4)->generate('1'));
        }
        $qr_color= "#63e2ff";
        $qr_layout= 'transaction/order_qr/qr_alpha_test_wo_color';
        $qr_image= asset('border_alpha.jpeg');
        switch ($request->type) {
            case 'alpha':
                $qr_color= "#63e2ff";
                $qr_image= asset('border_alpha.jpeg');
                $qr_layout= 'transaction/order_qr/qr_alpha_test_wo_color';
                break;
                case 'zeta':
                    $qr_color= "#05747e";
                    $qr_image= asset('border_zeta.jpeg');
                    $qr_layout= 'transaction/order_qr/qr_alpha_test_wo_color';
                    break;
                    case 'bridge':
                        $qr_color= "#000000";
                        $qr_image= asset('border_sticker.jpeg');
                        $qr_layout= 'transaction/order_qr/sticker_code_test';
                break;
            
            default:
                # code...
                break;
        }
        if($request->type){
            $jumlah_qr =$request->jumlah_qr;
        }
        if($request->clean=="false"){
                $pdf = PDF::loadView('transaction/order_qr/qr_alpha_test', [
                    "company_name" => "MMComp2",
                    "qrcode" => $qrcode,
                    "color" =>  $qr_color,
                    "qr_size" => $qr_size,
                    "border_size" => $size_mm,
                    "file_name" => $file_name,
                    "qr_image" => $qr_image,
                    
                    ]);
                }else{
            $pdf = PDF::loadView($qr_layout, [
                "company_name" => "MMComp2",
                "qrcode" => $qrcode,
                "color" => $qr_color,
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "qr_image" => $qr_image,

            ]);
        }
           
        $customPaperA3Plus = array(0,0,933,1369);
        $customPaperA3Normal = array(0,0,842,1191);
        $pdf->setPaper($customPaperA3Plus);
        // $font = $pdf->getFontMetrics()->get_font("helvetica", "bold");
        // $pdf->getCanvas()->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}");
        // return view($pdf);
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            $pdf->save($upload_dir . '/' . $file_name);
        }
        return $pdf->download($file_name);
    }
   
}
