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

class AuTahap1LogBookController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1-logbook';
    private $view = "timaudit::auditor_tahap_1_log_book";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Log Book Tahap I'),
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
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id")
            ->leftJoin('sis_audit_tahap1_logbook', "sis_audit_tahap1_tim.thp1_tim_id", "=", "sis_audit_tahap1_logbook.thp1_tim_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_audit_tahap1.aud_thp1_ditutup', '=', 'tidak');
        $data->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya');
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

        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, ' (' , UPPER(IF(sis_permohonan_detail.cust_sert_id IS NULL, 'baru', 'lama')), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->groupBy('sis_audit_tahap1.aud_thp1_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['aud_thp1_status_temuan']   = $d->aud_thp1_status_temuan;
            $x['aud_thp1_status']          = $d->aud_thp1_status;
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['aud_thp1_tanggal_mulai']   = $d->aud_thp1_tanggal_mulai;
            $x['aud_thp1_tanggal_selesai'] = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['jadw_jenis']               = $d->jadw_jenis;
            $x['aud_thp1_jenis']           = $d->aud_thp1_jenis;
            $x['thp1_logbook_filepath']    = ($d->thp1_logbook_filepath != '') ? '<a target="_blank" href = "' . url($d->thp1_logbook_filepath) . '"><i class="fas fa-download"></i> Download</a>' : '';
            $x['status_upload']            = ($d->thp1_logbook_filepath != '') ? 're-upload' : 'upload';
            $result[]                      = $x;
        }

        return response()->json(["rows" => $result]);
    }

    public function upload(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Log Book Tahap I'),
            new BreadcrumbsStruct('Upload'),
        ];
        $dataJadwal  = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
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
            DB::raw('GROUP_CONCAT(distinct master_pegawai.peg_id) as peg_id'),
            DB::raw('GROUP_CONCAT(distinct sis_audit_tahap1_tim.thp1_tim_id) as thp1_tim_id')
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
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id")
            ->leftJoin('sis_audit_tahap1_logbook', "sis_audit_tahap1_tim.thp1_tim_id", "=", "sis_audit_tahap1_logbook.thp1_tim_id")
            ->where('master_pegawai.user_id', '=', auth()->id())
            ->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya')
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("$this->view.upload")->with($parser);
    }

    public function save(Request $request)
    {
        $request->validate([
            "aud_thp1_id"                => 'required',
            "thp1_tim_id"                => 'required',
            "thp1_logbook_filepath"      => 'required',
            "thp1_logbook_filepath_lama" => 'nullable',
        ]);

        $uploadedPath = [];
        try {
            if (!$request->hasFile('thp1_logbook_filepath')) throw new ExpectedException("Mohon unggah file logbook", 400);
            // DEFINE BASE UPLOAD AND UPDATE thp1_logbook_filepath
            $baseFileUpload  = sprintf(config("app.path_file_tahap1"), $request['aud_thp1_id']);
            $fileLogbook     = $request->file('thp1_logbook_filepath');
            $fileLogbookName = Str::slug('file-logbook-auditor-' . $request['thp1_tim_id'] . '-' . $fileLogbook->getClientOriginalName()) . '-' . time() . '.' . $fileLogbook->getClientOriginalExtension();
            $fileLogbookPath = sprintf("%s/%s", $baseFileUpload, $fileLogbookName);
            $fileLogbook->move($baseFileUpload, $fileLogbookName);
            $uploadedPath[] = $fileLogbookPath;
            DB::beginTransaction();
            if (DB::table('sis_audit_tahap1_logbook')->where('thp1_tim_id', $request['thp1_tim_id'])->exists()) {
                DB::table('sis_audit_tahap1_logbook')
                    ->where('thp1_tim_id', $request['thp1_tim_id'])
                    ->update(['thp1_logbook_filepath' => $fileLogbookPath]);
            } else {
                DB::table('sis_audit_tahap1_logbook')->insert([
                    'thp1_tim_id'           => $request['thp1_tim_id'],
                    'thp1_logbook_filepath' => $fileLogbookPath,
                ]);
            }

            if ($request['thp1_logbook_filepath_lama'] != '') {
                @unlink($request['thp1_logbook_filepath_lama']);
            }

            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
