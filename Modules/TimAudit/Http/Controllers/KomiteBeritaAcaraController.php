<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SisPelanggan;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KomiteBeritaAcaraController extends Controller
{
	public $module = self::class;
    private $url = 'timaudit/komite/berita-acara';
    private $view = "timaudit::komite_berita_acara";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Berita Acara'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            default                 => null,
        };
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
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
        $data->whereIn('sis_audit_tim_komite.komite_posisi', ['ketua']);
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
        $data->where('sis_jadwal_audit.jadw_audit_status', '!=', 'on-going');
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

            $x['dftr_periksa_file'] = ($d->dftr_periksa_file != '') ? '<a target="_blank" href = "' . url($d->dftr_periksa_file) . '"><i class="fas fa-download"></i> Download</a>' : '';
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'berita-acara' => $this->edit_berita_acara($request),
            default                 => null,
        };
    }
	
	public function detail(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'detail-rekomendasi' => $this->detail_rekomendasi($request),
            'detail-periksa' => $this->detail_periksa($request),
            default                 => null,
        };
    }
	
	private function detail_periksa(Request $request)
	{
        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->leftJoin('sis_audit_komite_periksa', "sis_audit_komite_periksa.jadw_id", "=", "sis_jadwal.jadw_id");
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
		
		
		$dataAudit = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataAudit->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataAudit->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataAudit->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
		$dataAudit->select("*"); 
		$dataAudit->groupBy('sis_jadwal_audit.jadw_audit_id');
		
		$dataTim = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
		$dataTim->join('sis_audit_tim_komite', 'sis_audit_tim_komite.jadw_id', '=', 'sis_jadwal.jadw_id');
		$dataTim->join('master_pegawai', 'master_pegawai.peg_id', '=', 'sis_audit_tim_komite.peg_id');
		$dataTim->select("*"); 
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'dataJadwal' => $dataJadwal->get()[0],
			'dataAudit' => $dataAudit->get(),
			'dataTim' => $dataTim->get(),
		];
		
		$pdf    = PDF::loadView("$this->view.print.penilaian", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
	}
	

    private function detail_rekomendasi(Request $request)
	{
        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->join('sis_audit_tim_komite', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
        $dataJadwal->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
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
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'dataJadwal' => $dataJadwal->get()[0],
			'dataMohon' => $dataMohon->get(),
			'dataThp1' => $dataThp1->get(),
			'dataAudit' => $dataAudit->get(),
			'dataPPC' => $dataPPC->get(),
			'dataSertifikat' => $dataSertifikat->get(),
		];
        // return view("$this->view.print.rekomendasi")->with($parser);
		$pdf    = PDF::loadView("$this->view.print.rekomendasi", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
	}
	
    private function edit_berita_acara(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Berita Acara'),
            new BreadcrumbsStruct('Isi Berita Acara'),
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
		
		$dataAudit = SisJadwalAudit::where('sis_jadwal_audit.jadw_id', $request['jadw_id'])->select("*", "sis_jadwal.jadw_id AS jadw_id");
		$dataAudit->where('sis_jadwal_audit.jadw_audit_status_komite', 'submited');
		$dataAudit->where('sis_jadwal_audit.jadw_audit_status','!=' ,'on-going');
		$dataAudit->where('sis_jadwal_audit.jadw_audit_jenis','!=' ,'surveilans');
		$dataAudit->join('sis_jadwal', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataAudit->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
		$dataAudit->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataAudit->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'dataAudit' => $dataAudit->get()];
        return view("$this->view.edit_berita_acara")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'berita-acara' => $this->update_berita_acara($request),
            default                 => null,
        };
    }

    private function update_berita_acara(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
            "cust_id" => 'required',
            "jadw_berita_acara_nomor" => 'required',
            "jadw_berita_acara_tanggal" => 'required',
            "jadw_is_tutup" => 'required',
            "tanggal_terbit" => 'nullable',
            "tanggal_berakhir" => 'nullable',
            "tanggal_perubahan" => 'nullable',
        ]);
        try {
            DB::beginTransaction();
			$mohon_id = [];
			if($request['jadw_is_tutup'] == 'ya'){
				if(!empty($request['tanggal_terbit'])){
					foreach($request['tanggal_terbit'] as $key => $val){
						$restDataAudit = DB::table('sis_jadwal_audit')
							->join('sis_jadwal', 'sis_jadwal.jadw_id', '=', 'sis_jadwal_audit.jadw_id')
							->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id")
							->where('jadw_audit_id', $key)
							->first();
							
						if ($restDataAudit !== null) {
							if($restDataAudit->jadw_audit_jenis == 'sertifikasi'){
								if($restDataAudit->jadw_audit_status == 'berhak-memperoleh'){
									DB::table('sis_pelanggan_sertifikasi')
										->insert([
											'sert_id'  => $restDataAudit->sert_id,
											'cust_id'  => $restDataAudit->cust_id,
											'mohon_id'  => $restDataAudit->mohon_id,
											// digenerate
											'cust_sert_nomor_sertifikat'  => NULL,
											'cust_sert_nomor_referensi'  => $restDataAudit->jadw_audit_nomor_referensi,
											'cust_sert_nomor_sni'  => $restDataAudit->jadw_audit_sni,
											'cust_sert_lingkup'  => $restDataAudit->jadw_audit_ruang_lingkup,
											'kode_ea_nama'  => $restDataAudit->jadw_audit_kode_ea,
											'kode_nace_nama'  => $restDataAudit->jadw_audit_kode_nace,
											'komodt_id'  => $restDataAudit->komodt_id,
											'cust_sert_tipe'  => $restDataAudit->jadw_audit_tipe, 
											'cust_sert_merk' => $restDataAudit->jadw_audit_merk,
											'cust_sert_ukuran' => $restDataAudit->jadw_audit_ukuran,
											'cust_sert_produksi_tahunan'  => $restDataAudit->jadw_audit_kapasitas_produksi_tahunan,
											'cust_sert_produksi_tahunan_satuan'  => $restDataAudit->jadw_audit_kapasitas_produksi_tahunan_satuan,
											'cust_sert_status'  => 'on_going',
											'cust_sert_tgl_sertifikat_awal'  => $val,
											'cust_sert_expired_date'  => isset($request['tanggal_berakhir'][$key]) ? $request['tanggal_berakhir'][$key] : NULL,
											'cust_sert_tgl_sertifikat_perubahan'  => isset($request['tanggal_perubahan'][$key]) ? $request['tanggal_perubahan'][$key] : NULL,
											'cust_sert_survailen_date'  => date('Y-m-d', strtotime('+1 year')),
											'cust_sert_status_survailen'  => 'passed',
										]);
								}
							}
							elseif($restDataAudit->jadw_audit_jenis == 're-sertifikasi'){
								if($restDataAudit->jadw_audit_status == 'berhak-memperoleh-kembali'){
									DB::table('sis_pelanggan_sertifikasi')
										->where('cust_sert_id', $restDataAudit->cust_sert_id)
										->update([
											'cust_sert_status'  => 'on_going',
											'cust_sert_tgl_sertifikat_awal'  => $val,
											'cust_sert_expired_date'  => isset($request['tanggal_berakhir'][$key]) ? $request['tanggal_berakhir'][$key] : NULL,
											'cust_sert_tgl_sertifikat_perubahan'  => isset($request['tanggal_perubahan'][$key]) ? $request['tanggal_perubahan'][$key] : NULL,
											'cust_sert_status_survailen'  => 'passed',
											'cust_sert_survailen_date'  => date('Y-m-d', strtotime('+1 year')),
										]);
								}
								else{
									DB::table('sis_pelanggan_sertifikasi')
										->where('cust_sert_id', $restDataAudit->cust_sert_id)
										->update([
											'cust_sert_status'  => 'dibekukan',
										]);
								}
							}
							else{
								if($restDataAudit->jadw_audit_status != 'tidak-berhak-menggunakan'){
									DB::table('sis_pelanggan_sertifikasi')
										->where('cust_sert_id', $restDataAudit->cust_sert_id)
										->update([
											'cust_sert_status_survailen'  => 'passed',
											'cust_sert_status'  => 'on_going',
											'cust_sert_expired_date'  => isset($request['tanggal_berakhir'][$key]) ? $request['tanggal_berakhir'][$key] : NULL,
											'cust_sert_survailen_date'  => date('Y-m-d', strtotime('+1 year')),
										]);
								}
								else{
									DB::table('sis_pelanggan_sertifikasi')
										->where('cust_sert_id', $restDataAudit->cust_sert_id)
										->update([
											'cust_sert_status'  => 'dibekukan',
										]);
								}
							}
								
							if(!in_array($restDataAudit->mohon_id, $mohon_id, true)){
								array_push($mohon_id, $restDataAudit->mohon_id);
							}
						}
					}
				}
				
				if(!empty($mohon_id)){
					foreach($mohon_id as $val){
						SisPermohonanStatus::create([
							'status_mohon_id' => $val,
							'status_tipe'     => 'informasi',
							'status_pesan'    => 'Data Permohonan anda telah selesai di-audit, silahkan cek hasil audit anda.',
							'status_judul'    => 'Closing Pelaksanaan Audit',
						]);
					}
				}
			}
			
			DB::table('sis_jadwal')
									->where('jadw_id', $request['jadw_id'])
									->update([
										"jadw_berita_acara_nomor" => $request['jadw_berita_acara_nomor'],
										"jadw_berita_acara_tanggal" => $request['jadw_berita_acara_tanggal'],
										"jadw_is_tutup" => $request['jadw_is_tutup']
									]);

            DB::commit();
						
			if($request['jadw_is_tutup'] == 'ya'){
				// Notifikasi
				$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
				// Send Push
				$notifStruct            = new NotifStruct();
				$notifStruct->title     = "Proses Sertifikasi Telah Selesai";
				$notifStruct->message   = sprintf("Berita acara telah diterbitkan, proses sertifikasi anda telah selesai.");
				$notifStruct->user_id   = $data_pelanggan?->user_id;
				$notifStruct->click_url = url('/pelanggan/sertifikasi/data');
				sendNotification($notifStruct);

				// Send Email
				$structEmail          = new EmailStruct();
				$structEmail->subject = "Proses Sertifikasi Telah Selesai";
				$structEmail->body    = view("$this->view.mails.publish")
					->with([
						'nama'       => $data_pelanggan?->cust_nama,
						'message'       => sprintf("Berita acara telah diterbitkan, proses sertifikasi anda telah selesai"),
						'link_verif'        => url('/pelanggan/sertifikasi/data'),
					])->render();
				$structEmail->to      = $data_pelanggan?->cust_email;
				sendEmail($structEmail);
			}
            
			
			
            return responseJSON(200, ['asd'], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
