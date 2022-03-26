<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1PersetujuanRevisi;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisPermohonanStatus;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Tahap1PersetujuanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/tahap1/persetujuan-temuan';
    private $view = "pelanggan::tahap1_persetujuan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 1'),
            new BreadcrumbsStruct('Persetujuan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'view' => $this->view, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function approveTemuan(Request $request)
    {
        $request->validate(['aud_thp1_id' => 'required|integer', 'status' => ['required', Rule::in(['setuju', 'revisi'])]]);

        $newUploadedPath = [];
        try {
            if ($request['status'] == 'revisi') {
                if (empty($request['catatan'])) throw new Exception('Mohon tuliskan catatan');
            }

            DB::beginTransaction();
            $dataTahap1 = SisAuditTahap1::join("sis_permohonan", "sis_permohonan.mohon_id", "=", "sis_audit_tahap1.mohon_id")
                ->where("user_id", auth()->id())
                ->findOrFail($request['aud_thp1_id']);

            $dataTahap1->aud_thp1_status_temuan = $request['status'];
            $dataTahap1->save();

            $timeNow         = Carbon::now();
            if ($request['status'] == "setuju") {
                $responseMessage = sprintf("%s menyetujui temuan tahap 1, segera lakukan perbaikan apabila auditor memiliki temuan <a href='%s'>disini</a>", $dataTahap1->mohon_cust_nama, url( '/pelanggan/tahap1/perbaikan-temuan'));
                // Add permohonan Status
                SisPermohonanStatus::updateOrCreate(
                    [
                        "status_mohon_id" => $dataTahap1->mohon_id,
                        "status_tipe"     => "informasi",
                        "status_judul"    => "Temuan tahap 1",
                        "status_pesan"    => sprintf("%s menyetujui temuan tahap 1", $dataTahap1->mohon_cust_nama),
                        "created_at"      => $timeNow
                    ],
                    [
                        "updated_at" => $timeNow,
                    ]);

                // Upload Dokumens

                $baseFileUpload = sprintf(config("app.path_file_tahap1"), $dataTahap1->aud_thp1_id);

                if ($request->hasFile('file_surat_tugas')) {
                    // ======= SuratTugas ======= //
                    $fileSuratTugas     = $request->file('file_surat_tugas');
                    $fileSuratTugasName = Str::slug('file-scan-surattugas-' . $fileSuratTugas->getClientOriginalName()) . '-' . time() . '.' . $fileSuratTugas->getClientOriginalExtension();
                    $fileSuratTugasPath = sprintf("%s/%s", $baseFileUpload, $fileSuratTugasName);
                    $fileSuratTugas->move($baseFileUpload, $fileSuratTugasName);
                    $newUploadedPath[]                     = public_path($fileSuratTugasPath);
                    $dataTahap1->aud_thp1_file_surat_tugas = $fileSuratTugasPath;
                }

                if ($request->hasFile('file_notulen')) {
                    // ======= Notulen ======= //
                    $fileNotulen     = $request->file('file_notulen');
                    $fileNotulenName = Str::slug('file-scan-notulen-' . $fileNotulen->getClientOriginalName()) . '-' . time() . '.' . $fileNotulen->getClientOriginalExtension();
                    $fileNotulenPath = sprintf("%s/%s", $baseFileUpload, $fileNotulenName);
                    $fileNotulen->move($baseFileUpload, $fileNotulenName);
                    $newUploadedPath[]                 = public_path($fileNotulenPath);
                    $dataTahap1->aud_thp1_file_notulen = $fileNotulenPath;
                }

                // ======= Notulen ======= //
                if ($request->hasFile('file_subkontrak')) {
                    $fileSubkon     = $request->file('file_subkontrak');
                    $fileSubkonName = Str::slug('file-scan-subkon-' . $fileSubkon->getClientOriginalName()) . '-' . time() . '.' . $fileSubkon->getClientOriginalExtension();
                    $fileSubkonPath = sprintf("%s/%s", $baseFileUpload, $fileSubkonName);
                    $fileSubkon->move($baseFileUpload, $fileSubkonName);
                    $newUploadedPath[]                = public_path($fileSubkonPath);
                    $dataTahap1->aud_thp1_file_subkon = $fileSubkonPath;
                }

                $dataTahap1->save();
            } else {
                $responseMessage = sprintf("%s mengajuakan revisi pada temuan tahap 1", $dataTahap1->mohon_cust_nama);
                SisAuditTahap1PersetujuanRevisi::create([
                    'aud_thp1_id'                        => $dataTahap1->aud_thp1_id,
                    'aud_thp1_perseujuan_revisi_catatan' => $request['catatan'],
                ]);

                // Add permohonan Status
                SisPermohonanStatus::updateOrCreate(
                    [
                        "status_mohon_id" => $dataTahap1->mohon_id,
                        "status_tipe"     => "informasi",
                        "status_judul"    => "Temuan tahap 1",
                        "status_pesan"    => sprintf("%s mengajuakan revisi pada temuan tahap 1", $dataTahap1->mohon_cust_nama),
                        "created_at"      => $timeNow,
                    ],
                    [
                        "updated_at" => $timeNow,
                    ]
                );
            }

			// Send Notification to Auditor
			$groupAuditor = SisAuditTahap1Tim::join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
			$groupAuditor->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
			$groupAuditor->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id'])->select(DB::raw('sys_user.user_id AS user_id'), 'thp1_tim_posisi');
			foreach ($groupAuditor->get() as $auditor) {
				$notifStruct = new NotifStruct();
				if ($request['status'] == "setuju") {
					// Send Push
					$notifStruct->title   = sprintf("#%d Setuju temuan tahap 1", $dataTahap1->mohon_id);
					$notifStruct->message = sprintf("%s memberikan persetujuan pada temuan tahap 1", $dataTahap1->mohon_cust_nama);
					$notifStruct->click_url = url('/timaudit/auditor/tahap1-verif');
				} else {
					// Send Push
					$notifStruct->title   = sprintf("#%d Revisi temuan tahap 1", $dataTahap1->mohon_id);
					$notifStruct->message = sprintf("%s mengajuakan revisi pada temuan tahap 1", $dataTahap1->mohon_cust_nama);
					if($auditor?->thp1_tim_posisi == 'ketua'){
						$notifStruct->click_url = url('/timaudit/auditor/tahap1-rapat-akhir');
					}
					else{
						$notifStruct->click_url = url('/timaudit/auditor/tahap1-verif');
					}
				}
				$notifStruct->user_id   = $auditor->user_id;
				sendNotification($notifStruct);
			}

            DB::commit();
            return redirect()->back()->with('message', $responseMessage);
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($newUploadedPath as $path) {
                @unlink($path);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function detail(Request $request, $ID)
    {
        $data = SisAuditTahap1::with([
            'sis_permohonan_detail.master_sertifikasi',
            'sis_audit_tahap1_details.sis_audit_tahap1_revisis',
            'sis_audit_tahap1_tims.master_pegawai',
        ])
            ->findOrFail($ID);

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 1'),
            new BreadcrumbsStruct('Persetujuan Temuan', url($this->url)),
            new BreadcrumbsStruct('Detail'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.detail")->with($parser);
    }

    public function cetakTinjauan(Request $request, $ID)
    {
        $data   = SisAuditTahap1::with([
            'sis_permohonan',
            'sis_permohonan_detail.master_sertifikasi',
            'sis_permohonan_detail.sis_permohonan_komoditis.master_komoditi',
            'sis_audit_tahap1_details.sis_audit_tahap1_revisis',
            'sis_audit_tahap1_tims.master_pegawai',
        ])
            ->findOrFail($ID);
        $parser = ['data' => $data];

        $pdf = PDF::loadView("$this->view.print.tinjauan_sni", $parser)
            ->setPaper('a4', 'landscape');
        return $pdf->stream();
    }

    public function cetakLaporan(Request $request, $ID)
    {
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $ID);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as mohon_det_no_referensi'),
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT(mohon_kmditi_kapasitas_produksi_tahunan, ' ', mohon_kmditi_kapasitas_produksi_tahunan_satuan)) as produksi"),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $ID);

        $jmlTemuan = 0;
        foreach ($dataAuditKlausul->get() as $kla) {
            if ($kla->aud_thp1_det_is_tinjauan == 'ya') {
                if ($kla->aud_thp1_det_hasil_tinjauan == 'no') {
                    $jmlTemuan++;
                }
            }
        }

        $dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
        $dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        $dataTim->select('*');
        $dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $ID);

        $parser = [
            'restAudit'        => $restAudit,
            'dataAuditKlausul' => $dataAuditKlausul->get(),
            'dataTim'          => $dataTim->get(),
            'jmlTemuan'        => $jmlTemuan,
        ];

        $pdf = PDF::loadView("$this->view.print.laporan", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    public function cetakNotulen($tahap1Id)
    {
        $data = SisAuditTahap1::with([
            'sis_permohonan',
            'sis_permohonan_detail.master_sertifikasi',
            'sis_permohonan_detail.sis_permohonan_komoditis.master_komoditi',
            'sis_audit_tahap1_details.sis_audit_tahap1_revisis',
            'sis_audit_tahap1_tims.master_pegawai',
        ])
            ->findOrFail($tahap1Id);

        $ketua  = $data->sis_audit_tahap1_tims()->where('thp1_tim_posisi', 'ketua')->first();
        $parser = ['data' => $data, 'ketua' => $ketua];

        $pdf = PDF::loadView("$this->view.print.notulen_rapat", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    public function uploadScan($tahap1Id)
    {
        try {
            $data = SisAuditTahap1::join('sis_permohonan', 'sis_permohonan.mohon_id', '=', 'sis_audit_tahap1.mohon_id')
                ->where('sis_permohonan.user_id', '=', auth()->id())
                ->where('sis_audit_tahap1.aud_thp1_id', '=', $tahap1Id)
                ->first();
            if (empty($data)) throw new Exception("Jadwal tidak ditemukan");
            if ($data->aud_thp1_status_temuan != "setuju") throw new Exception("Anda belum diperbolehkan mengakses halaman upload scan");

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 1', url($this->url)),
                new BreadcrumbsStruct('Upload Scan'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];

            return view("$this->view.upload_scan")->with($parser);
        } catch (Exception $e) {
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function processUploadScan(Request $request, $tahap1Id)
    {
        $newUploadedPath = [];
        try {
            $data = SisAuditTahap1::join('sis_permohonan', 'sis_permohonan.mohon_id', '=', 'sis_audit_tahap1.mohon_id')
                ->where('sis_permohonan.user_id', '=', auth()->id())
                ->where('sis_audit_tahap1.aud_thp1_id', '=', $tahap1Id)
                ->first();
            if (empty($data)) throw new Exception("Jadwal tidak ditemukan");
            if ($data->aud_thp1_status_temuan != "setuju") throw new Exception("Anda belum diperbolehkan mengakses halaman upload scan");

            DB::beginTransaction();
            $baseFileUpload = sprintf(config("app.path_file_tahap1"), $data->aud_thp1_id);

            if ($request->hasFile('file_surat_tugas')) {
                // ======= SuratTugas ======= //
                $fileSuratTugas     = $request->file('file_surat_tugas');
                $fileSuratTugasName = Str::slug('file-scan-surattugas-' . $fileSuratTugas->getClientOriginalName()) . '-' . time() . '.' . $fileSuratTugas->getClientOriginalExtension();
                $fileSuratTugasPath = sprintf("%s/%s", $baseFileUpload, $fileSuratTugasName);
                $fileSuratTugas->move($baseFileUpload, $fileSuratTugasName);
                $newUploadedPath[]               = public_path($fileSuratTugasPath);
                $data->aud_thp1_file_surat_tugas = $fileSuratTugasPath;
            }

            if ($request->hasFile('file_notulen')) {
                // ======= Notulen ======= //
                $fileNotulen     = $request->file('file_notulen');
                $fileNotulenName = Str::slug('file-scan-notulen-' . $fileNotulen->getClientOriginalName()) . '-' . time() . '.' . $fileNotulen->getClientOriginalExtension();
                $fileNotulenPath = sprintf("%s/%s", $baseFileUpload, $fileNotulenName);
                $fileNotulen->move($baseFileUpload, $fileNotulenName);
                $newUploadedPath[]           = public_path($fileNotulenPath);
                $data->aud_thp1_file_notulen = $fileNotulenPath;
            }

            // ======= Notulen ======= //
            if ($request->hasFile('file_subkontrak')) {
                $fileSubkon     = $request->file('file_subkontrak');
                $fileSubkonName = Str::slug('file-scan-subkon-' . $fileSubkon->getClientOriginalName()) . '-' . time() . '.' . $fileSubkon->getClientOriginalExtension();
                $fileSubkonPath = sprintf("%s/%s", $baseFileUpload, $fileSubkonName);
                $fileSubkon->move($baseFileUpload, $fileSubkonName);
                $newUploadedPath[]          = public_path($fileSubkonPath);
                $data->aud_thp1_file_subkon = $fileSubkonPath;
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
        $data = SisAuditTahap1::with(['sis_permohonan_detail', 'sis_audit_tahap1_tims.master_pegawai', 'sis_permohonan'])
            ->with([
                'sis_audit_tahap1_details.sis_audit_tahap1_revisis' => function ($query) {
                    $query->orderBy('created_at');
                }
            ])
            ->join('sis_permohonan', 'sis_permohonan.mohon_id', '=', 'sis_audit_tahap1.mohon_id')
            ->leftJoin('sis_jadwal_audit', 'sis_audit_tahap1.mohon_id', '=', 'sis_jadwal_audit.mohon_id')
            ->leftJoin('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
            ->whereIn('aud_thp1_status_temuan', ['diajukan', 'setuju'])
            ->where('sis_permohonan.user_id', '=', auth()->id());
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
            'aud_thp1_id',
            'sert_tahap1_jenis',
            'aud_thp1_status_temuan',
            'jadw_file_jadwal',
            'aud_thp1_tanggal_mulai',
            'aud_thp1_file_surat_tugas',
            'aud_thp1_file_notulen',
            'aud_thp1_file_subkon',
            'aud_thp1_file_daftar_hadir',
            'aud_thp1_tanggal_selesai')
            ->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status = 'submited', 1, 0)) as total_submit");

        $data->groupBy('aud_thp1_id');
        $data->havingRaw('total_submit = ?', [0]);
        // Total
        $total = $data->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);


        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $team = [];
            foreach ($d->sis_audit_tahap1_tims as $tim) {
                $team[] = [
                    'kode'   => $tim->thp1_tim_kode,
                    'nama'   => $tim->master_pegawai->peg_nama,
                    'posisi' => $tim->thp1_tim_posisi,
                ];
            }

            if ($d->aud_thp1_status_temuan == "setuju") {
                /* Reminder peserta harus mengunggah
               1. Scan Surat Tugas
               2. Scan Notulen
               3. Scan Subkontrak
               */
                $dataFileUpload = [
                    [
                        'status' => !empty($d->aud_thp1_file_surat_tugas),
                        'name'   => 'Scan Surat Tugas',
                        'url'    => !empty($d->aud_thp1_file_surat_tugas) ? asset($d->aud_thp1_file_surat_tugas) : 'javascript:void(0)',
                    ],
                    [
                        'status' => !empty($d->aud_thp1_file_notulen),
                        'name'   => 'Scan Notulen',
                        'url'    => !empty($d->aud_thp1_file_notulen) ? asset($d->aud_thp1_file_notulen) : 'javascript:void(0)',
                    ],
                    [
                        'status' => !empty($d->aud_thp1_file_subkon),
                        'name'   => 'Scan Subkontrak',
                        'url'    => !empty($d->aud_thp1_file_subkon) ? asset($d->aud_thp1_file_subkon) : 'javascript:void(0)',
                    ],
                ];

                $x['file_upload'] = $dataFileUpload;
            }

            $x['aud_thp1_id']                = $d->aud_thp1_id;
            $x['enc_aud_thp1_id']            = encrypt($d->aud_thp1_id);
            $x['sert_tahap1_jenis']          = strtolower($d->sert_tahap1_jenis);
            $x['aud_thp1_status_temuan']     = strtolower($d->aud_thp1_status_temuan);
            $x['aud_thp1_file_notulen']      = $d->aud_thp1_file_notulen;
            $x['aud_thp1_file_daftar_hadir'] = $d->aud_thp1_file_daftar_hadir;
            $x['jadw_file_jadwal']           = $d->jadw_file_jadwal;
            $x['tanggal']                    = $d->aud_thp1_tanggal_mulai?->isoFormat("LL") . ' s/d ' . $d->aud_thp1_tanggal_selesai?->isoFormat("LL");
            $x['tims']                       = $team;
            $result[]                        = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
