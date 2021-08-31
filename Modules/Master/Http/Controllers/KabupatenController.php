<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterKabupaten;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class KabupatenController extends Controller
{
	public $module = self::class;
    private $url = 'master/kabupaten';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::kabupaten.index")->with($parser); // Lokasi di Modules\Master\Resources\views\kabupaten
    }
	
	public function create()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::kabupaten.create")->with($parser); // Lokasi di Modules\Master\Resources\views\kabupaten
    }

    public function store(Request $request)
    {
        $request->validate(['kab_nama' => 'required|string']); // auto redirect back jika tidak valid

        // Aktifkan dd jika ingin melihat data
        //dump($request->except('_token'));
        //dump($request->all());

        try {
            MasterKabupaten::create($request->except("_token"));
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $kabId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        // Check apakah ID tersedia
        $data = MasterKabupaten::findOrFail($kabId); // SELECT * FROM master_kabupaten where kab_id = $kabId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::kabupaten.edit")->with($parser); // Lokasi di Modules\Master\Resources\views\kabupaten
    }

    public function update(Request $request)
    {
        $request->validate([
            'kab_id' => 'required|integer',
            'kab_nama' => 'required|string'
        ]); // auto redirect back jika tidak valid

        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterKabupaten::findOrFail($request['kab_id'])
                ->update($request->only("kab_nama"));
            //DB::commit();
            return redirect()->back()->with('message', "Update data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }


    }

    public function destroy($kabId)
    {
        /*
        responseJSON : adalah helper standar untuk output JSON pada aplikasi (kecuali ajax easyui)
        Lokasi helper ada di App\Helpers\GlobalHelper
        */
        try {
            $data = MasterKabupaten::where("kab_id", $kabId)->firstOrFail();
            if ($data->delete()) {
                return responseJSON(200, [], "Data berhasil dihapus");
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
        $data = MasterKabupaten::select("*");
		$data->join('master_provinsi', 'master_provinsi.prov_id', '=', 'master_kabupaten.prov_id');
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
            $x['kab_id'] = $d->kab_id;
            $x['kab_nama'] = $d->kab_nama;
            $x['prov_nama'] = $d->prov_nama;
            $x['created_at'] = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['updated_at'] = $d->updated_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
