<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomiteRekomPersetujuanController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/komite/rekomendasi-persetujuan';
    private $view = "timaudit::komite_rekom_persetujuan";

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
        $data->leftJoin('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_audit_tim_komite.komite_posisi', ['ketua']);
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
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
		
		$data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
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
            'rekomendasi' => $this->edit_rekomendasi($request),
            'lihat-rekomendasi' => $this->edit_lihat_rekomendasi($request),
            default                 => null,
        };
    }
	
	private function edit_lihat_rekomendasi(Request $request)
	{
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
        $dataJadwal->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_audit_id", "=", "sis_jadwal_audit.jadw_audit_id");
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
        $dataAudit->leftJoin('sis_audit_lks', "sis_audit_lks.jadw_audit_id", "=", "sis_jadwal_audit.jadw_audit_id");
		$dataAudit->join('sis_jadwal_tim', 'sis_jadwal_tim.jadw_id', '=', 'sis_jadwal.jadw_id');
		$dataAudit->join('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_jadwal_tim.peg_id');
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
		$dataPPC->selectRaw("GROUP_CONCAT(distinct jadw_audit_sertifikat_nomor SEPARATOR ', ') AS jadw_audit_sertifikat_nomor");
		$dataPPC->groupBy('sis_jadwal.jadw_id');
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'breadcrumbs' => $breadcrumbs,
			'dataJadwal' => $dataJadwal->get()[0],
			'dataMohon' => $dataMohon->get(),
			'dataThp1' => $dataThp1->get(),
			'dataAudit' => $dataAudit->get(),
			'dataPPC' => $dataPPC->get(),
		];
        return view("$this->view.edit_lihat_rekomendasi")->with($parser);
	}

    private function edit_rekomendasi(Request $request)
    {
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
        $dataJadwal->groupBy('sis_jadwal.jadw_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("$this->view.edit_rekomendasi")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'rekomendasi' => $this->update_rekomendasi($request),
            default                 => null,
        };
    }

    private function update_rekomendasi(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
            "rekmd_komte_isi" => 'required',
            "rekmd_komte_status" => 'required',
        ]);

        $uploadedPath = [];
        try {
            DB::beginTransaction();
            $restData = DB::table('sis_audit_komite_rekomendasi')->where('jadw_id', $request['jadw_id'])->first();
            if ($restData !== null) {
                DB::table('sis_audit_komite_rekomendasi')
                    ->where('jadw_id', $request['jadw_id'])
                    ->update(['rekmd_komte_isi' => $request['rekmd_komte_isi'], 'rekmd_komte_status' => $request['rekmd_komte_status'], ]);
            } else {
                DB::table('sis_audit_komite_rekomendasi')->insert([
                    'jadw_id' => $request['jadw_id'],
                    'rekmd_komte_isi' => $request['rekmd_komte_isi'],
                    'rekmd_komte_status' => $request['rekmd_komte_status'],
                ]);
            }
			
            // Notifikasi
            /*
             */
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
