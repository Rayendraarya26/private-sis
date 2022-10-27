<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuUploadJadwalTahap1Controller extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/upload-jadwal-tahap1';
    private $view = "timaudit::auditor_upload_jadwal_tahap1";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Upload Jadwal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
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
        $data = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id())
            ->where('sis_audit_tahap1.aud_thp1_ditutup', '!=', 'ya')
            ->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya')
            ->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua');

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
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['aud_thp1_tanggal_mulai']   = $d->aud_thp1_tanggal_mulai;
            $x['aud_thp1_tanggal_selesai'] = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['aud_thp1_file_jadwal']     = ($d->aud_thp1_file_jadwal != '') ? '<a target="_blank" href = "' . url($d->aud_thp1_file_jadwal) . '"><i class="fas fa-download"></i> Download</div>' : '';
            $result[]                      = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-jadwal' => $this->edit_upload_jadwal($request),
            default         => null,
        };
    }

    private function edit_upload_jadwal(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Upload Jadwal Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Upload'),
        ];


        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_nace) as jadw_audit_kode_nace'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ea) as jadw_audit_kode_ea'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as jadw_audit_ruang_lingkup'),
            DB::raw('GROUP_CONCAT(distinct sis_audit_tahap1_tim.thp1_tim_posisi) as jadw_tim_posisi'),
            DB::raw('GROUP_CONCAT(distinct master_pegawai.peg_id) as peg_id')
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        $dataJadwal->where('master_pegawai.user_id', '=', auth()->id())
            ->where('sis_audit_tahap1.aud_thp1_ditutup', '!=', 'ya')
            ->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya')
            ->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua')
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("$this->view.edit_upload_jadwal")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-jadwal' => $this->update_upload_jadwal($request),
            default         => null,
        };
    }

    private function update_upload_jadwal(Request $request)
    {
        $request->validate([
            "aud_thp1_id"          => 'required',
            "aud_thp1_file_jadwal" => 'required',
        ]);

        $uploadedPath = [];
        try {
            if (!$request->hasFile('aud_thp1_file_jadwal')) throw new ExpectedException("Mohon unggah file jadwal", 400);

            $dataJadwal = SisAuditTahap1::where('aud_thp1_id', $request['aud_thp1_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->first();
            // DEFINE BASE UPLOAD AND UPDATE aud_thp1_file_jadwal
            $baseFileUpload = sprintf(config("app.path_file_tahap1"), $restJadwal->aud_thp1_id);
            $fileJadwal     = $request->file('aud_thp1_file_jadwal');
            $fileJadwalName = Str::slug('file-jadwal-tahap1-' . $fileJadwal->getClientOriginalName()) . '-' . time() . '.' . $fileJadwal->getClientOriginalExtension();
            $fileJadwalPath = sprintf("%s/%s", $baseFileUpload, $fileJadwalName);
            $fileJadwal->move($baseFileUpload, $fileJadwalName);
            $uploadedPath[] = $fileJadwalPath;
            DB::beginTransaction();

            DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request['aud_thp1_id'])
                ->update(['aud_thp1_file_jadwal' => $fileJadwalPath]);

            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            foreach ($uploadedPath as $path) {
                @unlink(public_path($path));
            }
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
