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

class ManageBiayaController extends Controller
{
    use ServiceSihalalTrait;
	
    public $module = self::class;
    private $url = 'sihalal/biaya';
    private $view = "sihalal::biaya";
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Biaya'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function detail(Request $request, $regId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Biaya'),
            new BreadcrumbsStruct('Detail Biaya'),
        ];
		
		$data_permohonan = [];
		$rest_permohonan = $this->getPermohonanDetail($regId);
		if(isset($rest_permohonan['payload'])){
			$data_permohonan = $rest_permohonan['payload'];
		}
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan];
        return view("$this->view.detail")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan-biaya'                 => $this->ajax_datagrid_permohonan_biaya($request),
            'datagrid-biaya'                 => $this->ajax_datagrid_biaya($request),
            default                    => responseJSON(404, null, "Invalid url"),
        };
    }
	
	public function addBiaya(Request $request)
    {
        $request->validate([
            // "id_reg" => 'required',
        ]);
        try {            
			/*
			$rest = $this->postUpdatePermohonan('Ajuan', $request['id_reg']);
			if(isset($rest['status'])){
				if($rest['status'] == 200){
					return responseJSON(200, [], 'Berhasil menyimpan data.');
				}
				else{
					return responseJSON(500, [], $rest['message']);
				}
			}
			else{
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
			} 
			*/
            
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
	public function updateBiaya(Request $request)
    {
        $request->validate([
            // "id_reg" => 'required',
        ]);
        try {            
			/*
			$rest = $this->postUpdatePermohonan('Ajuan', $request['id_reg']);
			if(isset($rest['status'])){
				if($rest['status'] == 200){
					return responseJSON(200, [], 'Berhasil menyimpan data.');
				}
				else{
					return responseJSON(500, [], $rest['message']);
				}
			}
			else{
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
			} 
			*/
            
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
	public function deleteBiaya(Request $request)
    {
        $request->validate([
            // "id_reg" => 'required',
        ]);
        try {            
			/*
			$rest = $this->postUpdatePermohonan('Ajuan', $request['id_reg']);
			if(isset($rest['status'])){
				if($rest['status'] == 200){
					return responseJSON(200, [], 'Berhasil menyimpan data.');
				}
				else{
					return responseJSON(500, [], $rest['message']);
				}
			}
			else{
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
			} 
			*/
            
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
	public function updateStatus(Request $request)
    {
        $request->validate([
            "id_reg" => 'required',
        ]);
        try {
			$rest = $this->postUpdatePermohonan('Biaya', $request['id_reg']);
			if(isset($rest['status'])){
				if($rest['status'] == 200){
					return responseJSON(200, [], 'Berhasil menyimpan data.');
				}
				else{
					return responseJSON(500, [], $rest['message']);
				}
			}
			else{
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
			} 
            
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
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
	
	private function ajax_datagrid_permohonan_biaya(Request $request)
    {
		$data = $this->getPermohonan('10020');
		
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
}
