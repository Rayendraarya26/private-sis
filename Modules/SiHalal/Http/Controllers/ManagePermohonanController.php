<?php

namespace Modules\SiHalal\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SiHalal\Http\Traits\ServiceSihalalTrait;

class ManagePermohonanController extends Controller
{
    use ServiceSihalalTrait;

    public $module = self::class;
    private $url = 'sihalal/permohonan';
    private $view = "sihalal::permohonan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Data Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function detail(Request $request, $regId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('SiHalal'),
            new BreadcrumbsStruct('Data Permohonan'),
            new BreadcrumbsStruct('Detail'),
        ];

        try {
            $data_permohonan = [];
            $rest_permohonan = $this->getPermohonanDetail($regId);
            if ($rest_permohonan['status_code'] != 200) throw new ExpectedException("data permohoanan dengan ID $regId tidak ditemukan");

            if (isset($rest_permohonan['payload'])) {
                $data_permohonan = $rest_permohonan['payload'];
            }

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan];
            return view("$this->view.detail")->with($parser);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan' => $this->ajax_datagrid_permohonan($request),
            default               => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid_permohonan(Request $request)
    {
        $data = $this->getPermohonan('10010');

        $result = [];
        if (isset($data['payload'])) {
            foreach ($data['payload'] as $d) {
                $x['id_reg']            = $d['id_reg'];
                $x['nama_pu']           = $d['nama_pu'];
                $x['nama_pu_alt']       = $d['nama_pu_alt'];
                $x['no_daftar']         = $d['no_daftar'];
                $x['tgl_daftar']        = $d['tgl_daftar'];
                $x['nama_jenis_daftar'] = $d['nama_jenis_daftar'];
                $x['nama_jenis_produk'] = $d['nama_jenis_produk'];
                $x['nama_status_reg']   = $d['nama_status_reg'];
                $x['jml_produk']        = $d['jml_produk'];
                $x['nama_jenis_usaha']  = $d['nama_jenis_usaha'];
                $x['nama_lph']          = $d['nama_lph'];
                $x['no_urut_ndpu']      = $d['no_urut_ndpu'];
                $x['no_ndpu']           = $d['no_ndpu'];
                $x['jenis_daftar']      = $d['jenis_daftar'];
                $x['jenis_produk']      = $d['jenis_produk'];
                $result[]               = $x;
            }
        }

        return response()->json(["rows" => $result]);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id_reg" => 'required',
        ]);
        try {
            $rest = $this->postUpdatePermohonan($request['id_reg']);
            if (isset($rest['status'])) {
                if ($rest['status'] == 200) {
                    return responseJSON(200, [], 'Berhasil menyimpan data.');
                } else {
                    return responseJSON(500, [], $rest['message']);
                }
            } else {
                return responseJSON(500, [], 'Gagal untuk diubah menjadi "Ajuan".');
            }

        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
