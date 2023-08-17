<?php

namespace App\Jobs;

use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SysUserFbToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotif implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected NotifStruct $struct;

    public function __construct(NotifStruct $struct)
    {
        $this->struct = $struct;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataToken = SysUserFbtoken::with("user")->where("fbtoken_user_id", $this->struct->user_id)->get();
        if (!empty($dataToken)) {
            $registrationIds = [];

            foreach ($dataToken as $token) {
                // add token penerima
                $registrationIds[] = $token->fbtoken_token;
            }

            $API_ACCESS_KEY = config("app.firebase_api_key");

            $url = 'https://fcm.googleapis.com/fcm/send';

            if (count($registrationIds) > 0) {
                // prepare the message
                $message = array(
                    'title' => $this->struct->title,
                    'body' => strip_tags($this->struct->message),
                    'vibrate' => 1,
                    'sound' => 1,
                    'url' => $this->struct->click_url,
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
                            SysUserFbtoken::where("fbtoken_user_id", $this->struct->user_id)->where("fbtoken_token", $registrationIds[$key])->delete();
                        }
                    }
                }
            }
        }
    }
}
