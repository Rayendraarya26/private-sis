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
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_audit_tim_komite.komite_posisi', ['ketua']);
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        $data->where('sis_audit_komite_rekomendasi.rekmd_komte_status', '=', 'ditutup');
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
            'lembar-periksa' => $this->edit_lembar_periksa($request),
            'lihat-rekomendasi' => $this->edit_lihat_rekomendasi($request),
            default                 => null,
        };
    }

    private function edit_lihat_rekomendasi(Request $request)
	{
		
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
		$dataAudit->where('sis_jadwal_audit.jadw_audit_status', 'on-going');
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
        ]);
        try {
            DB::beginTransaction();
            $restDataPeriksa = DB::table('sis_audit_komite_periksa')->where('jadw_id', $request['jadw_id'])->first();
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
			
			if(!empty($request['status'])){
				foreach($request['status'] as $key => $val){
					$restDataAudit = DB::table('sis_jadwal_audit')->where('jadw_audit_id', $key)->first();
					 if ($restDataAudit !== null) {
							$status = 'on-going';
							if($restDataAudit->jadw_audit_jenis == 'sertifikasi'){
								if($val == 'ya')
									$status = 'berhak-memperoleh';
								else
									$status = 'tidak-berhak-menggunakan';
							}
							elseif($restDataAudit->jadw_audit_jenis == 're-sertifikasi'){
								if($val == 'ya')
									$status = 'berhak-memperoleh-kembali';
								else
									$status = 'tidak-berhak-menggunakan';
							}
							else{
								if($val == 'ya')
									$status = 'tetap-dapat-menggunakan';
								else
									$status = 'tidak-berhak-menggunakan';
							}
							
						DB::table('sis_jadwal_audit')
							->where('jadw_audit_id', $key)
							->update([
								'jadw_audit_status' => $status,
							]);
					}
				}
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
