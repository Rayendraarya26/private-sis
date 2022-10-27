<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\PegawaiKompetensiKomite;
use App\Models\BbkkpSis\SysUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KompetensiKomiteController extends Controller
{
    public $module = self::class;
    private $url = 'admin/kompetensi/komite';
    private $view = 'admin::kompetensi_komite';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Administrator'),
            new BreadcrumbsStruct('Kompetensi Komite'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function editByPegawai(Request $request, $pegawaiId)
    {
        try {
            $dataPegawai = MasterPegawai::with('pegawai_kompetensi_komites')->find($pegawaiId);
            if (empty($dataPegawai)) throw new ExpectedException("Data pegawai tidak ditemukan");
            $dataSertifikat = MasterSertifikasi::get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Administrator'),
                new BreadcrumbsStruct('Kompetensi Komite'),
                new BreadcrumbsStruct($dataPegawai->peg_nama),
            ];

            $selectedSertifikatId = [];
            foreach ($dataPegawai->pegawai_kompetensi_komites as $kompetensi) {
                $selectedSertifikatId[] = $kompetensi->sert_id;
            }

            $parser = [
                'module'               => $this->module,
                'url'                  => $this->url,
                'breadcrumbs'          => $breadcrumbs,
                'pegawai'              => $dataPegawai,
                'dataSertifikat'       => $dataSertifikat,
                'selectedSertifikatId' => $selectedSertifikatId,
            ];
            return view("$this->view.edit_by_pegawai")->with($parser);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function saveByPegawai(Request $request)
    {
        try {
            if (!$request->ajax()) throw new ExpectedException("Endopoint ini utuk ajax");
            $request->validate([
                'peg_id'  => 'required',
                'sert_id' => 'required',
                'value'   => 'required',
            ]);

            $sertifikat = MasterSertifikasi::findOrFail($request['sert_id']);

            if ($request['value'] == 'allow') {
                PegawaiKompetensiKomite::create([
                    'peg_id'     => $request['peg_id'],
                    'sert_id'    => $request['sert_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'created_id' => auth()->id(),
                    'updated_id' => auth()->id(),
                ]);
                return responseJSON(200, [], sprintf('Berhasil memberikan izin menjadi komite pada "%s"', $sertifikat->sert_nama));
            } else {
                PegawaiKompetensiKomite::where('peg_id', $request['peg_id'])->where('sert_id', $request['sert_id'])->delete();
                return responseJSON(200, [], sprintf('Izin komite "%s" berhasil dicabut', $sertifikat->sert_nama));
            }
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
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
            ->with('master_pegawai.pegawai_kompetensi_komites.master_sertifikasi')
            ->whereNotIn('sys_user_group.ug_group_id', [1, 3])
            ->where('is_komite', '=', 'yes');

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
            foreach ($d->master_pegawai->pegawai_kompetensi_komites as $kompetensi) {
                $dataKompetensi[] = "<li>" . $kompetensi->master_sertifikasi->sert_nama . "</li>";
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
