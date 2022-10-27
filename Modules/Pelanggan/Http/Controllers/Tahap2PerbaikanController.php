<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisAuditLksFile;
use App\Models\BbkkpSis\SisAuditLksRevisi;
use App\Models\BbkkpSis\SisJadwal;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Pelanggan\Http\Traits\Tahap2Trait;

class Tahap2PerbaikanController extends Controller
{
    use Tahap2Trait;

    public $module = self::class;
    private $url = 'pelanggan/tahap2/perbaikan-temuan';
    private $view = "pelanggan::tahap2_perbaikan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahpa 2'),
            new BreadcrumbsStruct('Perbaikan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function temuanLKS(Request $request, $jadwalID)
    {
        $data = SisJadwal::with(['sis_audit_lks.sis_jadwal_tim'])
            ->where('sis_jadwal.jadw_id', $jadwalID)
            ->where('jadw_setujui_temuan', 'setuju')
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->firstOrFail();

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Audit', url($this->url)),
            new BreadcrumbsStruct('Temuan LKS'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.temuan_lks")->with($parser);
    }

    public function detailAllLKS(Request $request, $jadwalID)
    {
        try {
            $dataJadwal = $this->lksMustBeApprove($jadwalID);
            $dataLKS    = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
                ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 2'),
                new BreadcrumbsStruct('Persetujuan Temuan', url($this->url)),
                new BreadcrumbsStruct('Detail'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS];
            return view("$this->view.detail_all_lks")->with($parser);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect()->back()->withErrors(['message' => $e->getMessage() . ' | ' . $e->getLine()]);
        }
    }

    public function savePerbaikanText(Request $request, $jadwalID, $lksID)
    {
        $request->validate([
            'key'   => ['required', Rule::in(['lks_perbaikan_analisa', 'lks_perbaikan_koreksi', 'lks_perbaikan_tindakan', 'lks_bukti_tindakan_perbaikan'])],
            'value' => 'required'
        ]);

        try {
            $data = SisAuditLks::with(['sis_jadwal', 'sis_audit_lks_files'])
                ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_audit_lks.jadw_id')
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_audit_lks.lks_id', $lksID)
                ->first();
            if (empty($data)) throw new ExpectedException("Data Audit tidak ditemukan");

            DB::beginTransaction();

            $key              = $request['key'];
            $value            = $request['value'];
            $data->$key       = $value;
            $data->save();

            DB::commit();
            return responseJSON(200, [], "Perbaikan LKS berhasil disimpan");
        } catch (Exception $e) {
            DB::rollBack();
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage() . '|err:' . $e->getLine());
        }
    }

    public function savePerbaikanFile(Request $request, $jadwalID, $lksID)
    {
        // $failedDeletedPath = [];
        try {
            if (!$request->hasFile("files")) throw new ExpectedException("File tidak dapat kosong");

            $data = SisAuditLks::with(['sis_jadwal', 'sis_audit_lks_files'])
                ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_audit_lks.jadw_id')
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_audit_lks.lks_id', $lksID)
                ->first();
            if (empty($data)) throw new ExpectedException("Data Audit tidak ditemukan");
            DB::beginTransaction();

            if (count($data->sis_audit_lks_files) > 0) {
                foreach ($data->sis_audit_lks_files as $file) {
                    $successDeletedPath[] = public_path($file->lks_filepath);
                    $file->delete();
                }
            }

            $baseFileUpload = sprintf(config("app.path_file_perbaikan_lks"), $data->lks_id);
            foreach ($request->file('files') as $berkas) {
                if (!empty($berkas)) {
                    $filePebaikan       = $berkas;
                    $filePebaikanName   = Str::slug($filePebaikan->getClientOriginalName()) . '-' . time() . '.' . $filePebaikan->getClientOriginalExtension();
                    $filePebaikanFolder = $baseFileUpload;
                    if (!File::exists($filePebaikanFolder)) File::makeDirectory($filePebaikanFolder, 0777, true, true);
                    $filePebaikan->move($filePebaikanFolder, $filePebaikanName);

                    $newSisLksFile = SisAuditLksFile::create([
                        'lks_id'       => $data->lks_id,
                        'lks_filepath' => sprintf("%s/%s", $filePebaikanFolder, $filePebaikanName),
                        'created_at'   => Carbon::now(),
                    ]);
                    // $failedDeletedPath[] = public_path($newSisLksFile->lks_filepath);
                }
            }

            DB::commit();

            return responseJSON(200, [], "Perbaikan LKS berhasil disimpan");
        } catch (Exception $e) {
            DB::rollBack();
            // foreach ($failedDeletedPath as $del) {
            //     @unlink($del);
            // }
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage() . '|err:' . $e->getLine());
        }
    }

    public function submitLKS(Request $request, $jadwalID)
    {
        try {
            $submittedIDs = $request->get('ids');

            $dataJadwal = $this->lksMustBeApprove($jadwalID);
            if (empty($dataJadwal)) throw new ExpectedException("Data Audit tidak ditemukan");

            DB::beginTransaction();
            foreach ($dataJadwal->sis_audit_lks as $lks) {
                if (in_array($lks->lks_id, $submittedIDs)) {
                    $lks->lks_status = 'fixed';
                    $lks->save();

                    // Send Notification to Auditor terkait
                    $dataPegawai            = $lks->sis_jadwal_tim->master_pegawai;
                    $dataPelanggan          = $dataJadwal->sis_pelanggan;
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = "Pengajuan verifikasi LKS";
                    $notifStruct->message   = sprintf("%s mengajukan verifikasi LKS pada uraian %s", $dataPelanggan->cust_nama, Str::limit(strip_tags($lks->lks_uraian_ketidaksesuaian), 50));
                    $notifStruct->user_id   = $dataPegawai->user_id;
                    $notifStruct->click_url = url(sprintf('timaudit/auditor/lks/temuan/%d/verifikasi', $dataJadwal->jadw_id));
                    sendNotification($notifStruct);
                }
            }

            DB::commit();
            return responseJSON(200, ['redirect' => url($this->url)], "LKS berhasil diajukan ke Auditor");
        } catch (Exception $e) {
            DB::rollBack();
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage() . '|err:' . $e->getLine());
        }
    }

    public function upload(Request $request)
    {

    }

    public function processUpload(Request $request)
    {

    }

    public function cetak(Request $request, $jadwalID, $type)
    {
        try {
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->where('sis_pelanggan.user_id', auth()->id())
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);
            if (empty($data)) throw new ExpectedException('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'lks'          => $this->cetak_lks($request, $data),
                default        => throw new ExpectedException("Invalid URL"),
            };
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }

    }

    private function cetak_lks(Request $request, SisJadwal $dataJadwal)
    {
        $dataLKS = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
            ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->where('sis_jadwal.jadw_id', $dataJadwal->jadw_id)
            ->orderBy(DB::raw('CONVERT(lks_nomor,UNSIGNED INTEGER)'))
            ->get();

        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $parser = ['dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS, 'dataKetua' => $dataKetua];

        $headerPath = base_path('Modules/Pelanggan/Resources/views/tahap2_persetujuan/print/lks-header.html');
        $pdf        = SnappyPdf::loadView('pelanggan::tahap2_persetujuan.print.lks', $parser);
        $pdf->setPaper('a4');
        $pdf->setOrientation('landscape');
        $pdf->setOptions([
            'margin-top'               => 20,
            'enable-local-file-access' => true,
            'header-html'              => $headerPath,
            'header-spacing'           => 12,
            'footer-left'              => 'F-TA-9     Rev. 2.0        Tanggal Berlaku Sejak: 25 Mei 2022',
            'footer-right'             => "[page] dari [topage]",
            'footer-font-size'         => 8,
            'footer-spacing'           => 2,
        ]);
        return $pdf->inline('LKS-' . Str::of($dataJadwal->sis_pelanggan->cust_nama)->slug()->upper() . '-' . Str::of($dataJadwal->jadw_tanggal_mulai->isoFormat('LL'))->slug()->upper() . '.pdf');
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid'                 => $this->ajax_datagrid($request),
            'data-verif-revisi-by-lks' => $this->ajax_verif_revisi_by_lks($request),
            'tinymce-uploadimage'      => $this->ajax_tinymce_uploadimage($request),
            default                    => responseJSON(404, null, "Invalid url"),
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
            ->join('sis_jadwal_audit', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id');

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

        $data->select(
            'sis_jadwal.jadw_id',
            'sis_jadwal.jadw_jenis',
            'sis_jadwal.jadw_file_jadwal',
            'sis_jadwal.jadw_tanggal_mulai',
            'sis_jadwal.jadw_tanggal_selesai',
            'sis_jadwal.jadw_file_lks',
            'sis_jadwal.jadw_file_laporan_ringkas',
            'sis_jadwal.jadw_file_surat_tugas',
            'sis_jadwal.jadw_file_notulen',
            'sis_jadwal.jadw_file_subkon',
        )->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status = 'on-going', 1, 0)) as total_proses");

        $data->where('jadw_setujui_temuan', 'setuju');
        $data->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id);
        $data->groupBy('sis_jadwal.jadw_id');
        $data->havingRaw('total_proses > ?', [0]);

        // Total
        $total = $data->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows)->take($request->rows);

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

            $allowEditLks = false;
            if ($d->sis_jadwal_audits()->where('jadw_audit_status_komite', 'on-going')->count() > 0) {
                $allowEditLks = true;
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

            $x['allow_edit_lks']   = $allowEditLks;
            $x['tims']             = $timAudit;
            $x['audits']           = $jadwalAudit;
            $x['jadw_id']          = $d->jadw_id;
            $x['jadw_jenis']       = $d->jadw_jenis;
            $x['jadw_file_jadwal'] = asset($d->jadw_file_jadwal);
            $x['total_temuan']     = $d->sis_audit_lks->count();
            $x['file_upload']      = $dataFileUpload;
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

    private function ajax_verif_revisi_by_lks(Request $request)
    {
        $data = SisAuditLksRevisi::where('lks_id', $request['lks_id'])->orderBy('created_at', 'desc')->first();

        if (!empty($data)) {
            return responseJSON(200, $data, "data ditemukan");
        } else {
            return responseJSON(500, [], "data tidak ditemukan");
        }

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
}
