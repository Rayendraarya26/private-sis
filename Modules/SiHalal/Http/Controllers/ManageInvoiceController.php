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
		$result = [];

        return response()->json(["rows" => $result]);
    }
	
	public function update(Request $request)
    {
        $request->validate([
            "id_reg" => 'required',
        ]);
        try {
			return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
