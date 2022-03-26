<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisAuditLksHistory;
use App\Models\BbkkpSis\SisAuditLksRevisi;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SysUserGroup;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\TimAudit\Http\Traits\AuditorTraits;
use Modules\TimAudit\Http\Traits\LksTrait;

class AuLksController extends Controller
{
    use AuditorTraits, LksTrait;

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
            $dataJadwal = $this->involvedAuditorWithFilter($jadwalID, $request['auditor'] ?? "all");

            if (empty($request['auditor']) && !empty(auth()->user()->master_pegawai->peg_kode)) {
                return redirect("$this->url/temuan/$jadwalID?auditor=" . auth()->user()->master_pegawai->peg_kode);
            }

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Temuan'),
            ];

            try {
                $this->isKepalaAudit($dataJadwal->jadw_id);
                $isKepala = true;
            } catch (Exception) {
                $isKepala = false;
            }

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'is_kepala' => $isKepala];
            return view("$this->view.temuan")->with($parser);
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function generate(Request $request, $jadwalID)
    {
        $request->validate(['total' => 'required']);
        try {
            if ($request['total'] <= 0) throw new Exception("Total harus lebih dari 0");

            $pegawaiID  = auth()->user()->master_pegawai->peg_id;
            $dataJadwal = $this->involvedAuditor($jadwalID);
            $dataTim    = $dataJadwal->sis_jadwal_tims()->where("peg_id", $pegawaiID)->firstOrFail();

            DB::beginTransaction();
            for ($i = 0; $i < $request['total']; $i++) {
                SisAuditLks::updateOrCreate(
                    ['lks_id' => $request['lks_id'], 'jadw_id' => $dataJadwal->jadw_id],
                    [
                        'jadw_tim_id'                  => $dataTim->jadw_tim_id,
                        'lks_status'                   => 'proses',
                        'lks_uraian_ketidaksesuaian'   => null,
                        'lks_kategori_ketidaksesuaian' => "minor",
                        'lks_klausul_ketidaksesuaian'  => null,
                        'lks_expired_date_perbaikan'   => $dataJadwal->jadw_tanggal_selesai->addMonths(2),
                    ]
                );
            }
            $this->syncNomorLKS($jadwalID);
            DB::commit();

            return responseJSON(200, [], "Generate berhasil");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function saveDraft(Request $request, $jadwalID)
    {
        $request->validate([
            'lks_id' => 'required',
            'key'    => ['required', Rule::in(['lks_uraian_ketidaksesuaian', 'lks_kategori_ketidaksesuaian', 'lks_klausul_ketidaksesuaian', 'lks_expired_date_perbaikan', 'lks_nomor', 'lks_bagian_pendamping'])],
            'value'  => 'required'
        ]);

        $data = SisAuditLks::find($request['lks_id']);
        try {
            DB::beginTransaction();

            $key        = $request['key'];
            $value      = $request['value'];
            $data->$key = $value;
            $data->save();

            if ($key == "lks_kategori_ketidaksesuaian" && $value == "kritis") {
                SisJadwalAudit::where('jadw_id', $data->jadw_id)->update(['jadw_audit_status_komite' => 'submited']);
            }

            DB::commit();
            return responseJSON(200, [], "LKS berhasil disimpan");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage() . '| err:' . $e->getLine());
        }
    }

    public function processRekomendasi(Request $request)
    {
        try {
            $request->validate(['jadw_id' => 'required', 'rekomendasi' => 'required']);
            if (!$request->ajax()) throw new Exception("Endopoint ini utuk ajax");

            DB::beginTransaction();
            $dataJadwal                       = $this->isKepalaAuditDetail($request['jadw_id']);
            $dataJadwal->jadw_lks_rekomendasi = $request['rekomendasi'];
            $dataJadwal->save();

            foreach ($dataJadwal->sis_jadwal_audits as $jadwal_audit) {
                $jadwal_audit->jadw_audit_status_komite = "submited";
                $jadwal_audit->save();
            }

            // Send Notification to Opeartos LS
            $groupUsers = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
            if ($groupUsers) {
                foreach ($groupUsers as $user) {
                    // Send Push
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = sprintf("Proses Audit #%d telah selesai", $dataJadwal->jadw_id);
                    $notifStruct->message   = sprintf("Proses audit pada %s tanggal %s telah selesai, segera inputkan rekomendasi persetujuan. ", $dataJadwal->sis_pelanggan->cust_nama, $dataJadwal->jadw_tanggal_mulai->isoFormat("LL"));
                    $notifStruct->user_id   = $user?->ug_user_id;
                    $notifStruct->click_url = url('/operatorls/rekomendasi-persetujuan/edit?tipe=rekomendasi&jadw_id='.$request['jadw_id']);
                    sendNotification($notifStruct);
                }
            }

            // insert to log jadwal
            SisJadwalLog::create([
                'jadw_id'    => $dataJadwal->jadw_id,
                'jlog_tipe'  => "informasi",
                'jlog_judul' => "Proses Audit Telah Selesai",
                'jlog_pesan' => sprintf("%s telah memberikan rekomendasi LS. Sekarang dalam penyusunan tim komite pihak BBKKP", auth()->user()->user_fullname),
            ]);

            DB::commit();
            return responseJSON(200, [], "Rekomendasi berhasil ditambahkan");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function verifikasiTemuan(Request $request, $jadwalID)
    {
        try {
            // ============================== validation to access this page ==============================
            $dataJadwal = $this->involvedAuditorWithFilter($jadwalID, $request['auditor'] ?? "all");

            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Auditor', url($this->url)),
                new BreadcrumbsStruct('LKS', url($this->url)),
                new BreadcrumbsStruct('Verifikasi'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal];
            return view("$this->view.verifikasi")->with($parser);
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function processVerifikasiTemuan(Request $request, $jadwalID)
    {
        try {
            $request->validate([
                'lks_id'              => 'required',
                'lks_catatan_ditutup' => "required",
                // 'lks_status' => ['required', Rule::in(['memadai', 'tidak-memadai'])],
            ]);

            // 1.
            $lksID     = $request['lks_id'];
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $jadwal = $this->involvedAuditor($jadwalID);

            // check apakah saya kepala auditor
            $dataTim = $jadwal->sis_jadwal_tims()->where('peg_id', '=', $pegawaiID)->first();

            $dataLKS = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->when($dataTim->jadw_tim_posisi != "ketua", function ($query, $pegawaiID) {
                    $query->where("sis_jadwal_tim.peg_id", $pegawaiID);
                })->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");


            $dataLKS->lks_status          = 'memadai';
            $dataLKS->lks_sudah_ditutup   = 'ya';
            $dataLKS->lks_catatan_ditutup = $request->get('lks_catatan_ditutup');
            $dataLKS->lks_tanggal_ditutup = Carbon::now();
            $dataLKS->save();

            $this->sendNotifToLeadAuditorIfAllClose($jadwalID);
            return responseJSON(200, [], "Verifikasi berhasil");
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function processRevisiTemuan(Request $request, $jadwalID)
    {
        try {
            $request->validate([
                'catatan' => 'required',
                'lks_id'  => 'required'
            ]);

            DB::beginTransaction();
            // 1.
            $lksID     = $request['lks_id'];
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $dataJadwal = $this->involvedAuditor($jadwalID);
            $dataLKS    = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->where("sis_jadwal_tim.peg_id", $pegawaiID)->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");

            $dataLKS->lks_status        = 'revisi';
            $dataLKS->lks_sudah_ditutup = 'tidak';
            $dataLKS->save();

            $dataRevisi = SisAuditLksRevisi::create([
                'lks_id'             => $lksID,
                'lks_revisi_catatan' => $request['catatan'],
                'lks_revisi_oleh'    => 'auditor',
            ]);

            // save to log
            SisAuditLksHistory::create([
                'lks_revisi_id'         => $dataRevisi->lks_revisi_id,
                'lkshistory_data'       => json_encode($dataLKS),
                'lkshistory_file'       => json_encode($dataLKS->sis_audit_lks_files),
                'lkshistory_created_at' => Carbon::now(),
                'lkshistory_created_id' => auth()->id(),
            ]);

            $data_pelanggan = $dataJadwal->sis_pelanggan;
            // Send Push
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Revisi LKS';
            $notifStruct->message   = sprintf("Auditor melakukan revisi LKS pada nomor %s. Segera perbaiki dan kirim ulang ke auditor untuk di verifikasi ulang", $dataLKS->lks_nomor);
            $notifStruct->user_id   = $data_pelanggan?->user_id;
            $notifStruct->click_url = url('/pelanggan/tahap2/perbaikan-temuan/temuan-lks/' . $dataJadwal->jadw_id);
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Revisi LKS";
            $structEmail->body    = view("$this->view.mails.revisi_lks")
                ->with([
                    'name'    => $data_pelanggan?->cust_nama,
                    'message' => sprintf("%s (Auditor) melakukan revisi LKS pada nomor. Segera perbaiki dan kirim ulang ke auditor untuk di verifikasi ulang", auth()->user()->user_fullname, $dataLKS->lks_nomor),
                    'note'    => $request['catatan'],
                    'link'    => url('/pelanggan/tahap2/perbaikan-temuan/temuan-lks/' . $dataJadwal->jadw_id),
                ])->render();
            $structEmail->to      = $data_pelanggan?->cust_email;
            sendEmail($structEmail);

            DB::commit();
            return responseJSON(200, [], "Revisi berhasil");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function deleteTemuan(Request $request, $jadwalID, $lksID)
    {
        try {
            if (!$request->ajax()) throw new Exception("Endopoint ini utuk ajax");

            DB::beginTransaction();
            // 1.
            $pegawaiID = auth()->user()->master_pegawai->peg_id;

            $this->involvedAuditor($jadwalID);
            $dataLKS = SisAuditLks::join("sis_jadwal_tim", "sis_jadwal_tim.jadw_tim_id", '=', 'sis_audit_lks.jadw_tim_id')
                ->where("sis_jadwal_tim.peg_id", $pegawaiID)->find($lksID);
            if (empty($dataLKS)) throw new Exception("Anda tidak dapat mengubah LKS auditor lain");
            $dataLKS->delete();

            $this->syncNomorLKS($jadwalID);
            DB::commit();
            return responseJSON(200, [], "Delete berhasil");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function cetak(Request $request, $jadwalID, $type)
    {
        try {
            $data = $this->involvedAuditor($jadwalID);
            if (empty($data)) throw new Exception('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'lks'   => $this->cetak_lks($request, $data),
                default => throw new Exception("Invalid URL"),
            };
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    private function cetak_lks(Request $request, SisJadwal $dataJadwal)
    {
        $dataLKS = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
            ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
            ->where('sis_jadwal.cust_id', $dataJadwal->cust_id)
            ->where('sis_jadwal.jadw_id', $dataJadwal->jadw_id)
            ->orderBy('lks_nomor')
            ->get();

        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $parser = ['dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS, 'dataKetua' => $dataKetua];

        $pdf = PDF::loadView("pelanggan::tahap2_persetujuan.print.lks", $parser)
            ->setPaper('a4', 'landscape');
        return $pdf->stream();
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit'    => $this->ajax_datagrid_jadwal_audit($request),
            'data-verif-lks'           => $this->ajax_verif_data_lks($request),
            'data-verif-revisi-by-lks' => $this->ajax_verif_revisi_by_lks($request),
            'tinymce-uploadimage'      => $this->ajax_tinymce_uploadimage($request),
            default                    => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::with('sis_jadwal_audits', 'sis_audit_lks');
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

        // Pagination
        $data->select(
            "cust_nama",
            "jadw_setujui_temuan",
            "jadw_tanggal_mulai",
            "jadw_tanggal_selesai",
            "jadw_jenis",
            "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_tim_kesanggupan) AS jadw_tim_kesanggupan");
        $data->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status_komite = 'on-going', 1, 0)) as total_submit_komite");
        $data->selectRaw("SUM(IF(sis_jadwal_audit.jadw_audit_status = 'on-going', 1, 0)) as total_proses");
        $data->havingRaw('total_submit_komite > ?', [0]);
        $data->havingRaw('total_proses > ?', [0]);
        $data->groupBy('sis_jadwal.jadw_id');

        // Total
        $total = $data->count();

        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);

        $result = [];
        foreach ($data->get() as $d) {
            $totalTemuanLKS = $d->sis_audit_lks->count();

            $isCloseLKS       = false;
            $allowRekomendasi = false;
            if ($d->sis_audit_lks->where('lks_sudah_ditutup', '=', 'tidak')->count() == 0) {
                $isCloseLKS = true;
            }

            if ($isCloseLKS && $d->jadw_lks_rekomendasi == null && $d->sis_audit_lap_lengkap?->lap_lengkp_verifikasi_status == "ya") {
                $allowRekomendasi = true;
            }

            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_setujui_temuan']  = $d->jadw_setujui_temuan;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = ucwords($d->jadw_jenis);
            $x['total_jadwal']         = $d->sis_jadwal_audits->count();
            $x['total_temuan']         = $totalTemuanLKS;
            $x['allow_rekomendasi']    = $allowRekomendasi;
            $x['is_close_lks']         = $isCloseLKS;
            $result[]                  = $x;
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_verif_data_lks(Request $request)
    {
        try {
            $dataJadwal = $this->involvedAuditorWithFilter(
                $request['jadwal_id'],
                $request['auditor'] ?? "all",
                $request['status'] ?? "all"
            );

            $result = [];
            foreach ($dataJadwal->sis_audit_lks as $lks) {
                $perbaikanFile = [];
                if (count($lks->sis_audit_lks_files)) {
                    foreach ($lks->sis_audit_lks_files as $file) {
                        $perbaikanFile[] = [
                            'url' => asset($file->lks_filepath),
                        ];
                    }
                }

                $allowEdit = false;
                if ($lks->lks_status == 'fixed') {
                    $pegawaiID = auth()->user()->master_pegawai->peg_id;
                    $dataTim   = $dataJadwal->sis_jadwal_tims()->where("peg_id", $pegawaiID)->firstOrFail();
                    if ($dataTim->jadw_tim_posisi == "ketua") { // Ketua bisa edit semua
                        $allowEdit = true;
                    } else if ($lks->jadw_tim_id == $dataTim->jadw_tim_id) {
                        $allowEdit = true;
                    }
                }

                $hasilVerif = "";
                $verifKe    = 1;
                foreach ($lks->sis_audit_lks_revisis as $revisi) {
                    if ($revisi->lks_revisi_oleh == "auditor") {
                        $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi Ke-%d <br> %s</div>", $verifKe, $revisi->created_at->isoFormat("LL"));
                        $hasilVerif .= sprintf("<br> %s <br><br>", $revisi->lks_revisi_catatan);
                        $verifKe++;
                    }
                }

                if ($lks->lks_sudah_ditutup == "ya") {
                    $hasilVerif .= sprintf("<div style='text-align: center'>Verifikasi Ke-%d <br> %s </div>", $verifKe, $lks->lks_tanggal_ditutup->isoFormat("LL"));
                    $hasilVerif .= sprintf("<br> %s <br><br> <b>LKS %d DITUTUP</b>", $lks->lks_catatan_ditutup, $lks->lks_nomor);
                }

                $result[] = [
                    'lks_id'                       => $lks->lks_id,
                    'lks_status'                   => $lks->lks_status,
                    'lks_sudah_ditutup'            => $lks->lks_sudah_ditutup,
                    'jadw_tim_kode'                => $lks->sis_jadwal_tim->jadw_tim_kode,
                    'lks_uraian_ketidaksesuaian'   => $lks->lks_uraian_ketidaksesuaian,
                    'lks_kategori_ketidaksesuaian' => $lks->lks_kategori_ketidaksesuaian,
                    'lks_klausul_ketidaksesuaian'  => $lks->lks_klausul_ketidaksesuaian,
                    'lks_expired_date_perbaikan'   => $lks->lks_expired_date_perbaikan->isoFormat("LL"),
                    'lks_perbaikan_analisa'        => $lks->lks_perbaikan_analisa,
                    'lks_perbaikan_koreksi'        => $lks->lks_perbaikan_koreksi,
                    'lks_perbaikan_tindakan'       => $lks->lks_perbaikan_tindakan,
                    'lks_bagian_pendamping'        => $lks->lks_bagian_pendamping,
                    'lks_bukti_tindakan_perbaikan' => $lks->lks_bukti_tindakan_perbaikan,
                    'hasil_verif'                  => $hasilVerif,
                    'perbaikan_files'              => $perbaikanFile,
                    'allow_edit'                   => $allowEdit,
                ];
            }


            return responseJSON(200, $result, "data ditemukan");

        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage() . ' | ' . $e->getLine());
        }
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
