<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterKomoditi;
use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\PegawaiKompetensiPpc;
use App\Models\BbkkpSis\SysUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KompetensiPpcController extends Controller
{
    public $module = self::class;
    private $url = 'admin/kompetensi/ppc';
    private $view = 'admin::kompetensi_ppc';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Kompetensi PPC'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function editByPegawai(Request $request, $pegawaiId)
    {
        try {
            $dataPegawai = MasterPegawai::with('pegawai_kompetensi_ppcs')->find($pegawaiId);
            if (empty($dataPegawai)) throw new Exception("Data pegawai tidak ditemukan");
            $dataKomoditi = MasterKomoditi::get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Administrator'),
                new BreadcrumbsStruct('Kompetensi PPC'),
                new BreadcrumbsStruct($dataPegawai->peg_nama),
            ];

            $selectedKomoditiId = [];
            foreach ($dataPegawai->pegawai_kompetensi_ppcs as $kompetensi) {
                $selectedKomoditiId[] = $kompetensi->komodt_id;
            }

            $parser = [
                'module'             => $this->module,
                'url'                => $this->url,
                'breadcrumbs'        => $breadcrumbs,
                'pegawai'            => $dataPegawai,
                'dataKomoditi'       => $dataKomoditi,
                'selectedKomoditiId' => $selectedKomoditiId,
            ];
            return view("$this->view.edit_by_pegawai")->with($parser);
        } catch (Exception $e) {
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function saveByPegawai(Request $request)
    {
        try {
            if (!$request->ajax()) throw new Exception("Endopoint ini utuk ajax");
            $request->validate([
                'peg_id'    => 'required',
                'komodt_id' => 'required',
                'value'     => 'required',
            ]);

            $komoditi = MasterKomoditi::findOrFail($request['komodt_id']);

            if ($request['value'] == 'allow') {
                PegawaiKompetensiPpc::create([
                    'peg_id'     => $request['peg_id'],
                    'komodt_id'  => $request['komodt_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'created_id' => auth()->id(),
                    'updated_id' => auth()->id(),
                ]);
                return responseJSON(200, [], sprintf('Berhasil memberikan izin menjadi PPC pada "%s"', $komoditi->komodt_nama));
            } else {
                PegawaiKompetensiPpc::where('peg_id', $request['peg_id'])->where('komodt_id', $request['komodt_id'])->delete();
                return responseJSON(200, [], sprintf('Izin PPC "%s" berhasil dicabut', $komoditi->komodt_nama));
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function ajax(Request $request)
    {
        if (!$request->ajax()) responseJSON(404, null, "Endopoint ini utuk ajax");
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-by-pegawai'    => $this->ajax_datagrid_by_pegawai($request),
            'datagrid-by-kompetensi' => $this->ajax_datagrid_by_kompetensi($request),
            default                  => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid_by_pegawai(Request $request)
    {
        $data = SysUser::leftJoin('master_pegawai', 'master_pegawai.user_id', '=', 'sys_user.user_id')
            ->leftJoin('sys_user_group', 'ug_user_id', '=', 'sys_user.user_id')
            ->with('master_pegawai.pegawai_kompetensi_ppcs.master_komoditi')
            ->whereNotIn('sys_user_group.ug_group_id', [1, 3])
            ->where('is_ppc', '=', 'yes');

        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        } else {
            $data->orderBy('master_pegawai.peg_nama', 'asc');
        }

        $data = $data->groupBy('sys_user.user_id');

        // Total
        $total = $data->get()->count();
        // Pagination
        $data->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];

        foreach ($data->get() as $d) {
            $dataKompetensi = [];
            foreach ($d->master_pegawai->pegawai_kompetensi_ppcs as $kompetensi) {
                $dataKompetensi[] = "<li>" . $kompetensi->master_komoditi->komodt_nama . "</li>";
            }

            $x['peg_id']     = $d->peg_id;
            $x['peg_nama']   = $d->peg_nama;
            $x['peg_kode']   = $d->peg_kode;
            $x['kompetensi'] = implode(' ', $dataKompetensi);

            $result[] = $x;
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }
}
