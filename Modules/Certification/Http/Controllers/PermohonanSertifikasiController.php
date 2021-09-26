<?php

namespace Modules\Certification\Http\Controllers;

use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokuman;
use App\Models\BbkkpSis\SisPelanggan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PermohonanSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'sertifikasi/permohonan-sertifikasi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view('certification::permohonan_sertifikasi.index')->with($parser);
    }

    public function create()
    {
        $dataPelanggan = SisPelanggan::where("user_id", auth()->id())->first();
        $parser        = [
            'module'        => $this->module,
            'url'           => $this->url,
            'dataPelanggan' => $dataPelanggan,
        ];
        return view('certification::permohonan_sertifikasi.create')->with($parser);
    }

    public function store()
    {

    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            'combogrid_sertifikasi' => $this->ajax_combogrid_sertifikasi($request),
            'dokumen_sertifikat' => $this->ajax_dokumen_sertifikat($request),
            default => null,
        };
    }

    private function ajax_datagrid(Request $request)
    {
        return response()->json([]);
    }

    private function ajax_combogrid_sertifikasi(Request $request)
    {
        $data = MasterSertifikasi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%');
        }

        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        } else {
            $data->orderBy("sert_is_product", "asc");
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['sert_id']               = $d->sert_id;
            $x['sert_nama']             = $d->sert_nama;
            $x['sert_deskripsi']        = $d->sert_deskripsi;
            $x['sert_expired']          = $d->sert_expired;
            $x['sert_format_referensi'] = $d->sert_format_referensi;
            $x['sert_is_product']       = $d->sert_is_product;
            $x['created_at']            = $d->created_at?->format("Y-m-d H:i:s");
            $x['updated_at']            = $d->updated_at?->format("Y-m-d H:i:s");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_dokumen_sertifikat(Request $request)
    {
        try {
            $request->validate(['sert_id' => 'required|integer']);
            $dataDokumen = MasterSertifikasiDokuman::with("master_jenis_dok_perusahaan")->where("sert_id", $request['sert_id'])->get();
            $results     = [];
            foreach ($dataDokumen as $dt) {
                $results[] = [
                    'dt_id'        => $dt->sert_dok_id,
                    'dt_name'      => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text,
                    'dt_sample'    => !empty($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) ? asset($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) : null,
                    'dt_deskripsi' => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_deskripsi,
                    'my_document'  => null,
                ];
            }

            return responseJSON(200, $results, "data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }
}
