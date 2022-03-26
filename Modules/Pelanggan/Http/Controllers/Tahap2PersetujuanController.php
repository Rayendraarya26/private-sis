<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SysUserGroup;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\TimAudit\Http\Traits\AuditorTraits;
use Modules\TimAudit\Http\Traits\LksTrait;

class Tahap2PersetujuanController extends Controller
{
    use AuditorTraits, LksTrait;

    public $module = self::class;
    private $url = 'pelanggan/tahap2/persetujuan-temuan';
    private $view = "pelanggan::tahap2_persetujuan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 2'),
            new BreadcrumbsStruct('Persetujuan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function detail(Request $request, $jadwID)
    {
        try {
            $dataJadwal = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims.sis_audit_logbook'])->findOrFail($jadwID);

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 2'),
                new BreadcrumbsStruct('Persetujuan Temuan', url($this->url)),
                new BreadcrumbsStruct('Detail'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal, 'dataLKS' => $dataLKS];
            return view("$this->view.detail")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage() . ' | ' . $e->getLine()]);
        }

    }

    public function approveTemuan(Request $request)
    {
        $request->validate([
            'jadw_id'             => 'required',
            'jadw_setujui_temuan' => Rule::in(['setuju', 'revisi']),
        ]);

        $newUploadedPath = [];
        try {
            DB::beginTransaction();
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->where('sis_pelanggan.user_id', auth()->id())
                ->with('sis_pelanggan')->findOrFail($request['jadw_id']);

            $data->jadw_setujui_temuan = $request['jadw_setujui_temuan'];
            $data->save();

            if ($request['jadw_setujui_temuan'] == "revisi") {
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-temuan',
                    'jlog_judul' => 'Revis Temuan LKS',
                    'jlog_pesan' => $request['message'],
                ]);

                Session::flash("message", "Revisi diajukan ke Tim Audit");
            } else {
                // process upload scan files
                $baseFileUpload = sprintf(config("app.path_file_audit"), $data->jadw_id);
                if (!File::exists($baseFileUpload)) {
                    File::makeDirectory($baseFileUpload, 0777, true, true);
                }

                // ======= LKS ======= //
                if ($request->hasFile('file_lks')) {
                    $fileLks     = $request->file('file_lks');
                    $fileLksName = Str::slug('file-scan-lks-' . $fileLks->getClientOriginalName()) . '-' . time() . '.' . $fileLks->getClientOriginalExtension();
                    $fileLksPath = sprintf("%s/%s", $baseFileUpload, $fileLksName);
                    $fileLks->move($baseFileUpload, $fileLksName);
                    $newUploadedPath[]   = public_path($fileLksPath);
                    $data->jadw_file_lks = $fileLksPath;
                }

                // ======= LapRingkas ======= //
                if ($request->hasFile('file_lap_ringkas')) {
                    $fileLapRingkas     = $request->file('file_lap_ringkas');
                    $fileLapRingkasName = Str::slug('file-scan-lapringkas-' . $fileLapRingkas->getClientOriginalName()) . '-' . time() . '.' . $fileLapRingkas->getClientOriginalExtension();
                    $fileLapRingkasPath = sprintf("%s/%s", $baseFileUpload, $fileLapRingkasName);
                    $fileLapRingkas->move($baseFileUpload, $fileLapRingkasName);
                    $newUploadedPath[]               = public_path($fileLapRingkasPath);
                    $data->jadw_file_laporan_ringkas = $fileLapRingkasPath;
                }

                // ======= SuratTugas ======= //
                if ($request->hasFile('file_surat_tugas')) {
                    $fileSuratTugas     = $request->file('file_surat_tugas');
                    $fileSuratTugasName = Str::slug('file-scan-surattugas-' . $fileSuratTugas->getClientOriginalName()) . '-' . time() . '.' . $fileSuratTugas->getClientOriginalExtension();
                    $fileSuratTugasPath = sprintf("%s/%s", $baseFileUpload, $fileSuratTugasName);
                    $fileSuratTugas->move($baseFileUpload, $fileSuratTugasName);
                    $newUploadedPath[]           = public_path($fileSuratTugasPath);
                    $data->jadw_file_surat_tugas = $fileSuratTugasPath;
                }

                // ======= Notulen ======= //
                if ($request->hasFile('file_notulen')) {
                    $fileNotulen     = $request->file('file_notulen');
                    $fileNotulenName = Str::slug('file-scan-notulen-' . $fileNotulen->getClientOriginalName()) . '-' . time() . '.' . $fileNotulen->getClientOriginalExtension();
                    $fileNotulenPath = sprintf("%s/%s", $baseFileUpload, $fileNotulenName);
                    $fileNotulen->move($baseFileUpload, $fileNotulenName);
                    $newUploadedPath[]       = public_path($fileNotulenPath);
                    $data->jadw_file_notulen = $fileNotulenPath;
                }

                // ======= SubKontrak ======= //
                if ($request->hasFile('file_subkon')) {
                    $fileSubkon     = $request->file('file_subkon');
                    $fileSubkonName = Str::slug('file-scan-subkon-' . $fileSubkon->getClientOriginalName()) . '-' . time() . '.' . $fileSubkon->getClientOriginalExtension();
                    $fileSubkonPath = sprintf("%s/%s", $baseFileUpload, $fileSubkonName);
                    $fileSubkon->move($baseFileUpload, $fileSubkonName);
                    $newUploadedPath[]      = public_path($fileSubkonPath);
                    $data->jadw_file_subkon = $fileSubkonPath;
                }

                $data->save();
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'informasi',
                    'jlog_judul' => 'Approve Temuan LKS',
                    'jlog_pesan' => sprintf("%s menyetujui temuan LKS", $data->sis_pelanggan->cust_nama),
                ]);

                Session::flash("message", sprintf("Temuan telah disetujui, silakan melakukan <a href='%s'>Perbaikan Temuan</a>", url("pelanggan/tahap2/perbaikan-temuan")));
            }

            $message = "";
            // Send Notification to Operator LS
            $groupUsers = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
            if ($groupUsers) {
                foreach ($groupUsers as $user) {
                    $notifStruct = new NotifStruct();
                    if ($request['jadw_setujui_temuan'] == "setuju") {
                        $notifStruct->title   = sprintf("#%d temuan LKS disetujui", $data->jadw_id);
                        $notifStruct->message = sprintf("%s memberikan persetujuan pada temuan LKS dan telah mengunggah Scan LKS, Surat Tugas, dan Laporan Ringkas yang telah diberi ttd dan cap", $data->sis_pelanggan->cust_nama);
                    } else {
                        $notifStruct->title   = sprintf("#%d Revisi temuan LKS", $data->jadw_id);
                        $notifStruct->message = sprintf("%s mengajuakan revisi pada temuan LKS", $data->sis_pelanggan->cust_nama);
                    }
                    $notifStruct->user_id   = $user?->ug_user_id;
                    $notifStruct->click_url = url('/timaudit/auditor/lks');

                    $message = $notifStruct->message;
                    // Send Push
                    sendNotification($notifStruct);
                }
            }

            DB::commit();
            return responseJSON(200, [], $message);
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($newUploadedPath as $path) {
                @unlink($path);
            }
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function cetak(Request $request, $jadwalID, $type)
    {
        try {
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->where('sis_pelanggan.user_id', auth()->id())
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);
            if (empty($data)) throw new Exception('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'notulen'      => $this->cetak_notulen($request, $data),
                'lap-ringkas'  => $this->cetak_lap_ringkas($request, $data),
                'daftar-hadir' => $this->cetak_daftar_hadir($request, $data),
                'logbook'      => $this->cetak_logbook($request, $data),
                'lks'          => $this->cetak_lks($request, $data),
                default        => throw new Exception("Invalid URL"),
            };
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }

    }

    private function cetak_notulen(Request $request, SisJadwal $dataJadwal)
    {
        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $parser = ['dataJadwal' => $dataJadwal, 'dataKetua' => $dataKetua];
        $pdf    = PDF::loadView("$this->view.print.notulen", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
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

    private function cetak_daftar_hadir(Request $request, SisJadwal $dataJadwal)
    {
        if (empty($dataJadwal->jadw_file_kehadiran)) {
            abort(404);
        }
        return response()->download(public_path($dataJadwal->jadw_file_kehadiran));
    }

    private function cetak_logbook(Request $request)
    {
        $parser = [];

        $pdf = PDF::loadView("$this->view.print.lks", $parser)
            ->setPaper('a4', 'landscape');
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

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisJadwal::with(['sis_jadwal_tims.master_pegawai', 'sis_jadwal_audits'])
            ->with([
                'sis_jadwal_tims' => function ($query) {
                    $query->orderBy(DB::raw("FIELD(jadw_tim_posisi, 'ketua', 'auditor', 'ppc', 'observer')"));
                }
            ])
            ->with([
                'sis_jadwal_logs' => function ($query) {
                    $query->where('jlog_tipe', 'revisi-temuan')->orderBy('created_at', 'desc');
                }
            ])
            ->whereIn('jadw_setujui_temuan', ['diajukan', 'revisi'])
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id);
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
        foreach ($data->get() as $d) {
            // if ($d->sis_jadwal_audits()->where('jadw_audit_status_komite', 'on-going')->count() > 0) {
                $timAudit = [];
                foreach ($d->sis_jadwal_tims as $tim) {
                    $timAudit[] = [
                        "tim_nama"   => $tim->master_pegawai->peg_nama,
                        'tim_kode'   => $tim->jadw_tim_kode,
                        'tim_posisi' => ucwords($tim->jadw_tim_posisi),
                    ];
                }
                $jadwalAudit = [];
                foreach ($d->sis_jadwal_audits as $jadwal) {
                    $jadwalAudit[] = [
                        'jadw_audit_jenis'            => ucwords($jadwal->jadw_audit_jenis),
                        'jadw_audit_nomor_sertifikat' => $jadwal->jadw_audit_nomor_sertifikat,
                        'jadw_audit_nomor_referensi'  => $jadwal->jadw_audit_nomor_referensi,
                    ];
                }
                $dataRevisi = [];
                foreach ($d->sis_jadwal_logs as $log) {
                    $dataRevisi[] = [
                        'title'   => $log->jlog_judul,
                        'message' => $log->jlog_pesan,
                        'time'    => $log->created_at?->isoFormat("LLLL")
                    ];
                }

                $totalTemuanLKS = $d->sis_audit_lks->count();

                $x['tims']                = $timAudit;
                $x['audits']              = $jadwalAudit;
                $x['revisi']              = $dataRevisi;
                $x['jadw_id']             = $d->jadw_id;
                $x['jadw_jenis']          = $d->jadw_jenis;
                $x['jadw_setujui_temuan'] = $d->jadw_setujui_temuan;
                $x['jadw_file_jadwal']    = asset($d->jadw_file_jadwal);
                $x['total_temuan']        = $totalTemuanLKS;

                if ($d->jadw_tanggal_mulai == $d->jadw_tanggal_selesai) {
                    $x['tanggal'] = sprintf("%s", $d->jadw_tanggal_mulai->isoFormat("LL"));
                } else {
                    $x['tanggal'] = sprintf("%s s/d %s", $d->jadw_tanggal_mulai->isoFormat("LL"), $d->jadw_tanggal_selesai->isoFormat("LL"));
                }
                $result[] = $x;
            // }
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
