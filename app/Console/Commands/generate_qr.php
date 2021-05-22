<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use DB;
use DateTime;
use DateInterval;
use PDF;
use QrCode;

class generate_qr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR based with queue on TRORD, only when there are no queue in progress';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $check_in_progress = $this->check_progress();
        if ($check_in_progress != null) {
            return;
        }

        $check_queue = $this->check_queue();
        if ($check_queue == null) {
            Log::info("No queue to generate");
            return;
        }

        Log::info("Start at : ".date("Y-m-d H:i:s"));

        $code_alpha = generate_qr_code($check_queue["TRORD_CODE"], $check_queue["TRORD_MBRAN_CODE"], $check_queue["TRORD_QTY"], 1);
        $code_zeta = generate_qr_code($check_queue["TRORD_CODE"], $check_queue["TRORD_MBRAN_CODE"], $check_queue["TRORD_QTY"], 2);
        $sticker_code = generate_qr_code($check_queue["TRORD_CODE"], $check_queue["TRORD_MBRAN_CODE"], $check_queue["TRORD_QTY"], 3);

        for ($i=0; $i < count($code_alpha["data"]); $i++) {
            $insert_qr_alpha[$i] = [
                "TRQRA_CODE" => $code_alpha["data"][$i],
                "TRQRA_TRORD_CODE" => $check_queue["TRORD_CODE"],
                "TRQRA_MCOMP_CODE" => $check_queue["TRORD_MCOMP_CODE"],
                "TRQRA_MCOMP_TEXT" => $check_queue["TRORD_MCOMP_TEXT"],
                "TRQRA_MBRAN_CODE" => $check_queue["TRORD_MBRAN_CODE"],
                "TRQRA_MBRAN_TEXT" => $check_queue["TRORD_MBRAN_TEXT"],
                "TRQRA_CREATED_BY" => session("user_id"),
                "TRQRA_CREATED_TEXT" => session("user_name"),
                "TRQRA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        for ($i=0; $i < count($code_zeta["data"]); $i++) {
            $insert_qr_zeta[$i] = [
                "TRQRZ_CODE" => $code_zeta["data"][$i],
                "TRQRZ_TRORD_CODE" => $check_queue["TRORD_CODE"],
                "TRQRZ_MCOMP_CODE" => $check_queue["TRORD_MCOMP_CODE"],
                "TRQRZ_MCOMP_TEXT" => $check_queue["TRORD_MCOMP_TEXT"],
                "TRQRZ_MBRAN_CODE" => $check_queue["TRORD_MBRAN_CODE"],
                "TRQRZ_MBRAN_TEXT" => $check_queue["TRORD_MBRAN_TEXT"],
                "TRQRZ_CREATED_BY" => session("user_id"),
                "TRQRZ_CREATED_TEXT" => session("user_name"),
                "TRQRZ_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        $count_order = std_get([
            "select" => ["TRORD_ID"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $check_queue["TRORD_MCOMP_CODE"],
                ]
            ],
            "first_row" => true,
            "count" => true
        ]);

        $counter = 0;
        for ($i=0; $i < $count_order; $i++) { 
            $counter++;
            if ($counter == 8) {
                $counter = 1;
            }
        }

        for ($i=0; $i < count($sticker_code["data"]); $i++) {
            $insert_sticker_code[$i] = [
                "MASCO_CODE" => $sticker_code["data"][$i],
                "MASCO_TRORD_CODE" => $check_queue["TRORD_CODE"],
                "MASCO_MCOMP_CODE" => $check_queue["TRORD_MCOMP_CODE"],
                "MASCO_MCOMP_TEXT" => $check_queue["TRORD_MCOMP_TEXT"],
                "MASCO_MBRAN_CODE" => $check_queue["TRORD_MBRAN_CODE"],
                "MASCO_MBRAN_TEXT" => $check_queue["TRORD_MBRAN_TEXT"],
                "MASCO_COUNTER" => $counter,
                "MASCO_CREATED_BY" => session("user_id"),
                "MASCO_CREATED_TEXT" => session("user_name"),
                "MASCO_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
        }

        $company_detail = [
            "MCOMP_CODE" => $check_queue["TRORD_MCOMP_CODE"],
            "MCOMP_TEXT" => $check_queue["TRORD_MCOMP_TEXT"],
            "MBRAN_CODE" => $check_queue["TRORD_MBRAN_CODE"],
            "MBRAN_TEXT" => $check_queue["TRORD_MBRAN_TEXT"],
        ];

        DB::beginTransaction();
        try {
            $data_order = $this->check_qr_order($company_detail["MBRAN_CODE"]);
            $insert_alpha = std_insert([
                "table_name" => "TRQRA",
                "data" => $insert_qr_alpha
            ]);

            $generate_qr_alpha = $this->generate_qr_alpha(
                $check_queue["TRORD_SIZE"],
                $check_queue["TRORD_ORIENTATION"],
                $insert_qr_alpha,
                $company_detail,
                count($data_order)
            );

            $insert_zeta = std_insert([
                "table_name" => "TRQRZ",
                "data" => $insert_qr_zeta
            ]);

            $generate_qr_zeta = $this->generate_qr_zeta(
                $check_queue["TRORD_SIZE"],
                $check_queue["TRORD_ORIENTATION"],
                $insert_qr_zeta,
                $company_detail,
                count($data_order)
            );

            $insert_sticker = std_insert([
                "table_name" => "MASCO",
                "data" => $insert_sticker_code
            ]);

            $generate_sticker_code = $this->generate_sticker_code(
                $check_queue["TRORD_SIZE"],
                $check_queue["TRORD_ORIENTATION"],
                $insert_sticker_code,
                $company_detail,
                count($data_order)
            );

            $update_res = std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_GENERATE_STATUS" => "2",
                ],
                "where" => ["TRORD_CODE" => $check_queue["TRORD_CODE"]]
            ]);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            $update_res = std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_GENERATE_STATUS" => "0",
                ],
                "where" => ["TRORD_CODE" => $check_queue["TRORD_CODE"]]
            ]);

            Log::info($th->getMessage());
        }
        Log::info("End at : ".date("Y-m-d H:i:s"));
    }

    public function check_progress()
    {
        $check_queue = std_get([
            "select" => ["*"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_GENERATE_STATUS",
                    "operator" => "=",
                    "value" => "1",
                ]
            ],
            "first_row" => true,
        ]);

        if ($check_queue != null) {
            Log::info("Queue in progress : ".$check_queue["TRORD_MBRAN_TEXT"]." with order code : ".$check_queue["TRORD_CODE"]);
        }

        return $check_queue;
    }

    public function check_queue()
    {
        $data_queue = std_get([
            "select" => ["*"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_GENERATE_STATUS",
                    "operator" => "=",
                    "value" => 0,
                ]
            ],
            "first_row" => true,
        ]);
        
        if ($data_queue != null) {
            std_update([
                "table_name" => "TRORD",
                "data" => [
                    "TRORD_GENERATE_STATUS" => "1",
                    "TRORD_NOTES" => "System Approved",
                    "TRORD_APPROVED_BY" => session("user_id"),
                    "TRORD_APPROVED_TEXT" => session("user_name"),
                    "TRORD_APPROVED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "TRORD_UPDATED_BY" => session("user_id"),
                    "TRORD_UPDATED_TEXT" => session("user_name"),
                    "TRORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ],
                "where" => ["TRORD_CODE" => $data_queue["TRORD_CODE"]]
            ]);
        }

        return $data_queue;
    }
    public function check_qr_order( $brand_code)
    {
        $data_order = std_get([
            "select" => ["*"],
            "table_name" => "TRORD",
            "where" => [
                [
                    "field_name" => "TRORD_MBRAN_CODE",
                    "operator" => "=",
                    "value" => $brand_code,
                ]
            ]
        ]);
        return $data_order;
    }

    public function generate_qr_alpha($TRORD_SIZE,$orientation,$data_qr_alpha,$company_detail,$count_order)
    {
        $qr_size = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $qr_type = "alpha";
        $size_mm =$TRORD_SIZE;

        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $time->add(new DateInterval('P7D'));
        $stamp = $time->format('Y-m-d H:i:s');

        $file_name = $name_time . "_" . $company_detail["MBRAN_TEXT"] . "_" . count($data_qr_alpha) . "_".$count_order."_QR_". $qr_type . ".pdf";
        $file_name = str_replace(" ","_",$file_name);
        
        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly

        PDF::setOptions(['defaultFont' => 'arial']);
        $qrcode=null;

        for ($i = 0; $i < count($data_qr_alpha); $i++) {
            $qrcode[$i]["qr_code"] = $data_qr_alpha[$i]["TRQRA_CODE"];
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->color(145, 0, 4)->generate($data_qr_alpha[$i]["TRQRA_CODE"]));
        }
        shuffle($qrcode);

        if ($orientation == "ptr") {
            $border = "http://13.212.9.6/dev/border_alpha.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/qr_alpha', [
                "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "border" => $border,
            ]);
        } else {
            $border = "http://13.212.9.6/dev/border_alpha_landscape.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/qr_alpha_landscape', [
                "company_name" => $data_qr_alpha[0]["TRQRA_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#63e2ff",
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "border" => $border,
            ]);
        }

        $pdf->setPaper('a3', 'portrait');
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            $pdf->save($upload_dir . '/' . $file_name);
            $insert_fiord_data = [
                "FIORD_MCOMP_CODE" =>  $company_detail["MCOMP_CODE"],
                "FIORD_MCOMP_NAME" =>  $company_detail["MCOMP_TEXT"],
                "FIORD_MBRAN_CODE" =>  $company_detail["MBRAN_CODE"],
                "FIORD_MBRAN_NAME" => $company_detail["MBRAN_TEXT"],
                "FIORD_TRORD_CODE" => $data_qr_alpha[0]["TRQRA_TRORD_CODE"],
                "FIORD_TRORD_QTY" => count($data_qr_alpha),
                "FIORD_NAME" => $file_name,
                "FIORD_ORIENTATION" => $orientation,
                "FIORD_QR_TYPE" =>  "alpha",
                "FIORD_SIZE" => $TRORD_SIZE,
                "FIORD_UNIT" => "mm",
                "FIORD_STATUS" => 0, //0 ok, 1 deleted
                "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                "FIORD_CREATED_BY" => "SYS",
                "FIORD_CREATED_TEXT" => "System",
                "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            
            $insert_fiord = std_insert([
                "table_name" => "FIORD",
                "data" => $insert_fiord_data
            ]);
        }
        return true;
    }

    public function generate_qr_zeta($TRORD_SIZE,$orientation,$data_qr_zeta,$company_detail,$count_order)
    {
        $qr_size = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $qr_type = "zeta";
        $size_mm =$TRORD_SIZE;

        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $file_name = $name_time . "_" . $company_detail["MBRAN_TEXT"] . "_" . count($data_qr_zeta) . "_".$count_order."_QR_". $qr_type . ".pdf";
        $file_name = str_replace(" ","_",$file_name);

        $time->add(new DateInterval('P7D'));
        $stamp = $time->format('Y-m-d H:i:s');

        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly
        
        PDF::setOptions(['defaultFont' => 'arial']);
        $qrcode=null;

        for ($i = 0; $i < count($data_qr_zeta); $i++) {
            $qrcode[$i]["qr_code"] = $data_qr_zeta[$i]["TRQRZ_CODE"];
            $qrcode[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->color(5, 117, 127)->generate($data_qr_zeta[$i]["TRQRZ_CODE"]));
        }
        shuffle($qrcode);

        if ($orientation == "ptr") {
            $border = "http://13.212.9.6/dev/border_zeta.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/qr_zeta', [
                "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#05747e",
                "qr_size" => $qr_size,
                "border" => $border,
            ]);
        } else {
            $border = "http://13.212.9.6/dev/border_zeta_landscape.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/qr_zeta_landscape', [
                "company_name" => $data_qr_zeta[0]["TRQRZ_MBRAN_TEXT"],
                "qrcode" => $qrcode,
                "color" => "#05747e",
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "border" => $border,
            ]);
        }
        $pdf->setPaper('a3', 'portrait');
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
        } else {
            try {
                $pdf->save($upload_dir . '/' . $file_name);
                $insert_fiord_data = [
                    "FIORD_MCOMP_CODE" =>  $company_detail["MCOMP_CODE"],
                    "FIORD_MCOMP_NAME" =>  $company_detail["MCOMP_TEXT"],
                    "FIORD_MBRAN_CODE" =>  $company_detail["MBRAN_CODE"],
                    "FIORD_MBRAN_NAME" => $company_detail["MBRAN_TEXT"],
                    "FIORD_TRORD_CODE" => $data_qr_zeta[0]["TRQRZ_TRORD_CODE"],
                    "FIORD_TRORD_QTY" => count($data_qr_zeta),
                    "FIORD_NAME" => $file_name,
                    "FIORD_ORIENTATION" => $orientation,
                    "FIORD_QR_TYPE" =>  "zeta",
                    "FIORD_SIZE" => $TRORD_SIZE,
                    "FIORD_UNIT" => "mm",
                    "FIORD_STATUS" => 0, //0 ok, 1 deleted
                    "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                    "FIORD_CREATED_BY" => "SYS",
                    "FIORD_CREATED_TEXT" => "System",
                    "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_fiord = std_insert([
                    "table_name" => "FIORD",
                    "data" => $insert_fiord_data
                ]);
            } catch (\Throwable $th) {
                Log::info("FIORD Zeta", $th->getMessage());
            }
        }
        return true;
    }
    
    public function generate_sticker_code($TRORD_SIZE,$orientation,$data_sticker_code,$company_detail,$count_order)
    {
        $qr_size = round($TRORD_SIZE * 3.333333333333333); //mm to pixel
        $size_mm =$TRORD_SIZE;
        $qr_type = "bridge";

        $time = new DateTime();
        $name_time = $time->format('YmdHis');
        $time->add(new DateInterval('P7D'));
        $stamp = $time->format('Y-m-d H:i:s');

        $file_name = $name_time . "_" . $company_detail["MBRAN_TEXT"] . "_" . count($data_sticker_code) . "_".$count_order."_QR_". $qr_type . ".pdf";
        $file_name = str_replace(" ","_",$file_name);

        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly
        
        PDF::setOptions(['defaultFont' => 'arial']);
        $sticker_code=null;

        for ($i = 0; $i < count($data_sticker_code); $i++) {
            $sticker_code[$i]["qr_code"] = $data_sticker_code[$i]["MASCO_CODE"];
            $sticker_code[$i]["image"] = base64_encode(QrCode::format('svg')->size($qr_size)->errorCorrection('H')->generate($data_sticker_code[$i]["MASCO_CODE"]));
        }
        shuffle($sticker_code);
        if ($orientation == "ptr") {
            $border = "http://13.212.9.6/dev/border_sticker.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/sticker_code', [
                "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
                "sticker_code" => $sticker_code,
                "color" => "#000000",
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "border" => $border,
            ]);
        } else {
            $border = "http://13.212.9.6/dev/border_sticker_code_landscape.jpeg";

            $pdf = PDF::loadView('transaction/order_qr/sticker_code_landscape', [
                "company_name" => $data_sticker_code[0]["MASCO_MBRAN_TEXT"],
                "sticker_code" => $sticker_code,
                "color" => "#000000",
                "qr_size" => $qr_size,
                "border_size" => $size_mm,
                "file_name" => $file_name,
                "border" => $border,
            ]);
        }
        $customPaperA3Plus = array(0,0,933,1369);
        $customPaperA3Normal = array(0,0,842,1191);
        $pdf->setPaper($customPaperA3Plus);
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ], 500);
            Log::info("gagal simpan");
        } else {
            try {
                $pdf->save($upload_dir . '/' . $file_name);
                $insert_fiord_data = [
                    "FIORD_MCOMP_CODE" =>  $company_detail["MCOMP_CODE"],
                    "FIORD_MCOMP_NAME" =>  $company_detail["MCOMP_TEXT"],
                    "FIORD_MBRAN_CODE" =>  $company_detail["MBRAN_CODE"],
                    "FIORD_MBRAN_NAME" => $company_detail["MBRAN_TEXT"],
                    "FIORD_TRORD_CODE" => $data_sticker_code[0]["MASCO_TRORD_CODE"],
                    "FIORD_TRORD_QTY" => count($data_sticker_code),
                    "FIORD_NAME" => $file_name,
                    "FIORD_ORIENTATION" => $orientation,
                    "FIORD_QR_TYPE" =>  "bridge",
                    "FIORD_SIZE" => $TRORD_SIZE,
                    "FIORD_UNIT" => "mm",
                    "FIORD_STATUS" => 0, //0 ok, 1 deleted
                    "FIORD_START_GENERATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_END_GENERATED_TIMESTAMP" => $stamp,
                    "FIORD_CREATED_BY" => "SYS",
                    "FIORD_CREATED_TEXT" => "System",
                    "FIORD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];         
                $insert_fiord = std_insert([
                    "table_name" => "FIORD",
                    "data" => $insert_fiord_data
                ]);       
            } catch (\Throwable $th) {
                Log::info("FIORD Sticker", $th->getMessage());
            }
        }
        return true;
    }
}
