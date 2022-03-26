<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisJadwal;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\TimAudit\Http\Traits\AuditorTraits;
use Modules\TimAudit\Http\Traits\LksTrait;

class AuPengajuanKomiteController extends Controller
{
    use AuditorTraits, LksTrait;

    public $module = self::class;
    private $url = 'timaudit/auditor/pengajuan-komite';
    private $view = "timaudit::auditor_pengajuan_komite";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Pengajuan Komite'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function detail(Request $request, $jadwalID, $type)
    {
        try {
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);
            if (empty($data)) throw new Exception('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'lap-lengkap'  => $this->cetak_lap_lengkap($request, $data),
                'lap-ringkas'  => $this->cetak_lap_ringkas($request, $data),
                'lks'          => $this->cetak_lks($request, $data),
                'detail-audit' => $this->detail_audit($request),
                default        => throw new Exception("Invalid URL"),
            };
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    private function detail_audit(Request $request)
    {
        try {
            $dataJadwal  = $this->isKepalaAuditDetail($request['jadw_id']);
            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
                new BreadcrumbsStruct('Pengajuan Komite', url($this->url)),
                new BreadcrumbsStruct('Detail Audit'),
            ];

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

            $dataAuditTim = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
                ->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
                ->leftJoin('sis_audit_daftar_periksa', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_daftar_periksa.jadw_tim_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->where('sis_jadwal_tim.jadw_tim_posisi', '!=', 'ppc')->select('*');


            $dataTimLogbook = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
                ->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
                ->leftJoin('sis_audit_logbook', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_logbook.jadw_tim_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');

            $dataSertifikat = SisJadwal::join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id")
                ->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');

            $parser = [
                'module'         => $this->module,
                'url'            => $this->url,
                'view'           => $this->view,
                'breadcrumbs'    => $breadcrumbs,
                'data'           => $dataJadwal,
                'dataLKS'        => $dataLKS,
                'dataAuditTim'   => $dataAuditTim->get(),
                'dataTimLogbook' => $dataTimLogbook->get(),
                'dataSertifikat' => $dataSertifikat->get(),
            ];

            return view("$this->view.detail_audit")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    private function cetak_lap_ringkas(Request $request, SisJadwal $dataJadwal)
    {
        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $dataLKS = $this->calculateTemuanLKS($dataJadwal);

        $parser = ['dataJadwal' => $dataJadwal, 'dataKetua' => $dataKetua, 'dataLKS' => $dataLKS];
        $pdf    = PDF::loadView("$this->view.print.lap-ringkas", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    private function cetak_lks(Request $request, SisJadwal $dataJadwal)
    {
        $dataLKS = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
            ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->where('sis_jadwal.jadw_id', $dataJadwal->jadw_id)
            ->orderBy('lks_nomor')
            ->get();

        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $parser = ['dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS, 'dataKetua' => $dataKetua];

        $pdf = PDF::loadView("$this->view.print.lks", $parser)
            ->setPaper('a4', 'landscape');
        return $pdf->stream();
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
            $pdf    = PDF::loadView("$this->view.print.lap-lengkap", $parser)->setPaper('a4', 'portrait');
            return $pdf->stream();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $dataJadwal  = $this->isKepalaAudit($request['jadw_id']);
            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
                new BreadcrumbsStruct('Pengajuan Komite', url($this->url)),
                new BreadcrumbsStruct('Proses Ajukan'),
            ];

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

            $dataAuditTim = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
                ->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
                ->leftJoin('sis_audit_daftar_periksa', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_daftar_periksa.jadw_tim_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->where('sis_jadwal_tim.jadw_tim_posisi', '!=', 'ppc')->select('*');


            $dataTimLogbook = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
                ->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
                ->leftJoin('sis_audit_logbook', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_logbook.jadw_tim_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');

            $dataSertifikat = SisJadwal::join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id")
                ->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id")
                ->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');


            $parser = [
                'module'         => $this->module,
                'url'            => $this->url,
                'view'           => $this->view,
                'breadcrumbs'    => $breadcrumbs,
                'data'           => $dataJadwal,
                'dataLKS'        => $dataLKS,
                'dataAuditTim'   => $dataAuditTim->get(),
                'dataTimLogbook' => $dataTimLogbook->get(),
                'dataSertifikat' => $dataSertifikat->get(),
            ];


            return view("$this->view.edit")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            "jadw_id"                   => 'required',
            "cust_nama"                 => 'required',
            "cust_email"                => 'required',
            "cust_id"                   => 'required',
            "user_id"                   => 'required',
            "jadw_file_laporan_ringkas" => 'required',
            "jadw_file_lks"             => 'required',
        ]);

        $newFilePath = [];
        $oldFilePath = [];
        $updateData  = [];

        try {
            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];
            if (!empty($restJadwal->jadw_file_laporan_ringkas)) array_push($oldFilePath, $restJadwal->jadw_file_laporan_ringkas);
            if (!empty($restJadwal->jadw_file_lks)) array_push($oldFilePath, $restJadwal->jadw_file_lks);

            $baseFileUpload = sprintf(config("app.path_file_audit"), $restJadwal->jadw_id);
            if ($request->hasFile('jadw_file_laporan_ringkas')) {
                $fileLks     = $request->file('jadw_file_laporan_ringkas');
                $fileLksName = Str::slug('file-lks-' . $request['jadw_id'] . '-' . $fileLks->getClientOriginalName()) . '-' . time() . '.' . $fileLks->getClientOriginalExtension();
                $fileLksPath = sprintf("%s/%s", $baseFileUpload, $fileLksName);
                $fileLks->move($baseFileUpload, $fileLksName);

                $updateData['jadw_file_laporan_ringkas'] = $fileLksPath;
                array_push($newFilePath, public_path($fileLksPath));
            }

            if ($request->hasFile('jadw_file_lks')) {
                $fileRingkas     = $request->file('jadw_file_lks');
                $fileRingkasName = Str::slug('file-lap-ringkas-' . $request['jadw_id'] . '-' . $fileRingkas->getClientOriginalName()) . '-' . time() . '.' . $fileRingkas->getClientOriginalExtension();
                $fileRingkasPath = sprintf("%s/%s", $baseFileUpload, $fileRingkasName);
                $fileRingkas->move($baseFileUpload, $fileRingkasName);

                $updateData['jadw_file_lks'] = $fileRingkasPath;
                array_push($newFilePath, public_path($fileRingkasPath));
            }

            DB::beginTransaction();

            DB::table('sis_jadwal')
                ->where('jadw_id', $request['jadw_id'])
                ->update($updateData);

            DB::table('sis_jadwal_audit')
                ->where('jadw_id', $request['jadw_id'])
                ->update(['jadw_audit_status_komite' => 'submited']);

            // Notifikasi
            /*
			ke LS dan Pelanggan
			*/
            DB::commit();
            return responseJSON(200, [], 'Berhasil diajukan ke Komite.');
        } catch (Exception $e) {
            foreach ($newFilePath as $path) { // remove new file uploaded
                @unlink($path);
            }
            return responseJSON(500, [], $e->getMessage());
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
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sis_billing', "sis_jadwal.bill_id", "=", "sis_billing.bill_id");
        $data->join('sis_audit_lap_lengkap', "sis_jadwal.jadw_id", "=", "sis_audit_lap_lengkap.jadw_id");
        $data->join('sis_audit_lap_ringkas', "sis_jadwal.jadw_id", "=", "sis_audit_lap_ringkas.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ketua');
        $data->where('sis_jadwal.jadw_setujui_temuan', '=', 'setuju');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        $data->where('sis_audit_lap_lengkap.lap_lengkp_verifikasi_status', '=', 'ya');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'jadw_id')
                    $data->where('sis_jadwal.jadw_id', 'LIKE', '%' . $f->value . '%');
                else if ($f->field == 'status_komite')
                    $data->where('sis_jadwal_audit.jadw_audit_status_komite', 'LIKE', '%' . $f->value . '%');
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
                else if ($sort[$i] == 'status_komite')
                    $data->orderBy('sis_jadwal_audit.jadw_audit_status_komite', $order[$i]);
                else
                    $data->orderBy($sort[$i], $order[$i]);
            }
        }
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_status_komite) AS status_komite");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
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
            $x['status_komite']        = ($d->status_komite);
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }
}
