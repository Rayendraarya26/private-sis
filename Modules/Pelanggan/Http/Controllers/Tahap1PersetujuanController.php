<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUserGroup;
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

    public function detail(Request $request)
    {

    }

    public function cetak(Request $request)
    {

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

        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
