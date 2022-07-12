<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisAuditKomiteRekomendasi;
use App\Models\BbkkpSis\SisAuditKomiteRekomendasiFiles;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekomPersetujuanController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/rekomendasi-persetujuan';
    private $view = "operatorls::rekom_persetujuan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Rekomendasi Persetujuan'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            'tinymce-uploadimage'   => $this->ajax_tinymce_uploadimage($request),
            'dropzone-getfile'      => $this->ajax_dropzone_get($request),
            'dropzone-uploadfile'   => $this->ajax_dropzone_upload($request),
            'dropzone-deletefile'   => $this->ajax_dropzone_delete($request),
            default                 => null,
        };
    }

    private function ajax_dropzone_get(Request $request)
    {
        $request->validate(['jadw_id' => 'required']);
        $dataRekom = SisAuditKomiteRekomendasi::with('sis_audit_komite_rekomendasi_files')
            ->where('jadw_id', $request['jadw_id'])->first();

        $results = [];
		if(isset($dataRekom->sis_audit_komite_rekomendasi_files)){
			foreach ($dataRekom->sis_audit_komite_rekomendasi_files as $file) {
				$results[] = [
					'file_id'   => encrypt($file->rekmdfile_id),
					'file_name' => $file->rekmdfile_name,
					'file_url'  => asset($file->rekmdfile_path),
					'file_size' => $file->rekmdfile_size_byte,
				];
			}
		}
        

        return responseJSON(200, $results, "Data ditemukan");
    }

    private function ajax_dropzone_upload(Request $request)
    {
        $uploadedFile = [];
        try {
            $request->validate([
                'file'    => 'required|mimetypes:image/jpeg,image/png,image/jpg,application/pdf',
                'jadw_id' => 'required'
            ]);

            $dataRekom = SisAuditKomiteRekomendasi::firstOrCreate(
                ['jadw_id' => $request['jadw_id']],
                ['rekmd_komte_status' => 'on-going']
            );

            $baseFileUpload = sprintf(config("app.path_file_audit"), $request['jadw_id']);
            $fileData       = $request->file('file');
            $fileOriName    = $fileData->getClientOriginalName();
            $fileSize       = $fileData->getSize();
            $fileExtension  = $fileData->getClientOriginalExtension();
            $fileName       = Str::slug('file-rekomendasi-persetujuan-' . $fileData->getClientOriginalName()) . '-' . time() . '.' . $fileData->getClientOriginalExtension();
            $filePath       = sprintf("%s/%s", $baseFileUpload, $fileName);
            $fileData->move($baseFileUpload, $fileName);
            $uploadedFile[] = public_path($filePath);

            SisAuditKomiteRekomendasiFiles::create([
                'rekmd_komte_id'       => $dataRekom->rekmd_komte_id,
                'rekmdfile_name'       => $fileOriName,
                'rekmdfile_path'       => $filePath,
                'rekmdfile_size_byte'  => $fileSize,
                'rekmdfile_extension'  => $fileExtension,
                'rekmdfile_created_at' => Carbon::now(),
                'rekmdfile_updated_at' => Carbon::now(),
                'rekmdfile_created_id' => auth()->id(),
                'rekmdfile_updated_id' => auth()->id(),
            ]);

            return responseJSON(200, [], 'Upload Success');
        } catch (Exception $e) {
            foreach ($uploadedFile as $file) {
                @unlink($file);
            }
            return responseJSON(500, [], "Failed to upload: " . $e->getMessage());
        }
    }

    private function ajax_dropzone_delete(Request $request)
    {
        $request->validate([
            'jadw_id' => 'required',
            'file_id' => 'required',
        ]);
        try {
            $fileId   = decrypt($request['file_id']);
            $data     = SisAuditKomiteRekomendasiFiles::find($fileId);
            $fileName = $data->rekmdfile_name;
            unlink(public_path($data->rekmdfile_path));
            $data->delete();
            return responseJSON(200, [], sprintf('Delete %s success', $fileName));
        } catch (Exception $e) {
            return responseJSON(500, [], 'Failed to delete');
        }
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

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->leftJoin('sis_audit_lap_lengkap', "sis_audit_lap_lengkap.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->leftJoin('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');

        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'jadw_id')
                    $data->where('sis_jadwal.jadw_id', 'LIKE', '%' . $f->value . '%');
                else
                    $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if ($sort[$i] == 'jadw_id')
                    $data->orderBy('sis_jadwal.jadw_id', $order[$i]);
                else
                    $data->orderBy($sort[$i], $order[$i]);
            }
        }

        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', UPPER(jadw_audit_jenis) ) SEPARATOR ',<br/>') AS jadw_audit_jenis");
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
			if($d->lap_lengkp_id != ''){
				$x['lap_lengkp_id']   = ($d->lap_lengkp_verifikasi_status == 'ya') ? true : false;
			}
			else{
				 $x['lap_lengkp_id']   = false;
			}
            $x['rekmd_komte_status']   = ($d->rekmd_komte_status != '') ? $d->rekmd_komte_status : 'on-going';
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;

            $x['dftr_periksa_file'] = ($d->dftr_periksa_file != '') ? '<a target="_blank" href = "' . url($d->dftr_periksa_file) . '"><i class="fas fa-download"></i> Download</a>' : '';
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'rekomendasi'       => $this->edit_rekomendasi($request),
            'lihat-rekomendasi' => $this->edit_lihat_rekomendasi($request),
            default             => null,
        };
    }

    private function edit_lihat_rekomendasi(Request $request)
    {
		try {
            $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
			$dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataJadwal->leftJoin('sis_audit_tim_komite', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->leftJoin('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
			$dataJadwal->leftJoin('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tipe) AS jadw_audit_tipe");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_sni) AS jadw_audit_sni");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_nomor_referensi) AS jadw_audit_nomor_referensi");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_nace) AS jadw_audit_kode_nace");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_ea) AS jadw_audit_kode_ea");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_standart_acuan) AS jadw_audit_standart_acuan");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_ruang_lingkup) AS jadw_audit_ruang_lingkup");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tujuan_audit) AS jadw_audit_tujuan_audit");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct komite_posisi) AS komite_posisi");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kegiatan) AS jadw_audit_kegiatan");
			$dataJadwal->selectRaw("MAX(lks_tanggal_ditutup) AS lks_tanggal_ditutup");
			$dataJadwal->groupBy('sis_jadwal.jadw_id');

			$dataMohon = SisJadwalAudit::where('sis_jadwal_audit.jadw_id', $request['jadw_id']);
			$dataMohon->join('sis_permohonan', "sis_permohonan.mohon_id", "=", "sis_jadwal_audit.mohon_id");
			$dataMohon->groupBy('sis_permohonan.mohon_id');

			$dataThp1 = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataThp1->join('sis_billing', 'sis_billing.bill_id', '=', 'sis_jadwal.bill_id');
			$dataThp1->join('sis_audit_tahap1', 'sis_billing.bill_id', '=', 'sis_audit_tahap1.bill_id');
			$dataThp1->leftJoin('sis_audit_tahap1_detail', 'sis_audit_tahap1_detail.aud_thp1_id', '=', 'sis_audit_tahap1.aud_thp1_id');
			$dataThp1->leftJoin('sis_audit_tahap1_tim', 'sis_audit_tahap1_tim.aud_thp1_id', '=', 'sis_audit_tahap1.aud_thp1_id');
			$dataThp1->leftJoin('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_audit_tahap1_tim.peg_id');
			$dataThp1->select("sis_audit_tahap1.*");
			$dataThp1->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', upper(thp1_tim_posisi), ' : ', peg_nama) SEPARATOR '<br/>') AS tim_list");
			$dataThp1->selectRaw("SUM(case when aud_thp1_det_hasil_tinjauan = 'no' then 1 else 0 end) AS total_temuan");
			$dataThp1->selectRaw("COUNT(*) AS total_data");
			$dataThp1->selectRaw("COUNT(distinct aud_thp1_det_id) AS total_det");
			$dataThp1->groupBy('sis_audit_tahap1.aud_thp1_id');

			$dataAudit = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataAudit->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataAudit->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataAudit->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataAudit->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataAudit->leftJoin('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
			$dataAudit->leftJoin('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
			$dataAudit->select("*");
			$dataAudit->selectRaw("GROUP_CONCAT(distinct CONCAT(upper(jadw_audit_jenis), ' ', sert_nama)) AS jenis_jadwal");
			$dataAudit->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', upper(jadw_tim_posisi), ' : ', peg_nama) SEPARATOR '<br/>') AS tim_list");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'kritis' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_kritis");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'mayor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_mayor");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'minor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_minor");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'observasi' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_observasi");
			$dataAudit->selectRaw("COUNT(*) AS total_data");
			$dataAudit->selectRaw("COUNT(distinct lks_id) AS lks_total");
			$dataAudit->groupBy('sis_jadwal_audit.jadw_id');

			$dataPPC = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('jadw_tim_posisi', 'ppc');
			$dataPPC->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataPPC->join('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
			$dataPPC->join('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
			$dataPPC->selectRaw("GROUP_CONCAT(distinct peg_nama SEPARATOR ', ') AS peg_nama");
			$dataPPC->groupBy('sis_jadwal.jadw_id');

			$dataSertifikat = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('prod_sert_status_hasil', 'memenuhi');
			$dataSertifikat->join('sis_audit_sertifikat_produk', "sis_jadwal.jadw_id", "=", "sis_audit_sertifikat_produk.jadw_id");

			$parser = [
				'module'         => $this->module,
				'url'            => $this->url,
				'dataJadwal'     => $dataJadwal->get()[0],
				'dataMohon'      => $dataMohon->get(),
				'dataThp1'       => $dataThp1->get(),
				'dataAudit'      => $dataAudit->get(),
				'dataPPC'        => $dataPPC->get(),
				'dataSertifikat' => $dataSertifikat->get(),
			];
			// return view("$this->view.print.rekomendasi")->with($parser);

			$pdf = PDF::loadView("$this->view.print.rekomendasi", $parser)
				->setPaper('a4', 'portrait');
			return $pdf->stream();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    private function edit_rekomendasi(Request $request)
    {
		try {
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Komite', url($this->url)),
				new BreadcrumbsStruct('Rekomendasi Persetujuan', url($this->url)),
				new BreadcrumbsStruct('Isi Rekomendasi'),
			];

			$dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
			$dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataJadwal->leftJoin('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");

			$dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tipe) AS jadw_audit_tipe");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_sni) AS jadw_audit_sni");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_nomor_referensi) AS jadw_audit_nomor_referensi");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_nace) AS jadw_audit_kode_nace");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_ea) AS jadw_audit_kode_ea");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_standart_acuan) AS jadw_audit_standart_acuan");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_ruang_lingkup) AS jadw_audit_ruang_lingkup");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tujuan_audit) AS jadw_audit_tujuan_audit");
			$dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kegiatan) AS jadw_audit_kegiatan");
			$dataJadwal->selectRaw("MAX(lks_expired_date_perbaikan) AS lks_expired_date_perbaikan");
			$dataJadwal->groupBy('sis_jadwal.jadw_id');

			$dataMohon = SisJadwalAudit::where('sis_jadwal_audit.jadw_id', $request['jadw_id']);
			$dataMohon->join('sis_permohonan', "sis_permohonan.mohon_id", "=", "sis_jadwal_audit.mohon_id");
			$dataMohon->groupBy('sis_permohonan.mohon_id');

			$dataThp1 = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataThp1->join('sis_billing', 'sis_billing.bill_id', '=', 'sis_jadwal.bill_id');
			$dataThp1->join('sis_audit_tahap1', 'sis_billing.bill_id', '=', 'sis_audit_tahap1.bill_id');
			$dataThp1->join('sis_audit_tahap1_detail', 'sis_audit_tahap1_detail.aud_thp1_id', '=', 'sis_audit_tahap1.aud_thp1_id');
			$dataThp1->join('sis_audit_tahap1_tim', 'sis_audit_tahap1_tim.aud_thp1_id', '=', 'sis_audit_tahap1.aud_thp1_id');
			$dataThp1->join('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_audit_tahap1_tim.peg_id');
			$dataThp1->select("sis_audit_tahap1.*");
			$dataThp1->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', upper(thp1_tim_posisi), ' : ', peg_nama) SEPARATOR '<br/>') AS tim_list");
			$dataThp1->selectRaw("SUM(case when aud_thp1_det_hasil_tinjauan = 'no' then 1 else 0 end) AS total_temuan");
			$dataThp1->selectRaw("COUNT(*) AS total_data");
			$dataThp1->selectRaw("COUNT(distinct aud_thp1_det_id) AS total_det");
			$dataThp1->groupBy('sis_audit_tahap1.aud_thp1_id');

			$dataAudit = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataAudit->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataAudit->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataAudit->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataAudit->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataAudit->leftJoin('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
			$dataAudit->leftJoin('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
			$dataAudit->select("*");
			$dataAudit->selectRaw("GROUP_CONCAT(distinct CONCAT(upper(jadw_audit_jenis), ' ', sert_nama)) AS jenis_jadwal");
			$dataAudit->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', upper(jadw_tim_posisi), ' : ', peg_nama) SEPARATOR '<br/>') AS tim_list");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'kritis' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_kritis");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'mayor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_mayor");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'minor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_minor");
			$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'observasi' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_observasi");
			$dataAudit->selectRaw("COUNT(*) AS total_data");
			$dataAudit->selectRaw("COUNT(distinct lks_id) AS lks_total");
			$dataAudit->groupBy('sis_jadwal_audit.jadw_id');

			$dataPPC = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('jadw_tim_posisi', 'ppc');
			$dataPPC->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataPPC->leftJoin('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
			$dataPPC->leftJoin('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
			$dataPPC->selectRaw("GROUP_CONCAT(DISTINCT peg_nama SEPARATOR ', ') AS peg_nama");
			$dataPPC->groupBy('sis_jadwal.jadw_id');

			$dataSertifikat = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('prod_sert_status_hasil', 'memenuhi');
			$dataSertifikat->join('sis_audit_sertifikat_produk', "sis_jadwal.jadw_id", "=", "sis_audit_sertifikat_produk.jadw_id");

			$dataAuditTim = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
				->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
				->leftJoin('sis_audit_daftar_periksa', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_daftar_periksa.jadw_tim_id")
				->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->where('sis_jadwal_tim.jadw_tim_posisi', '!=', 'ppc')->select('*');

			$dataTimLogbook = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
				->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
				->leftJoin('sis_audit_logbook', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_logbook.jadw_tim_id")
				->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');

			$dataFilePpc = SisJadwal::join('sis_audit_ppc', "sis_jadwal.jadw_id", "=", "sis_audit_ppc.jadw_id")
				->where('sis_jadwal.jadw_id', '=', $request['jadw_id'])->select('*');

			$parser = [
				'module'         => $this->module,
				'url'            => $this->url,
				'breadcrumbs'    => $breadcrumbs,
				'dataJadwal'     => $dataJadwal->get()[0],
				'dataMohon'      => $dataMohon->get(),
				'dataThp1'       => $dataThp1->get(),
				'dataAudit'      => $dataAudit->get(),
				'dataPPC'        => $dataPPC->get(),
				'dataSertifikat' => $dataSertifikat->get(),
				'dataAuditTim'   => $dataAuditTim->get(),
				'dataTimLogbook' => $dataTimLogbook->get(),
				'dataFilePpc'    => $dataFilePpc->get(),
			];
			return view("$this->view.edit_rekomendasi")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'rekomendasi' => $this->update_rekomendasi($request),
            default       => null,
        };
    }

    private function update_rekomendasi(Request $request)
    {
        $request->validate([
            "jadw_id"            => 'required',
            "rekmd_komte_isi"    => 'required',
            "rekmd_komte_status" => 'required',
        ]);

        try {
            DB::beginTransaction();
            /* SisAuditKomiteRekomendasi::updateOrCreate([
                ['jadw_id' => $request['jadw_id']],
                [
                    'rekmd_komte_isi'    => $request['rekmd_komte_isi'],
                    'rekmd_komte_status' => $request['rekmd_komte_status'],
                ]
            ]); */
			$data_rekomendasi = SisAuditKomiteRekomendasi::where('jadw_id', $request->jadw_id)->select('jadw_id')->first();
			if($data_rekomendasi?->jadw_id){
				DB::table('sis_audit_komite_rekomendasi')
											->where('jadw_id', $request->jadw_id)
											->update([
												'rekmd_komte_isi'    => $request['rekmd_komte_isi'],
												'rekmd_komte_status' => $request['rekmd_komte_status'],
											]);
			}
			else{
				DB::table('sis_audit_komite_rekomendasi')
											->insert([
												'jadw_id'    => $request['jadw_id'],
												'rekmd_komte_isi'    => $request['rekmd_komte_isi'],
												'rekmd_komte_status' => $request['rekmd_komte_status'],
											]);
			}

            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
