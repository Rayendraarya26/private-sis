<?php

namespace Modules\Master\Http\Controllers;

use App\Models\BbkkpSis\MasterRuangLingkup;
use App\Http\Structs\BreadcrumbsStruct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RuangLingkupController extends Controller
{
    public $module = self::class;
    private $url = 'master/ruang-lingkup';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Master'),
            new BreadcrumbsStruct('Ruang Lingkup'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("master::ruang_lingkup.index")->with($parser);
    }

    public function create()
    {
		
        $breadcrumbs = [
            new BreadcrumbsStruct('Master'),
            new BreadcrumbsStruct('Ruang Lingkup'),
            new BreadcrumbsStruct('Tambah'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url];
        return view("master::ruang_lingkup.create")->with($parser);
    }

    public function store(Request $request)
    {		
		$request->validate([
            'ruang_ling_nama' => 'required|string',
        ]);
		
		
		$dataInsert = [
            'ruang_ling_nama' => $request->ruang_ling_nama,
        ];
		
        try {
            MasterRuangLingkup::create($dataInsert);
            //DB::commit();
            return redirect()->back()->with('message', "Tambah data berhasil");
        } catch (Exception $e) {
            //DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $rlId)
    {
        // Check apakah ID tersedia
        $data = MasterRuangLingkup::findOrFail($rlId); // SELECT * FROM master_negara where ruang_ling_id = $negaraId | findOrFail akan otomatis redirect 404 jika data tidak ditemukan primary key degan id tersebut

        $parser = ['module' => $this->module, 'url' => $this->url, 'data' => $data];
        return view("master::ruang_lingkup.edit")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ruang_ling_id' => 'required|integer',
            'ruang_ling_nama' => 'required|string',
        ]); // auto redirect back jika tidak valid
		
		
		$dataUpdate = [
            'ruang_ling_nama'      => $request->ruang_ling_nama,
        ];
		
        try {
            //DB::beginTransaction(); // Jika mau menggunkan transaction
            $data = MasterRuangLingkup::findOrFail($request['ruang_ling_id'])
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
                $data = MasterRuangLingkup::where("ruang_ling_id", $id)->firstOrFail();
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
        $data = MasterRuangLingkup::select("*");
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
            $x['ruang_ling_id'] = $d->ruang_ling_id;
            $x['ruang_ling_nama'] = $d->ruang_ling_nama;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
