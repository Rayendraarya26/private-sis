<?php

namespace Modules\Email\Http\Controllers;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\MasterEmailTemplate;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SysUser;
use Carbon\Carbon;
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

    public function sendReminderSurvailant()
    {
        $reminderInMonths = 6;

        $dataTemplate = MasterEmailTemplate::where("template_code", "SURVAILANT_REMINDER")->firstOrFail();
        $dataReminder = SisPelangganSertifikasi::with(['sis_pelanggan.sys_user', 'master_sertifikasi'])
            ->where("cust_sert_survailen_date", '>=', date("Y-m-d H:i:s"))
            ->where('cust_sert_survailen_date', '<=', Carbon::now()->addMonths($reminderInMonths)->format("Y-m-d H:i:s"))
            ->where('cust_sert_status_survailen', '=', 'passed')
            ->get();

        $totalReminderSent = 0;
        foreach ($dataReminder as $d) {
            $doReminder = true;
            if (!empty($d->cust_sert_survailen_reminder_at)) { // Memastikan reminder hanya dikirim setiap 1 bulan sekali
                $lastReminder = $d->cust_sert_survailen_reminder_at;
                $nextReminder = $lastReminder->addMonth();
                if (Carbon::now()->isBefore($nextReminder)) $doReminder = false;
            }

            if ($doReminder) {
                $totalReminderSent += 1;
                $subject           = $dataTemplate->template_mail_subject;
                $subject           = $this->parse($subject, "CUST_NAME", $d->sis_pelanggan->cust_nama);

                $body = $dataTemplate->template_mail_body;
                $body = $this->parse($body, "CUST_NAME", $d->sis_pelanggan->cust_nama);
                $body = $this->parse($body, "SERT_NAME", $d->master_sertifikasi->sert_nama);
                $body = $this->parse($body, "TGL_SURVAILEN", $d->cust_sert_survailen_date);

                $struct          = new EmailStruct();
                $struct->subject = $subject;
                $struct->body    = $body;
                $struct->to      = $d->sis_pelanggan->sys_user->user_email;
                $struct->type    = "scheduler";

                sendEmail($struct);

                $d->cust_sert_survailen_reminder_count += 1;
                $d->cust_sert_survailen_reminder_at    = Carbon::now();
                $d->save();
            }
        }

        return responseJSON(200, null, "$totalReminderSent Reminder sent");
    }
}
