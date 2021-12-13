<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SysUserGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Tahap2PersetujuanController extends Controller
{
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
            $dataJadwal = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims'])->findOrFail($jadwID);
            $dataLKS    = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
                ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_jadwal.jadw_id', $jadwID)
                ->get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 2'),
                new BreadcrumbsStruct('Persetujuan Temuan', url($this->url)),
                new BreadcrumbsStruct('Detail'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS];
            return view("$this->view.detail")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage() . ' | ' . $e->getLine()]);
        }

    }

    public function approveTemuan(Request $request)
    {
        $request->validate([
            'jadw_id'             => 'required',
            'jadw_setujui_temuan' => Rule::in(['setuju', 'revisi'])
        ]);

        try {
            DB::beginTransaction();
            $data                      = SisJadwal::with('sis_pelanggan')->findOrFail($request['jadw_id']);
            $data->jadw_setujui_temuan = $request['jadw_setujui_temuan'];
            $data->save();

            $message = "";

            // Send Notification to Operator LS
            $groupUsers = SysUserGroup::with('user')->where('ug_group_id', 6)->get();
            if ($groupUsers) {
                foreach ($groupUsers as $user) {
                    $notifStruct = new NotifStruct();
                    if ($request['jadw_setujui_temuan'] == "setuju") {
                        $notifStruct->title   = sprintf("#%d temuan LKS disetujui", $data->jadw_id);
                        $notifStruct->message = sprintf("%s memberikan persetujuan pada temuan LKS", $data->sis_pelanggan->cust_nama);
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

            if ($request['jadw_setujui_temuan'] == "revisi") {
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-temuan',
                    'jlog_judul' => 'Revis Temuan LKS',
                    'jlog_pesan' => $request['message'],
                ]);
            } else {
                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'informasi',
                    'jlog_judul' => 'Approve Temuan LKS',
                    'jlog_pesan' => sprintf("%s menyetujui temuan LKS", $data->sis_pelanggan->cust_nama),
                ]);
            }

            DB::commit();
            return responseJSON(200, [], $message);
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
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

            $x['tims']                = $timAudit;
            $x['audits']              = $jadwalAudit;
            $x['revisi']              = $dataRevisi;
            $x['jadw_id']             = $d->jadw_id;
            $x['jadw_jenis']          = $d->jadw_jenis;
            $x['jadw_setujui_temuan'] = $d->jadw_setujui_temuan;
            $x['jadw_file_jadwal']    = asset($d->jadw_file_jadwal);

            if ($d->jadw_tanggal_mulai == $d->jadw_tanggal_selesai) {
                $x['tanggal'] = sprintf("%s", $d->jadw_tanggal_mulai->isoFormat("LL"));
            } else {
                $x['tanggal'] = sprintf("%s s/d %s", $d->jadw_tanggal_mulai->isoFormat("LL"), $d->jadw_tanggal_selesai->isoFormat("LL"));
            }
            $result[] = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
