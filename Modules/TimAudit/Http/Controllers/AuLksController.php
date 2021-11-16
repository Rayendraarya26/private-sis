<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisJadwal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\TimAudit\Http\Traits\AuditorTraits;

class AuLksController extends Controller
{
    use AuditorTraits;

    public $module = self::class;
    private $url = 'timaudit/auditor/lks';
    private $view = "timaudit::auditor_lks";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('LKS'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function temuan(Request $request, $jadwalID)
    {
        try {
            // ============================== validation to access this page ==============================
            // 1.
            $dataJadwal = $this->involvedAuditor($jadwalID);

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Temuan'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal];
            return view("$this->view.temuan")->with($parser);
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function addTemuan(Request $request, $jadwalID)
    {
        try {
            // ============================== validation to access this page ==============================
            // 1.
            $dataJadwal = $this->involvedAuditor($jadwalID);

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Temuan', url($this->url . '/temuan/' . $jadwalID)),
                new BreadcrumbsStruct('Tambah'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal];
            return view("$this->view.temuan_add")->with($parser);
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function storeTemuan(Request $request, $jadwalID)
    {
        $request->validate([
            'jadw_audit_id'                => 'required|numeric',
            'lks_kategori_ketidaksesuaian' => ['required', Rule::in(['kritis', 'mayor', 'minor', 'observasi'])],
            'lks_klausul_ketidaksesuaian'  => 'required',
            'lks_uraian_ketidaksesuaian'   => 'required',
            'lks_expired_date_perbaikan'   => 'required'
        ]);

        try {
            $pegawaiID  = auth()->user()->master_pegawai->peg_id;
            $dataJadwal = $this->involvedAuditor($jadwalID);

            $dataTim = $dataJadwal->sis_jadwal_tims()->where("peg_id", $pegawaiID)->firstOrFail();

            SisAuditLks::updateOrCreate(
                ['lks_id' => $request['lks_id'], 'jadw_audit_id' => $request['jadw_audit_id']],
                [
                    'jadw_audit_id'                => $request['jadw_audit_id'],
                    'jadw_tim_id'                  => $dataTim->jadw_tim_id,
                    'lks_status'                   => 'proses',
                    'lks_uraian_ketidaksesuaian'   => $request['lks_uraian_ketidaksesuaian'],
                    'lks_kategori_ketidaksesuaian' => $request['lks_kategori_ketidaksesuaian'],
                    'lks_klausul_ketidaksesuaian'  => $request['lks_klausul_ketidaksesuaian'],
                    'lks_expired_date_perbaikan'   => $request['lks_expired_date_perbaikan']
                ]
            );
            return redirect($this->url . '/temuan/' . $dataJadwal->jadw_id)->with("message", "LKS berhasil ditambahkan");
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()])->withInput($request->except('_token'));
        }
    }

    public function editTemuan(Request $request, $jadwalID, $lksID)
    {
        try {
            // ============================== validation to access this page ==============================
            // 1.
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $dataJadwal = $this->involvedAuditor($jadwalID);
            $dataLKS    = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->where("sis_jadwal_tim.peg_id", $pegawaiID)->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Temuan', url($this->url . '/temuan/' . $jadwalID)),
                new BreadcrumbsStruct('Ubah'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'lks' => $dataLKS];
            return view("$this->view.temuan_edit")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function detailTemuan(Request $request, $jadwalID, $lksID)
    {
        try {
            // 1.
            $dataJadwal = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims'])->findOrFail($jadwalID);
            $dataLKS    = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])->findOrFail($lksID);

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Temuan', url($this->url . '/temuan/' . $jadwalID)),
                new BreadcrumbsStruct('Ubah'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'lks' => $dataLKS];
            return view("$this->view.temuan_detail")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function deleteTemuan(Request $request, $jadwalID, $lksID)
    {
        try {
            if (!$request->ajax()) throw new Exception("Endopoint ini utuk ajax");

            // 1.
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $this->involvedAuditor($jadwalID);
            $dataLKS = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->where("sis_jadwal_tim.peg_id", $pegawaiID)->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");

            $dataLKS->delete();
            return responseJSON(200, [], "Delete berhasil");
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function verifTemuan(Request $request, $jadwalID, $lksID)
    {
        try {
            $request->validate(['lks_status' => ['required', Rule::in(['memadai', 'tidak-memadai'])]]);

            // 1.
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $this->involvedAuditor($jadwalID);
            $dataLKS = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->where("sis_jadwal_tim.peg_id", $pegawaiID)->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");

            $dataLKS->lks_status        = $request['lks_status'];
            $dataLKS->lks_sudah_ditutup = 'ya';
            $dataLKS->save();

            return redirect()->back()->with('message', "Verifikasi berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-lks'          => $this->ajax_datagrid_lks($request),
            default                 => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::with('sis_jadwal_audits.sis_audit_lks');
        $data->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        });
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua', 'auditor']);
        // tambah jika not null file jadwal
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
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
        $total = $data->select(DB::raw('count(distinct sis_jadwal.jadw_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_tim_kesanggupan) AS jadw_tim_kesanggupan");
        $data->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status_komite = 'on-going', 1, 0)) as total_submit_komite");
        $data->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status = 'on-going', 1, 0)) as total_proses");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->havingRaw('total_submit_komite > ?', [0]);
        $data->havingRaw('total_proses > ?', [0]);
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $totalTemuanLKS = 0;
            foreach ($d->sis_jadwal_audits as $ja) {
                $totalTemuanLKS += $ja->sis_audit_lks->count();
            }

            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = ucwords($d->jadw_jenis);
            $x['total_jadwal']         = $d->sis_jadwal_audits->count();
            $x['total_temuan']         = $totalTemuanLKS;
            array_push($result, $x);
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_lks(Request $request)
    {
        $request->validate(['jadwal_id' => 'required|numeric']);
        $data                                  = SisAuditLks::join('sis_jadwal_audit', 'sis_jadwal_audit.jadw_audit_id', '=', 'sis_audit_lks.jadw_audit_id')
            ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
            ->leftJoin('sis_jadwal_tim', 'sis_jadwal_tim.jadw_tim_id', '=', 'sis_audit_lks.jadw_tim_id')
            ->where('sis_jadwal.jadw_id', $request['jadwal_id']);

        // Filter
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
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];

        $pegawaiID = auth()->user()->master_pegawai->peg_id;
        foreach ($data->get() as $d) {
            $allowModify = $d->peg_id == $pegawaiID;

            $x['allow_modify']                 = $allowModify;
            $x['jadw_tim_kode']                = $d->jadw_tim_kode . ($allowModify ? '<br><i title="Anda sebagai auditor">(<i class="fad fa-user"></i> Anda)</i>' : '');
            $x['lks_uraian_ketidaksesuaian']   = Str::limit(strip_tags($d->lks_uraian_ketidaksesuaian), 50);
            $x['lks_kategori_ketidaksesuaian'] = Str::limit(strip_tags($d->lks_kategori_ketidaksesuaian), 50);
            $x['lks_klausul_ketidaksesuaian']  = Str::limit(strip_tags($d->lks_klausul_ketidaksesuaian), 50);
            $x['lks_perbaikan_analisa']        = Str::limit(strip_tags($d->lks_perbaikan_analisa), 50);
            $x['lks_perbaikan_koreksi']        = Str::limit(strip_tags($d->lks_perbaikan_koreksi), 50);
            $x['lks_perbaikan_tindakan']       = Str::limit(strip_tags($d->lks_perbaikan_tindakan), 50);
            $x['lks_bagian_pendamping']        = $d->lks_bagian_pendamping;
            $x['lks_bukti_tindakan_perbaikan'] = $d->lks_bukti_tindakan_perbaikan;
            $x['lks_expired_date_perbaikan']   = $d->lks_expired_date_perbaikan?->format("Y-m-d H:i:s");
            $x['lks_input_date_perbaikan']     = $d->lks_input_date_perbaikan?->format("Y-m-d H:i:s");
            $x['lks_sudah_ditutup']            = $d->lks_sudah_ditutup;
            $x['lks_status']                   = $d->lks_status;
            $x['lks_id']                       = $d->lks_id;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
