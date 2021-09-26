<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterJenisDokPerusahaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;


class JenisDokPerusahaanController extends Controller
{
	public $module = self::class;
    private $url = 'master/jenis-dok-perusahaan';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::jenis_dok_perusahaan.index")->with($parser); // Lokasi di Modules\Master\Resources\views\jenis_dok_perusahaan
    }
	
	public function create()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::jenis_dok_perusahaan.create")->with($parser); // Lokasi di Modules\Master\Resources\views\jenis_dok_perusahaan
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_dok_perusahaan_text' => 'required|string',
            'jenis_dok_perusahaan_deskripsi' => 'nullable|string',
            'jenis_dok_perusahaan_sample_file' => 'nullable|max:2048|mimes:csv,zip,docx,doc,xlx,xls,pdf'
        ]);

        $dataInsert = [
            'jenis_dok_perusahaan_text'  => $request->jenis_dok_perusahaan_text,
            'jenis_dok_perusahaan_deskripsi'     => $request->jenis_dok_perusahaan_deskripsi,
        ];

        if ($request->hasFile("jenis_dok_perusahaan_sample_file")) {
            $path = $request->file('jenis_dok_perusahaan_sample_file');

            $dataInsert['jenis_dok_perusahaan_sample_file'] = $path;
        }
		
        try {
            MasterJenisDokPerusahaan::create($request->except("_token"));
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $jenisDokPerusahaanId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        // Check apakah ID tersedia
        $data = MasterJenisDokPerusahaan::findOrFail($jenisDokPerusahaanId); // SELECT * FROM master_jenis_dok_perusahaan where kab_id = $jenisDokPerusahaanId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::jenis_dok_perusahaan.edit")->with($parser); // Lokasi di Modules\Master\Resources\views\jenis_dok_perusahaan
    }

    public function update(Request $request)
    {
        $request->validate([
            'jenis_dok_perusahaan_id' => 'required|integer',
            'jenis_dok_perusahaan_text' => 'required|string'
        ]); // auto redirect back jika tidak valid

        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterJenisDokPerusahaan::findOrFail($request['jenis_dok_perusahaan_id'])
                ->update($request->only("jenis_dok_perusahaan_text"));
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
				$data = MasterJenisDokPerusahaan::findOrFail($id);
				if ($data->delete()) {
					
				} else {
					$status_return = FALSE;
					break;
				}
			}
			
			if ($status_return == TRUE) {
				return responseJSON(200, [], "Berhasil menghapus data" );
			} else {
				return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
			}
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'datagrid' => $this->ajax_datagrid($request),
            default => null,
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = MasterJenisDokPerusahaan::select("*");
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
            $x['jenis_dok_perusahaan_id'] = $d->jenis_dok_perusahaan_id;
            $x['jenis_dok_perusahaan_text'] = $d->jenis_dok_perusahaan_text;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}