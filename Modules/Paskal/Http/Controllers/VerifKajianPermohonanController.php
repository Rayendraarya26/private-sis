<?php

namespace Modules\Paskal\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class VerifKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'paskal/verifikasi';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Paskal'),
            new BreadcrumbsStruct('Verifikasi Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("paskal::verif_kajian.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan' => $this->ajax_datagrid_permohonan($request),
            default               => null,
        };
    }

    private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_pjt', ['ya']);
        $data->whereNotNull('mohon_kajian_permohonan_pjt_file');
        $data->whereIn('mohon_verif_kajian_permohonan_paskal', ['proses']);
        $data->whereNotNull('mohon_kajian_permohonan_paskal_file');
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
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_sert_id']       = $d->cust_sert_id;
            $x['mohon_id']           = $d->mohon_id;
            $x['cust_id']            = $d->cust_id;
            $x['user_id']            = $d->user_id;
            $x['sert_id']            = $d->sert_id;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_cust_nama']    = $d->mohon_cust_nama;
            $x['mohon_jenis_status'] = $d->mohon_jenis_status;
            $x['created_at']         = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']          = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    public function detail(Request $request, $mohonID)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'detail-permohonan' => $this->detail_permohonan($request, $mohonID),
            default             => null,
        };
    }

    private function detail_permohonan(Request $request, $mohonID)
    {
        $dataPermohon = SisPermohonan::where('mohon_id', $mohonID);
        $dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');

        $dataPermohon->join('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id');
        $dataPermohon->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id');
        $dataPermohon->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id');
        $dataPermohon->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id');
        $dataPermohon->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');
        $dataPermohon->select('*');

        $dataPermohonKomoditi = SisPermohonanKomoditi::where('mohon_id', $mohonID);
        $dataPermohonKomoditi->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');
        $dataPermohonKomoditi->select('*');


        $dataPermohonPabrik = SisPermohonanPabrik::where('mohon_id', $mohonID);
        $dataPermohonPabrik->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan_pabrik.kab_id');
        $dataPermohonPabrik->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan_pabrik.kec_id');
        $dataPermohonPabrik->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan_pabrik.prov_id');
        $dataPermohonPabrik->select('*');

        $dataPermohonanDokumen = SisPermohonanDokumen::where('mohon_id', $mohonID);
        $dataPermohonanDokumen->join('master_jenis_dok_perusahaan', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id', '=', 'sis_permohonan_dokumen.jenis_dok_perusahaan_id');
        $dataPermohonanDokumen->select('*');

        $dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID);
        $dataPermohonanStatus->select('*');

        $breadcrumbs = [
            new BreadcrumbsStruct('PASKAL'),
            new BreadcrumbsStruct('Verifikasi Kajian Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $mohonID . '"'),
        ];

        $parser = [
            'module'                => $this->module,
            'url'                   => $this->url,
            'dataPermohon'          => $dataPermohon->get()[0],
            'dataPermohonKomoditi'  => $dataPermohonKomoditi->get(),
            'dataPermohonPabrik'    => $dataPermohonPabrik->get(),
            'dataPermohonanDokumen' => $dataPermohonanDokumen->get(),
            'dataPermohonanStatus'  => $dataPermohonanStatus->get(),
            'breadcrumbs'           => $breadcrumbs
        ];
        return view('paskal::verif_kajian.detail_permohonan')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'edit-accepted' => $this->edit_accepted($request),
            default         => null,
        };
    }

    private function edit_accepted(Request $request)
    {
        $dataInsert = [
            'mohon_id' => $request->mohon_id,
        ];

        DB::transaction(function () use ($request, $dataInsert) {
            SisPermohonan::findOrFail($request['mohon_id'])->update([
                'mohon_verif_kajian_permohonan_paskal' => 'ya',
            ]);
        });

        return redirect($this->url)->with('message', "Verifikasi Kajian Permohonan #" . $request->mohon_id . " telah diterima.");
    }
}
