<?php

namespace Modules\Email\Http\Controllers;

use App\Models\BbkkpSisLog\LogEmailOutbox;
use Illuminate\Routing\Controller;

class EmailController extends Controller
{
    public function open($uuid)
    {
        $dataLog = LogEmailOutbox::where("outbox_uuid", $uuid)->where("outbox_read", "no")->first();
        if ($dataLog) {
            $dataLog->outbox_read = "yes";
            $dataLog->outbox_read_at = date("Y-m-d H:i:s");
            $dataLog->save();
        }
    }
}
