<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class JadwalSurveilantController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/jadwal-surveilant';
    private $view = "operatorls::jadwal_surveilant";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Jadwal Surveilant'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function reminderFinance(Request $request)
    {
        $request->validate(['cust_sert_id' => 'required|int']);
        try {
            $data = SisPelangganSertifikasi::with(['sis_pelanggan.sys_user', 'master_sertifikasi'])->find($request['cust_sert_id']);
            if (empty($data)) throw new ExpectedException("Data sertifikat tidak ditemukan");

            // Send Notification to Keuangan
            $groupUsers = SysUserGroup::with('user')->whereIn('ug_group_id', [7])->get();
            if ($groupUsers) {
                foreach ($groupUsers as $group) {
                    $notifStruct = new NotifStruct();
                    // Send Push
                    $notifStruct->title     = "Reminder Billing Surveilant " . $data?->sis_pelanggan?->cust_nama;
                    $notifStruct->message   = sprintf("Mohon segera terbitkan billing surveilant untuk sertifikat <b>%s</b> pada perusahaan <b>%s</b> yang akan dilaksanakan tanggal %s.", $data->master_sertifikasi->sert_nama, $data?->sis_pelanggan?->cust_nama, $data->cust_sert_survailen_date->isoFormat("LL"));
                    $notifStruct->user_id   = $group?->ug_user_id;
                    $notifStruct->click_url = url('/keuangan/billing/create');

                    sendNotification($notifStruct);

                    // ====================================================================================================

                    // Send Email
                    $structEmail          = new EmailStruct();
                    $structEmail->subject = "Reminder Billing Surveilant " . $data?->sis_pelanggan?->cust_nama;
                    $structEmail->body    = view("$this->view.mails.finance_create_billing")
                        ->with([
                            'cert_name'      => $data->master_sertifikasi->sert_nama,
                            'company'        => $data?->sis_pelanggan?->cust_nama,
                            'tgl_surveilant' => $data->cust_sert_survailen_date->isoFormat("LL"),
                            'link'           => url('/keuangan/billing/create'),
                        ])->render();
                    $structEmail->to      = $group->user->user_email;

                    sendEmail($structEmail);
                }
            }

            $data->cust_sert_survailen_reminder_internal_count += 1;
            $data->cust_sert_survailen_reminder_internal_at    = Carbon::now();
            $data->save();
            return responseJSON(200, [], 'Notifikasi ke Finance berhasil dikirim');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));

            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => null,
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = (new SisPelangganSertifikasi)
            ->join('sis_pelanggan', 'sis_pelanggan.cust_id', 'sis_pelanggan_sertifikasi.cust_id')
            ->join('master_sertifikasi', 'master_sertifikasi.sert_id', 'sis_pelanggan_sertifikasi.sert_id')
            ->leftJoin('sis_billing_items', 'sis_billing_items.cust_sert_id', '=', 'sis_pelanggan_sertifikasi.cust_sert_id')
			->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_pelanggan.negara_id')
			->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_pelanggan.kab_id')
			->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_pelanggan.kec_id')
			->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_pelanggan.prov_id')
            ->where("cust_sert_survailen_date", '>=', date("Y-m-d H:i:s"))
            ->where('cust_sert_status_survailen', '=', 'passed');

        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == "is_bill_created") {
                    if ($f->value == "yes") {
                        $data->whereNotNull('sis_billing_items.cust_sert_id');
                    } else if ($f->value == "no") {
                        $data->whereNull('sis_billing_items.cust_sert_id');
                    }
                } else {
                    $data->where($f->field, 'LIKE', '%' . $f->value . '%');
                }
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        } else {
            $data->orderBy('cust_sert_survailen_date');
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select(DB::raw("
            sis_pelanggan_sertifikasi.*,
            master_sertifikasi.sert_nama,
            sis_billing_items.itms_bil_id,
            sis_pelanggan.*
            "))
            ->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_alamat']                                = $d->cust_alamat;
            $x['cust_sert_id']                                = $d->cust_sert_id;
            $x['cust_id']                                     = $d->cust_id;
            $x['cust_nama']                                   = $d->cust_nama;
            $x['cust_sert_survailen_reminder_internal_count'] = $d->cust_sert_survailen_reminder_internal_count;
            $x['sert_nama']                                   = $d->sert_nama;
            $x['cust_sert_survailen_date']                    = $d->cust_sert_survailen_date->format("Y-m-d");
            $x['is_bill_created']                             = !empty($d->itms_bil_id);
            $result[]                                         = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
