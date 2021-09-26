<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterNegara;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class NegaraController extends Controller
{
    public $module = self::class;
    private $url = 'master/negara';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::negara.index")->with($parser); // Lokasi di Modules\Master\Resources\views\negara
    }

    public function create()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::negara.create")->with($parser); // Lokasi di Modules\Master\Resources\views\negara
    }

    public function store(Request $request)
    {		
		$request->validate([
            'negara_nama' => 'required|string',
			// 'negara_kode' => 'nullable|string'
        ]); // auto redirect back jika tidak valid
		
		
		$dataInsert = [
            'negara_nama' => $request->negara_nama,
            // 'negara_kode' => $request->negara_kode,
        ];
		
        try {
            MasterNegara::create($dataInsert);
            //DB::commit();
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $negaraId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        // Check apakah ID tersedia
        $data = MasterNegara::findOrFail($negaraId); // SELECT * FROM master_negara where negara_id = $negaraId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::negara.edit")->with($parser); // Lokasi di Modules\Master\Resources\views\negara
    }

    public function update(Request $request)
    {
        $request->validate([
            'negara_id' => 'required|integer',
            'negara_nama' => 'required|string',
			// 'negara_kode' => 'nullable|string'
        ]); // auto redirect back jika tidak valid
		
		
		$dataUpdate = [
            'negara_nama'      => $request->negara_nama,
            // 'negara_kode' => $request->negara_kode,
        ];
		
        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterNegara::findOrFail($request['negara_id'])
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
                $data = MasterNegara::where("negara_id", $id)->firstOrFail();
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
        $data = MasterNegara::select("*");
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
            $x['negara_id'] = $d->negara_id;
            $x['negara_nama'] = $d->negara_nama;
            $x['negara_kode'] = $d->negara_kode;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
