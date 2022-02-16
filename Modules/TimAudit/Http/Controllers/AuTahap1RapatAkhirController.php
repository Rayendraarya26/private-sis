<?php

namespace Modules\TimAudit\Http\Controllers;

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

class AuTahap1RapatAkhirController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1-rapat-akhir';
    private $view = "timaudit::auditor_tahap_1_rapat_akhir";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1'),
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
			->join('sis_audit_tahap1_detail', "sis_audit_tahap1_detail.aud_thp1_id", "=", "sis_audit_tahap1.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
			->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_audit_tahap1.aud_thp1_status_temuan', '=', 'proses');
        $data->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya');
        $data->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua');
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
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Rapat Penutup'),
        ];

        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_nace) as jadw_audit_kode_nace'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ea) as jadw_audit_kode_ea'),
            DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as jadw_audit_ruang_lingkup'),
            DB::raw('GROUP_CONCAT(distinct sis_audit_tahap1_tim.thp1_tim_posisi) as jadw_tim_posisi'),
            DB::raw('GROUP_CONCAT(distinct master_pegawai.peg_id) as peg_id')
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
			->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
			->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
			->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id")
			->where('master_pegawai.user_id', '=', auth()->id())
			->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya')
			->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua')
			->groupBy('sis_audit_tahap1.aud_thp1_id');

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
		
		$dataLogbook = DB::table('sis_audit_tahap1_tim')->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")->leftJoin('sis_audit_tahap1_logbook', "sis_audit_tahap1_logbook.thp1_tim_id", "=", "sis_audit_tahap1_tim.thp1_tim_id")->where('aud_thp1_id', '=', $request['aud_thp1_id'])->get();
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'dataAuditKlausul' => $dataAuditKlausul->get(), 'dataLogbook' => $dataLogbook];
        return view("$this->view.edit_audit_tahap1")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate([
            "aud_thp1_id"           => 'required',
            "cust_id"               => 'required',
            "sert_id"               => 'required',
            "mohon_id"              => 'required',
            "status_audit"          => 'required',
            "aud_thp1_file_daftar_hadir" => 'required',
            "aud_thp1_notulen" => 'required',
        ]);
		
		$uploadedPathDaftar = [];
        try {
            $baseFileUpload = sprintf(config("app.path_file_tahap1"), $request['aud_thp1_id']);	
            DB::beginTransaction();
			$updateJadwal = [
                    "aud_thp1_status"  => $request['status_audit'],
                    "updated_at"       => Carbon::now(),
                ];
				
			if ($request->hasFile('aud_thp1_file_daftar_hadir')){
				$fileDaftar     = $request->file('aud_thp1_file_daftar_hadir');
				$fileDaftarName = Str::slug('file-jadwal-' . $fileDaftar->getClientOriginalName()) . '-' . time() . '.' . $fileDaftar->getClientOriginalExtension();
				$fileDaftarPath = sprintf("%s/%s", $baseFileUpload, $fileDaftarName);
				$fileDaftar->move($baseFileUpload, $fileDaftarName);
				array_push($uploadedPathDaftar, $fileDaftarPath);
				$updateJadwal['aud_thp1_file_daftar_hadir'] = $fileDaftarPath;
			}
			
			$updateJadwal['aud_thp1_status_temuan'] = 'diajukan';
			$updateJadwal['aud_thp1_notulen'] = $request['aud_thp1_notulen'];
			$updateJadwal['aud_thp1_tanggal_rapat_akhir'] = Carbon::now()->format('Y-m-d');
			
            DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request['aud_thp1_id'])
                ->update($updateJadwal);
            DB::commit();
			
			
			// Notifikasi
			$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
			// Send Push
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = "Proses Audit Tahap I";
			$notifStruct->message   = sprintf("Proses Audit Tahap I telah ditentukan untuk jadwal audit tahap 1 no #%s, silahkan upload file-file rapat dan klarifikasi temuan jika ada.", $request['aud_thp1_id']);
			$notifStruct->user_id   = $data_pelanggan?->user_id;
			$notifStruct->click_url = url('/pelanggan/jadwal');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Proses Audit Tahap I";
			$structEmail->body    = view("$this->view.mails.publish")
				->with([
					'nama'       => $data_pelanggan?->cust_nama,
					'message'       => sprintf("Proses Audit Tahap I telah ditentukan untuk jadwal audit tahap 1 no #%s, silahkan upload file-file rapat dan klarifikasi temuan jika ada.", $request['aud_thp1_id']),
					'link_verif'        => url('/pelanggan/jadwal'),
				])->render();
			$structEmail->to      = $data_pelanggan?->cust_email;
			sendEmail($structEmail);
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
	public function print(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'hasil-tinjauan' => $this->print_hasil_tinjauan($request),
            'audit-tahap1' => $this->print_audit_tahap1($request),
            'lihat-revisi' => $this->print_revisi($request),
            default        => null,
        };
    }
	
	private function print_revisi(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Revisi Audit Tahap 1'),
        ];

        $dataRevisi = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id'])
			->join('sis_audit_tahap1_revisi', "sis_audit_tahap1_revisi.aud_thp1_id", "=", "sis_audit_tahap1.aud_thp1_id")
			->select('*');

        $parser = [
			'module' => $this->module
			, 'url' => $this->url
			, 'breadcrumbs' => $breadcrumbs
			, 'dataRevisi' => $dataRevisi->get()
		];
        return view("$this->view.print_revisi")->with($parser);
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
		
		if($restAudit['sert_tahap1_jenis'] == 'sni'){
			// return view("$this->view.print_hasil_tinjauan_sni")->with($parser);
			$pdf    = PDF::loadView("$this->view.print_hasil_tinjauan_sni", $parser)->setPaper('a4', 'portrait');
			return $pdf->stream();
		}
		else{
			$dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
			$dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
			$dataTim->select('*');
			$dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
			
			$parser['dataTim'] = $dataTim->get();
			$pdf    = PDF::loadView("$this->view.print_hasil_tinjauan_pusat", $parser)->setPaper('a4', 'portrait');
			return $pdf->stream();
			// return view("$this->view.print_hasil_tinjauan_pusat")->with($parser);
		}
	}
	
	private function print_audit_tahap1(Request $request)
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
		
        // return view("$this->view.print_audit_tahap1")->with($parser);
		$pdf    = PDF::loadView("$this->view.print_audit_tahap1", $parser)->setPaper('a4', 'portrait');
		return $pdf->stream();
	}
}
