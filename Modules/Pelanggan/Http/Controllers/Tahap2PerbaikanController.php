<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SisAuditLksFile;
use App\Models\BbkkpSis\SisAuditLksRevisi;
use App\Models\BbkkpSis\SisJadwal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Tahap2PerbaikanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/tahap2/perbaikan-temuan';
    private $view = "pelanggan::tahap2_perbaikan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Tahpa 2'),
            new BreadcrumbsStruct('Perbaikan Temuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function temuanLKS(Request $request, $jadwalID)
    {
        $data = SisJadwal::with(['sis_audit_lks.sis_jadwal_tim'])
            ->where('sis_jadwal.jadw_id', $jadwalID)
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->first();

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Audit', url($this->url)),
            new BreadcrumbsStruct('Temuan LKS'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.temuan_lks")->with($parser);
    }

    public function detailAllLKS(Request $request, $jadwalID)
    {
        try {
            $dataJadwal = SisJadwal::with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims'])->findOrFail($jadwalID);
            $dataLKS    = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
                ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->get();

            $breadcrumbs = [
                new BreadcrumbsStruct('Pelanggan'),
                new BreadcrumbsStruct('Tahap 2'),
                new BreadcrumbsStruct('Persetujuan Temuan', url($this->url)),
                new BreadcrumbsStruct('Detail'),
            ];

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS];
            return view("$this->view.detail_all_lks")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage() . ' | ' . $e->getLine()]);
        }
    }

    public function detailLKS(Request $request, $jadwalID, $lksID)
    {
        $data = SisAuditLks::with(['sis_jadwal_audit.sis_jadwal', 'sis_jadwal_audit.sis_permohonan', 'sis_audit_lks_files'])
            ->join('sis_jadwal_audit', 'sis_jadwal_audit.jadw_audit_id', '=', 'sis_audit_lks.jadw_audit_id')
            ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
            ->where('sis_jadwal.jadw_id', $jadwalID)
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->where('sis_audit_lks.lks_id', $lksID)
            ->firstOrFail();

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Audit', url($this->url)),
            new BreadcrumbsStruct('Detail LKS'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.detail_lks")->with($parser);
    }

    public function perbaikanLKS(Request $request, $jadwalID, $lksID)
    {
        $data = SisAuditLks::with(['sis_jadwal_audit.sis_jadwal', 'sis_jadwal_audit.sis_permohonan', 'sis_jadwal_tim'])
            ->join('sis_jadwal_audit', 'sis_jadwal_audit.jadw_audit_id', '=', 'sis_audit_lks.jadw_audit_id')
            ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
            ->where('sis_jadwal.jadw_id', $jadwalID)
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->where('sis_audit_lks.lks_id', $lksID)
            ->firstOrFail();

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Audit', url($this->url)),
            new BreadcrumbsStruct('Perbaikan LKS'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view("$this->view.perbaikan")->with($parser);
    }

    public function processPerbaikanLKS(Request $request, $jadwalID, $lksID)
    {
        $request->validate([
            'perbaikan_text_analisis'           => 'required',
            'perbaikan_text_koreksi'            => 'required',
            'perbaikan_text_tindakan'           => 'required',
            'perbaikan_text_tindakan_perbaikan' => 'required',
        ]);

        $successDeletedPath = [];
        $failedDeletedPath  = [];
        try {
            $data = SisAuditLks::with(['sis_jadwal_audit.sis_jadwal', 'sis_jadwal_audit.sis_permohonan', 'sis_audit_lks_files'])
                ->join('sis_jadwal_audit', 'sis_jadwal_audit.jadw_audit_id', '=', 'sis_audit_lks.jadw_audit_id')
                ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_audit_lks.lks_id', $lksID)
                ->first();
            if (empty($data)) throw new Exception("Data Audit tidak ditemukan");

            DB::beginTransaction();

            $data->lks_perbaikan_analisa        = $request['perbaikan_text_analisis'];
            $data->lks_perbaikan_koreksi        = $request['perbaikan_text_koreksi'];
            $data->lks_perbaikan_tindakan       = $request['perbaikan_text_tindakan'];
            $data->lks_bukti_tindakan_perbaikan = $request['perbaikan_text_tindakan_perbaikan'];
            $data->lks_status                   = 'fixed';
            $data->save();

            if (count($data->sis_audit_lks_files) > 0) {
                foreach ($data->sis_audit_lks_files as $file) {
                    $successDeletedPath[] = public_path($file->lks_filepath);
                    $file->delete();
                }
            }

            if ($request->hasFile('berkas')) {
                $baseFileUpload = sprintf(config("app.path_file_perbaikan_lks"), $data->lks_id);
                foreach ($request->file('berkas') as $berkas) {
                    $filePebaikan       = $berkas;
                    $filePebaikanName   = Str::slug($filePebaikan->getClientOriginalName()) . '-' . time() . '.' . $filePebaikan->getClientOriginalExtension();
                    $filePebaikanFolder = $baseFileUpload;
                    if (!File::exists($filePebaikanFolder)) File::makeDirectory($filePebaikanFolder, 0777, true, true);
                    $filePebaikan->move($filePebaikanFolder, $filePebaikanName);

                    $newSisLksFile = SisAuditLksFile::create([
                        'lks_id'       => $data->lks_id,
                        'lks_filepath' => sprintf("%s/%s", $filePebaikanFolder, $filePebaikanName),
                        'created_at'   => Carbon::now(),
                    ]);

                    $failedDeletedPath[] = public_path($newSisLksFile->lks_filepath);
                }
            }

            SisAuditLksRevisi::create([
                'lks_id'             => $data->lks_id,
                'lks_revisi_catatan' => sprintf("%s telah memperbaiki LKS", $data->sis_jadwal_audit->sis_permohonan->mohon_cust_nama),
                'lks_revisi_oleh'    => "customer",
            ]);

            DB::commit();
            foreach ($successDeletedPath as $del) {
                @unlink($del);
            }
            return responseJSON(200, [], "Perbaikan LKS berhasil dikirim");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($failedDeletedPath as $del) {
                @unlink($del);
            }
            return responseJSON(500, [], $e->getMessage() . '|err:' . $e->getLine());
        }
    }

    public function savePerbaikanText(Request $request, $jadwalID, $lksID)
    {
        $request->validate([
            'key'   => ['required', Rule::in(['lks_perbaikan_analisa', 'lks_perbaikan_koreksi', 'lks_perbaikan_tindakan', 'lks_bukti_tindakan_perbaikan'])],
            'value' => 'required'
        ]);

        try {
            $data = SisAuditLks::with(['sis_jadwal', 'sis_audit_lks_files'])
                ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_audit_lks.jadw_id')
                ->where('sis_jadwal.jadw_id', $jadwalID)
                ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
                ->where('sis_audit_lks.lks_id', $lksID)
                ->first();
            if (empty($data)) throw new Exception("Data Audit tidak ditemukan");

            DB::beginTransaction();

            $key              = $request['key'];
            $value            = $request['value'];
            $data->$key      = $value;
            $data->lks_status = 'fixed';
            $data->save();

            DB::commit();
            return responseJSON(200, [], "Perbaikan LKS berhasil disimpan");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage() . '|err:' . $e->getLine());
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid'            => $this->ajax_datagrid($request),
            'datagrid_lks'        => $this->ajax_datagrid_lks($request),
            'tinymce-uploadimage' => $this->ajax_tinymce_uploadimage($request),
            default               => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisJadwal::with(['sis_jadwal_tims.master_pegawai', 'sis_jadwal_audits'])
            ->with([
                'sis_jadwal_tims' => function ($query) {
                    $query->orderBy(DB::raw("FIELD(jadw_tim_posisi, 'ketua', 'auditor', 'ppc', 'observer')"));
                }
            ])
            ->where('jadw_setujui_temuan', 'setuju')
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id);
        // Filter
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
            $timAudit = [];
            foreach ($d->sis_jadwal_tims as $tim) {
                $timAudit[] = [
                    "tim_nama"   => $tim->master_pegawai->peg_nama,
                    'tim_kode'   => $tim->jadw_tim_kode,
                    'tim_posisi' => ucwords($tim->jadw_tim_posisi),
                ];
            }
            $jadwalAudit = [];
            foreach ($d->sis_jadwal_audits as $jadwal) {
                $jadwalAudit[] = [
                    'jadw_audit_jenis'            => ucwords($jadwal->jadw_audit_jenis),
                    'jadw_audit_nomor_sertifikat' => $jadwal->jadw_audit_nomor_sertifikat,
                    'jadw_audit_nomor_referensi'  => $jadwal->jadw_audit_nomor_referensi,
                ];
            }

            $x['tims']             = $timAudit;
            $x['audits']           = $jadwalAudit;
            $x['jadw_id']          = $d->jadw_id;
            $x['jadw_jenis']       = $d->jadw_jenis;
            $x['jadw_file_jadwal'] = asset($d->jadw_file_jadwal);

            if ($d->jadw_tanggal_mulai == $d->jadw_tanggal_selesai) {
                $x['tanggal'] = sprintf("%s", $d->jadw_tanggal_mulai->isoFormat("LL"));
            } else {
                $x['tanggal'] = sprintf("%s s/d %s", $d->jadw_tanggal_mulai->isoFormat("LL"), $d->jadw_tanggal_selesai->isoFormat("LL"));
            }
            $result[] = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_lks(Request $request)
    {
        $request->validate(['jadwal_id' => 'required|integer']);

        $data = SisAuditLks::with(['sis_audit_lks_revisis', 'sis_audit_lks_files'])
            ->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_audit_lks.jadw_id')
            ->where('sis_jadwal.jadw_id', $request['jadwal_id'])
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id);
        // Filter
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
            $x['jadw_id'] = $d->jadw_id;

            $x['jadw_audit_nomor_referensi'] = $d->jadw_audit_nomor_referensi;
            $x['jadw_audit_ruang_lingkup']   = $d->jadw_audit_ruang_lingkup;
            $x['jadw_audit_kegiatan']        = $d->jadw_audit_kegiatan;
            $x['jadw_audit_nomor_referensi'] = $d->jadw_audit_nomor_referensi;
            $x['jadw_audit_kode_nace']       = $d->jadw_audit_kode_nace;

            $x['lks_id']                       = $d->lks_id;
            $x['lks_sudah_ditutup']            = $d->lks_sudah_ditutup;
            $x['lks_status']                   = $d->lks_status;
            $x['lks_kategori_ketidaksesuaian'] = $d->lks_kategori_ketidaksesuaian;
            $x['lks_input_date_perbaikan']     = $d->lks_input_date_perbaikan;
            $x['lks_perbaikan_analisa']        = $d->lks_perbaikan_analisa;
            $x['lks_perbaikan_koreksi']        = $d->lks_perbaikan_koreksi;
            $x['lks_perbaikan_tindakan']       = $d->lks_perbaikan_tindakan;
            $x['lks_expired_date_perbaikan']   = $d->lks_expired_date_perbaikan?->format('Y-m-d H:i:s');

            $result[] = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_tinymce_uploadimage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimetypes:image/jpeg,image/png|max:1000', // 1MB
            ]);

            $img     = $request->file('file');
            $imgName = $img->hashName();
            $img->move(public_path(config('app.path_file_tinymce')), $imgName);
            $publicUrl = asset(config('app.path_file_tinymce') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }
}
