<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Models\BbkkpSis\MasterBadanHukum;
use App\Models\BbkkpSis\MasterJenisPerusahaan;
use App\Models\BbkkpSis\MasterKabupaten;
use App\Models\BbkkpSis\MasterKecamatan;
use App\Models\BbkkpSis\MasterKomoditi;
use App\Models\BbkkpSis\MasterNegara;
use App\Models\BbkkpSis\MasterProvinsi;
use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokuman;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganDokuman;
use App\Models\BbkkpSis\SisPelangganPabrik;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SertifikasiPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/sertifikasi/permohonan';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view('pelanggan::sertifikasi_permohonan.index')->with($parser);
    }

    public function create()
    {
        $dataPelanggan         = SisPelanggan::where("user_id", auth()->id())->first();
        $masterBadanHukum      = MasterBadanHukum::all();
        $masterJenisPerusahaan = MasterJenisPerusahaan::all();
        $parser                = [
            'module'                => $this->module,
            'url'                   => $this->url,
            'dataPelanggan'         => $dataPelanggan,
            'masterBadanHukum'      => $masterBadanHukum,
            'masterJenisPerusahaan' => $masterJenisPerusahaan,
        ];
        return view('pelanggan::sertifikasi_permohonan.create')->with($parser);
    }

    public function store(Request $request)
    {
        $request->validate([
            "pertanyaan_tambahan" => 'required',
            "jenis_sertifikasi"   => 'required',
            "data_komoditas"      => 'required',
        ]);

        return responseJSON(200, null, "Permohonan berhasil dan sedang tahap verifikasi");
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            'combogrid_sertifikasi' => $this->ajax_combogrid_sertifikasi($request),
            'combogrid_komoditas' => $this->ajax_combogrid_komoditas($request),
            'combogrid_negara' => $this->ajax_combogrid_negara($request),
            'combogrid_provinsi' => $this->ajax_combogrid_provinsi($request),
            'combogrid_kabupaten' => $this->ajax_combogrid_kabupaten($request),
            'combogrid_kecamatan' => $this->ajax_combogrid_kecamatan($request),
            'dokumen_sertifikat' => $this->ajax_dokumen_sertifikat($request),
            "upload_document" => $this->ajax_upload_document($request),
            "data_pemohon" => $this->ajax_data_pemohon($request),
            "update_data_pemohon" => $this->ajax_update_data_pemohon($request),
            "pabrik_data" => $this->ajax_pabrik_data($request),
            "pabrik_add" => $this->ajax_pabrik_add($request),
            "pabrik_update" => $this->ajax_pabrik_update($request),
            "pabrik_delete" => $this->ajax_pabrik_delete($request),
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

    private function ajax_combogrid_negara(Request $request)
    {
        $data = MasterNegara::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('negara_id', $request->q);
            $data->orWhere('negara_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['negara_id']   = $d->negara_id;
            $x['negara_kode'] = $d->negara_kode;
            $x['negara_nama'] = $d->negara_nama;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_provinsi(Request $request)
    {
        $data = MasterProvinsi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('prov_id', $request->q);
            $data->orWhere('prov_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['prov_id']   = $d->prov_id;
            $x['prov_nama'] = $d->prov_nama;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_kabupaten(Request $request)
    {
        $data = MasterKabupaten::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('kab_id', $request->q);
            $data->orWhere('kab_nama', 'LIKE', '%' . $request->q . '%');
        }
        if (!empty($request->prov_id)) {
            $data->where('prov_id', $request->prov_id);
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
            $x['kab_nama'] = $d->kab_nama;
            $x['kab_id']   = $d->kab_id;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_kecamatan(Request $request)
    {
        $data = MasterKecamatan::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('kec_id', $request->q);
            $data->orWhere('kec_nama', 'LIKE', '%' . $request->q . '%');
        }
        if (!empty($request->kab_id)) {
            $data->where('kab_id', $request->kab_id);
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
            $x['kec_id']   = $d->kec_id;
            $x['kec_nama'] = $d->kec_nama;
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

    private function ajax_data_pemohon(Request $request)
    {
        try {
            $dataPemohon = auth()->user()?->sis_pelanggan;
            return responseJSON(200, $dataPemohon, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_data(Request $request)
    {
        try {
            $dataPabrik = auth()->user()?->sis_pelanggan?->sis_pelanggan_pabriks;
            return responseJSON(200, $dataPabrik, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_add(Request $request)
    {
        try {
            $dataPelanggan = auth()->user()?->sis_pelanggan;
            $dataPabrik    = $dataPelanggan?->sis_pelanggan_pabriks;

            $newPabrik              = new SisPelangganPabrik();
            $newPabrik->cust_id     = $dataPelanggan->cust_id;
            $newPabrik->pabrik_nama = sprintf("Pabrik %d", count($dataPabrik) + 1);

            $allField = $newPabrik->getFillable();
            foreach ($allField as $field) {
                if (!in_array($field, ['cust_id', 'pabrik_nama', 'kec_id', 'kab_id', 'prov_id',])) {
                    $newPabrik->$field = "-";
                }
            }
            $newPabrik->save();
            return responseJSON(200, $newPabrik, "Data pabrik ditambahkan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_update(Request $request)
    {
        try {
            $request->validate(['pabrik_id' => 'required|integer', "parameter" => "required", "value" => "required"]);
            $parameter = $request['parameter'];
            $value     = $request['value'] == '--' ? NULL : $request['value'];

            $dataPabrik             = SisPelangganPabrik::findOrFail($request['pabrik_id']);
            $dataPabrik->$parameter = $value;
            $dataPabrik->save();
            return responseJSON(200, null, "Data pabrik diperbarui");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_delete(Request $request)
    {
        try {
            $request->validate(['pabrik_id' => 'required|integer']);

            $dataPabrik = SisPelangganPabrik::findOrFail($request['pabrik_id']);
            $dataPabrik->delete();
            return responseJSON(200, null, "Data pabrik dihapus");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_update_data_pemohon(Request $request)
    {
        try {
            $request->validate(["parameter" => "required", "value" => "required"]);
            $parameter = $request['parameter'];
            $value     = $request['value'] == '--' ? NULL : $request['value'];

            $dataPemohon                          = auth()->user()?->sis_pelanggan;
            $dataPemohon->$parameter              = $value;
            $dataPemohon->cust_jumlah_operasional = $dataPemohon->cust_jumlah_shift_1 + $dataPemohon->cust_jumlah_shift_2 + $dataPemohon->cust_jumlah_shift_3;
            $dataPemohon->save();
            return responseJSON(200, $dataPemohon, "Data diperbarui");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }
}
