<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Revisi;
use App\Models\BbkkpSis\SisAuditTahap1RevisiFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tahap1PerbaikanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/tahap1/perbaikan-temuan';
    private $view = "pelanggan::tahap1_perbaikan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahap 1'),
            new BreadcrumbsStruct('Perbaikan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'url2' => 'pelanggan/tahap1/persetujuan-temuan', 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function revisi($encTahap1Id)
    {
        try {
            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 1', url($this->url)),
                new BreadcrumbsStruct('Perbaikan', url($this->url)),
                new BreadcrumbsStruct('Revisi'),
            ];

            $tahap1Id   = decrypt($encTahap1Id);
            $dataTahap1 = SisAuditTahap1::with('sis_audit_tahap1_details')->findOrFail($tahap1Id);

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataTahap1];
            return view("$this->view.revisi")->with($parser);
        } catch (Exception $e) {
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function processRevisi(Request $request, $encTahap1Id)
    {
        $uploadedPath = [];
        try {
            $request->validate([
                'revisi_perbaikan' => 'required',
            ]);

            $tahap1Id   = decrypt($encTahap1Id);
            $dataTahap1 = SisAuditTahap1::with('sis_audit_tahap1_details')->findOrFail($tahap1Id);


            $baseFileUpload = sprintf(config("app.path_file_tahap1"), $dataTahap1->aud_thp1_id);

            DB::beginTransaction();
            // Updating Revisi Text
            foreach ($request['revisi_perbaikan'] as $detId => $revText) {
                $detailRev                        = SisAuditTahap1Revisi::find($detId);
                $detailRev->thp1_revisi_perbaikan = $revText;
                $detailRev->thp1_revisi_status    = 'fixed';
                $detailRev->save();
            }
            // Updating Revisi Files
            if ($request->has('revisi_files')) {
                foreach ($request['revisi_files'] as $detId => $revFiles) {
                    SisAuditTahap1RevisiFile::where('thp1_revisi_id', '=', $detId)->delete();

                    foreach ($revFiles as $file) {
                        $filePerbaikan     = $file;
                        $filePerbaikanName = Str::slug('perbaikan-tahap1-' . time() . '-' . $filePerbaikan->getClientOriginalName());
                        $filePerbaikanPath = sprintf("%s/%s", $baseFileUpload, $filePerbaikanName);
                        $filePerbaikan->move($baseFileUpload, $filePerbaikanName);
                        $uploadedPath[] = public_path($filePerbaikanPath);

                        SisAuditTahap1RevisiFile::create([
                            'thp1_revisi_id'        => $detId,
                            'thp1_revisi_file_path' => $filePerbaikanPath,
                        ]);
                    }
                }
            }


            // Kirim Notifikasi ke TIM Audit Tahap 1
            foreach ($dataTahap1->sis_audit_tahap1_tims as $tim) {
                $notifStruct            = new NotifStruct();
                $notifStruct->title     = sprintf("#%d Revisi tahap 1 dikirim", $dataTahap1->aud_thp1_id);
                $notifStruct->message   = sprintf("%s telah melakukan perbaikan revisi tahap 1, harap segera lakukan verifiaksi", $dataTahap1->sis_permohonan->mohon_cust_nama);
                $notifStruct->user_id   = $tim->master_pegawai->user_id;
                $notifStruct->click_url = url('/timaudit/tahap1');
                sendNotification($notifStruct);
            }

            DB::commit();
            return redirect()->back()->with('message', "Revisi berhasil dikirim ke auditor");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedPath as $path) {
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
            ->where('aud_thp1_status_temuan', '=', 'setuju')
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
            'aud_thp1_file_notulen',
            'aud_thp1_file_daftar_hadir',
            'jadw_file_jadwal',
            'aud_thp1_tanggal_mulai',
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

            $x['aud_thp1_id']                = $d->aud_thp1_id;
            $x['enc_aud_thp1_id']            = encrypt($d->aud_thp1_id);
            $x['sert_tahap1_jenis']          = strtolower($d->sert_tahap1_jenis);
            $x['aud_thp1_status_temuan']     = strtolower($d->aud_thp1_status_temuan);
            $x['aud_thp1_file_notulen']      = $d->aud_thp1_file_notulen;
            $x['aud_thp1_file_daftar_hadir'] = $d->aud_thp1_file_daftar_hadir;
            $x['jadw_file_jadwal']           = $d->jadw_file_jadwal;
            $x['tanggal']                    = $d->aud_thp1_tanggal_mulai?->isoFormat("LL") . ' s/d ' . $d->aud_thp1_tanggal_selesai?->isoFormat("LL");
            $x['tims']                       = $team;
            $x['total_temuan']               = $d->sis_audit_tahap1_details->where('aud_thp1_det_hasil_tinjauan', '=', 'no')->count();
            $result[]                        = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
