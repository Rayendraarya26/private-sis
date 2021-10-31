<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/jadwal';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::jadwal.index')->with($parser);
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
        $data = SisJadwal::with(['sis_audit_tim_komites']);
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
            $x['jadw_tanggal_status']  = $d->jadw_tanggal_status;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai;
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai;
            $x['jadw_team_status']     = $d->jadw_team_status;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_file_jadwal']     = $d->jadw_file_jadwal;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
