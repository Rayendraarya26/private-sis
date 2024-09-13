<?php

namespace App\Console\Commands;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\MasterEmailTemplate;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Email\Http\Traits\EmailTrait;

class ReminderSurveilantInternal extends Command
{
    use EmailTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:reminder-survailant-internal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pengingat agar internal BBKKP membuat billing untuk surveilant';

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
        $this->info("Pengingat agar internal BBKKP membuat billing untuk surveilant %s", Carbon::now());

        try {
            DB::beginTransaction();
            $this->sendReminder();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }


        return true;
    }

    private function sendReminder(): void
    {
        $reminderInMonths = 6;

        $dataTemplate = MasterEmailTemplate::where("template_code", "REMINDERSURVAILANT_INTERNAL")->firstOrFail();
        $dataReminder = SisPelangganSertifikasi::with(['sis_pelanggan.sys_user', 'master_sertifikasi'])
            ->where("cust_sert_survailen_date", '>=', date("Y-m-d H:i:s"))
            ->where('cust_sert_survailen_date', '<=', Carbon::now()->addMonths($reminderInMonths)->format("Y-m-d H:i:s"))
            ->where('cust_sert_status_survailen', '=', 'passed')
            ->where('cust_sert_survailen_reminder_count', '<', 3)
            ->whereNull('sis_billing_items.cust_sert_id')
            ->leftJoin('sis_billing_items', 'sis_billing_items.cust_sert_id', '=', 'sis_pelanggan_sertifikasi.cust_sert_id')
            ->orderBy('cust_sert_survailen_date')
            ->select('sis_pelanggan_sertifikasi.*')
            ->get();

        if ($dataReminder->isEmpty()) {
            $this->info("Semua surveilant sudah dibuat billing");
            return;
        }

        $billData = "<table><tr><th>Perusahaan</th><th>Sertifikasi</th><th>Tgl Surveilant</th></tr>";
        $bilCount = 0;
        foreach ($dataReminder as $d) {
            $bilCount++;
            $billData .= sprintf("
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>
            ", $d?->sis_pelanggan?->cust_nama ?? "Belum diberi nama", $d->master_sertifikasi->sert_nama, $d->cust_sert_survailen_date->isoFormat("LL"));

            $d->cust_sert_survailen_reminder_count += 1;
            $d->cust_sert_survailen_reminder_at    = Carbon::now();
            $d->save();
        }
        $billData .= "</table>";

        // Send Notification to Marketing, Keuangan, Operator LS
        $groupUsers = SysUserGroup::with('user')->whereIn('ug_group_id', [7])->get();
        if ($groupUsers) {
            foreach ($groupUsers as $user) {
                $notifStruct = new NotifStruct();
                // Send Push
                $notifStruct->title     = "Reminder Surveilant";
                $notifStruct->message   = sprintf("%s billing belum dibuat", $bilCount);
                $notifStruct->user_id   = $user?->ug_user_id;
                $notifStruct->click_url = url('/keuangan/billing/create');

                sendNotification($notifStruct);

                // ====================================================================================================

                // Send Email
                $subject = $dataTemplate->template_mail_subject;
                $subject = $this->parse($subject, "BIL_TOTAL", $bilCount);

                $body = $dataTemplate->template_mail_body;
                $body = $this->parse($body, "BIL_TOTAL", $bilCount);
                $body = $this->parse($body, "BIL_DATA", $billData);

                $struct          = new EmailStruct();
                $struct->subject = $subject;
                $struct->body    = $body;
                $struct->to      = $user->user?->user_email;
                $struct->type    = "scheduler";

                sendEmail($struct);

                // ====================================================================================================
                $this->info(sprintf("mengirim notifikasi email ke %s success", $user->user?->user_email));
            }
        }
    }

    private function log(string $message): void
    {
        $this->info("cron:reminder-survailant-user: $message");
    }
}
