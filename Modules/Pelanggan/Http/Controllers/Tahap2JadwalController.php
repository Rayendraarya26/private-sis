<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SysUserGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Tahap2JadwalController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/tahap2/jadwal';
    private $view = 'pelanggan::tahap2_jadwal';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 2'),
            new BreadcrumbsStruct('Jadwal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid'            => $this->ajax_datagrid($request),
            'tinymce-uploadimage' => $this->ajax_tinymce_uploadimage($request),
            default               => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisJadwal::with(['sis_audit_tim_komites', 'sis_jadwal_tims'])
            ->with([
                'sis_jadwal_logs' => function ($query) {
                    $query->orderBy('jlog_tipe')->orderBy('jlog_id', 'desc')->whereIn('jlog_tipe', ['revisi-team', 'revisi-tanggal']);
                }
            ])
            ->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
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

        $data->where('sis_pelanggan.user_id', '=', auth()->id());
        // $data->where('jadw_team_status', '=', 'accepted');
        // $data->where('jadw_setujui_temuan', '!=', 'setuju');

        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {

            $logs = [];
            foreach ($d->sis_jadwal_logs as $log) {
                $logs[] = [
                    'tipe'    => $log->jlog_tipe,
                    'judul'   => $log->jlog_judul,
                    'pesan'   => $log->jlog_pesan,
                    'tanggal' => $log->created_at->isoFormat('LLLL'),
                ];
            }

            /* Reminder peserta harus mengunggah
            1. Scan LKS
            2. Scan Laporan Ringkas
            3. Scan Surat Tugas
            4. Scan Notulen
            5. Scan Subkontrak
            */
            $dataFileUpload = [
                [
                    'status' => !empty($d->jadw_file_lks),
                    'name'   => 'Scan LKS',
                    'url'    => !empty($d->jadw_file_lks) ? asset($d->jadw_file_lks) : 'javascript:void(0)',
                ],
                [
                    'status' => !empty($d->jadw_file_laporan_ringkas),
                    'name'   => 'Scan Lap Ringkas',
                    'url'    => !empty($d->jadw_file_laporan_ringkas) ? asset($d->jadw_file_laporan_ringkas) : 'javascript:void(0)',
                ],
                [
                    'status' => !empty($d->jadw_file_surat_tugas),
                    'name'   => 'Scan Surat Tugas',
                    'url'    => !empty($d->jadw_file_surat_tugas) ? asset($d->jadw_file_surat_tugas) : 'javascript:void(0)',
                ],
                [
                    'status' => !empty($d->jadw_file_notulen),
                    'name'   => 'Scan Notulen',
                    'url'    => !empty($d->jadw_file_notulen) ? asset($d->jadw_file_notulen) : 'javascript:void(0)',
                ],
                [
                    'status' => !empty($d->jadw_file_subkon),
                    'name'   => 'Scan Subkontrak',
                    'url'    => !empty($d->jadw_file_subkon) ? asset($d->jadw_file_subkon) : 'javascript:void(0)',
                ],
            ];

            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_status']  = $d->jadw_tanggal_status;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['jadw_setujui_temuan']  = $d->jadw_setujui_temuan;
            $x['jadw_team_status']     = $d->jadw_team_status;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_file_jadwal']     = empty($d->jadw_file_jadwal) ? "" : asset($d->jadw_file_jadwal);
            $x['enable_approval_tim']  = $d->sis_jadwal_tims->count() > 0;
            $x['logs']                 = $logs;
            $x['file_upload']          = $dataFileUpload;
            $result[]                  = $x;

        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_tinymce_uploadimage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimetypes:image/jpeg,image/png|max:1000', // 1MB
            ]);

            $img     = $request->file('file');
            $imgName = $img->hashName();
            $img->move(public_path(config('app.path_file_tinymce')), $imgName);
            $publicUrl = asset(config('app.path_file_tinymce') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }

    public function approveTanggal(Request $request, $jadwalID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal', url($this->url)),
            new BreadcrumbsStruct('Persetujuan Tanggal'),
        ];

        $data = SisJadwal::with(['sis_pelanggan', 'sis_jadwal_audits'])
            ->where('jadw_id', $jadwalID)
            ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
            ->firstOrFail();

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.approve_tanggal")->with($parser);
    }

    public function processApproveTanggal(Request $request, $jadwalID)
    {
        $request->validate(['jadw_tanggal_status' => ['required', Rule::in(['revisi', 'accepted'])]]);

        try {
            DB::beginTransaction();
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->firstOrFail();

            if ($request['jadw_tanggal_status'] == "revisi") {
                if (strip_tags($request['editor_revisi']) == "") throw new Exception("Anda harus mengisikan keterangan revisi");

                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-tanggal',
                    'jlog_judul' => sprintf('Revisi Tanggal Oleh %s', auth()->user()?->sis_pelanggan->cust_nama),
                    'jlog_pesan' => $request['editor_revisi'],
                ]);
            } else {
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'informasi',
                    'jlog_judul' => "Jadwal pelaksanaan audit disetujui oleh BBKKP dan Client",
                    'jlog_pesan' => sprintf('%s menyetujui pelaksanaan audit tanggal %s', auth()->user()?->sis_pelanggan->cust_nama, $data->jadw_tanggal_mulai->isoFormat('LL')),
                ]);

                // Notifikasi ke operator LS untuk membuat susunan TIM
                $groupUsers = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
                if ($groupUsers) {
                    foreach ($groupUsers as $user) {
                        // Send Push
                        $notifStruct            = new NotifStruct();
                        $notifStruct->title     = sprintf("#%d Tanggal disetujui", $data->jadw_id);
                        $notifStruct->message   = sprintf("%s telah menyetujui pelaksanaan audit tanggal %s s/d %s. Segera lakukan penyusunan Tim Audit", $data->sis_pelanggan->cust_nama, $data->jadw_tanggal_mulai, $data->jadw_tanggal_selesai);
                        $notifStruct->user_id   = $user?->ug_user_id;
                        $notifStruct->click_url = url('/operatorls/tim');
                        sendNotification($notifStruct);
                    }
                }
            }

            $data->jadw_tanggal_status = $request['jadw_tanggal_status'];
            $data->save();

            DB::commit();
            return redirect(url($this->url))->with("message", sprintf("Persetujuan berhasil dikirim (%s)", $request['jadw_tanggal_status']));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function approveTim(Request $request, $jadwalID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal', url($this->url)),
            new BreadcrumbsStruct('Persetujuan Tim'),
        ];

        $data = SisJadwal::with(['sis_pelanggan', 'sis_jadwal_audits', 'sis_jadwal_tims.master_pegawai'])
            ->with([
                'sis_jadwal_tims' => function ($query) {
                    $query->orderBy(DB::raw("FIELD(jadw_tim_posisi, 'ketua', 'auditor', 'ppc', 'observer')"));
                }
            ])
            ->where('jadw_id', $jadwalID)
            ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
            ->firstOrFail();

        if ($data->sis_jadwal_tims->count() == 0) abort(404);


        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.approve_tim")->with($parser);
    }

    public function processApproveTim(Request $request, $jadwalID)
    {
        $request->validate(['jadw_team_status' => ['required', Rule::in(['revisi', 'accepted'])]]);

        try {
            DB::beginTransaction();
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->with('sis_jadwal_tims.master_pegawai')
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->firstOrFail();

            if ($request['jadw_team_status'] == "revisi") {
                if (strip_tags($request['editor_revisi']) == "") throw new Exception("Anda harus mengisikan keterangan revisi");

                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-team',
                    'jlog_judul' => sprintf('Revisi Tim Oleh %s', auth()->user()?->sis_pelanggan->cust_nama),
                    'jlog_pesan' => $request['editor_revisi'],
                ]);
            } else {
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'informasi',
                    'jlog_judul' => "Tim pelaksanaan audit disetujui oleh BBKKP dan Client",
                    'jlog_pesan' => sprintf('%s menyetujui susunan tim audit', auth()->user()?->sis_pelanggan->cust_nama),
                ]);

                // Notifikasi ke operator LS
                $groupUsers = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
                if ($groupUsers) {
                    foreach ($groupUsers as $user) {
                        // Send Push
                        $notifStruct            = new NotifStruct();
                        $notifStruct->title     = sprintf("#%d Tim Audit disetujui", $data->jadw_id);
                        $notifStruct->message   = sprintf("%s telah menyetujui tim audit. Mohon infokan kepada auditor terkait untuk melakukan persetujuan auditor", $data->sis_pelanggan->cust_nama, $data->jadw_tanggal_mulai, $data->jadw_tanggal_selesai);
                        $notifStruct->user_id   = $user?->ug_user_id;
                        $notifStruct->click_url = url('/operatorls/tim');
                        sendNotification($notifStruct);
                    }
                }

                // Notifikasi ke Auditor terkait Untuk Persetujuan Auditor
                foreach ($data->sis_jadwal_tims as $tim) {
                    // Send Push
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = sprintf("#%d anda ditunjuk sebagai '%s' auditor", $data->jadw_id, strtoupper($tim->jadw_tim_posisi));
                    $notifStruct->message   = sprintf("Harap segera melakukan persetujuan %s auditor pada perusahaan %s tanggal %s s/d %s", strtoupper($tim->jadw_tim_posisi), $data->sis_pelanggan->cust_nama, $data->jadw_tanggal_mulai, $data->jadw_tanggal_selesai);
                    $notifStruct->user_id   = $tim->master_pegawai?->user_id;
                    $notifStruct->click_url = url('/timaudit/persetujuan-tim/auditor');
                    sendNotification($notifStruct);
                }

            }

            $data->jadw_team_status = $request['jadw_team_status'];
            $data->save();

            DB::commit();
            return redirect(url($this->url))->with("message", sprintf("Persetujuan berhasil dikirim (%s)", $request['jadw_team_status']));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function uploadScan(Request $request, $jadwalID)
    {
        try {
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->first();
            if (empty($data)) throw new Exception("Jadwal tidak ditemukan");
            if ($data->jadw_setujui_temuan != "setuju") throw new Exception("Anda belum diperbolehkan mengakses halaman upload scan");

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 2', url($this->url)),
                new BreadcrumbsStruct('Upload Scan'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];

            return view("$this->view.upload_scan")->with($parser);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function processUploadScan(Request $request, $jadwalID)
    {
        $newUploadedPath = [];
        try {
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->first();
            if (empty($data)) throw new Exception("Jadwal tidak ditemukan");

            // process upload scan files
            $baseFileUpload = sprintf(config("app.path_file_audit"), $data->jadw_id);
            if (!File::exists($baseFileUpload)) {
                File::makeDirectory($baseFileUpload, 0777, true, true);
            }

            DB::beginTransaction();
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

            DB::commit();

            return redirect(url($this->url))->with('message', 'Upload success');
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($newUploadedPath as $path) {
                @unlink($path);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }

    }
}
