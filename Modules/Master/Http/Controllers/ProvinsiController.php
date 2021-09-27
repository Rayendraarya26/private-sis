<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterProvinsi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class ProvinsiController extends Controller
{
	public $module = self::class;
    private $url = 'master/provinsi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::provinsi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }
	
	public function create()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::provinsi.create")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }

    public function store(Request $request)
    {
        $request->validate(['prov_nama' => 'required|string']); // auto redirect back jika tidak valid

        // Aktifkan dd jika ingin melihat data
        //dump($request->except('_token'));
        //dump($request->all());

        try {
            MasterProvinsi::create($request->except("_token"));
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $provId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        // Check apakah ID tersedia
        $data = MasterProvinsi::findOrFail($provId); // SELECT * FROM master_provinsi where kab_id = $provId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::provinsi.edit")->with($parser); // Lokasi di Modules\Master\Resources\views\provinsi
    }

    public function update(Request $request)
    {
        $request->validate([
            'prov_id' => 'required|integer',
            'prov_nama' => 'required|string'
        ]); // auto redirect back jika tidak valid

        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterProvinsi::findOrFail($request['prov_id'])
                ->update($request->only("prov_nama"));
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
                $data = MasterProvinsi::where("prov_id", $id)->firstOrFail();
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
        $data = MasterProvinsi::select("*");
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
            $x['prov_id'] = $d->prov_id;
            $x['prov_nama'] = $d->prov_nama;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}