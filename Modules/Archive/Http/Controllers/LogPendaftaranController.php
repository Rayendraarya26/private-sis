<?php

namespace Modules\Archive\Http\Controllers;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;

class LogPendaftaranController extends Controller
{
    
    public $module = self::class;
    private $url = 'archive/log_pendaftaran';
    private $view = "archive::log_pendaftaran";
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Archive'),
            new BreadcrumbsStruct('Log Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan' => $this->ajax_datagrid_permohonan($request),
            'tinymce-uploadimage' => $this->ajax_tinymce_uploadimage($request),
            default               => null,
        };
    }
	
	private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->leftJoin('sis_billing_items', "sis_permohonan.mohon_id", "=", "sis_billing_items.mohon_id")
            ->leftJoin('sis_billing', "sis_billing.bill_id", "=", "sis_billing_items.bill_id")
            ->leftJoin('sis_audit_tahap1', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->leftJoin('sis_jadwal', "sis_billing.bill_id", "=", "sis_jadwal.bill_id");
        // Filter
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
        $data->select("sis_jadwal.*", "sis_jadwal.jadw_id AS jadw_id", "sis_billing.bill_payment_status AS bill_payment_status", "sis_billing.bill_id AS bill_id", "sis_permohonan.*", "sis_permohonan.created_at AS created_at", "sis_permohonan.updated_at AS updated_at", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_permohonan.mohon_id');
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['step_pengajuan'] = '';
			if($d->mohon_cancel_status == 'no'){
				if (in_array($d->mohon_approved_status, ['accepted', 'rejected'])) {
					if($d->mohon_verif_kajian_permohonan_paskal == 'ya' && $d->mohon_verif_kajian_permohonan_pjt == 'ya' && $d->mohon_kajian_permohonan_paskal_file != ''){
						if($d->mohon_tagihan_biaya_status == 'setuju' && $d->mohon_tagihan_biaya_file != ''){
							if($d->mohon_pernyataan_persetujuan_file != ''){
								if($d->bill_id != ''){
									if($d->bill_payment_status == 'lunas'){
										if($d->jadw_id != ''){
											if($d->jadw_is_tutup == 'tidak'){
												if($d->jadw_tanggal_status == 'accepted'){
													if($d->jadw_team_status == 'accepted'){
														$x['step_pengajuan'] = 'Proses Audit';
													}
													else
													{
														$x['step_pengajuan'] = 'Persetujuan<br/>Team Audit';
													}
												}
												else
												{
													$x['step_pengajuan'] = 'Persetujuan<br/>Tanggal Audit';
												}
												
											}
											else{
												$x['step_pengajuan'] = 'Selesai';
											}
										}
										else{
											$x['step_pengajuan'] = 'Proses Penjadwalan';
										}
									}
									else{
										$x['step_pengajuan'] = 'Biling Menunggu Pelunasan';
									}
								}
								else{
									$x['step_pengajuan'] = 'Biling Belum<br/>Ter-Entry';
								}
							}
							else{
								$x['step_pengajuan'] = 'Pernyataan Persetujuan - LS';
							}
						}
						else{
							$x['step_pengajuan'] = 'Proses Persetujuan Biaya';
						}
					}
					else{
						$x['step_pengajuan'] = 'Proses Kajian Permohonan';
					}
				}
				else{
					$x['step_pengajuan'] = 'Verifikasi - Marketing';
				}
			}
			else{
				$x['step_pengajuan'] = 'Pembatalan Permohonan';
			}
            

            $x['mohon_approved_status']           = $d->mohon_approved_status;
            $x['cust_sert_id']           = $d->cust_sert_id;
            $x['mohon_id']               = $d->mohon_id;
            $x['cust_id']                = $d->cust_id;
            $x['user_id']                = $d->user_id;
            $x['sert_nama']              = $d->sert_nama;
            $x['mohon_cust_nama']        = $d->mohon_cust_nama;
            $x['mohon_det_jenis_status'] = $d->mohon_det_jenis_status;
            $x['created_at']             = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['updated_at']              = $d->updated_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function detail(Request $request, $mohonID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Archive'),
            new BreadcrumbsStruct('Log Permohonan'),
            new BreadcrumbsStruct('Detail Permohonan "#' . $mohonID . '"'),
        ];

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

        $parser = [
            'module'                  => $this->module,
            'url'                     => $this->url,
            'dataPermohon'            => $dataPermohon->get()[0],
            'dataPermohonKomoditi'    => $dataPermohonKomoditi->get(),
            'dataPermohonPabrik'      => $dataPermohonPabrik->get(),
            'dataPermohonanDokumen'   => $dataPermohonanDokumen->get(),
            'dataPermohonSertifikasi' => $dataPermohonSertifikasi->get(),
            'dataPermohonanStatus'    => $dataPermohonanStatus->get(),
            'breadcrumbs'             => $breadcrumbs
        ];
        return view("$this->view.detail")->with($parser);

    }
}
