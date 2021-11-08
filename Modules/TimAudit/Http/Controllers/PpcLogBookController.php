<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisAuditPpc;
use App\Models\BbkkpSis\SisPelangganPabrik;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PpcLogBookController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/ppc/log-book';
    private $view = "timaudit::ppc_log_book";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('PPC', url($this->url)),
            new BreadcrumbsStruct('Log Book'),
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
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->leftJoin('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $join->on('sis_jadwal_tim.jadw_tim_posisi', '=', DB::raw("'ppc'"));
        });

        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->leftJoin('sis_audit_logbook', function ($join) {
            $join->on("sis_audit_logbook.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
            $join->on('logbook_jenis', '=', DB::raw("'ppc'"));
        });

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ppc');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'on-going');
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
		
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_tim_kesanggupan) AS jadw_tim_kesanggupan");
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;

            $x['logbook_filepath'] = ($d->logbook_filepath != '') ? '<a class="btn-xs btn-success btn-block" target="_blank" href = "' . url($d->logbook_filepath) . '"><i class="fas fa-cloud-download"></i> Download</a>' : '';
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-logbook' => $this->edit_upload_logbook($request),
            default          => null,
        };
    }

    private function edit_upload_logbook(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('PPC', url($this->url)),
            new BreadcrumbsStruct('Log Book'),
            new BreadcrumbsStruct('Upload Log Book'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $join->on('sis_jadwal_tim.jadw_tim_posisi', '=', DB::raw("'ppc'"));
        });

        $dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataJadwal->leftJoin('sis_audit_logbook', function ($join) {
            $join->on("sis_audit_logbook.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
            $join->on('logbook_jenis', '=', DB::raw("'ppc'"));
        });

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
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_id) AS jadw_tim_id");
        $dataJadwal->groupBy('sis_jadwal.jadw_id');
        $restJadwal = $dataJadwal->get()[0];
		
		$dataPabrik = SisPelangganPabrik::where('cust_id', $restJadwal->cust_id);
        $dataPabrik->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_pelanggan_pabrik.kab_id');
        $dataPabrik->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_pelanggan_pabrik.kec_id');
        $dataPabrik->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_pelanggan_pabrik.prov_id');
        $dataPabrik->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'dataPabrik' => $dataPabrik->get()];
        return view("$this->view.edit_upload_logbook")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-logbook' => $this->update_upload_logbook($request),
            default          => null,
        };
    }

    private function update_upload_logbook(Request $request)
    {
        $request->validate([
            "jadw_id"               => 'required',
            "jadw_tim_id"           => 'required',
            "logbook_filepath"      => 'required',
            "logbook_filepath_lama" => 'nullable',
        ]);

        $uploadedPath = [];
        try {
            if (!$request->hasFile('logbook_filepath')) throw new Exception("Mohon unggah file logbook", 400);

            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];
            // DEFINE BASE UPLOAD AND UPDATE logbook_filepath
            $baseFileUpload  = sprintf(config("app.path_file_audit"), $restJadwal->jadw_id);
            $fileLogbook     = $request->file('logbook_filepath');
            $fileLogbookName = Str::slug('file-logbook-ppc-' . $fileLogbook->getClientOriginalName()) . '-' . time() . '.' . $fileLogbook->getClientOriginalExtension();
            $fileLogbookPath = sprintf("%s/%s", $baseFileUpload, $fileLogbookName);
            $fileLogbook->move($baseFileUpload, $fileLogbookName);
            array_push($uploadedPath, $fileLogbookPath);
            DB::beginTransaction();
            if (DB::table('sis_audit_logbook')->where('jadw_tim_id', $request['jadw_tim_id'])->where('logbook_jenis', 'ppc')->exists()) {
                DB::table('sis_audit_logbook')
                    ->where('jadw_tim_id', $request['jadw_tim_id'])
                    ->where('logbook_jenis', 'ppc')
                    ->update(['logbook_filepath' => $fileLogbookPath]);
            } else {
                DB::table('sis_audit_logbook')->insert([
                    'jadw_tim_id'      => $request['jadw_tim_id'],
                    'logbook_jenis'    => 'ppc',
                    'logbook_filepath' => $fileLogbookPath,
                ]);
            }

            if ($request['logbook_filepath_lama'] != '') {
                @unlink($request['logbook_filepath_lama']);
            }

            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
