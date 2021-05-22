<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use DB;
use DateTime;
use DateInterval;

class Checking_FIORD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checking_fiord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking Fiord every day, to delete file';

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
        $upload_dir = "public/storage/file/qr_file/";
        // $upload_dir = "storage/file/qr_file/"; //localhostonly

        $data_qr_generated = std_get([
            "select" => ["*"],
            "table_name" => "FIORD",
            "where" => [
                [
                    "field_name" => "FIORD_END_GENERATED_TIMESTAMP",
                    "operator" => "<",//< jika waktu expired dibawah waktu sekarang
                    "value" =>date("Y-m-d H:i:s"),
                ]
            ],
        ]);

        if ($data_qr_generated != null) {
            for ($index=0; $index < count($data_qr_generated); $index++) { 
                $file_address= $upload_dir.$data_qr_generated[$index]["FIORD_NAME"];
                if (file_exists($file_address)) {
                    unlink($file_address);
                }
                $update_data = [
                    "FIORD_STATUS" => 1, //0 ok, 1 deleted
                    "FIORD_DELETED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "FIORD_UPDATED_BY" => "SYS",
                    "FIORD_UPDATED_TEXT" => "System",
                    "FIORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $update_res = std_update([
                    "table_name" => "FIORD",
                    "where" => ["FIORD_ID" => $data_qr_generated[$index]["FIORD_ID"]],
                    "data" => $update_data
                ]);
                $update_trord = std_update([
                    "table_name" => "TRORD",
                    "data" => [
                        "TRORD_STATUS" => 2,//1 means oke, 2 means deleted
                        "TRORD_UPDATED_BY" => "SYS",
                        "TRORD_UPDATED_TEXT" => "System",
                        "TRORD_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                    ],
                    "where" => ["TRORD_CODE" => $data_qr_generated[$index]["FIORD_TRORD_CODE"]]
                ]);
            }
        }
        
    }
}
