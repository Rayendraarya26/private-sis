<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SysUser;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisPelanggan;

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
use Barryvdh\DomPDF\Facade as PDF;

class AuTahap1VerifController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1-verif';
    private $view = "timaudit::auditor_tahap_1_verif";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Verifikasi Audit Tahap 1'),
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

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('sis_audit_tahap1_detail', "sis_audit_tahap1_detail.aud_thp1_id", "=", "sis_audit_tahap1.aud_thp1_id")
			->leftJoin('sis_audit_tahap1_revisi', "sis_audit_tahap1_revisi.aud_thp1_det_id", "=", "sis_audit_tahap1_detail.aud_thp1_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
			->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->whereIn('sis_audit_tahap1.aud_thp1_status_temuan', ['setuju']);
        $data->where('sis_audit_tahap1.aud_thp1_ditutup', '=' ,'tidak');
        $data->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya');
        $data->whereNotNull('sis_audit_tahap1.aud_thp1_file_jadwal');
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
        $total = $data->select(DB::raw('count(distinct sis_audit_tahap1.aud_thp1_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, ' (' , UPPER(IF(sis_permohonan_detail.cust_sert_id IS NULL, 'baru', 'lama')), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("SUM(IF(thp1_revisi_status='fixed',1,0)) revisi_fixed_total");
        $data->selectRaw("SUM(IF(thp1_revisi_status='open',1,0)) revisi_open_total");
		
        $data->selectRaw("GROUP_CONCAT(DISTINCT thp1_tim_posisi) thp1_tim_posisi");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_audit_tahap1.aud_thp1_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['aud_thp1_status_temuan']	= $d->aud_thp1_status_temuan;
            $x['aud_thp1_status']          = $d->aud_thp1_status;
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['aud_thp1_tanggal_mulai']   = $d->aud_thp1_tanggal_mulai;
            $x['aud_thp1_tanggal_selesai'] = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['jadw_jenis']               = $d->jadw_jenis;
            $x['aud_thp1_jenis']           = $d->aud_thp1_jenis;
            $x['revisi_fixed_total']           = $d->revisi_fixed_total;
            $x['revisi_open_total']           = $d->revisi_open_total;
            $x['thp1_tim_posisi']           = $d->thp1_tim_posisi;
            array_push($result, $x);
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

    public function edit(Request $request)
    {		
		try {
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
				new BreadcrumbsStruct('Verifikasi Audit Tahap 1', url($this->url)),
				new BreadcrumbsStruct('Verifikasi'),
			];

           $dataTahap1 = SisAuditTahap1::with('sis_audit_tahap1_details')->findOrFail($request['aud_thp1_id']);
            $dataKetua = SisAuditTahap1Tim::join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataKetua->where('master_pegawai.user_id', '=', auth()->id());
            $dataKetua->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
            $dataKetua->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua')->get();
			if(!empty($dataKetua)){
				$isKetua = true;
			}
			else{
				$isKetua = false;
			}
			
            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataTahap1 ,'isKetua' => $isKetua ];
            
			return view("$this->view.edit")->with($parser);
        } catch (Exception $e) {
            return redirect(url($this->url))->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'revisi-temuan' => $this->update_revisi_temuan($request),
            'tutup-temuan' => $this->update_tutup_temuan($request),
            'tutup-tahap1' => $this->update_tutup_tahap1($request),
            default        => responseJSON(200, [], 'Nothing Found'),
        };
    }
	
	public function update_tutup_tahap1(Request $request)
    {
        $request->validate([
            'cust_nama'                => 'required',
            'cust_email'               => 'required',
            'user_id'                  => 'required',
            'aud_thp1_id'                  => 'required',
            'aud_thp1_ditutup'                  => 'required',
            // 'file_verifikasi'                  => 'required',
            // 'file_laporan'                  => 'required',
        ]);

        // $newFilePath = [];
        try {
           /*  $baseFileUpload = sprintf(config("app.path_file_tahap1"), $request->aud_thp1_id);
            if ($request->hasFile('file_verifikasi')) {
				$fileVerifikasi     = $request->file('file_verifikasi');
				$fileVerifikasiName = Str::slug('file-file-verifikasi-' . $fileVerifikasi->getClientOriginalName()) . '-' . time() . '.' . $fileVerifikasi->getClientOriginalExtension();
				$fileVerifikasiPath = sprintf("%s/%s", $baseFileUpload, $fileVerifikasiName);
				$fileVerifikasi->move($baseFileUpload, $fileVerifikasiName);
				array_push($newFilePath, $fileVerifikasiPath);
				
				$dataUpdate['aud_thp1_file_temuan'] = $fileVerifikasiPath;
            }
			
			if ($request->hasFile('file_laporan')) {
				$fileLap     = $request->file('file_laporan');
				$fileLapName = Str::slug('file-file-verifikasi-' . $fileLap->getClientOriginalName()) . '-' . time() . '.' . $fileLap->getClientOriginalExtension();
				$fileLapPath = sprintf("%s/%s", $baseFileUpload, $fileLapName);
				$fileLap->move($baseFileUpload, $fileLapName);
				array_push($newFilePath, $fileLapPath);
				
				$dataUpdate['aud_thp1_file_laporan'] = $fileLapPath;
            }
			 */
			DB::beginTransaction();
			DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request->aud_thp1_id)
                ->update([
                    "aud_thp1_ditutup"    => 'ya',
                    // "aud_thp1_file_laporan"    => $dataUpdate['aud_thp1_file_laporan'],
                    // "aud_thp1_file_temuan"    => $dataUpdate['aud_thp1_file_temuan'],
                    "updated_at"          => Carbon::now(),
                ]);
            DB::commit();
			
			$dataUser = SysUser::whereIn('ug_group_id', ['6'])->select('*')->join('sys_user_group', 'ug_user_id', '=','user_id');
			foreach ($dataUser->get() as $us) {
				$notifUsr            = new NotifStruct();
				$notifUsr->title     = 'Informasi Billing';
				$notifUsr->message   = sprintf("Tahap 1 pada jadwal No #%s, telah dinyatakan ditutup seilahkan lanjutkan penjadwalan tahap 2.", $request->aud_thp1_id);
				$notifUsr->user_id   = $us->user_id;
				$notifUsr->click_url = url('/operatorls/penjadwalan');
				sendNotification($notifUsr);
			}
			
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Penutupan Tahap 1';
            $notifStruct->message   = sprintf("Tahap 1 pada jadwal No #%s, telah dinyatakan ditutup seilahkan lanjutkan ke proses tahap 2.", $request->aud_thp1_id);
            $notifStruct->user_id   = $request->user_id;
            $notifStruct->click_url = url('/pelanggan/tahap1/perbaikan-temuan');
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Penutupan Tahap 1";
            $structEmail->body    = view($this->view . '.mails.publish')
                ->with([
                    'nama'       => $request->cust_nama,
                    'message'    => sprintf("Tahap 1 pada jadwal No #%s, telah dinyatakan ditutup seilahkan lanjutkan ke proses tahap 2.", $request->aud_thp1_id),
                    'link_verif' => url('/pelanggan/tahap1/perbaikan-temuan'),
                ])->render();
            $structEmail->to      = $request->cust_email;
            sendEmail($structEmail);

            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            /* foreach ($newFilePath as $path) { // remove new file uploaded
                @unlink($path);
            } */
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
	private function update_tutup_temuan(Request $request)
	{
		$request->validate([
            "aud_thp1_det_id" => 'required',
        ]);
		
        try {
            DB::beginTransaction();
			DB::table('sis_audit_tahap1_detail')
                ->where('aud_thp1_det_id', $request['aud_thp1_det_id'])
                ->update([
                    "aud_thp1_det_status"    => 'closed',
                    "updated_at"          => Carbon::now(),
                ]);
			
			DB::table('sis_audit_tahap1_revisi')
                ->where('aud_thp1_det_id', $request['aud_thp1_det_id'])
                ->update([
                    "thp1_revisi_status"    => 'closed',
                    "updated_at"          => Carbon::now(),
                ]);
            DB::commit();
            return responseJSON(200, ['isConfirmed' => true], []);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
	
	private function update_revisi_temuan(Request $request)
	{
		$request->validate([
            "aud_thp1_det_id" => 'required',
            "aud_thp1_id" => 'required',
            "thp1_revisi_catatan" => 'required',
            "cust_email" => 'required',
            "cust_nama" => 'required',
            "user_id" => 'required',
        ]);
		
        try {
            DB::beginTransaction();
			DB::table('sis_audit_tahap1_detail')
                ->where('aud_thp1_det_id', $request['aud_thp1_det_id'])
                ->update([
                    "aud_thp1_det_status"    => 'proses',
                    "updated_at"          => Carbon::now(),
                ]);
			
			DB::table('sis_audit_tahap1_revisi')
                ->where('aud_thp1_det_id', $request['aud_thp1_det_id'])
                ->update([
                    "thp1_revisi_status"    => 'closed',
                    "updated_at"          => Carbon::now(),
                ]);
			
			DB::table('sis_audit_tahap1_revisi')
                ->insert([
                    "aud_thp1_det_id"    => $request['aud_thp1_det_id'],
                    "thp1_revisi_catatan"    => $request['thp1_revisi_catatan'],
                    "thp1_revisi_status"    => 'open',
                    "created_at"          => Carbon::now(),
                    "updated_at"          => Carbon::now(),
                ]);
            DB::commit();
			
			$notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Revisi Tahap 1';
            $notifStruct->message   = sprintf("Silahkan lakukan perbaikan untuk jadwal tahap 1 no #.", $request['aud_thp1_id']);
            $notifStruct->user_id   = $request['user_id'];
            $notifStruct->click_url = url('/pelanggan/tahap1/perbaikan-temuan');
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Revisi Tahap 1";
            $structEmail->body    = view($this->view . '.mails.publish')
                ->with([
                    'nama'       => $request['cust_nama'],
                    'message'    => sprintf("Silahkan lakukan perbaikan untuk jadwal tahap 1 no #.", $request['aud_thp1_id']),
                    'link_verif' => url('/pelanggan/tahap1/perbaikan-temuan'),
                ])->render();
            $structEmail->to      = $request['cust_email'];
            sendEmail($structEmail);
			
            return responseJSON(200, ['isConfirmed' => true], []);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
	
	public function cetak(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'hasil-tinjauan' => $this->print_hasil_tinjauan($request),
            'lap_lengkap' => $this->print_lap_lengkap($request),
            default        => null,
        };
    }
	
	
	private function print_hasil_tinjauan(Request $request)
	{
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as no_referensi'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as ruang_lingkup'),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
			->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
			->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'restAudit' => $restAudit,
			'dataAuditKlausul' => $dataAuditKlausul->get(),
		];
		
		$dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
			$dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
			$dataTim->select('*');
			$dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
		foreach($dataTim->get() as $tim){
			if($tim->thp1_tim_posisi == 'ketua'){
				$parser['ketua_tim'] = $tim->peg_nama;
				break;
			}
		}
		$parser['dataTim'] = $dataTim->get();
		if($restAudit['sert_tahap1_jenis'] == 'sni'){
			// return view("$this->view.print.hasil_tinjauan_sni")->with($parser);
			$pdf    = PDF::loadView("$this->view.print.hasil_tinjauan_sni", $parser)->setPaper('a4', 'portrait');
			return $pdf->stream();
		}
		else{
			$pdf    = PDF::loadView("$this->view.print.hasil_tinjauan_pusat", $parser)->setPaper('a4', 'portrait');
			return $pdf->stream();
			// return view("$this->view.print.hasil_tinjauan_pusat")->with($parser);
		}
	}
	
	private function print_lap_lengkap(Request $request)
	{
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as mohon_det_no_referensi'),
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT(mohon_kmditi_kapasitas_produksi_tahunan, ' ', mohon_kmditi_kapasitas_produksi_tahunan_satuan)) as produksi"),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
			->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
			->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
		
		$jmlTemuan = 0;
		foreach($dataAuditKlausul->get() as $kla){
			if($kla->aud_thp1_det_is_tinjauan == 'ya'){
				if($kla->aud_thp1_det_hasil_tinjauan == 'no'){
					$jmlTemuan++;
				}
			}
		}
		
		$dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
		$dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
		$dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
		$dataTim->select('*');
		$dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
		
        $parser = [
			'module' => $this->module,
			'url' => $this->url,
			'restAudit' => $restAudit,
			'dataAuditKlausul' => $dataAuditKlausul->get(),
			'dataTim' => $dataTim->get(),
			'jmlTemuan' => $jmlTemuan,
		];
		
		foreach($dataTim->get() as $tim){
			if($tim->thp1_tim_posisi == 'ketua'){
				$parser['ketua_tim'] = $tim->peg_nama;
				$parser['peg_ttd_base64'] = $tim->peg_ttd_base64;
				$parser['peg_ttd_file'] = $tim->peg_ttd_file;
				break;
			}
		}
		
        // return view("$this->view.print.lap_lengkap")->with($parser);
		$pdf    = PDF::loadView("$this->view.print.lap_lengkap", $parser)->setPaper('a4', 'portrait');
		return $pdf->stream();
	}
}
