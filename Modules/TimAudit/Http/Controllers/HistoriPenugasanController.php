<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\TimAudit\Http\Traits\AuditorTraits;

class HistoriPenugasanController extends Controller
{
    use AuditorTraits;

    public $module = self::class;
    private $url = 'timaudit/histori-audit';
    private $view = "timaudit::histori_audit";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Histori Penugasan', url($this->url)),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }


    public function detail(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Histori Penugasan'),
            new BreadcrumbsStruct('Detail Penugasan'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");

        $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());

        $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_nomor_referensi) AS jadw_audit_nomor_referensi");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_nace) AS jadw_audit_kode_nace");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_ea) AS jadw_audit_kode_ea");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_standart_acuan) AS jadw_audit_standart_acuan");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_ruang_lingkup) AS jadw_audit_ruang_lingkup");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tujuan_audit) AS jadw_audit_tujuan_audit");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kegiatan) AS jadw_audit_kegiatan");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_posisi) AS jadw_tim_posisi");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.peg_id) AS peg_id");

        $dataJadwal->groupBy('sis_jadwal.jadw_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'jenis' => $request['jenis'], 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("$this->view.detail")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-penugasan' => $this->ajax_datagrid_penugasan($request),
            default              => null,
        };
    }

    private function ajax_datagrid_penugasan(Request $request)
    {
        $result = [];
        $data   = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        // $data->where('sis_jadwal.jadw_is_tutup', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
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
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);

        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', UPPER(jadw_audit_jenis) ) SEPARATOR ',<br/>') AS jadw_audit_jenis");
        $data->groupBy('sis_jadwal.jadw_id');

        foreach ($data->get() as $d) {
            if ($d->jadw_id != '') {
                $x['jadw_status']              = 'tahap-2';
                $x['jadw_id']                  = $d->jadw_id;
                $x['jadw_tanggal_mulai']       = $d->jadw_tanggal_mulai?->format("Y-m-d");
                $x['jadw_tanggal_selesai']     = $d->jadw_tanggal_selesai?->format("Y-m-d");
                $x['cust_nama']                = $d->cust_nama;
                $x['sert_nama']                = $d->sert_nama;
                $x['jadw_jenis']               = $d->jadw_jenis;
                $x['jadw_audit_jenis']         = $d->jadw_audit_jenis;
                $x['jadw_tim_kesanggupan']     = $d->jadw_tim_kesanggupan;
                $x['jadw_tim_kesanggupan_tgl'] = $d->jadw_tim_kesanggupan_tgl;
                $result[]                      = $x;
            }
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

}
