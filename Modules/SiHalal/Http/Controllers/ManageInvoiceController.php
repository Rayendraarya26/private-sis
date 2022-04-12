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

        $parser = ['view' => $this->view, 'module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser); 
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-invoice'                 => $this->ajax_datagrid_invoice($request),
            default                    => responseJSON(404, null, "Invalid url"),
        };
    }
	
	private function multi_array_search($array, $search)
	{
		$result = array();
		foreach ($array as $key => $value)
		{
		  foreach ($search as $k => $v)
		  {
			$pattern = '/.*' . preg_quote($v) . '.*/i';
			if (!isset($value[$k]) ||  preg_match($pattern, $value[$k]) == false) // $value[$k] != $v ||
			{
			  continue 2;
			}

		  }
		  $result[] = $key;
		}
		return $result;
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
				$x['Status'] = $d['Status'];
				$x['kategori_transaksi'] = $d['kategori_transaksi'];
				$x['asal'] = $d['asal'];
				$x['duedate'] = $d['duedate'];
				$x['status_payment'] = $d['status_payment'] == 'SB004' ? 'Lunas' : 'Tidak Lunas';
				$x['total_inv'] = $d['total_inv'];
				$x['unik_id'] = $d['unik_id'];
				$x['create_by'] = $d['create_by'];
				$x['create_on'] = $d['create_on'];
				$x['update_by'] = $d['update_by'];
				
				$x['update_on'] = $d['update_on'];
				$x['id_pu'] = $d['id_pu'];
				$x['file_inv'] = $d['file_inv'];
				$x['no_daftar'] = $d['no_daftar'];
				array_push($result, $x);
			}
		}
		
		$result_data = $result;
		$where = [];
		if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				$where[$f->field] = $f->value;
            }
			
			$search_array = $this->multi_array_search($result, $where);
			$result_data = [];
			foreach ($search_array as $val) {
				$result_data[] = $result[$val];
			}
        }
		
		$page = !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
		$total = count( $result_data ); //total items in array    
		$limit = !empty( $_GET['rows'] ) ? (int) $_GET['rows'] : 10;; //per page    
		$totalPages = ceil( $total/ $limit ); //calculate total pages
		$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
		$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
		$offset = ($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;

		$response = array_slice( $result_data, $offset, $limit );

        return response()->json(["total" => $total,"rows" => $response]);
    }
	
	public function update(Request $request)
    {
        $request->validate([
            "id_inv" => 'required',
        ]);
		
		
        try {
			$rest = $this->putInvoiceLunas($request['id_inv']);
			if(isset($rest['status_code'])){
				return responseJSON(500, [], 'Gagal untuk diubah menjadi "Lunas". => '. $rest['message']);
			}
			else{
				return responseJSON(200, [], 'Berhasil menyimpan data.');
			}
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
