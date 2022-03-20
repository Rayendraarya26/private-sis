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

class ManageAuditController extends Controller
{
    use ServiceSihalalTrait;
	
    public $module = self::class;
    private $url = 'sihalal/audit';
    private $view = "sihalal::audit";
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Audit'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function detail(Request $request, $regId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Manajemen Audit'),
            new BreadcrumbsStruct('Detail'),
        ];
		
		$data_permohonan = [];
		$rest_permohonan = $this->getPermohonanDetail($regId);
		if(isset($rest_permohonan['payload'])){
			$data_permohonan = $rest_permohonan['payload'];
		}
		
		$pane_active = ($request->pane_active != '') ? $request->pane_active : 'jadwal';
		
        $parser = ['pane_active' => $pane_active, 'view' => $this->view, 'module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan];
        return view("$this->view.detail")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan-audit'                 => $this->ajax_datagrid_permohonan_audit($request),
            'datagrid-jadwal-audit'                 => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-auditor-audit'                 => $this->ajax_datagrid_auditor_audit($request),
            default                    => responseJSON(404, null, "Invalid url"),
        };
    }
	
	private function ajax_datagrid_permohonan_audit(Request $request)
    {
		$data = $this->getPermohonan('10030');
		
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
	
	private function ajax_datagrid_jadwal_audit(Request $request)
    {
		$data = $this->getListJadawlAudit();
		
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
	
	public function addJadwal(Request $request)
    {
		$request->validate([
            "id_reg"			=> 'required',
            "jadwal_awal"		=> 'required',
            "jadwal_akhir"		=> 'required',
            "jml_hari"			=> 'required',
        ]);
		
		try {
			$data_save = [
				'id_reg' => $request->id_reg
				, 'jadwal_awal' => $request->jadwal_awal
				, 'jadwal_akhir' => $request->jadwal_akhir
				, 'jml_hari' => $request->jml_hari
			];
			
			$this->postAddListJadawlAudit($data_save);
			
			return redirect($this->url."/detail/$request->id_reg?pane_active=jadwal")->with('message', "Jadwal berhasil disimpan untuk reg_id #" . $request->id_reg . " sudah berhasil disimpan.");
		} catch (Exception $e) {
			return redirect($this->url."/detail/$request->id_reg?pane_active=jadwal")->with('message', $e->getMessage());
        }
    }
	
	public function updateJadwal(Request $request)
    {
        $request->validate([
            "id_reg"			=> 'required',
            "jadwal_awal"		=> 'required',
            "jadwal_akhir"		=> 'required',
            "jml_hari"			=> 'required',
            "id_audit"             => 'required',
        ]);
		
		try {
			$data_save = [
				'id_reg' => $request->id_reg
				, 'jadwal_awal' => $request->jadwal_awal
				, 'jadwal_akhir' => $request->jadwal_akhir
				, 'jml_hari' => $request->jml_hari
			];
			
			$this->putUpdateListJadawlAudit($data_save, $request->id_audit);
			
			return redirect($this->url."/detail/$request->id_reg?pane_active=jadwal")->with('message', "Jadwal berhasil disimpan untuk reg_id #" . $request->id_reg . " sudah berhasil disimpan.");
		} catch (Exception $e) {
			return redirect($this->url."/detail/$request->id_reg?pane_active=jadwal")->with('message', $e->getMessage());
        }
    }
	
	public function destroyJadwal(Request $request)
    {
         try {
            $status_return = TRUE;
            if(!empty($request->ids)){
                foreach ($request->ids as $id) {
					$rest = $this->deleteListJadawlAudit($id);
                    if ($rest) {

                    } else {
                        $status_return = FALSE;
                        break;
                    }
                }
            } else{
                $status_return = FALSE;
            }


            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data, data belum dipilih atau kesalahan system, silahkan ulangi lagi.");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
