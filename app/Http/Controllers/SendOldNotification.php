<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOldNotification extends Controller
{
    public function send_mail()
    {
        $curl_data = curl_get("http://134.209.124.184/api/v4/get_user/user", [
            "limit" => 200,
            "offset" => 1200
        ]);

        $user = $curl_data["data"]["data"];

        for ($i=0; $i < count($user); $i++) { 
            if ($user[$i] != null) {
                $to_email = $user[$i]["user_email"];

                try {
                    Mail::send("mail.notification_old_application", [], function ($message) use ($to_email) {
                        $message
                            ->to($to_email)
                            ->subject("CekOri Application Update Notification Email");
                        $message->from("admin@cekori.com", "CekOri Application Update Notification Email");
                    });

                    Log::info("Success on send email : ".$user[$i]["user_email"]);
                } catch (\Throwable $th) {
                    Log::critical("Fail on send email : ".$user[$i]["user_email"]);
                }
            }
        }
    }
}

    