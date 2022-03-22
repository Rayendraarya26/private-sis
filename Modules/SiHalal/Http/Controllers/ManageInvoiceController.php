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

class ManageInvoiceController extends Controller
{
    use ServiceSihalalTrait;
	
    public $module = self::class;
    private $url = 'sihalal/invoice';
    private $view = "sihalal::invoice";
	
    public function index()
    {
		$breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Data Invoice'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser); 
    }
	
	public function detail(Request $request, $id)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Data Invoice'),
            new BreadcrumbsStruct('Detail'),
        ];

        $data_permohonan = [];
		$rest_permohonan = $this->getPermohonanDetail($id);
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
            'datagrid-invoice'                 => $this->ajax_datagrid_invoice($request),
            default                    => responseJSON(404, null, "Invalid url"),
        };
    }
	
	private function ajax_datagrid_invoice(Request $request)
    {
        $data = $this->getInvoice();
		
        $result = [];
		if(isset($data['payload'])){
			foreach ($data['payload'] as $d) {
				$x['id_inv'] = $d['id_inv'];
				$x['no_inv'] = $d['no_inv'];
				$x['no_ref'] = $d['no_ref'];
				$x['id_ref'] = $d['id_ref'];
				$x['tgl_inv'] = $d['tgl_inv'];
				$x['tipe_trans'] = $d['tipe_trans'];
				$x['nama_pu'] = $d['nama_pu'];
				$x['ndpu'] = $d['ndpu'];
				$x['alamat1'] = $d['alamat1'];
				$x['alamat2'] = $d['alamat2'];
				$x['alamat3'] = $d['alamat3'];
				$x['No_telp'] = $d['No_telp'];
				$x['gol_prod'] = $d['gol_prod'];
				$x['status'] = $d['status'];
				$x['kategori_transaksi'] = $d['kategori_transaksi'];
				$x['asal'] = $d['asal'];
				$x['duedate'] = $d['duedate'];
				$x['status_payment'] = $d['status_payment'];
				$x['total_inv'] = $d['total_inv'];
				$x['unik_id'] = $d['unik_id'];
				$x['create_by'] = $d['create_by'];
				$x['create_on'] = $d['create_on'];
				$x['update_by'] = $d['update_by'];
				
				$x['update_on'] = $d['update_on'];
				$x['id_pu'] = $d['id_pu'];
				$x['file_inv'] = $d['file_inv'];
				array_push($result, $x);
			}
		}

        return response()->json(["rows" => $result]);
    }
	
	public function update(Request $request)
    {
        $request->validate([
            "id_inv" => 'required',
        ]);
        try {
			return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
