<?php

namespace Modules\SiHalal\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\SiHalal\Http\Traits\ServiceSihalalTrait;

class ArsipPermohonanController extends Controller
{
    use ServiceSihalalTrait;
	
    public $module = self::class;
    private $url = 'sihalal/arsip';
    private $view = "sihalal::arsip";
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Arsip Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function detail(Request $request, $regId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Arsip Permohonan'),
            new BreadcrumbsStruct('Detail'),
        ];
		
		$data_permohonan = [];
		$rest_permohonan = $this->getPermohonanDetail($regId);
		if(isset($rest_permohonan['payload'])){
			$data_permohonan = $rest_permohonan['payload'];
		}
		
		$pane_active = ($request->pane_active != '') ? $request->pane_active : 'jadwal';
		
		
		$rest_pelaporan = $this->getAuditResult();
        $data_pelaporan = null;
		if(isset($rest_pelaporan['payload'])){
			foreach ($rest_pelaporan['payload'] as $d) {
				if($d['id_reg'] == $regId){
					$data_pelaporan = $d;
				}
			}
		}
		
        $parser = ['data_pelaporan' => $data_pelaporan, 'pane_active' => $pane_active, 'view' => $this->view, 'module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan];
        return view("$this->view.detail")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan-audit'                 => $this->ajax_datagrid_permohonan_audit($request),
            'datagrid-jadwal-audit'                 => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-auditor-audit'                 => $this->ajax_datagrid_auditor_audit($request),
            'datagrid-biaya-audit'                 => $this->ajax_datagrid_biaya($request),
            default                    => responseJSON(404, null, "Invalid url!"),
        };
    }
	
	private function ajax_datagrid_biaya(Request $request)
    {
		$data = $this->getBiaya();
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				if($d['id_reg'] == $request->id_reg){
					$x['id_biaya'] = $d['id_biaya'];
					$x['id_reg'] = $d['id_reg'];
					$x['keterangan'] = $d['keterangan'];
					$x['qty'] = $d['qty'];
					$x['harga'] = $d['harga'];
					$x['total'] = $d['total'];
					array_push($result, $x);
				}
			}
		}
        return response()->json(["rows" => $result]);
    }
	
	private function ajax_datagrid_permohonan_audit(Request $request)
    {
		$data_permohonan = $this->getPermohonan('10040');
		
        $result_permohonan = [];
        $result_data = [];
		
		if(isset($data_permohonan['payload'])){
			foreach ($data_permohonan['payload'] as $d) {
				$result_permohonan[$d['id_reg']] = $d;
			}
		}
		
		if(!empty($result_permohonan)){
			foreach ($result_permohonan as $d) {
				$x['id_reg'] = $d['id_reg'];
				$x['nama_pu'] = $d['nama_pu'];
				$x['nama_pu_alt'] = $d['nama_pu_alt'];
				$x['no_daftar'] = $d['no_daftar'];
				$x['tgl_daftar'] = $d['tgl_daftar'];
				$x['nama_jenis_daftar'] = $d['nama_jenis_daftar'];
				$x['nama_jenis_produk'] = $d['nama_jenis_produk'];
				$x['nama_status_reg'] = $d['nama_status_reg'];
				$x['jml_produk'] = $d['jml_produk'];
				$x['nama_jenis_usaha'] = $d['nama_jenis_usaha'];
				$x['nama_lph'] = $d['nama_lph'];
				$x['no_urut_ndpu'] = $d['no_urut_ndpu'];
				$x['no_ndpu'] = $d['no_ndpu'];
				$x['jenis_daftar'] = $d['jenis_daftar'];
				$x['jenis_produk'] = $d['jenis_produk'];
				array_push($result_data, $x);
			}
		}
        

        return response()->json(["rows" => $result_data]);
    }
	
	private function ajax_datagrid_jadwal_audit(Request $request)
    {
		$data = $this->getListJadwalAudit();
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				if($d['id_reg'] == $request->id_reg){
					$x['id_audit'] = $d['id_audit'];
					$x['id_reg'] = $d['id_reg'];
					$x['jadwal_awal'] = $d['jadwal_awal'];
					$x['jadwal_akhir'] = $d['jadwal_akhir'];
					$x['jml_hari'] = $d['jml_hari'];
					array_push($result, $x);
				}
			}
		}
        return response()->json(["rows" => $result]);
    }
	
	private function ajax_datagrid_auditor_audit(Request $request)
    {
		$data = $this->getListJadwalAuditor();
		$data_auditor = $this->getRefAuditor();
		$search_data = [];
        $result = [];
		if(isset($data_auditor['payload'])){
			foreach ($data_auditor['payload'] as $da) {
				$search_data[$da['auditor_id']] = $da;
			}
		}
		
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				if($d['id_reg'] == $request->id_reg){
					$x['auditor_id'] = $d['auditor_id'];
					if (array_key_exists($d['auditor_id'], $search_data)){
						$x['nama_auditor'] = (isset($search_data[$d['auditor_id']]['nama'])) ? $search_data[$d['auditor_id']]['nama'] : '';
					}
					else{
						$x['nama_auditor'] = '';
					}
					$x['id_audit_person'] = $d['id_audit_person'];
					$x['id_reg'] = $d['id_reg'];
					$x['create_by'] = $d['create_by'];
					$x['create_on'] = $d['create_on'];
					array_push($result, $x);
				}
			}
		}
        return response()->json(["rows" => $result]);
    }
}
