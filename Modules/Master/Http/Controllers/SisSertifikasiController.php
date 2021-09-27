<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokumen;
use App\Models\BbkkpSis\MasterSertifikasiKlausul;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SisSertifikasiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/sertifikasi';
	
	public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_sertifikasi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
    }
	
    public function ajax(Request $request)
    {
		$request->validate(['action' => 'required']);
        $response = null;
        switch ($request['action']) {
            case 'datagrid-sertifikasi':
                $response = $this->ajax_datagrid_sertifikasi($request);
                break;
            case 'tinymce-uploadimage':
                $response = $this->ajax_tinymce_uploadimage($request);
                break;
            default:
                abort(404);
        }

        return $response;
    }
	
	private function ajax_tinymce_uploadimage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimetypes:image/jpeg,image/png|max:1000', // 1MB
            ]);

            $img = $request->file('file');
            $imgName = $img->hashName();
            $img->move(public_path(config('app.path_file_master')), $imgName);
            $publicUrl = asset(config('app.path_file_master') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }
	
    private function ajax_datagrid_sertifikasi(Request $request)
    {
        $data = MasterSertifikasi::select("*");
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort = explode(",", $request->sort);
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
            $x['sert_id'] = $d->sert_id;
            $x['sert_nama'] = $d->sert_nama;
            $x['sert_deskripsi'] = $d->sert_deskripsi;
            $x['sert_expired'] = $d->sert_expired;
            $x['sert_format_referensi'] = $d->sert_format_referensi;
            $x['sert_is_product'] = $d->sert_is_product;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function create( Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'create-sertifikasi' => $this->create_sertifikasi($request),
            default => null,
        };
    }
	
	private function create_sertifikasi(Request $request)
    {
		$parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_sertifikasi.create-sertifikasi")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	
	public function store( Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'store-sertifikasi' => $this->store_sertifikasi($request),
            default => null,
        };
    }
	
    private function store_sertifikasi( Request $request)
    {
        $request->validate([
							'sert_nama' => 'required|string',
							'sert_deskripsi' => 'required|string',
							'sert_expired' => 'required|integer',
							'sert_format_referensi' => 'required|string',
							'sert_is_product' => 'required|string'
						]); // auto redirect back jika tidak valid
		$dataInsert = [
            'sert_nama'      => $request->sert_nama,
            'sert_deskripsi' => $request->sert_deskripsi,
            'sert_expired' => $request->sert_expired,
            'sert_format_referensi' => $request->sert_format_referensi,
            'sert_is_product' => $request->sert_is_product,
        ];

        try {
            MasterSertifikasi::create($request->except("_token"));
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit( Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'edit-sertifikasi' => $this->edit_sertifikasi($request),
            default => null,
        };
    }
	
	private function edit_sertifikasi( Request $request)
    {
		// Check apakah ID tersedia
        $data = MasterSertifikasi::findOrFail($request['sert_id']); // SELECT * FROM master_sertifikasi where kab_id = $sertId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_sertifikasi.edit-sertifikasi")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_sertifikasi
	}
	

    public function update(Request $request)
    {
		$request->validate(['tipe' => 'required']);
		return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'update-sertifikasi' => $this->update_sertifikasi($request),
            default => null,
        };
    }

	private function update_sertifikasi(Request $request)
	{
		 $request->validate([
            'sert_id' => 'required|integer',
			'sert_nama' => 'required|string',
			'sert_deskripsi' => 'required|string',
			'sert_expired' => 'required|integer',
			'sert_format_referensi' => 'required|string',
			'sert_is_product' => 'required|string'
		]); // auto redirect back jika tidak valid
		
		$dataUpdate = [
            'sert_nama'      => $request->sert_nama,
            'sert_deskripsi' => $request->sert_deskripsi,
            'sert_expired' => $request->sert_expired,
            'sert_format_referensi' => $request->sert_format_referensi,
            'sert_is_product' => $request->sert_is_product,
        ];
        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterSertifikasi::findOrFail($request['sert_id'])
                ->update($dataUpdate);
            //DB::commit();
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	public function destroy(Request $request)
    {
        /*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
		
		try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = MasterSertifikasi::where("sert_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
