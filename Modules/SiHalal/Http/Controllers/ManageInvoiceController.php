<?php

namespace Modules\SiHalal\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EasyuiDatagridBuilder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
            'datagrid-invoice' => $this->ajax_datagrid_invoice($request),
            default            => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid_invoice(Request $request)
    {
        $data = $this->getInvoice();

        $result = [];
        if (isset($data['payload'])) {
            foreach ($data['payload'] as $d) {
                $x['id_inv']             = $d['id_inv'];
                $x['no_inv']             = $d['no_inv'];
                $x['no_ref']             = $d['no_ref'];
                $x['id_ref']             = $d['id_ref'];
                $x['tgl_inv']            = $d['tgl_inv'];
                $x['tipe_trans']         = $d['tipe_trans'];
                $x['nama_pu']            = $d['nama_pu'];
                $x['ndpu']               = $d['ndpu'];
                $x['alamat1']            = $d['alamat1'];
                $x['alamat2']            = $d['alamat2'];
                $x['alamat3']            = $d['alamat3'];
                $x['No_telp']            = $d['No_telp'];
                $x['gol_prod']           = $d['gol_prod'];
                $x['Status']             = $d['Status'];
                $x['kategori_transaksi'] = $d['kategori_transaksi'];
                $x['asal']               = $d['asal'];
                $x['duedate']            = $d['duedate'];
                $x['status_payment']     = $d['status_payment'] == 'SB004' ? 'Lunas' : 'Tidak Lunas';
                $x['total_inv']          = $d['total_inv'];
                $x['unik_id']            = $d['unik_id'];
                $x['create_by']          = $d['create_by'];
                $x['create_on']          = $d['create_on'];
                $x['update_by']          = $d['update_by'];

                $x['update_on'] = $d['update_on'];
                $x['id_pu']     = $d['id_pu'];
                $x['file_inv']  = $d['file_inv'];
                $x['no_daftar'] = $d['no_daftar'];
                $result[] = $x;
            }
        }

        $datagridBuilder = new EasyuiDatagridBuilder($result);
        $datagridBuilder->take($request['page'] ?? 1, $request['rows'] ?? 10);

        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $datagridBuilder->search($f->field, $f->value);
            }
        }

        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            $sorter = [];
            for ($i = 0; $i < count($sort); $i++) {
                $sorter[] = [$sort[$i], $order[$i]];
            }
            $datagridBuilder->sort($sorter);
        }

        return response()->json($datagridBuilder->toArray());
    }

    public function update(Request $request)
    {
        $request->validate(["id_inv" => 'required']);

        try {
            $rest = $this->putInvoiceLunas($request['id_inv']);
            if (isset($rest['status_code'])) {
                return responseJSON(500, [], 'Gagal untuk diubah menjadi "Lunas". => ' . $rest['message']);
            } else {
                return responseJSON(200, [], 'Berhasil menyimpan data.');
            }
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
