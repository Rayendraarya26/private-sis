<?php

namespace Modules\Email\Http\Controllers;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\MasterEmailTemplate;
use App\Models\BbkkpSis\SysUser;
use Illuminate\Routing\Controller;
use Modules\Email\Http\Traits\EmailTrait;

class SchedulerController extends Controller
{
    use EmailTrait;

    public function sendGreeting()
    {
        $dataTemplate = MasterEmailTemplate::where("template_code", "GREETING")->firstOrFail();

        $dataUsers = SysUser::all();

        foreach (SysUser::all() as $user) {
            $subject = $dataTemplate->template_mail_subject;
            $subject = $this->parse($subject, "FULLNAME", $user->user_fullname);
            $subject = $this->parse($subject, "EMAIL", $user->user_email);

            $body = $dataTemplate->template_mail_body;
            $body = $this->parse($body, "FULLNAME", $user->user_fullname);
            $body = $this->parse($body, "EMAIL", $user->user_email);

            $struct          = new EmailStruct();
            $struct->subject = $subject;
            $struct->body    = $body;
            $struct->to      = $user->user_email;
            $struct->type    = "scheduler";

            sendEmail($struct);
        }

        return responseJSON(200, [], sprintf("success mengirim ke %d orang", $dataUsers->count()));
    }
}
