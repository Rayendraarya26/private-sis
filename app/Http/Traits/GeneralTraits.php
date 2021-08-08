<?php

namespace App\Http\Traits;

use App\Models\BbkkpSis\SysUserFbToken;
use App\Models\BbkkpSis\SysUserNotif;
use Illuminate\Http\JsonResponse;

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

    public function sendNotification($title = 'Judul', $body = 'message', $penerimaID = 0, $clickUrl = "/")
    {
        // Add to Notif System
        SysUserNotif::create([
            'notif_user_id' => $penerimaID,
            'notif_title' => $title,
            'notif_desc' => $body,
            'notif_link' => $clickUrl,
            'notif_is_read' => "no",
            'notif_created_at' => Date("Y-m-d H:i:s"),
        ]);
        // Send to firebase
        $dataToken = SysUserFbtoken::with("user")->where("fbtoken_user_id", $penerimaID)->get();
        if (!empty($dataToken)) {
            $registrationIds = [];

            foreach ($dataToken as $token) {
                // add token penerima
                array_push($registrationIds, $token->token_token);
            }

            $API_ACCESS_KEY = 'AAAADMTuaS4:APA91bEKb5VRnWm5B_XJxpKtFmkGMAls1RMudXUZsylmUVd2zYiG095wZxV35MGQKS2yVCaV25jpQtSiW030U8_O4du9Qmppek_MzuqEJIybIwy_GBt77zcajOl2YE8Pj8v35AQhWJ0p';

            $url = 'https://fcm.googleapis.com/fcm/send';

            if (count($registrationIds) > 0) {
                // prepare the message
                $message = array(
                    'title' => $title,
                    'body' => strip_tags($body),
                    'vibrate' => 1,
                    'sound' => 1,
                    'url' => $clickUrl,
                );
                $fields = array(
                    'registration_ids' => $registrationIds,
                    'data' => $message
                );
                $headers = array(
                    'Authorization: key=' . $API_ACCESS_KEY,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);


                // remove token yang deprecated
                if ($result) {
                    foreach (json_decode($result)->results as $key => $value) {
                        if (isset($value->error)) {
                            SysUserFbtoken::where("fbtoken_user_id", $penerimaID)->where("fbtoken_token", $registrationIds[$key])->delete();
                        }
                    }
                }
            }
        }
    }
}
