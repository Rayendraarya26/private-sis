<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;

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

class KelengkapanPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/kelengkapan-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Pernyataan Persetujuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::kelengkapan_permohonan.index")->with($parser);
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
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->leftJoin('sis_billing_items', "sis_billing_items.mohon_id", "=", "sis_permohonan.mohon_id")
			;
        // Filter
		
        $data->where('mohon_cancel_status', '=', 'no');
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_pjt', ['ya']);
        $data->whereIn('mohon_verif_kajian_permohonan_paskal', ['ya']);
        $data->whereIn('mohon_tagihan_biaya_status', ['setuju']);
        // $data->whereNull('mohon_pernyataan_persetujuan_file');
		if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				if($f->field == 'mohon_id'){
					$data->where('sis_permohonan.mohon_id', 'LIKE', '%' . $f->value . '%');
				}
				else if($f->field == 'created_at'){
					$data->where('sis_permohonan.created_at', 'LIKE', '%' . $f->value . '%');
				}
				else if($f->field == 'status_pernyataan'){
					if($f->value == 'ya'){
						$data->whereNotNull('mohon_pernyataan_persetujuan_file');
					}
					else{
						$data->whereNull('mohon_pernyataan_persetujuan_file');
					}
				}
				else{
					$data->where($f->field, 'LIKE', '%' . $f->value . '%');
				}
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
        $data->select("*",'sis_permohonan.mohon_id AS mohon_id', "sis_billing_items.bill_id AS bill_id", "sis_permohonan.created_at AS created_at", "sis_permohonan.updated_at AS updated_at", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->skip(($request->page - 1) * $request->rows)->take($request->rows);
		
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status_step'] = 'upload';
            $x['status_pernyataan'] = 'Proses';
            if (!is_null($d->mohon_pernyataan_persetujuan_file)) {
				if($d->bill_id != ''){
					$x['status_step'] = 'done';
				}
				else{
					$x['status_step'] = 're-upload';
				}
				
				$x['status_pernyataan'] = '<span style="color:blue;">Ter-Upload</span>';
            }

            $x['cust_sert_id']       = $d->cust_sert_id;
            $x['mohon_id']           = $d->mohon_id;
            $x['cust_id']            = $d->cust_id;
            $x['user_id']            = $d->user_id;
            $x['sert_id']            = $d->sert_id;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_pernyataan_persetujuan_file'] = url($d->mohon_pernyataan_persetujuan_file);
            $x['mohon_cust_nama']    = $d->mohon_cust_nama;
            $x['mohon_jenis_status'] = $d->mohon_jenis_status;
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
            'upload_file' => $this->detail_upload_file($request, $mohonID),
            default       => null,
        };

    }

    private function detail_upload_file(Request $request, $mohonID)
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
            new BreadcrumbsStruct('Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Pernyataan Persetujuan', url($this->url)),
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
        return view('operatorls::kelengkapan_permohonan.detail_upload_file')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['status' => 'required']);
        return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'persetujuan' => $this->edit_upload_persetujuan($request),
            default       => null,
        };
    }

    private function edit_upload_persetujuan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Pernyataan Persetujuan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $request['mohon_id'] . '"', url($this->url . '/' . 'detail/' . $request['mohon_id'] . '?action=upload_file')),
            new BreadcrumbsStruct('Upload Pernyataan Persetujuan "#' . $request['mohon_id'] . '"'),
        ];

        $dataPermohon = SisPermohonan::where('sis_permohonan_detail.mohon_id', $request['mohon_id'])->select('*', DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->groupBy('sis_permohonan_detail.mohon_id')
								->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
								->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");

        $parser = [
            'module'       => $this->module,
            'url'          => $this->url,
            'dataPermohon' => $dataPermohon->get()[0],
            'breadcrumbs'  => $breadcrumbs
        ];
        return view("operatorls::kelengkapan_permohonan.edit_upload")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-persetujuan' => $this->update_file_persetujuan($request),
            default              => null,
        };
    }


    private function update_file_persetujuan(Request $request)
    {
        $request->validate([
            'mohon_id'                          => 'required|integer',
            'status_tipe'                       => 'required|string',
            'mohon_pernyataan_persetujuan_file' => 'required|mimes:pdf'
        ]);

        $dataInsert = [
            'mohon_id'                          => $request->mohon_id,
            'mohon_pernyataan_persetujuan_file' => $request->mohon_pernyataan_persetujuan_file,
            'status_mohon_id'                   => $request->mohon_id,
            'status_tipe'                       => $request->status_tipe,
            'status_pesan'                      => 'Lembaga sertifikasi telah mengupload data Pernyataan Persetujuan untuk nomor #' . $request->mohon_id . ' telah diupload.',
            'status_judul'                      => 'Informasi Pengajuan Permohonan'
        ];

        if ($request->hasFile("mohon_pernyataan_persetujuan_file")) {
            $file     = $request->file('mohon_pernyataan_persetujuan_file');
            $namaFile = Str::slug($request->mohon_id) . '_pernyataan_persetujuan_file_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = sprintf(config("app.path_file_pengajuan"), $request->mohon_id);
            $file->move(public_path($path), $namaFile);
            $dataInsert['mohon_pernyataan_persetujuan_file'] = $path . '/' . $namaFile;

            DB::transaction(function () use ($request, $dataInsert) {
                SisPermohonanStatus::create([
                    'status_mohon_id' => $dataInsert['status_mohon_id'],
                    'status_tipe'     => $dataInsert['status_tipe'],
                    'status_pesan'    => $dataInsert['status_pesan'],
                    'status_judul'    => $dataInsert['status_judul']
                ]);
                // Delete User Group
                SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_pernyataan_persetujuan_file' => $dataInsert['mohon_pernyataan_persetujuan_file']]);
            });
			
			$dataPemohon = SisPermohonan::find($request['mohon_id']);

			$dataUser = SysUser::whereIn('ug_group_id', ['8', '7'])->select('*')->join('sys_user_group', 'ug_user_id', '=','user_id');
			foreach ($dataUser->get() as $us) {
				if($us->ug_group_id == '8'){
					$notifUsr            = new NotifStruct();
					$notifUsr->title     = 'Upload SPK Permohonan No #' . $dataPemohon['mohon_id'];
					$notifUsr->message   = sprintf("Silahkan upload SPK untuk permohonan #%s atas nama pemohon %s.", $dataPemohon['mohon_id'], $dataPemohon['mohon_cust_nama']);
					$notifUsr->user_id   = $us->user_id;
					$notifUsr->click_url = url('kerjasama/spk/detail?action=detail-permohonan&mohon_id='. $dataPemohon['mohon_id']);
				}
				else if($us->ug_group_id == '7'){
					$notifUsr            = new NotifStruct();
					$notifUsr->title     = 'Penerbitan Billing No #' . $dataPemohon['mohon_id'];
					$notifUsr->message   = sprintf("Silahkan terbitkan billing untuk permohonan #%s atas nama pemohon %s.", $dataPemohon['mohon_id'], $dataPemohon['mohon_cust_nama']);
					$notifUsr->user_id   = $us->user_id;
					$notifUsr->click_url = url('keuangan/billing/create');
				}
				sendNotification($notifUsr);
			}

            return redirect($this->url)->with('message', "Upload Pernyataan Persetujuan untuk nomor #" . $request->mohon_id . " sudah berhasil disimpan.");
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }
}
