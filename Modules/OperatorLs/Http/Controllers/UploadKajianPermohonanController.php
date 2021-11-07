<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\MasterKodeEa;
use App\Models\BbkkpSis\MasterKodeNace;
use App\Models\BbkkpSis\MasterRuangLingkup;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadKajianPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/kajian-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator LS'),
            new BreadcrumbsStruct('Kajian Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::kajian_permohonan.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan'         => $this->ajax_datagrid_permohonan($request),
            'combobox-kode-ea'            => $this->ajax_combobox_kode_ea($request),
            'combobox-kode-nace'          => $this->ajax_combobox_kode_nace($request),
            'combobox-kode-ruang-lingkup' => $this->ajax_combobox_ruang_lingkup($request),
            default                       => null,
        };
    }

    private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_pjt', ['proses']);
        $data->whereIn('mohon_verif_kajian_permohonan_paskal', ['proses']);
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status_step'] = 'upload';
            if (!is_null($d->mohon_kajian_permohonan_pjt_file)) {
                $x['status_step'] = 're-upload';
            }

            $x['cust_sert_id']       = $d->cust_sert_id;
            $x['mohon_id']           = $d->mohon_id;
            $x['cust_id']            = $d->cust_id;
            $x['user_id']            = $d->user_id;
            $x['sert_id']            = $d->sert_id;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_cust_nama']    = $d->mohon_cust_nama;
            $x['mohon_jenis_status'] = $d->mohon_jenis_status;
            $x['created_at']         = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']          = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_kode_ea(Request $request)
    {
        $data   = MasterKodeEa::select('*');
        $result = [];
        foreach ($data->get() as $d) {
            $x['id']   = $d->kode_ea_id;
            $x['nama'] = $d->kode_ea_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    private function ajax_combobox_kode_nace(Request $request)
    {
        $data   = MasterKodeNace::select('*');
        $result = [];
        foreach ($data->get() as $d) {
            $x['id']   = $d->kode_nace_id;
            $x['nama'] = $d->kode_nace_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    private function ajax_combobox_ruang_lingkup(Request $request)
    {
        $data   = MasterRuangLingkup::select('*');
        $result = [];
        foreach ($data->get() as $d) {
            $x['id']   = $d->ruang_ling_id;
            $x['nama'] = $d->ruang_ling_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    public function detail(Request $request, $mohonID)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'detail-permohonan' => $this->detail_permohonan($request, $mohonID),
            default             => null,
        };
    }

    private function detail_permohonan(Request $request, $mohonID)
    {
        $dataPermohon = SisPermohonan::where('mohon_id', $mohonID);
        $dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');

        $dataPermohon->join('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id');
        $dataPermohon->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id');
        $dataPermohon->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id');
        $dataPermohon->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id');
        $dataPermohon->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');
        $dataPermohon->select('*');

        $dataPermohonKomoditi = SisPermohonanKomoditi::where('mohon_id', $mohonID);
        $dataPermohonKomoditi->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');
        $dataPermohonKomoditi->select('*');


        $dataPermohonPabrik = SisPermohonanPabrik::where('mohon_id', $mohonID);
        $dataPermohonPabrik->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan_pabrik.kab_id');
        $dataPermohonPabrik->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan_pabrik.kec_id');
        $dataPermohonPabrik->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan_pabrik.prov_id');
        $dataPermohonPabrik->select('*');

        $dataPermohonanDokumen = SisPermohonanDokumen::where('mohon_id', $mohonID);
        $dataPermohonanDokumen->join('master_jenis_dok_perusahaan', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id', '=', 'sis_permohonan_dokumen.jenis_dok_perusahaan_id');
        $dataPermohonanDokumen->select('*');

        $dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID);
        $dataPermohonanStatus->select('*');

        $breadcrumbs = [
            new BreadcrumbsStruct('Operator LS'),
            new BreadcrumbsStruct('Kajian Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $mohonID . '"'),
        ];

        $parser = [
            'module'                => $this->module,
            'url'                   => $this->url,
            'dataPermohon'          => $dataPermohon->get()[0],
            'dataPermohonKomoditi'  => $dataPermohonKomoditi->get(),
            'dataPermohonPabrik'    => $dataPermohonPabrik->get(),
            'dataPermohonanDokumen' => $dataPermohonanDokumen->get(),
            'dataPermohonanStatus'  => $dataPermohonanStatus->get(),
            'breadcrumbs'           => $breadcrumbs
        ];
        return view('operatorls::kajian_permohonan.detail_permohonan')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['status' => 'required']);
        return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'upload-kajian-permohonan' => $this->edit_upload_kajian_permohonan($request),
            default                    => null,
        };
    }

    private function edit_upload_kajian_permohonan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator LS'),
            new BreadcrumbsStruct('Kajian Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $request['mohon_id'] . '"', url($this->url . '/' . 'detail/' . $request['mohon_id'] . '?action=detail-permohonan')),
            new BreadcrumbsStruct('Upload Kajian Permohonan "#' . $request['mohon_id'] . '"'),
        ];

        $dataPermohon = SisPermohonan::where('mohon_id', $request['mohon_id']);
        $dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');
        $dataPermohon->select('*');

        $dataPermohonKomoditi = SisPermohonanKomoditi::where('mohon_id', $request['mohon_id']);
        $dataPermohonKomoditi->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');
        $dataPermohonKomoditi->select('*');

        $parser = [
            'module'               => $this->module,
            'url'                  => $this->url,
            'dataPermohon'         => $dataPermohon->get()[0],
            'dataPermohonKomoditi' => $dataPermohonKomoditi->get(),
            'breadcrumbs'          => $breadcrumbs
        ];
        return view("operatorls::kajian_permohonan.edit_upload_kajian_permohonan")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-upload-kajian-permohonan' => $this->update_upload_kajian_permohonan($request),
            default                           => null,
        };
    }

    private function update_upload_kajian_permohonan(Request $request)
    {
        $request->validate([
            'mohon_id'                          => 'required|integer',
            'mohon_perlu_tahap1'                => 'required|string',
            'status_tipe'                       => 'required|string',
            'mohon_kmditi_ruang_lingkup'        => 'required|array|min:1',
            'mohon_kmditi_nace'                 => 'required|array|min:1',
            'mohon_kmditi_ea'                   => 'required|array|min:1',
            'mohon_kajian_permohonan_file_lama' => 'nullable|string',
            'mohon_kajian_permohonan_file'      => 'required|file|mimes:doc,pdf,docx,zip,xls,xlsx'
        ]);

        $dataInsert = [
            'mohon_id'                   => $request->mohon_id,
            'mohon_perlu_tahap1'         => $request->mohon_perlu_tahap1,
            'status_mohon_id'            => $request->mohon_id,
            'mohon_kmditi_ruang_lingkup' => $request->mohon_kmditi_ruang_lingkup,
            'mohon_kmditi_nace'          => $request->mohon_kmditi_nace,
            'mohon_kmditi_ea'            => $request->mohon_kmditi_ea,
        ];

        if ($request->hasFile("mohon_kajian_permohonan_file")) {
            if ($request["mohon_kajian_permohonan_file_lama"] != '')
                @unlink($request["mohon_kajian_permohonan_file_lama"]);

            $file     = $request->file('mohon_kajian_permohonan_file');
            $namaFile = Str::slug($request->mohon_id) . '_kajian_permohonan_file_pjt_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = sprintf(config("app.path_file_pengajuan"), $request->mohon_id);
            $file->move(public_path($path), $namaFile);
            $dataInsert['mohon_kajian_permohonan_pjt_file'] = $path . '/' . $namaFile;

            DB::transaction(function () use ($request, $dataInsert) {
                SisPermohonan::findOrFail($request['mohon_id'])->update([
                    'mohon_verif_kajian_permohonan_pjt' => 'proses',
                    'mohon_perlu_tahap1'                => $dataInsert['mohon_perlu_tahap1'],
                    'mohon_kajian_permohonan_pjt_file'  => $dataInsert['mohon_kajian_permohonan_pjt_file'],
                ]);


                if (!empty($dataInsert['mohon_kmditi_ruang_lingkup'])) {
                    foreach ($dataInsert['mohon_kmditi_ruang_lingkup'] as $key => $val) {
                        DB::table('sis_permohonan_komoditi')
                            ->where('mohon_kmditi_id', $key)
                            ->update([
                                "mohon_kmditi_ruang_lingkup" => $val,
                                "mohon_kmditi_nace"          => implode(';', $dataInsert['mohon_kmditi_nace'][$key]),
                                "mohon_kmditi_ea"            => $dataInsert['mohon_kmditi_ea'][$key],
                            ]);
                    }
                }
            });

            return redirect($this->url)->with('message', "Upload Kajian Permohonan #" . $request->mohon_id . " telah disimpan, silahkan menunggu konfirmasi validasi oleh PJT.");
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }
}
