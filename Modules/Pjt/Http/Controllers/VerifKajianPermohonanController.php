<?php

namespace Modules\Pjt\Http\Controllers;

use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUser;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VerifKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'pjt/verifikasi';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('PJT'),
            new BreadcrumbsStruct('Verifikasi Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("pjt::verif_kajian.index")->with($parser);
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
        $data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->where('mohon_cancel_status', '=', 'no');
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_pjt', ['proses']);
        $data->whereNotNull('mohon_kajian_permohonan_pjt_file');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				if($f->field == 'mohon_id')
					$data->where('sis_permohonan.mohon_id', 'LIKE', '%' . $f->value . '%');
				else if($f->field == 'created_at')
					$data->where('sis_permohonan.created_at', 'LIKE', '%' . $f->value . '%');
				else
					$data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
				if($sort[$i] == 'mohon_id')
					$data->orderBy('sis_permohonan.mohon_id', $order[$i]);
				else if($sort[$i] == 'created_at')
					$data->orderBy('sis_permohonan.created_at', $order[$i]);
				else
					$data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(DISTINCT sis_permohonan.mohon_id) as total'))->first()->total;
        // Pagination
		$data->groupBy('sis_permohonan.mohon_id');
        $data->select("*", "sis_permohonan.created_at AS created_at", "sis_permohonan.updated_at AS updated_at", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['mohon_id']           = $d->mohon_id;
            $x['cust_id']            = $d->cust_id;
            $x['user_id']            = $d->user_id;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_cust_nama']    = $d->mohon_cust_nama;
            $x['created_at']         = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
           $x['updated_at']              = $d->updated_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
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
        $dataPermohon = SisPermohonan::where('mohon_id', $mohonID)->select('*')
						->join('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id')
						->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id')
						->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id')
						->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id')
						->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');
						
        
		$dataPermohonSertifikasi = SisPermohonan::where('sis_permohonan_detail.mohon_id', $mohonID)->select('*')
								->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
								->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
								
        $dataPermohonKomoditi = SisPermohonanKomoditi::where('sis_permohonan_detail.mohon_id', $mohonID)->select('*')
								->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
								->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
								->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');


        $dataPermohonPabrik = SisPermohonanPabrik::where('mohon_id', $mohonID)->select('*')
			->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan_pabrik.kab_id')
			->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan_pabrik.kec_id')
			->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan_pabrik.prov_id');

        $dataPermohonanDokumen = SisPermohonanDokumen::where('mohon_id', $mohonID)->select('*')
							->join('master_jenis_dok_perusahaan', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id', '=', 'sis_permohonan_dokumen.jenis_dok_perusahaan_id');
							

        $dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID)->where('status_tipe', 'revisi')->select('*');

        $breadcrumbs = [
            new BreadcrumbsStruct('PJT'),
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
            'dataPermohonSertifikasi'  => $dataPermohonSertifikasi->get(),
            'breadcrumbs'           => $breadcrumbs
        ];
        return view('pjt::verif_kajian.detail_permohonan')->with($parser);
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
                'mohon_verif_kajian_permohonan_pjt' => 'ya',
            ]);
        });
		
		$dataPermohon = SisPermohonan::where('mohon_id', $request['mohon_id'])->select('*')->get()[0];
		if($dataPermohon->mohon_verif_kajian_permohonan_paskal == 'ya' && $dataPermohon->mohon_verif_kajian_permohonan_pjt == 'ya'){
			$dataUser = SysUser::whereIn('ug_group_id', ['4'])->select('*')->join('sys_user_group', 'ug_user_id', '=','user_id');
			foreach ($dataUser->get() as $us) {
				$notifUsr            = new NotifStruct();
				$notifUsr->title     = 'Verifikasi Kajian Permohonan(PJT) No. #' . $request['mohon_id'];
				$notifUsr->message   = sprintf("Verifikasi Kajian Permohonan untuk permohonan nomor #%s untuk %s telah diverifikasi, silahkan lakukan proses Tagihan Biaya.", $dataPermohon->mohon_id, $dataPermohon->mohon_cust_nama);
				$notifUsr->user_id   = $us->user_id;
				$notifUsr->click_url = url('/marketing/tagihan-biaya');
				sendNotification($notifUsr);
			}
		}
		
        return redirect($this->url)->with('message', "Verifikasi Kajian Permohonan #" . $request->mohon_id . " telah diterima.");
    }
}
