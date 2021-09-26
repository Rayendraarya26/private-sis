<?php

namespace Modules\Certification\Http\Controllers;

use App\Models\BbkkpSis\MasterKomoditi;
use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokuman;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganDokuman;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
            'combogrid_komoditas' => $this->ajax_combogrid_komoditas($request),
            'dokumen_sertifikat' => $this->ajax_dokumen_sertifikat($request),
            "upload_document" => $this->ajax_upload_document($request),
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

    private function ajax_combogrid_komoditas(Request $request)
    {
        $data = MasterKomoditi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('komodt_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['komodt_id']   = $d->komodt_id;
            $x['komodt_nama'] = $d->komodt_nama;
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
                $findMyDoc = SisPelangganDokuman::where("cust_id", auth()->user()?->sis_pelanggan->cust_id)
                    ->where("jenis_dok_perusahaan_id", $dt->jenis_dok_perusahaan_id)->first();
                $results[] = [
                    'dt_id'        => $dt->sert_dok_id,
                    'dt_name'      => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text,
                    'dt_sample'    => !empty($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) ? asset($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) : null,
                    'dt_deskripsi' => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_deskripsi,
                    'my_document'  => !empty($findMyDoc) ? asset($findMyDoc->cust_dok_filepath) : null,
                ];
            }

            return responseJSON(200, $results, "data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_upload_document(Request $request)
    {
        try {
            $request->validate([
                'sert_dok_id' => 'required|integer',
                'file'        => 'required|mimetypes:application/pdf|max:10000', // 10MB
            ]);

            $dataMasterSertDok = MasterSertifikasiDokuman::with('master_jenis_dok_perusahaan')->findOrFail($request['sert_dok_id']);

            $dataFile = $request->file("file");
            $filePath = sprintf(config("app.path_file_customer"), auth()->id());
            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            $fileName = Str::slug($dataMasterSertDok?->master_jenis_dok_perusahaan?->jenis_dok_perusahaan_text) . '-' . time() . '.' . $dataFile->getClientOriginalExtension();
            $dataFile->move($filePath, $fileName);

            $dokumen = SisPelangganDokuman::updateOrCreate(
                ['cust_id' => auth()->user()->sis_pelanggan->cust_id, 'jenis_dok_perusahaan_id' => $dataMasterSertDok->jenis_dok_perusahaan_id],
                ['cust_dok_filepath' => $filePath . '/' . $fileName]
            );

            return responseJSON(200, $dokumen, "Dokumen berhasil di unggah");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }
}
