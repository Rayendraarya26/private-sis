<?php

namespace Modules\Marketing\Http\Controllers;

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

class UploadKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'marketing/kajian-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("marketing::kajian_permohonan.index")->with($parser);
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
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_paskal', ['proses']);
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
        $data->select("*", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status_step'] = 'upload';
            if (!is_null($d->mohon_kajian_permohonan_paskal_file)) {
                $x['status_step'] = 're-upload';
            }
            $x['mohon_id']           = $d->mohon_id;
            $x['cust_id']            = $d->cust_id;
            $x['user_id']            = $d->user_id;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_cust_nama']    = $d->mohon_cust_nama;
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
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Kajian Permohonan', url($this->url)),
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
        return view('marketing::kajian_permohonan.detail_permohonan')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['status' => 'required']);
        return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'upload-kajian-permohonan' => $this->edit_upload_kajian_permohonan($request),
            default                    => null,
        };
    }

    private function edit_upload_kajian_permohonan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Kajian Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $request['mohon_id'] . '"', url($this->url . '/' . 'detail/' . $request['mohon_id'] . '?action=detail-permohonan')),
            new BreadcrumbsStruct('Upload Kajian Permohonan "#' . $request['mohon_id'] . '"'),
        ];

         $dataPermohon = SisPermohonan::where('sis_permohonan.mohon_id', $request['mohon_id'])->select('*', DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->groupBy('sis_permohonan.mohon_id');

        $parser = [
            'module'       => $this->module,
            'url'          => $this->url,
            'dataPermohon' => $dataPermohon->get()[0],
            'breadcrumbs'  => $breadcrumbs
        ];
        return view("marketing::kajian_permohonan.edit_upload_kajian_permohonan")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-upload-kajian-permohonan' => $this->update_upload_kajian_permohonan($request),
            default                           => null,
        };
    }

    private function update_upload_kajian_permohonan(Request $request)
    {
        $request->validate([
            'mohon_id'                          => 'required|integer',
            'status_tipe'                       => 'required|string',
            'mohon_kajian_permohonan_file_lama' => 'nullable|string',
            'mohon_kajian_permohonan_file'      => 'required|mimes:pdf'
        ]);

        $dataInsert = [
            'mohon_id'               => $request->mohon_id,
            'status_mohon_id'        => $request->mohon_id,
        ];

        if ($request->hasFile("mohon_kajian_permohonan_file")) {
            if ($request["mohon_kajian_permohonan_file_lama"] != '')
                @unlink($request["mohon_kajian_permohonan_file_lama"]);

            $file     = $request->file('mohon_kajian_permohonan_file');
            $namaFile = Str::slug($request->mohon_id) . '_kajian_permohonan_file_paskal_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = sprintf(config("app.path_file_pengajuan"), $request->mohon_id);
            $file->move(public_path($path), $namaFile);
            $dataInsert['mohon_kajian_permohonan_paskal_file'] = $path . '/' . $namaFile;

            DB::transaction(function () use ($request, $dataInsert) {
                SisPermohonan::findOrFail($request['mohon_id'])->update([
                    'mohon_verif_kajian_permohonan_paskal' => 'proses',
                    'mohon_kajian_permohonan_paskal_file'  => $dataInsert['mohon_kajian_permohonan_paskal_file'],
                ]);
            });

            $dataPemohon = SisPermohonan::find($request['mohon_id']);

			$dataUser = SysUser::whereIn('ug_group_id', ['10'])->select('*')->join('sys_user_group', 'ug_user_id', '=','user_id');
			foreach ($dataUser->get() as $us) {
				$notifUsr            = new NotifStruct();
				$notifUsr->title     = 'Verifikasi Kajian Permohonan(PASKAL) No #' . $request['mohon_id'];
				$notifUsr->message   = sprintf("Upload Kajian Permohonan untuk permohonan nomor #%s untuk %s telah diupload silahkan verifikasi..", $dataPemohon['mohon_id'], $dataPemohon['mohon_cust_nama']);
				$notifUsr->user_id   = $us->user_id;
				$notifUsr->click_url = url('/paskal/verifikasi');
				sendNotification($notifUsr);
			}

            return redirect($this->url)->with('message', "Upload Kajian Permohonan #" . $request->mohon_id . " telah disimpan, silahkan menunggu konfirmasi validasi oleh PASKAL.");
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }
}
