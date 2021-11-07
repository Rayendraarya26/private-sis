<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditPpc;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisPelangganPabrik;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PpcLaporanController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/ppc/laporan';
    private $view = "timaudit::ppc_laporan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('PPC', url($this->url)),
            new BreadcrumbsStruct('Laporan PPC'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-ppc-laporan'  => $this->ajax_datagrid_ppc_laporan($request),
            default                 => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $join->on('sis_jadwal_tim.jadw_tim_posisi', '=', DB::raw("'ppc'"));
        });

        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->leftJoin('sis_audit_ppc', "sis_audit_ppc.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ppc');
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
        $data->selectRaw("count(distinct sis_audit_ppc.audit_ppc_id) as total_file");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->havingRaw('total_submit_komite > ?', [0]);
        $data->havingRaw('total_proses > ?', [0]);
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
            $x['total_file']           = $d->total_file;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_ppc_laporan(Request $request)
    {

        $data = SisAuditPpc::join('sis_jadwal', "sis_audit_ppc.jadw_id", "=", "sis_jadwal.jadw_id");

        $data->where('sis_jadwal.jadw_id', '=', $request->jadw_id);
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }

        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }

        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");

        $result = [];
        foreach ($data->get() as $d) {
            $x['audit_ppc_id']         = $d->audit_ppc_id;
            $x['jadw_id']              = $d->jadw_id;
            $x['audit_ppc_jenis_file'] = $d->audit_ppc_jenis_file;
            $x['audit_ppc_filepath']   = ($d->audit_ppc_filepath != '') ? '<a class="btn-xs btn-success btn-block" target="_blank" href = "' . url($d->audit_ppc_filepath) . '"><i class="fas fa-cloud-download"></i> Download</a>' : '';

            $x['created_at'] = $d->created_at?->format("Y-m-d");
            $x['updated_at'] = $d->updated_at?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-laporan' => $this->edit_upload_laporan($request),
            default          => null,
        };
    }

    private function edit_upload_laporan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('PPC', url($this->url)),
            new BreadcrumbsStruct('Laporan PPC', url($this->url)),
            new BreadcrumbsStruct('Upload Laporan PPC'),
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
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.peg_id) AS peg_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_id) AS jadw_tim_id");
        $dataJadwal->groupBy('sis_jadwal.jadw_id');
        $restJadwal = $dataJadwal->get()[0];

        $dataPabrik = SisPelangganPabrik::where('cust_id', $restJadwal->cust_id);
        $dataPabrik->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_pelanggan_pabrik.kab_id');
        $dataPabrik->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_pelanggan_pabrik.kec_id');
        $dataPabrik->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_pelanggan_pabrik.prov_id');
        $dataPabrik->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $restJadwal, 'dataPabrik' => $dataPabrik->get()];
        return view("$this->view.edit_upload_laporan")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-laporan' => $this->update_upload_laporan($request),
            default          => null,
        };
    }

    private function update_upload_laporan(Request $request)
    {
        $request->validate([
            "jadw_id"              => 'required',
            "audit_ppc_filepath"   => 'nullable',
            "audit_ppc_jenis_file" => 'required',
        ]);

        $uploadedPath = [];
        try {
            if (!$request->hasFile('audit_ppc_filepath')) throw new Exception("Mohon unggah file jadwal", 400);

            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal      = $dataJadwal->get()[0];
            $baseFileUpload  = sprintf(config("app.path_file_audit"), $restJadwal->jadw_id);
            $fileLaporan     = $request->file('audit_ppc_filepath');
            $fileLaporanName = Str::slug('file-laporan-ppc-' . $fileLaporan->getClientOriginalName()) . '-' . time() . '.' . $fileLaporan->getClientOriginalExtension();
            $fileLaporanPath = sprintf("%s/%s", $baseFileUpload, $fileLaporanName);
            $fileLaporan->move($baseFileUpload, $fileLaporanName);
            array_push($uploadedPath, $fileLaporanPath);
            DB::beginTransaction();

            $restLaporan = DB::table('sis_audit_ppc')->where('jadw_id', $request['jadw_id'])->where('audit_ppc_jenis_file', $request['audit_ppc_jenis_file'])->first();
            if ($restLaporan !== null) {
                @unlink($restLaporan->audit_ppc_filepath);

                DB::table('sis_audit_ppc')
                    ->where('jadw_id', $request['jadw_id'])
                    ->where('audit_ppc_jenis_file', $request['audit_ppc_jenis_file'])
                    ->update(['audit_ppc_filepath' => $fileLaporanPath]);
            } else {
                DB::table('sis_audit_ppc')->insert([
                    'jadw_id'              => $request['jadw_id'],
                    'audit_ppc_jenis_file' => $request['audit_ppc_jenis_file'],
                    'audit_ppc_filepath'   => $fileLaporanPath,
                ]);
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
