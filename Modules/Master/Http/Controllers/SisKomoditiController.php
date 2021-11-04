<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterKomoditi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SisKomoditiController extends Controller
{
    public $module = self::class;
    private $url = 'master/sis/komoditi';

    public function index()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_komoditi.index")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_komoditi
    }
	
	public function create()
    {
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::sis_komoditi.create")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_komoditi
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'komodt_nama' => 'required|string',
            'komodt_sni' => 'required|string'
        ]); // auto redirect back jika tidak valid

        // Aktifkan dd jika ingin melihat data
        //dump($request->except('_token'));
        //dump($request->all());

        try {
            MasterKomoditi::create(['komodt_nama' => $request['komodt_nama'], 'komodt_sni' => $request['komodt_sni']]);
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $komodtId) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        // Check apakah ID tersedia
        $data = MasterKomoditi::findOrFail($komodtId); // SELECT * FROM master_komoditi where kab_id = $komodtId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::sis_komoditi.edit")->with($parser); // Lokasi di Modules\Master\Resources\views\sis_komoditi
    }

    public function update(Request $request)
    {
        $request->validate([
            'komodt_id' => 'required|integer',
            'komodt_nama' => 'required|string',
            'komodt_sni' => 'required|string'
        ]); // auto redirect back jika tidak valid

        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterKomoditi::findOrFail($request['komodt_id'])
                ->update(['komodt_nama' => $request['komodt_nama'], 'komodt_sni' => $request['komodt_sni']]);
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
                $data = MasterKomoditi::where("komodt_id", $id)->firstOrFail();
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
        $data = MasterKomoditi::select("*");
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
            $x['komodt_id'] = $d->komodt_id;
            $x['komodt_nama'] = $d->komodt_nama;
            $x['komodt_sni'] = $d->komodt_sni;
			
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
