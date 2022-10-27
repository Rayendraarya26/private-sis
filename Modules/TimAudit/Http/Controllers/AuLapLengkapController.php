<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLapLengkap;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SysUser;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\TimAudit\Http\Traits\AuditorTraits;
use Modules\TimAudit\Http\Traits\LksTrait;

class AuLapLengkapController extends Controller
{
    use AuditorTraits, LksTrait;

    public $module = self::class;
    private $url = 'timaudit/auditor/laporan-lengkap';
    private $view = "timaudit::auditor_laporan_lengkap";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Laporan Lengkap'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function laporan(Request $request, $jadwalID)
    {
        try {
            $dataJadwal  = $this->isKepalaAudit($jadwalID);
            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('Laporan Lengkap', url($this->url)),
                new BreadcrumbsStruct('Laporan'),
            ];

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'dataLKS' => $dataLKS];

            return view("$this->view.add_or_update_lap_lengkap")->with($parser);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function processLaporan(Request $request, $jadwalID)
    {
        try {
            $dataJadwal = $this->isKepalaAudit($jadwalID);

            $where          = ['jadw_id' => $dataJadwal->jadw_id];
            $updateOrCreate = $request->except('_token');
            if ($updateOrCreate['lap_lengkp_verifikasi_diajukan'] == 'ya') {
                $dataUser = SysUser::whereIn('ug_group_id', ['11'])->select('*')->join('sys_user_group', 'ug_user_id', '=', 'user_id');
                foreach ($dataUser->get() as $us) {
                    $notifUsr            = new NotifStruct();
                    $notifUsr->title     = 'Pengajuan Laporan Lengkap Jadwal #' . $jadwalID;
                    $notifUsr->message   = sprintf("Pengajuan laporan lengkap untuk jadwal nomor #%s telah didelegasikan, silahkan lakukan proses Verifikasi.", $jadwalID);
                    $notifUsr->user_id   = $us->user_id;
                    $notifUsr->click_url = url('/koordinatorsertifikasi/verif');
                    sendNotification($notifUsr);
                }
            }

            SisAuditLapLengkap::updateOrCreate($where, $updateOrCreate);

            return redirect(url($this->url))->with('message', "Data berhasil ditambahkan/diperbarui");
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function preview(Request $request, $jadwalID, $type)
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
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
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
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
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
        $data = SisJadwal::with('sis_audit_lap_lengkap');
        $data->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        });
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        // $data->leftJoin('sis_audit_lks', "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_jadwal.jadw_setujui_temuan', ['setuju']);
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua', 'auditor']);
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
        // $data->selectRaw("SUM(case when lks_sudah_ditutup = 'tidak' then 1 else 0 end) as total_lks_belum_selesai");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');
        // $data->havingRaw('total_lks_belum_selesai = 0');

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']                        = $d->jadw_id;
            $x['jadw_tanggal_mulai']             = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai']           = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']                      = $d->cust_nama;
            $x['sert_nama']                      = $d->sert_nama;
            $x['jadw_jenis']                     = $d->jadw_jenis;
            $x['jadw_audit_jenis']               = ucwords($d->jadw_audit_jenis);
            $x['sudah_mengisi']                  = $d->sis_audit_lap_lengkap?->count() > 0;
            $x['lap_lengkp_verifikasi_status']   = $d->sis_audit_lap_lengkap?->count() > 0 ? $d->sis_audit_lap_lengkap->lap_lengkp_verifikasi_status : 'none';
            $x['lap_lengkp_verifikasi_diajukan'] = $d->sis_audit_lap_lengkap?->count() > 0 ? $d->sis_audit_lap_lengkap->lap_lengkp_verifikasi_diajukan : 'tidak';
            $result[]                            = $x;
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }

}
