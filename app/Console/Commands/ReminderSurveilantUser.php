<?php

namespace App\Console\Commands;

use App\Http\Structs\EmailStruct;
use App\Models\BbkkpSis\MasterEmailTemplate;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Email\Http\Traits\EmailTrait;

class ReminderSurveilantUser extends Command
{
    use EmailTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:reminder-survailant-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pengingat agar pelanggan mengikuti surveilant sesuai jadwal';

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
        $this->log(sprintf("Pengingat agar pelanggan mengikuti surveilant sesuai jadwal %s", Carbon::now()));

        $reminderInMonths = 6;

        $dataTemplate = MasterEmailTemplate::where("template_code", "REMINDERSURVAILANT_USER")->firstOrFail();

        $dataReminder = SisPelangganSertifikasi::with(['sis_pelanggan.sys_user', 'master_sertifikasi'])
            ->where("cust_sert_survailen_date", '>=', date("Y-m-d H:i:s"))
            ->where('cust_sert_survailen_date', '<=', Carbon::now()->addMonths($reminderInMonths)->format("Y-m-d H:i:s"))
            ->where('cust_sert_status_survailen', '=', 'passed')
            ->where('sis_billing.bill_payment_status', '=', 'menunggu pembayaran')
            ->join('sis_billing_items', 'sis_billing_items.cust_sert_id', '=', 'sis_pelanggan_sertifikasi.cust_sert_id')
            ->join('sis_billing', 'sis_billing_items.bill_id', '=', 'sis_billing.bill_id')
            ->get();

        foreach ($dataReminder as $d) {
            $totalReminderSent = 0;
            $doReminder        = true;
            if (!empty($d->cust_sert_survailen_reminder_at)) { // Memastikan reminder hanya dikirim setiap 1 bulan sekali
                $lastReminder = $d->cust_sert_survailen_reminder_at;
                $nextReminder = $lastReminder->addMonth();
                if (Carbon::now()->isBefore($nextReminder)) $doReminder = false;
            }

            if ($doReminder) {
                $totalReminderSent += 1;
                $dayLeft           = Carbon::now()->diffInDays($d->cust_sert_survailen_date);
                $subject           = $dataTemplate->template_mail_subject;
                $subject           = $this->parse($subject, "CERT_NAME", $d->master_sertifikasi->sert_nama);

                $body = $dataTemplate->template_mail_body;
                $body = $this->parse($body, "CUST_NAME", $d?->sis_pelanggan?->cust_nama);
                $body = $this->parse($body, "CERT_NAME", $d->master_sertifikasi->sert_nama);
                $body = $this->parse($body, "SURVAILANT_DATE", $d->cust_sert_survailen_date->isoFormat("LL"));
                $body = $this->parse($body, "DAY_LEFT", $dayLeft);
                $body = $this->parse($body, "REMINDER_COUNTER", $totalReminderSent);
                $body = $this->parse($body, "LINK", url('/pelanggan/sertifikasi/permohonan?type=surveilant'));

                $struct          = new EmailStruct();
                $struct->subject = $subject;
                $struct->body    = $body;
                $struct->to      = $d->sis_pelanggan->sys_user->user_email;
                $struct->type    = "scheduler";

                sendEmail($struct);

                $d->cust_sert_survailen_reminder_count += 1;
                $d->cust_sert_survailen_reminder_at    = Carbon::now();
                $d->save();

                $this->log(sprintf("mengirim ke %s | reminder ke %d | cust_sert_id %d \r\n", $d->sis_pelanggan->sys_user->user_email, $totalReminderSent, $d->cust_sert_id));
            }
        }

        return true;
    }

    private function log(string $message): void
    {
        $this->info("cron:reminder-survailant-user: $message");
    }
}
