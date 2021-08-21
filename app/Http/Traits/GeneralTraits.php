<?php

namespace App\Http\Traits;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Http\Structs\PushNotifStruct;
use App\Jobs\SendMail;
use App\Jobs\SendNotif;
use App\Models\BbkkpSis\SysUserNotif;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait GeneralTraits
{
    function moneyFormat($value)
    {
        return number_format($value, 0, ",", ".");
    }

    function monthIndonesia($month)
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulan[$month];
    }

    public function responseJSON($code = 200, $result = [], $message = ""): JsonResponse
    {
        $output = [
            'code' => $code,
            'results' => $result,
            'message' => $message,
        ];
        return response()->json($output, $code);
    }

    public function sendNotification(NotifStruct $struct)
    {
        // Add to Notif System
        SysUserNotif::create([
            'notif_user_id' => $struct->user_id,
            'notif_title' => $struct->title,
            'notif_content' => $struct->message,
            'notif_link' => $struct->click_url,
            'notif_is_read' => "no",
            'notif_created_at' => Date("Y-m-d H:i:s"),
        ]);
        // Send to firebase
        SendNotif::dispatch($struct);
    }

    public function sendEmail(EmailStruct $struct)
    {
        try {
            $email = filter_var($struct->to, FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                SendMail::dispatch($struct);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
