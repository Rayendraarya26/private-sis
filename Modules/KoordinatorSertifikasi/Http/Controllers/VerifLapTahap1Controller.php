<?php

namespace Modules\KoordinatorSertifikasi\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class VerifLapTahap1Controller extends Controller
{
    public $module = self::class;
    private $view = "koordinatorsertifikasi::verif_lap_tahap1";
    private $url = 'koordinatorsertifikasi/verif-lap-tahap1';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Koorinator Sertifikasi'),
            new BreadcrumbsStruct('Verifikasi Lap. Tahap 1'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'view' => $this->view];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            default                 => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisAuditTahap1::join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id");

        // Filter
        $data->whereIn('sis_audit_tahap1.aud_thp1_status_temuan', ['diajukan', 'setuju']);
        $data->whereIn('sis_audit_tahap1.aud_thp1_lap_verifikasi_status', ['none', 'revisi']);
        $data->whereIn('sis_audit_tahap1.aud_thp1_verifikasi_diajukan', ['ya']);
        $data->whereIn('sis_audit_tahap1.aud_thp1_ditutup', ['tidak']);
        $data->whereNotNull('sis_audit_tahap1.aud_thp1_file_jadwal');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(distinct sis_audit_tahap1.aud_thp1_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, ' (' , UPPER(IF(sis_permohonan_detail.cust_sert_id IS NULL, 'baru', 'lama')), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_audit_tahap1.aud_thp1_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['aud_thp1_status_temuan']       = $d->aud_thp1_status_temuan;
            $x['aud_thp1_status']              = $d->aud_thp1_status;
            $x['aud_thp1_id']                  = $d->aud_thp1_id;
            $x['aud_thp1_tanggal_mulai']       = $d->aud_thp1_tanggal_mulai;
            $x['aud_thp1_tanggal_selesai']     = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                    = $d->cust_nama;
            $x['sert_nama']                    = $d->sert_nama;
            $x['jadw_jenis']                   = $d->jadw_jenis;
            $x['aud_thp1_jenis']               = $d->aud_thp1_jenis;
            $x['aud_thp1_verifikasi_diajukan'] = $d->aud_thp1_verifikasi_diajukan;
            $result[]                          = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function doVerif(Request $request)
    {
        $request->validate([
            "aud_thp1_id" => 'required',
            "catatan"     => 'nullable',
            "status"      => 'required',
        ]);
        try {
            DB::beginTransaction();

            DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request['aud_thp1_id'])
                ->update([
                    "aud_thp1_lap_verifikasi_status" => $request['status'],
                    "aud_thp1_lap_revisi_note"       => ($request['status'] != 'ya') ? $request['catatan'] : null,
                    "aud_thp1_verifikasi_tanggal"    => ($request['status'] != 'ya') ? Carbon::now() : null,
                    "aud_thp1_verifikasi_diajukan"   => ($request['status'] == 'ya') ? 'ya' : 'tidak',
                ]);

            // Send Notification to Auditor
            $groupAuditor = SisAuditTahap1Tim::join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
            $groupAuditor->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
            $groupAuditor->where('thp1_tim_posisi', '=', 'ketua')->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id'])->select(DB::raw('sys_user.user_id AS user_id'), 'thp1_tim_posisi');
            foreach ($groupAuditor->get() as $auditor) {
                $notifStruct = new NotifStruct();
                if ($request['status'] == "ya") {
                    $notifStruct->title   = sprintf("#%d Valid Laporan tahap 1", $request['aud_thp1_id']);
                    $notifStruct->message = sprintf("Jadwal nomor %s pada laporan tahap 1 telah dilakuan verifikasi dengan hasil ter-verifikasi.", $request['aud_thp1_id']);
                } else {
                    $notifStruct->title   = sprintf("#%d Revisi Laporan tahap 1", $request['aud_thp1_id']);
                    $notifStruct->message = sprintf("Jadwal nomor %s pada laporan tahap 1 telah dilakuan verifikasi dengan hasil direvisi.", $request['aud_thp1_id']);
                }
                $notifStruct->click_url = url('/timaudit/auditor/tahap1-lap');
                $notifStruct->user_id   = $auditor->user_id;
                sendNotification($notifStruct);
            }

            $responseMessage = sprintf("Data berhasil disimpan untuk jadwal #%d", $request['aud_thp1_id']);
            DB::commit();
            return redirect()->back()->with('message', $responseMessage);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function cetak(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'hasil-tinjauan' => $this->print_hasil_tinjauan($request),
            'lap_lengkap'    => $this->print_lap_lengkap($request),
            default          => null,
        };
    }

    private function print_hasil_tinjauan(Request $request)
    {
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as no_referensi'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as ruang_lingkup'),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);

        $parser = [
            'module'           => $this->module,
            'url'              => $this->url,
            'restAudit'        => $restAudit,
            'dataAuditKlausul' => $dataAuditKlausul->get(),
        ];

        $dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
        $dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        $dataTim->select('*');
        $dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
        foreach ($dataTim->get() as $tim) {
            if ($tim->thp1_tim_posisi == 'ketua') {
                $parser['ketua_tim'] = $tim->peg_nama;
                break;
            }
        }
        $parser['dataTim'] = $dataTim->get();
        if ($restAudit['sert_tahap1_jenis'] == 'sni') {
            // return view("$this->view.print.hasil_tinjauan_sni")->with($parser);
            $pdf = PDF::loadView("$this->view.print.hasil_tinjauan_sni", $parser)->setPaper('a4', 'portrait');
            return $pdf->stream();
        } else {
            $pdf = PDF::loadView("$this->view.print.hasil_tinjauan_pusat", $parser)->setPaper('a4', 'portrait');
            return $pdf->stream();
            // return view("$this->view.print.hasil_tinjauan_pusat")->with($parser);
        }
    }

    private function print_lap_lengkap(Request $request)
    {
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as mohon_det_no_referensi'),
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT(mohon_kmditi_kapasitas_produksi_tahunan, ' ', mohon_kmditi_kapasitas_produksi_tahunan_satuan)) as produksi"),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);

        $jmlTemuan = 0;
        foreach ($dataAuditKlausul->get() as $kla) {
            if ($kla->aud_thp1_det_is_tinjauan == 'ya') {
                if ($kla->aud_thp1_det_hasil_tinjauan == 'no') {
                    $jmlTemuan++;
                }
            }
        }

        $dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
        $dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        $dataTim->select('*');
        $dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);

        $parser = [
            'module'           => $this->module,
            'url'              => $this->url,
            'restAudit'        => $restAudit,
            'dataAuditKlausul' => $dataAuditKlausul->get(),
            'dataTim'          => $dataTim->get(),
            'jmlTemuan'        => $jmlTemuan,
        ];

        foreach ($dataTim->get() as $tim) {
            if ($tim->thp1_tim_posisi == 'ketua') {
                $parser['ketua_tim']      = $tim->peg_nama;
                $parser['peg_ttd_base64'] = $tim->peg_ttd_base64;
                $parser['peg_ttd_file']   = $tim->peg_ttd_file;
                break;
            }
        }

        // return view("$this->view.print.lap_lengkap")->with($parser);
        $pdf = PDF::loadView("$this->view.print.lap_lengkap", $parser)->setPaper('a4', 'portrait');
        return $pdf->stream();
    }
}
