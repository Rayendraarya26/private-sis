<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUserGroup;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function approveTemuan(Request $request)
    {
        $request->validate(['aud_thp1_id' => 'required|integer', 'status' => ['required', Rule::in(['setuju', 'revisi'])]]);

        try {
            DB::beginTransaction();
            $dataAudit1 = SisAuditTahap1::join("sis_permohonan", "sis_permohonan.mohon_id", "=", "sis_audit_tahap1.mohon_id")
                ->where("user_id", auth()->id())
                ->findOrFail($request['aud_thp1_id']);

            $dataAudit1->aud_thp1_status_temuan = $request['status'];
            $dataAudit1->save();

            // Send Notification to Operator LS
            $groupMarketing = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
            if ($groupMarketing) {
                foreach ($groupMarketing as $marketing) {
                    $notifStruct = new NotifStruct();
                    if ($request['status'] == "setuju") {
                        // Send Push
                        $notifStruct->title     = sprintf("#%d Setuju temuan tahap 1", $dataAudit1->mohon_id);
                        $notifStruct->message   = sprintf("%s memberikan persetujuan pada temuan tahap 1", $dataAudit1->mohon_cust_nama);
                        $notifStruct->user_id   = $marketing?->ug_user_id;
                        $notifStruct->click_url = url('/operatorls/penjadwalan-tahap1');
                        sendNotification($notifStruct);

                        // Add Pengajuan Status
                        SisPermohonanStatus::create([
                            "status_mohon_id" => $dataAudit1->mohon_id,
                            "status_tipe"     => "informasi",
                            "status_judul"    => "Temuan tahap 1",
                            "status_pesan"    => sprintf("%s menyetujui temuan tahap 1", $dataAudit1->mohon_cust_nama),
                            "created_at"      => Carbon::now(),
                            "updated_at"      => Carbon::now(),
                        ]);
                    } else {
                        // Send Push
                        $notifStruct->title     = sprintf("#%d Revisi temuan tahap 1", $dataAudit1->mohon_id);
                        $notifStruct->message   = sprintf("%s mengajuakan revisi pada temuan tahap 1", $dataAudit1->mohon_cust_nama);
                        $notifStruct->user_id   = $marketing?->ug_user_id;
                        $notifStruct->click_url = url('/operatorls/penjadwalan-tahap1');
                        sendNotification($notifStruct);

                        // Add Pengajuan Status
                        SisPermohonanStatus::create([
                            "status_mohon_id" => $dataAudit1->mohon_id,
                            "status_tipe"     => "informasi",
                            "status_judul"    => "Temuan tahap 1",
                            "status_pesan"    => sprintf("%s mengajuakan revisi pada temuan tahap 1", $dataAudit1->mohon_cust_nama),
                            "created_at"      => Carbon::now(),
                            "updated_at"      => Carbon::now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return responseJSON(200, null, "Approval berhasil " . ucwords($request['status']));
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function detail(Request $request, $ID)
    {
        $data = SisAuditTahap1::with([
            'sis_permohonan_detail.master_sertifikasi',
            'sis_audit_tahap1_details',
            'sis_audit_tahap1_revisis',
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
            'sis_audit_tahap1_details',
            'sis_audit_tahap1_revisis',
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
                'sis_audit_tahap1_revisis' => function ($query) {
                    $query->orderBy('created_at');
                }
            ]);
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
                $team = [];
                foreach ($d->sis_audit_tahap1_tims as $tim) {
                    $team[] = [
                        'kode'   => $tim->thp1_tim_kode,
                        'nama'   => $tim->master_pegawai->peg_nama,
                        'posisi' => $tim->thp1_tim_posisi,
                    ];
                }

                $x['aud_thp1_id']                = $d->aud_thp1_id;
                $x['sert_tahap1_jenis']          = strtolower($d->sert_tahap1_jenis);
                $x['aud_thp1_status_temuan']     = strtolower($d->aud_thp1_status_temuan);
                $x['aud_thp1_file_notulen']      = $d->aud_thp1_file_notulen;
                $x['aud_thp1_file_daftar_hadir'] = $d->aud_thp1_file_daftar_hadir;
                $x['jadw_file_jadwal']           = $d->jadw_file_jadwal;
                $x['tanggal']                    = $d->aud_thp1_tanggal_mulai?->isoFormat("LL") . ' s/d ' . $d->aud_thp1_tanggal_selesai?->isoFormat("LL");
                $x['tims']                       = $team;
                $result[]                        = $x;
            // }
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
