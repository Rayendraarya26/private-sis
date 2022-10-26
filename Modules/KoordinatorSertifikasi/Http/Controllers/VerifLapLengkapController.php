<?php

namespace Modules\KoordinatorSertifikasi\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLapLengkap;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalTim;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\TimAudit\Http\Traits\AuditorTraits;
use Modules\TimAudit\Http\Traits\LksTrait;

class VerifLapLengkapController extends Controller
{
    use AuditorTraits, LksTrait;

    public $module = self::class;
    private $view = "koordinatorsertifikasi::verif_lap_lengkap";
    private $url = 'koordinatorsertifikasi/verif';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Koorinator Sertifikasi'),
            new BreadcrumbsStruct('Verifikasi Lap. Tahap 2'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'view' => $this->view];
        return view("$this->view.index")->with($parser);
    }

    public function detail(Request $request, $jadwalID)
    {
        try {

            $breadcrumbs = [
                new BreadcrumbsStruct('Koorinator Sertifikasi'),
                new BreadcrumbsStruct('Verifikasi Lap. Tahap 2'),
                new BreadcrumbsStruct('Detail Laporan Lengkap'),
            ];

            $dataJadwal = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);

            $dataLKS   = $this->calculateTemuanLKS($dataJadwal);
            $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'dataLKS' => $dataLKS, 'dataKetua' => $dataKetua];

            return view("$this->view.detail")->with($parser);
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function verifikasi(Request $request)
    {
        try {
            $where          = ['jadw_id' => $request->jadw_id];
            $updateOrCreate = $request->except('_token');
            $groupAuditor   = SisJadwalTim::join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
            $groupAuditor->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
            $groupAuditor->where('jadw_tim_posisi', '=', 'ketua')->where('sis_jadwal_tim.jadw_id', '=', $request->jadw_id)->select(DB::raw('sys_user.user_id AS user_id'), 'jadw_tim_posisi');

            if ($updateOrCreate['lap_lengkp_verifikasi_status'] == 'ya') {
                $updateOrCreate['lap_lengkp_verifikasi_tanggal'] = Carbon::now();
                foreach ($groupAuditor->get() as $auditor) {
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = 'Verifikasi Laporan Lengkap';
                    $notifStruct->message   = sprintf("Laporan lengkap untuk jadwal nomor #%s telah di-verifikasi dengan status valid, silahkan lakukan mengisikan rekomendasi LKS.", $request->jadw_id);
                    $notifStruct->user_id   = $auditor->user_id;
                    $notifStruct->click_url = url('/timaudit/auditor/lks');
                    sendNotification($notifStruct);
                }
            } else {
                foreach ($groupAuditor->get() as $auditor) {
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = 'Revisi Laporan Lengkap';
                    $notifStruct->message   = sprintf("Laporan lengkap untuk jadwal nomor #%s telah di-revisi, silahkan lihat revisi dan ajukan kembali.", $request->jadw_id);
                    $notifStruct->user_id   = $auditor->user_id;
                    $notifStruct->click_url = url('/timaudit/auditor/laporan-lengkap');
                    sendNotification($notifStruct);
                }
                $updateOrCreate['lap_lengkp_verifikasi_diajukan'] = 'tidak';
            }

            SisAuditLapLengkap::updateOrCreate($where, $updateOrCreate);
            $responseMessage = sprintf("Data berhasil disimpan untuk jadwal #", $request->jadw_id);
            return redirect()->back()->with('message', $responseMessage);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
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
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_audit_lap_lengkap', "sis_jadwal.jadw_id", "=", "sis_audit_lap_lengkap.jadw_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");

        // Filter
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_jadwal.jadw_setujui_temuan', ['setuju']);
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        $data->where('sis_audit_lap_lengkap.lap_lengkp_verifikasi_diajukan', '=', 'ya');
        $data->whereIn('sis_audit_lap_lengkap.lap_lengkp_verifikasi_status', ['none', 'revisi']);
        // tambah jika not null file jadwal
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'jadw_id')
                    $data->where('sis_jadwal.jadw_id', 'LIKE', '%' . $f->value . '%');
                else
                    $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if ($sort[$i] == 'jadw_id')
                    $data->orderBy('sis_jadwal.jadw_id', $order[$i]);
                else
                    $data->orderBy($sort[$i], $order[$i]);

            }
        }
        // Total
        $total = $data->select(DB::raw('count(distinct sis_jadwal.jadw_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(DISTINCT jadw_audit_jenis) AS jadw_audit_jenis");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = ucwords($d->jadw_audit_jenis);
            $x['sudah_mengisi']        = $d->sis_audit_lap_lengkap?->count() > 0;
            array_push($result, $x);
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function cetak(Request $request, $jadwalID, $type)
    {
        try {
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);
            if (empty($data)) throw new ExpectedException('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'lap-lengkap' => $this->cetak_lap_lengkap($request, $data),
                default       => throw new ExpectedException("Invalid URL"),
            };
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) {
                log_error($e, $request->except("_token"));
            }
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    private function cetak_lap_lengkap(Request $request, SisJadwal $dataJadwal)
    {

        try {
            $restJadwal = SisJadwal::where('sis_jadwal.jadw_id', $dataJadwal->jadw_id);
            $restJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
            $restJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
            $restJadwal->join('sis_audit_lap_lengkap', "sis_jadwal.jadw_id", "=", "sis_audit_lap_lengkap.jadw_id");
            $restJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
            $restJadwal->join("sis_jadwal_tim", "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $restJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
            $restJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
            $restJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_kegiatan) SEPARATOR ',<br/>' ) AS jadw_audit_kegiatan");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', komodt_nama) SEPARATOR ',<br/>' ) AS komodt_nama");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT(jadw_audit_nomor_referensi) SEPARATOR ',' ) AS jadw_audit_nomor_referensi");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_standart_acuan) SEPARATOR ',<br/>' ) AS jadw_audit_standart_acuan");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_ruang_lingkup) SEPARATOR ',<br/>' ) AS jadw_audit_ruang_lingkup");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_tujuan_audit) SEPARATOR ',<br/>' ) AS jadw_audit_tujuan_audit");

            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT IF(sis_jadwal_tim.jadw_tim_posisi = 'ketua', CONCAT(peg_nama), '') SEPARATOR ', ') as ketua");
            $restJadwal->selectRaw("GROUP_CONCAT(DISTINCT IF(sis_jadwal_tim.jadw_tim_posisi != 'ketua', CONCAT(peg_nama, '(', jadw_tim_posisi , ')'), '') SEPARATOR ', ') as anggota");
            $restJadwal->groupBy('sis_jadwal.jadw_id');
            $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

            $parser = ['dataJadwal' => $restJadwal->get()[0], 'dataLKS' => $dataLKS, 'itemLKS' => $dataJadwal->sis_audit_lks, 'dataKetua' => $dataKetua];
            // return view("$this->view.print.lap-lengkap")->with($parser);
            $pdf = PDF::loadView("$this->view.print.lap-lengkap", $parser)->setPaper('a4', 'portrait');
            return $pdf->stream();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
