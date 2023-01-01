<?php

namespace Modules\SiHalal\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EasyuiDatagridBuilder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
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

        try {
            $data_permohonan = [];
            $rest_permohonan = $this->getPermohonanDetail($regId);
            if ($rest_permohonan['status_code'] != 200) throw new ExpectedException("data permohoanan dengan ID $regId tidak ditemukan");

            if (isset($rest_permohonan['payload'])) {
                $data_permohonan = $rest_permohonan['payload'];
            }

            $rest_pelaporan = $this->getAuditResult();
            $data_pelaporan = null;
            if (isset($rest_pelaporan['payload'])) {
                foreach ($rest_pelaporan['payload'] as $d) {
                    if ($d['id_reg'] == $regId) {
                        $data_pelaporan = $d;
                    }
                }
            }

            $parser = ['view' => $this->view, 'module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_permohonan' => $data_permohonan, 'data_pelaporan' => $data_pelaporan];
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
        $data = $this->getPermohonan('10030');

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
            $sort   = explode(",", $request->sort);
            $order  = explode(",", $request->order);
            $sorter = [];
            for ($i = 0; $i < count($sort); $i++) {
                $sorter[] = [$sort[$i], $order[$i]];
            }
            $datagridBuilder->sort($sorter);
        }

        return response()->json($datagridBuilder->toArray());
    }

    public function prosesAudit1(Request $request)
    {
        $request->validate([
            "id_reg"      => 'required',
            "tgl_selesai" => 'required',
            "keterangan"  => 'required',
            'file_data'   => 'required'
        ]);

        if ($request->hasFile("file_data")) {
            $file     = $request->file('file_data');
            $namaFile = Str::slug($request->id_reg) . '_laporan_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = sprintf(config("app.sihalal_file_upload"), $request->id_reg);
            $file->move(public_path($path), $namaFile);

            $data_save = [
                'id_reg'        => $request->id_reg
                , 'tgl_selesai' => $request->tgl_selesai
                , 'keterangan'  => $request->keterangan
                , 'hasil_audit' => "PR005"
                , 'file'        => $path . '/' . $namaFile
                , 'nama_file'   => $namaFile
            ];

            $rest = $this->postProsesAudit2($data_save);
            if ($rest['status_code'] != 400) {
                return redirect($this->url . "/detail/$request->id_reg")->with('message', "Laporan audit berhasil disimpan untuk reg_id #" . $request->id_reg . " sudah berhasil disimpan.");
            } else {
                return redirect($this->url . "/detail/$request->id_reg")->with('message', $rest['message']);
            }
        } else {
            return redirect()->back($this->url . "/detail/$request->id_reg")->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            "id_reg" => 'required',
        ]);
        try {
            $rest = $this->postUpdatePermohonan($request['id_reg'], 'Periksa');
            if (isset($rest['status'])) {
                if ($rest['status'] == 200) {
                    return responseJSON(200, [], 'Berhasil menyimpan data.');
                } else {
                    return responseJSON(500, [], $rest['message']);
                }
            } else {
                return responseJSON(500, [], 'Gagal untuk diubah menjadi "Periksa".');
            }

        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function get_data_pelaporan(Request $request)
    {
        $data = $this->getAuditResult();

        $result = [];
        if (isset($data['payload'])) {
            foreach ($data['payload'] as $d) {
                if ($d['id_reg'] == $request->id_reg) {
                    $x['id_audit_hasil'] = $d['id_audit_hasil'];
                    $x['id_reg']         = $d['id_reg'];
                    $x['tgl_selesai']    = $d['tgl_selesai'];
                    $x['keterangan']     = $d['keterangan'];
                    $x['hasil_audit']    = $d['hasil_audit'];
                    $result[]            = $x;
                }
            }
        }

        return response()->json(["rows" => $result]);
    }
}
