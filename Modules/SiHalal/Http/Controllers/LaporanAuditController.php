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

class LaporanAuditController extends Controller
{
    use ServiceSihalalTrait;
	
    public $module = self::class;
    private $url = 'sihalal/laporan';
    private $view = "sihalal::laporan";
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Laporan Audit'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function detail(Request $request, $regId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Laporan Audit'),
            new BreadcrumbsStruct('Detail Laporan Audit'),
        ];
		
		$data_permohonan = [];
		$rest_permohonan = $this->getPermohonanDetail($regId);
		if(isset($rest_permohonan['payload'])){
			$data_permohonan = $rest_permohonan['payload'];
		}
		
        $parser = ['view' => $this->view, 'module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan];
        return view("$this->view.detail")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan'                 => $this->ajax_datagrid_permohonan($request),
            'datagrid-hasil-audit'                 => $this->ajax_datagrid_hasil_audit($request),
            default                    => responseJSON(404, null, "Invalid url"),
        };
    }
	
	private function ajax_datagrid_hasil_audit(Request $request)
    {
		$data = $this->getAuditResult();
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				if($d['id_reg'] == $request->id_reg){
					$x['id_audit_hasil'] = $d['id_audit_hasil'];
					$x['id_reg'] = $d['id_reg'];
					$x['tgl_selesai'] = $d['tgl_selesai'];
					$x['keterangan'] = $d['keterangan'];
					$x['hasil_audit'] = $d['hasil_audit'];
					array_push($result, $x);
				}
			}
		}
        
        return response()->json(["rows" => $result]);
    }
	
	private function ajax_datagrid_permohonan(Request $request)
    {
		$data = $this->getPermohonan('10040');
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
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
				array_push($result, $x);
			}
		}

        return response()->json(["rows" => $result]);
    }
	
	public function prosesAudit1(Request $request)
    {
		$request->validate([
            "id_reg"			=> 'required',
            "tgl_selesai"		=> 'required',
            "keterangan"		=> 'required',
            "hasil_audit"			=> 'required',
        ]);
		
		try {
			$data_save = [
				'id_reg' => $request->id_reg
				, 'tgl_selesai' => $request->tgl_selesai
				, 'keterangan' => $request->keterangan
				, 'hasil_audit' => $request->hasil_audit
			];
			
			$this->postProsesAudit1($data_save);
			
			return redirect($this->url."/detail/$request->id_reg")->with('message', "Laporan audit berhasil disimpan untuk reg_id #" . $request->id_reg . " sudah berhasil disimpan.");
		} catch (Exception $e) {
			return redirect($this->url."/detail/$request->id_reg")->with('message', $e->getMessage());
        }
    }
}
