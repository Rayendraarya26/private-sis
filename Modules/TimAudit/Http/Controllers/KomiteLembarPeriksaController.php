<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisPermohonanStatus;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KomiteLembarPeriksaController extends Controller
{
	public $module = self::class;
    private $url = 'timaudit/komite/lembar-periksa';
    private $view = "timaudit::komite_lembar_periksa";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Lembar Periksa'),
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
            default                 => null,
        };
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

        $data->join('sis_audit_tim_komite', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_audit_tim_komite.komite_posisi', ['ketua']);
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
        $data->where('sis_audit_komite_rekomendasi.rekmd_komte_status', '=', 'ditutup');
        // $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				if($f->field == 'jadw_id')
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
				if($sort[$i] == 'jadw_id')
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
            $x['rekmd_komte_status'] = $d->rekmd_komte_status;
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;

            $x['dftr_periksa_file'] = ($d->dftr_periksa_file != '') ? '<a class="btn-xs btn-success btn-block" target="_blank" href = "' . url($d->dftr_periksa_file) . '"><i class="fas fa-cloud-download"></i> Download</a>' : '';
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'lembar-periksa' => $this->edit_lembar_periksa($request),
            'lihat-rekomendasi' => $this->edit_lihat_rekomendasi($request),
            default                 => null,
        };
    }

    private function edit_lihat_rekomendasi(Request $request)
	{
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
		$dataAudit->selectRaw("CONCAT(upper(jadw_audit_jenis), ' ', sert_nama) AS jenis_jadwal");
		$dataAudit->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', upper(jadw_tim_posisi), ' : ', peg_nama) SEPARATOR '<br/>') AS tim_list");
		$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'kritis' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_kritis");
		$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'mayor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_mayor");
		$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'minor' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_minor");
		$dataAudit->selectRaw("SUM(case when (lks_kategori_ketidaksesuaian = 'observasi' and lks_sudah_ditutup = 'ya') then 1 else 0 end) AS total_observasi");
        $dataAudit->selectRaw("COUNT(*) AS total_data");
        $dataAudit->selectRaw("COUNT(distinct lks_id) AS lks_total"); 
		$dataAudit->groupBy('sis_jadwal_audit.jadw_audit_id');
		
		$dataPPC = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('jadw_tim_posisi', 'ppc');
        $dataPPC->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
		$dataPPC->join('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
		$dataPPC->join('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
		
		$dataPPC->selectRaw("GROUP_CONCAT(distinct peg_nama SEPARATOR ', ') AS peg_nama");
		$dataPPC->selectRaw("GROUP_CONCAT(DISTINCT jadw_audit_sertifikat_nomor ORDER BY sis_jadwal_audit.jadw_audit_id ASC SEPARATOR ', ') AS jadw_audit_sertifikat_nomor");
		$dataPPC->selectRaw("GROUP_CONCAT(DISTINCT jadw_audit_sertifikat_filepath ORDER BY sis_jadwal_audit.jadw_audit_id ASC SEPARATOR '; ') AS jadw_audit_sertifikat_filepath");
		$dataPPC->groupBy('sis_jadwal.jadw_id');
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'dataJadwal' => $dataJadwal->get()[0],
			'dataMohon' => $dataMohon->get(),
			'dataThp1' => $dataThp1->get(),
			'dataAudit' => $dataAudit->get(),
			'dataPPC' => $dataPPC->get(),
		];
        return view("$this->view.edit_lihat_rekomendasi")->with($parser);
	}
	
    private function edit_lembar_periksa(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Lembar Periksa'),
            new BreadcrumbsStruct('Isi Lembar Periksa'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->join('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");
        $dataJadwal->leftJoin('sis_audit_komite_periksa', "sis_audit_komite_periksa.jadw_id", "=", "sis_jadwal.jadw_id");
		
        $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tipe) AS jadw_audit_tipe");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_sni) AS jadw_audit_sni");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_merk) AS jadw_audit_merk");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_nomor_referensi) AS jadw_audit_nomor_referensi");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_nace) AS jadw_audit_kode_nace");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_ea) AS jadw_audit_kode_ea");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_standart_acuan) AS jadw_audit_standart_acuan");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_ruang_lingkup) AS jadw_audit_ruang_lingkup");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tujuan_audit) AS jadw_audit_tujuan_audit");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kegiatan) AS jadw_audit_kegiatan");
        $dataJadwal->groupBy('sis_jadwal.jadw_id');
		
		$dataAudit = SisJadwalAudit::where('sis_jadwal_audit.jadw_id', $request['jadw_id']);
		$dataAudit->where('sis_jadwal_audit.jadw_audit_status_komite', 'submited');
		$dataAudit->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataAudit->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'dataAudit' => $dataAudit->get()];
        return view("$this->view.edit_lembar_periksa")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'lembar-periksa' => $this->update_lembar_periksa($request),
            default                 => null,
        };
    }

    private function update_lembar_periksa(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
            "komte_priksa_penilaian_1" => 'required',
            "komte_priksa_penilaian_2" => 'required',
            "komte_priksa_penilaian_3" => 'required',
            "komte_priksa_penilaian_4" => 'required',
            "komte_priksa_penilaian_5" => 'required',
            "komte_priksa_penilaian_6" => 'required',
            "komte_priksa_penilaian_7" => 'required',
            "komte_priksa_penilaian_8" => 'required',
            "komte_priksa_penilaian_9" => 'required',
            "komte_priksa_penilaian_10" => 'required',
            "komte_priksa_penilaian_11" => 'required',
            "komte_priksa_penilaian_12" => 'required',
            "komte_priksa_penilaian_13" => 'required',
            "status" => 'required',
            'jadw_file_kehadiran_komite' => 'required',
            'jadw_file_kehadiran_komite_lama' => 'nullable'
        ]);
		
        try {
			if (!$request->hasFile('jadw_file_kehadiran_komite')) throw new Exception("Mohon unggah file jadwal", 400);
			
			$dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];
            // DEFINE BASE UPLOAD AND UPDATE jadw_file_kehadiran_komite
            $baseFileUpload = sprintf(config("app.path_file_audit"), $restJadwal->jadw_id);
            $fileData     = $request->file('jadw_file_kehadiran_komite');
            $fileName = Str::slug('file-kehadiran-komite-' . $fileData->getClientOriginalName()) . '-' . time() . '.' . $fileData->getClientOriginalExtension();
            $filePath = sprintf("%s/%s", $baseFileUpload, $fileName);
            $fileData->move($baseFileUpload, $fileName);
			if ($request['jadw_file_kehadiran_komite_lama'] != '') {
                @unlink($request['jadw_file_kehadiran_komite_lama']);
            }
			
            $restDataPeriksa = DB::table('sis_audit_komite_periksa')->where('jadw_id', $request['jadw_id'])->first();
            DB::beginTransaction();
			
			DB::table('sis_jadwal')
                ->where('jadw_id', $request['jadw_id'])
                ->update(['jadw_file_kehadiran_komite' => $filePath]);
			
            if ($restDataPeriksa !== null) {
                DB::table('sis_audit_komite_periksa')
                    ->where('jadw_id', $request['jadw_id'])
                    ->update([
						'komte_priksa_penilaian_1' => $request['komte_priksa_penilaian_1'],
						'komte_priksa_penilaian_2' => $request['komte_priksa_penilaian_2'],
						'komte_priksa_penilaian_3' => $request['komte_priksa_penilaian_3'],
						'komte_priksa_penilaian_4' => $request['komte_priksa_penilaian_4'],
						'komte_priksa_penilaian_5' => $request['komte_priksa_penilaian_5'],
						'komte_priksa_penilaian_6' => $request['komte_priksa_penilaian_6'],
						'komte_priksa_penilaian_7' => $request['komte_priksa_penilaian_7'],
						'komte_priksa_penilaian_8' => $request['komte_priksa_penilaian_8'],
						'komte_priksa_penilaian_9' => $request['komte_priksa_penilaian_9'],
						'komte_priksa_penilaian_10' => $request['komte_priksa_penilaian_10'],
						'komte_priksa_penilaian_11' => $request['komte_priksa_penilaian_11'],
						'komte_priksa_penilaian_12' => $request['komte_priksa_penilaian_12'],
						'komte_priksa_penilaian_13' => $request['komte_priksa_penilaian_13'],
					]);
            } else {
                DB::table('sis_audit_komite_periksa')->insert([
                    'jadw_id' => $request['jadw_id'],
                    'komte_priksa_penilaian_1' => $request['komte_priksa_penilaian_1'],
                    'komte_priksa_penilaian_2' => $request['komte_priksa_penilaian_2'],
                    'komte_priksa_penilaian_3' => $request['komte_priksa_penilaian_3'],
                    'komte_priksa_penilaian_4' => $request['komte_priksa_penilaian_4'],
                    'komte_priksa_penilaian_5' => $request['komte_priksa_penilaian_5'],
                    'komte_priksa_penilaian_6' => $request['komte_priksa_penilaian_6'],
                    'komte_priksa_penilaian_7' => $request['komte_priksa_penilaian_7'],
                    'komte_priksa_penilaian_8' => $request['komte_priksa_penilaian_8'],
                    'komte_priksa_penilaian_9' => $request['komte_priksa_penilaian_9'],
                    'komte_priksa_penilaian_10' => $request['komte_priksa_penilaian_10'],
                    'komte_priksa_penilaian_11' => $request['komte_priksa_penilaian_11'],
                    'komte_priksa_penilaian_12' => $request['komte_priksa_penilaian_12'],
                    'komte_priksa_penilaian_13' => $request['komte_priksa_penilaian_13'],
                ]);
            }
			
			$total_survailent = 0;
			$total_data = 0;
			$mohon_id = [];
			if(!empty($request['status'])){
				foreach($request['status'] as $key => $val){
					$restDataAudit = DB::table('sis_jadwal_audit')
						->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
						->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id")
						->where('jadw_audit_id', $key)
						->first();
						
					 if ($restDataAudit !== null) {
							$total_data++;
							$status = 'on-going';
							if($restDataAudit->jadw_audit_jenis == 'sertifikasi'){
								if($val == 'ya'){
									$status = 'berhak-memperoleh';
								}
								else{
									$status = 'tidak-berhak-menggunakan';
								}
							}
							elseif($restDataAudit->jadw_audit_jenis == 're-sertifikasi'){
								if($val == 'ya'){
									$status = 'berhak-memperoleh-kembali';
								}
								else{
									$status = 'tidak-berhak-menggunakan';
								}
							}
							elseif($restDataAudit->jadw_audit_jenis == 'pengaktifan'){
								$status = 'berhak-memperoleh-kembali';
							}
							elseif($restDataAudit->jadw_audit_jenis == 'pencabutan'){
								$status = 'tidak-berhak-menggunakan';
							}
							elseif($restDataAudit->jadw_audit_jenis == 'surveilans'){
								$total_survailent++;
								if($val == 'ya'){
									$status = 'tetap-dapat-menggunakan';
								}
								else{
									$status = 'tidak-berhak-menggunakan';
								}
							}
							
						DB::table('sis_jadwal_audit')
							->where('jadw_audit_id', $key)
							->update([
								'jadw_audit_status' => $status,
							]);
						
						if(!in_array($restDataAudit->mohon_id, $mohon_id, true)){
							array_push($mohon_id, $restDataAudit->mohon_id);
						}
					}
				}
			}
			
			if($total_survailent == $total_data){
				DB::table('sis_jadwal')
							->where('jadw_id', $request['jadw_id'])
							->update([
								'jadw_is_tutup' => 'ya',
							]);
			}
			
			
            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, ['asd'], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
