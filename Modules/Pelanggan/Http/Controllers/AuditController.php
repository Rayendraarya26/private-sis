<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwalAudit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/audit';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Audit'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::audit.index')->with($parser);
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
        $data = SisJadwalAudit::with(['sis_jadwal', 'sis_permohonan', 'sis_audit_lks.sis_audit_lks_files', 'sis_audit_lks.sis_audit_lks_revisis']);
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
            $x['jadw_audit_id']                = $d->jadw_audit_id;
            $x['jadw_audit_status']            = $d->jadw_audit_status;
            $x['jadw_audit_status_komite']     = $d->jadw_audit_status_komite;
            $x['jadw_audit_jenis']             = $d->jadw_audit_jenis;
            $x['lks_uraian_ketidaksesuaian']   = $d->lks_uraian_ketidaksesuaian;
            $x['lks_kategori_ketidaksesuaian'] = $d->lks_kategori_ketidaksesuaian;
            $x['lks_klausul_ketidaksesuaian']  = $d->lks_klausul_ketidaksesuaian;
            $x['lks_perbaikan_analisa']        = $d->lks_perbaikan_analisa;
            $x['lks_perbaikan_koreksi']        = $d->lks_perbaikan_koreksi;
            $x['lks_perbaikan_tindakan']       = $d->lks_perbaikan_tindakan;
            $x['lks_bagian_pendamping']        = $d->lks_bagian_pendamping;
            $x['lks_bukti_tindakan_perbaikan'] = $d->lks_bukti_tindakan_perbaikan;
            $x['lks_expired_date_perbaikan']   = $d->lks_expired_date_perbaikan?->format("Y-m-d H:i:s");
            $x['lks_input_date_perbaikan']     = $d->lks_input_date_perbaikan;
            $x['lks_status']                   = $d->lks_status;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
