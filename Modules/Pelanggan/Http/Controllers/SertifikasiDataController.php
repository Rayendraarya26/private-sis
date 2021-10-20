<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SertifikasiDataController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/sertifikasi/data';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Data Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::sertifikasi_data.index')->with($parser);
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
        $data = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi']);
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
            $x['cust_sert_nomor_sertifikat']    = $d->cust_sert_nomor_sertifikat;
            $x['cust_sert_nomor_referensi']     = $d->cust_sert_nomor_referensi;
            $x['cust_sert_nomor_sni']           = $d->cust_sert_nomor_sni;
            $x['cust_sert_status']              = $d->cust_sert_status;
            $x['cust_sert_tgl_sertifikat_awal'] = $d->cust_sert_tgl_sertifikat_awal;
            $x['cust_sert_expired_date']        = $d->cust_sert_expired_date->format("Y-m-d H:i:s");
            $x['cust_sert_status_survailen']    = $d->cust_sert_status_survailen;
            $x['cust_sert_filepath']            = !empty($d->cust_sert_filepath) ? asset($d->cust_sert_filepath) : null;
            $x['komodt_nama']                   = $d->master_komoditi->komodt_nama;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
