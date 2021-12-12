<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditTahap1;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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
            new BreadcrumbsStruct('Perbaikan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
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
