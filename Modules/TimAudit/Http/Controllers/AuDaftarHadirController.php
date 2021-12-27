<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SisAuditLks;
use Modules\TimAudit\Http\Traits\AuditorTraits;

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


class AuDaftarHadirController extends Controller
{
    use AuditorTraits;

    public $module = self::class;
    private $url = 'timaudit/auditor/daftar-hadir';
    private $view = "timaudit::auditor_daftar_hardir";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Rapat Akhir'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function unggah(Request $request, $jadwalID)
    {		
		try {
            $dataJadwal  = $this->isKepalaAuditDetail($jadwalID);
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
                new BreadcrumbsStruct('Rapat Akhir'),
				new BreadcrumbsStruct('Kelengkapan'),
			];

            $dataLKS = [
                'jumlah' => ['kritis' => 0, 'mayor' => 0, 'minor' => 0, 'total' => 0],
            ];
            foreach ($dataJadwal as $ja) {
				if(!empty($ja->sis_audit_lks)){foreach ($ja->sis_audit_lks as $lks) {
						switch ($lks->lks_kategori_ketidaksesuaian) {
							case 'kritis':
								// jumlah
								$dataLKS['jumlah']['kritis'] += 1;
								$dataLKS['jumlah']['total']  += 1;
								break;
							case 'mayor':
								// jumlah
								$dataLKS['jumlah']['mayor'] += 1;
								$dataLKS['jumlah']['total'] += 1;
								break;
							case 'minor':
							case 'observasi':
								// jumlah
								$dataLKS['jumlah']['minor'] += 1;
								$dataLKS['jumlah']['total'] += 1;
								break;
						}
					}
				}
            }
			
			$dataAuditTim = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
							->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
							->leftJoin('sis_audit_daftar_periksa', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_daftar_periksa.jadw_tim_id")
							->where('sis_jadwal.jadw_id', '=', $jadwalID)->where('sis_jadwal_tim.jadw_tim_posisi', '!=', 'ppc')->select('*');
			
			
			$dataTimLogbook = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
							->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
							->leftJoin('sis_audit_logbook', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_logbook.jadw_tim_id")
							->where('sis_jadwal.jadw_id', '=', $jadwalID)->select('*');
			
			
			
			$SisJadwalLog = SisJadwalLog::where('jadw_id', $jadwalID)->where('jlog_tipe', 'revisi-temuan')->select('*');
			
            $parser = [
				'module' => $this->module, 
				'url' => $this->url, 
				'breadcrumbs' => $breadcrumbs, 
				'data' => $dataJadwal, 
				'dataLKS' => $dataLKS,
				'dataAuditTim' => $dataAuditTim->get(),
				'dataTimLogbook' => $dataTimLogbook->get(),
				'SisJadwalLog' => $SisJadwalLog->get(),
			];

            return view("$this->view.unggah")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
	
    public function storeUnggah(Request $request, $jadwalID)
    {
		$request->validate([
            'cust_nama' => 'required',
            'cust_email' => 'required',
            'user_id' => 'required',
            'cust_id' => 'required',
            'jadw_id' => 'required',
            'jadw_tanggal_rapat_akhir' => 'required|string',
        ]);
		
        $newFilePath = [];
        $oldFilePath = [];
        try {
            $dataJadwal = $this->isKepalaAudit($jadwalID);
            if (!empty($dataJadwal->jadw_file_kehadiran)) array_push($oldFilePath, $dataJadwal->jadw_file_kehadiran);

            $baseFileUpload = sprintf(config("app.path_file_audit"), $dataJadwal->jadw_id);
            if ($request->hasFile('jadw_file_kehadiran')) {
                $fileKehadiran     = $request->file('jadw_file_kehadiran');
                $fileKehadiranName = Str::slug('file-kehadiran-' . $request['jadw_tim_id'] . '-' . $fileKehadiran->getClientOriginalName()) . '-' . time() . '.' . $fileKehadiran->getClientOriginalExtension();
                $fileKehadiranPath = sprintf("%s/%s", $baseFileUpload, $fileKehadiranName);
                $fileKehadiran->move($baseFileUpload, $fileKehadiranName);

                $dataJadwal->jadw_file_kehadiran = $fileKehadiranPath;
                array_push($newFilePath, public_path($fileKehadiranPath));
            }
			
			$dataJadwal->jadw_setujui_temuan = 'diajukan';
			$dataJadwal->jadw_tanggal_rapat_akhir = $request['jadw_tanggal_rapat_akhir'];
			
            $dataJadwal->save();
            foreach ($oldFilePath as $path) { // remove old file
                @unlink($path);
            }
			
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = 'Tinjauan Rapat Akhir';
			$notifStruct->message   = sprintf("Silahkan konfirmasi tinjauan Rapat akhir untuk temuan dari LKS yang sudah ditemukan pada jadwal No #%s.", $request['jadw_id']);
			$notifStruct->user_id   = $request['user_id'];
			$notifStruct->click_url = url('/pelanggan/audit');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Tinjauan rapat Akhir";
			$structEmail->body    = view($this->view.'.mails.publish')
				->with([
					'nama'       => $request['cust_nama'],
					'message'       => sprintf("Silahkan konfirmasi tinjauan Rapat akhir untuk temuan dari LKS yang sudah ditemukan pada jadwal No #%s.", $request['jadw_id']),
					'link_verif'        => url('/pelanggan/audit'),
				])->render();
			$structEmail->to      = $request['cust_email'];
			sendEmail($structEmail);
			
            return redirect(url($this->url))->with('message', "Unggah berhasil");
        } catch (Exception $e) {
            foreach ($newFilePath as $path) { // remove new file uploaded
                @unlink($path);
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
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
        $data->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        });

        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->leftJoin('sis_audit_daftar_periksa', function ($join) {
            $join->on("sis_audit_daftar_periksa.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
        });

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'on-going');
        $data->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua']);
        $data->whereIn('sis_jadwal.jadw_setujui_temuan', ['diajukan', 'revisi', 'none']);
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');

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
        // Total
        $total = $data->select(DB::raw('count(distinct sis_jadwal.jadw_id) as total'))->first()->total;

        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $isUploaded = !empty($d->jadw_file_kehadiran);

            $x['is_uploaded']          = $isUploaded;
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_setujui_temuan']              = $d->jadw_setujui_temuan;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = ucwords($d->jadw_jenis);
            $x['total_jadwal']         = $d->sis_jadwal_audits->count();
            array_push($result, $x);
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function detail(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'lap-ringkas' => $this->detail_lap_ringkas($request),
            'lap-lengkap' => $this->detail_lap_lengkap($request),
            default         => null,
        };
    }
	
	public function detail_lap_ringkas(Request $request)
    {
		try {
			$dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])->where('sis_jadwal_tim.jadw_tim_posisi', 'ketua');
			$dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
			$dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataJadwal->join('sis_audit_lap_ringkas', "sis_jadwal.jadw_id", "=", "sis_audit_lap_ringkas.jadw_id");
			$dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataJadwal->join("sis_jadwal_tim", "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', komodt_nama) SEPARATOR ',<br/>' ) AS komodt_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_nomor_referensi) SEPARATOR ',<br/>' ) AS jadw_audit_nomor_referensi");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_standart_acuan) SEPARATOR ',<br/>' ) AS jadw_audit_standart_acuan");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_ruang_lingkup) SEPARATOR ',<br/>' ) AS jadw_audit_ruang_lingkup");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_tujuan_audit) SEPARATOR ',<br/>' ) AS jadw_audit_tujuan_audit");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_kegiatan) SEPARATOR ',<br/>') as jadw_audit_kegiatan");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT(peg_nama) SEPARATOR ', ') as peg_nama");
			$dataJadwal->groupBy('sis_jadwal.jadw_id');
			
			
			$dataLks = SisAuditLks::where('sis_audit_lks.jadw_id', $request['jadw_id'])->groupBy('lks_kategori_ketidaksesuaian');
			$dataLks->selectRaw("lks_kategori_ketidaksesuaian as lks_kategori_ketidaksesuaian");
			$dataLks->selectRaw("GROUP_CONCAT(DISTINCT lks_klausul_ketidaksesuaian SEPARATOR ',') as lks_klausul_ketidaksesuaian");
			$dataLks->selectRaw("GROUP_CONCAT(DISTINCT lks_nomor SEPARATOR ',') as lks_nomor");
			$dataLks->selectRaw("MAX(lks_expired_date_perbaikan) as lks_expired_date_perbaikan");
			$dataLks->selectRaw("COUNT(DISTINCT lks_id) as lks_jumlah");
			
			if(!isset($dataJadwal->get()[0])){
				 return redirect()->back()->withErrors(['message' => 'Laporan belum diisi.']);
			}
			else{
				$parser = [
					'module' => $this->module, 
					'url' => $this->url,
					'dataJadwal' => $dataJadwal->get()[0],
					'dataLks' => $dataLks->get(),
				];
				return view("$this->view.detail.lap_ringkas")->with($parser);
			}
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
	}
	
	public function detail_lap_lengkap(Request $request)
    {
		try {
			$dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
			$dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
			$dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
			$dataJadwal->join('sis_audit_lap_lengkap', "sis_jadwal.jadw_id", "=", "sis_audit_lap_lengkap.jadw_id");
			$dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
			$dataJadwal->join("sis_jadwal_tim", "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
			$dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_kegiatan) SEPARATOR ',<br/>' ) AS jadw_audit_kegiatan");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', komodt_nama) SEPARATOR ',<br/>' ) AS komodt_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT(jadw_audit_nomor_referensi) SEPARATOR ',' ) AS jadw_audit_nomor_referensi");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_standart_acuan) SEPARATOR ',<br/>' ) AS jadw_audit_standart_acuan");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_ruang_lingkup) SEPARATOR ',<br/>' ) AS jadw_audit_ruang_lingkup");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', jadw_audit_tujuan_audit) SEPARATOR ',<br/>' ) AS jadw_audit_tujuan_audit");
			
        $dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT IF(sis_jadwal_tim.jadw_tim_posisi = 'ketua', CONCAT(peg_nama), '') SEPARATOR ', ') as ketua");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT IF(sis_jadwal_tim.jadw_tim_posisi != 'ketua', CONCAT(peg_nama, '(', jadw_tim_posisi , ')'), '') SEPARATOR ', ') as anggota");
			$dataJadwal->groupBy('sis_jadwal.jadw_id');
			
			
			$dataLks = SisAuditLks::where('sis_audit_lks.jadw_id', $request['jadw_id'])->select("*");
			$sumLKS = [
                'kritis' => 0, 'mayor' => 0, 'minor' => 0, 'total' => 0
            ];
            foreach ($dataLks->get() as $lks) {
				switch ($lks->lks_kategori_ketidaksesuaian) {
					case 'kritis':
						// jumlah
						$sumLKS['kritis'] += 1;
						$sumLKS['total']  += 1;
						break;
					case 'mayor':
						// jumlah
						$sumLKS['mayor'] += 1;
						$sumLKS['total'] += 1;
						break;
					case 'minor':
						// jumlah
						$sumLKS['minor'] += 1;
						$sumLKS['total'] += 1;
						break;
					case 'observasi':
						// jumlah
						$sumLKS['observasi'] += 1;
						$sumLKS['total'] += 1;
						break;
				}
            }
			
			if(!isset($dataJadwal->get()[0])){
				 return redirect()->back()->withErrors(['message' => 'Laporan belum diisi.']);
			}
			else{
				$parser = [
					'module' => $this->module, 
					'url' => $this->url,
					'dataJadwal' => $dataJadwal->get()[0],
					'dataLks' => $dataLks->get(),
					'sumLKS' => $sumLKS,
				];
				
				return view("$this->view.detail.lap_lengkap")->with($parser);
				}
			
			
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
	}
}
