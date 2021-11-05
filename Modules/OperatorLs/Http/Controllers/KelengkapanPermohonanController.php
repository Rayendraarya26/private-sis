<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KelengkapanPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/kelengkapan-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Kelengkapan Permohonan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::kelengkapan_permohonan.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan' => $this->ajax_datagrid_permohonan($request),
            default               => null,
        };
    }

    private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->where('mohon_approved_status', '=', 'accepted');
        $data->where('mohon_verif_kajian_permohonan_pjt', '=', 'ya');
        $data->where('mohon_verif_kajian_permohonan_paskal', '=', 'ya');
        $data->whereNull('mohon_pernyataan_persetujuan_file');
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
            $x['status_step'] = '';
            if (is_null($d->mohon_pernyataan_persetujuan_file) && is_null($d->mohon_spk_file)) {
                $x['status_step'] = 'verifikasi';
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

    public function detail(Request $request, $mohonID)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'upload_file' => $this->detail_upload_file($request, $mohonID),
            default       => null,
        };

    }

    private function detail_upload_file(Request $request, $mohonID)
    {
        $dataPermohon = SisPermohonan::where('mohon_id', $mohonID)->where('mohon_approved_status', '=', 'accepted')->where('mohon_verif_kajian_permohonan_pjt', '=', 'ya')->where('mohon_verif_kajian_permohonan_paskal', '=', 'ya');
        $dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');

        $dataPermohon->leftJoin('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id');
        $dataPermohon->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id');
        $dataPermohon->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id');
        $dataPermohon->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id');
        $dataPermohon->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');
        $dataPermohon->select('*');
        $breadcrumbs          = [
            new BreadcrumbsStruct('Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Kelengkapan Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $mohonID . '"'),
        ];
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

        $dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID)->where('status_tipe', 'revisi');
        $dataPermohonanStatus->select('*');

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
        return view('operatorls::kelengkapan_permohonan.detail_upload_file')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['status' => 'required']);
        return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'persetujuan' => $this->edit_upload_persetujuan($request),
            default       => null,
        };
    }

    private function edit_upload_persetujuan(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Kelengkapan Permohonan', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $request['mohon_id'] . '"', url($this->url . '/' . 'detail/' . $request['mohon_id'] . '?action=upload_file')),
            new BreadcrumbsStruct('Upload Pernyataan Persetujuan "#' . $request['mohon_id'] . '"'),
        ];

        $dataPermohon = SisPermohonan::where('mohon_id', $request['mohon_id']);
        $dataPermohon->join('master_sertifikasi', 'master_sertifikasi.sert_id', '=', 'sis_permohonan.sert_id');
        $dataPermohon->select('*');

        $parser = [
            'module'       => $this->module,
            'url'          => $this->url,
            'dataPermohon' => $dataPermohon->get()[0],
            'breadcrumbs'  => $breadcrumbs
        ];
        return view("operatorls::kelengkapan_permohonan.edit_upload")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-persetujuan' => $this->update_file_persetujuan($request),
            default              => null,
        };
    }


    private function update_file_persetujuan(Request $request)
    {
        $request->validate([
            'mohon_id'                          => 'required|integer',
            'status_tipe'                       => 'required|string',
            'mohon_pernyataan_persetujuan_file' => 'required|mimes:pdf'
        ]);

        $dataInsert = [
            'mohon_id'                          => $request->mohon_id,
            'mohon_pernyataan_persetujuan_file' => $request->mohon_pernyataan_persetujuan_file,
            'status_mohon_id'                   => $request->mohon_id,
            'status_tipe'                       => $request->status_tipe,
            'status_pesan'                      => 'Lembaga sertifikasi telah mengupload data Pernyataan Persetujuan untuk nomor #' . $request->mohon_id . ' telah diupload.',
            'status_judul'                      => 'Informasi Pengajuan Permohonan'
        ];

        if ($request->hasFile("mohon_pernyataan_persetujuan_file")) {
            $file     = $request->file('mohon_pernyataan_persetujuan_file');
            $namaFile = Str::slug($request->mohon_id) . '_pernyataan_persetujuan_file_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = sprintf(config("app.path_file_pengajuan"), $request->mohon_id);
            $file->move(public_path($path), $namaFile);
            $dataInsert['mohon_pernyataan_persetujuan_file'] = $path . '/' . $namaFile;

            DB::transaction(function () use ($request, $dataInsert) {
                SisPermohonanStatus::create([
                    'status_mohon_id' => $dataInsert['status_mohon_id'],
                    'status_tipe'     => $dataInsert['status_tipe'],
                    'status_pesan'    => $dataInsert['status_pesan'],
                    'status_judul'    => $dataInsert['status_judul']
                ]);
                // Delete User Group
                SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_pernyataan_persetujuan_file' => $dataInsert['mohon_pernyataan_persetujuan_file']]);
            });

            return redirect($this->url)->with('message', "Upload Pernyataan Persetujuan untuk nomor #" . $request->mohon_id . " sudah berhasil disimpan.");
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }
}
