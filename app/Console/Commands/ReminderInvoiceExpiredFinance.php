<?php

namespace App\Console\Commands;

use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderInvoiceExpiredFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:reminder-invoice-expired-finance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Push Notif ke finance jika ada billing yang expired';

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
        echo sprintf("________________________%s________________________ \r\n", Carbon::now());

        $dataExpiredInvoice = SisBilling::with('sis_pelanggan')
            ->where('bill_notif_count_finance', '=', 0)
            ->where('bill_due_date', '<', Carbon::now())->get();
        if ($dataExpiredInvoice) {
            // Send Notification to Keuangan
            $groupUsers = SysUserGroup::with('user')->whereIn('ug_group_id', [6])->get();
            foreach ($dataExpiredInvoice as $invoice) {
                foreach ($groupUsers as $user) {
                    $notifStruct = new NotifStruct();
                    // Send Push
                    $notifStruct->title     = sprintf("%s Invoice Expired", $invoice->sis_pelanggan->cust_nama);
                    $notifStruct->message   = sprintf("Billing dengan nomor %s telah expired tanggal %s", $invoice->bill_nomor_billing, $invoice->bill_due_date->isoFormat('LL'));
                    $notifStruct->user_id   = $user?->ug_user_id;
                    $notifStruct->click_url = url('/keuangan/billing/create');

                    sendNotification($notifStruct);

                    echo sprintf("mengirim invoice expired %s ke %s berhasil\r\n", $invoice->sis_pelanggan->cust_nama, $user->user?->user_email);
                }

                $invoice->bill_notif_count_finance += 1;
                $invoice->save();
            }
        }
        return 1;
    }
}
