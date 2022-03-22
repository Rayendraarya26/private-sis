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
            'combobox-auditor'                 => $this->ajax_combobox_auditor($request),
            default                    => responseJSON(404, null, "Invalid url!"),
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
	
	private function ajax_combobox_auditor(Request $request)
    {
		$data = $this->getRefAuditor();
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				$x['id'] = $d['auditor_id'];
				$x['text'] = $d['nama'];
				array_push($result, $x);
			}
		}
        return response()->json($result);
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
			
			$this->postAddListJadwalAudit($data_save);
			
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
			
			$this->putUpdateListJadwalAudit($data_save, $request->id_audit);
			
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
					$rest = $this->deleteListJadwalAudit($id);
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
	
	public function addAuditor(Request $request)
    {
		$request->validate([
            "id_reg"			=> 'required',
            "auditor_id"		=> 'required',
        ]);
		
		try {
			$data_save = [
				'id_reg' => $request->id_reg
				, 'auditor_id' => $request->auditor_id
				, 'create_by' => 'LPH00000XX'
			];
			
			$this->postAddListJadwalAuditor($data_save);
			
			return redirect($this->url."/detail/$request->id_reg?pane_active=auditor")->with('message', "Auditor berhasil disimpan untuk reg_id #" . $request->id_reg . " sudah berhasil disimpan.");
		} catch (Exception $e) {
			return redirect($this->url."/detail/$request->id_reg?pane_active=auditor")->with('message', $e->getMessage());
        }
    }
	
	public function destroyAuditor(Request $request)
    {
         try {
            $status_return = TRUE;
            if(!empty($request->ids)){
                foreach ($request->ids as $id) {
					$rest = $this->deleteListJadwalAuditor($id);
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
	
	public function updateStatus(Request $request)
    {
        $request->validate([
            "id_reg" => 'required',
        ]);
        try {
			$rest = $this->postUpdatePermohonan('Periksa', $request['id_reg']);
			if(isset($rest['status'])){
				if($rest['status'] == 200){
					return responseJSON(200, [], 'Berhasil menyimpan data.');
				}
				else{
					return responseJSON(500, [], $rest['message']);
				}
			}
			else{
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Periksa".');
			} 
            
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
