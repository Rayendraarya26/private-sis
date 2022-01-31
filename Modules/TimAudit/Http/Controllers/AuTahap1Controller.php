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

class AuTahap1Controller extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1';
    private $view = "timaudit::auditor_tahap_1";

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
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
			->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_audit_tahap1.aud_thp1_status_temuan', '!=', 'setuju');
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
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'audit-tahap1' => $this->edit_audit_tahap1($request),
            default        => null,
        };
    }

    private function edit_audit_tahap1(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Proses Audit Tahap 1'),
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

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $status_entry = false;
        if ($dataAuditKlausul->exists()) {
            $status_entry = true;
        }

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'statusEntry' => $status_entry, 'dataAuditKlausul' => $dataAuditKlausul->get(), 'dataAudit' => $restAudit];
        return view("$this->view.edit_audit_tahap1")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-generate-tahap1' => $this->update_generate_tahap1($request),
            'update-audit-tahap1'    => $this->update_audit_tahap1($request),
            default                  => null,
        };
    }

    private function update_generate_tahap1(Request $request)
    {
        $request->validate([
            "aud_thp1_id" => 'required',
            "sert_id"     => 'required',
            "mohon_id"    => 'required',
            "sert_tahap1_jenis"    => 'required',
        ]);

        try {
            DB::beginTransaction();
            $restData = DB::table('sis_audit_tahap1')->where('aud_thp1_id', $request['aud_thp1_id'])->first();
            if ($restData !== null) {
                DB::table('sis_audit_tahap1')
                    ->where('aud_thp1_id', $request['aud_thp1_id'])
                    ->update(["created_at" => Carbon::now(), "updated_at" => Carbon::now(),]);
                $aud_thp1_id = $restData->aud_thp1_id;
            } else {
                $newTahap1              = new SisAuditTahap1();
                $newTahap1->aud_thp1_id = $request['aud_thp1_id'];
                $newTahap1->created_at  = Carbon::now();
                $newTahap1->updated_at  = Carbon::now();
                $newTahap1->save();
                $aud_thp1_id = $newTahap1->aud_thp1_id;
            }

            $restDataDet = DB::table('sis_audit_tahap1_detail')->where('aud_thp1_id', $request['aud_thp1_id'])->first();
            if ($restDataDet === null) {
				if($request['sert_tahap1_jenis'] == 'sni'){
					$dts = DB::table('master_klausul_tahap1')->where('sert_id', '=', $request['sert_id'])->orderBy(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 1), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 2), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 3), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 4), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 5), '.', -1) + 0"))->get();

					if ($dts !== null) {
						foreach ($dts as $dt) {
							DB::table('sis_audit_tahap1_detail')->insert([
								'aud_thp1_id'                 => intval($aud_thp1_id),
								'klausul_thp1_id'             => intval($dt->klausul_thp1_id),
								'aud_thp1_det_thp1_nomor'     => $dt->klausul_thp1_nomor,
								'aud_thp1_det_peryataan'      => $dt->klausul_thp1_peryataan,
								'aud_thp1_det_is_tinjauan'    => $dt->klausul_thp1_is_tinjauan,
								'aud_thp1_det_kode_dok'       => NULL,
								'aud_thp1_det_judul_dok'      => NULL,
								'aud_thp1_det_hasil_tinjauan' => NULL,
								'aud_thp1_det_keterangan'     => NULL,
							]);
						}
					}
				}
				else if($request['sert_tahap1_jenis'] == 'pusat'){
					$dts = DB::table('master_klausul_tahap1')->where('sert_id', '=', $request['sert_id'])->orderBy(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 1), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 2), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 3), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 4), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 5), '.', -1) + 0"))->get();

					if ($dts !== null) {
						foreach ($dts as $dt) {
							DB::table('sis_audit_tahap1_detail')->insert([
								'aud_thp1_id'                 => intval($aud_thp1_id),
								'klausul_thp1_id'             => intval($dt->klausul_thp1_id),
								'aud_thp1_det_thp1_nomor'     => $dt->klausul_thp1_nomor,
								'aud_thp1_det_persyaratan'      => $dt->klausul_thp1_peryataan,
								'aud_thp1_det_is_tinjauan'    => $dt->klausul_thp1_is_tinjauan,
								'aud_thp1_det_nilai'       => NULL,
								'aud_thp1_det_satuan'      => NULL,
							]);
						}
					}
				}
                
            }

            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function update_audit_tahap1(Request $request)
    {
        $request->validate([
            "aud_thp1_id"           => 'required',
            "cust_id"               => 'required',
            "sert_id"               => 'required',
            "mohon_id"              => 'required',
            "jenis"              => 'required',
            "kolom_v"               => 'required',
            "kolom_vi"              => 'required',
            "kolom_vii"             => 'required',
            "kolom_viii"            => 'required',
            "kolom_ix"              => 'required',
            "kolom_x"               => 'required',
            "kolom_xi"              => 'required',
            "kolom_xii"             => 'required',
            "status_audit"          => 'required',
            "tutup_audit"           => 'required',
            "detail_hasil_tinjauan" => 'required',
            "detail_keterangan"     => 'required',
            "detail_judul_dok"      => 'nullable',
            "detail_kode_dok"       => 'nullable',
            "detail_nilai"       => 'nullable',
            "detail_satuan"       => 'nullable',
            "aud_thp1_file_daftar_hadir"       => 'required',
            "aud_thp1_file_notulen"       => 'required',
        ]);
		$uploadedPathDaftar = [];
		$uploadedPathNotulen = [];
        try {
            $baseFileUpload = sprintf(config("app.path_file_tahap1"), $request['aud_thp1_id']);	
            DB::beginTransaction();
			$updateJadwal = [
                    "aud_thp1_status"  => $request['status_audit'],
                    "aud_thp1_ditutup" => $request['tutup_audit'],
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
			
			if ($request->hasFile('aud_thp1_file_notulen')){
				$fileNotulen     = $request->file('aud_thp1_file_notulen');
				$fileNotulenName = Str::slug('file-jadwal-' . $fileNotulen->getClientOriginalName()) . '-' . time() . '.' . $fileNotulen->getClientOriginalExtension();
				$fileNotulenPath = sprintf("%s/%s", $baseFileUpload, $fileNotulenName);
				$fileNotulen->move($baseFileUpload, $fileNotulenName);
				array_push($uploadedPathNotulen, $fileNotulenPath);
				
				$updateJadwal['aud_thp1_file_notulen'] = $fileNotulenPath;
			}
						
            
			if($request['tutup_audit'] == 'ya' ){
				$updateJadwal['aud_thp1_status_temuan'] = 'diajukan';
			}
			
            DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request['aud_thp1_id'])
                ->update($updateJadwal);

            DB::table('sis_audit_tahap1')
                ->where('aud_thp1_id', $request['aud_thp1_id'])
                ->update([
                    "aud_thp1_kolom_v"    => $request['kolom_v'],
                    "aud_thp1_kolom_vi"   => $request['kolom_vi'],
                    "aud_thp1_kolom_vii"  => $request['kolom_vii'],
                    "aud_thp1_kolom_viii" => $request['kolom_viii'],
                    "aud_thp1_kolom_ix"   => $request['kolom_ix'],
                    "aud_thp1_kolom_x"    => $request['kolom_x'],
                    "aud_thp1_kolom_xi"   => $request['kolom_xi'],
                    "aud_thp1_kolom_xii"  => $request['kolom_xii'],
                    "updated_at"          => Carbon::now(),
                    "aud_thp1_tanggal_rapat_akhir"          => Carbon::now()->format('Y-m-d'),
                ]);
			
			if($request['jenis'] == 'sni'){
				if (!empty($request['detail_kode_dok'])) {
					foreach ($request['detail_kode_dok'] as $key => $val) {
						DB::table('sis_audit_tahap1_detail')
							->where('aud_thp1_det_id', $key)
							->update([
								'aud_thp1_det_kode_dok'       => $val,
								'aud_thp1_det_judul_dok'      => isset($request['detail_judul_dok'][$key]) ? $request['detail_judul_dok'][$key] : NULL,
								'aud_thp1_det_hasil_tinjauan' => isset($request['detail_hasil_tinjauan'][$key]) ? $request['detail_hasil_tinjauan'][$key] : NULL,
								'aud_thp1_det_keterangan'     => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : NULL,
							]);
					}
				}
			}
			else if($request['jenis'] == 'pusat'){
				if (!empty($request['detail_nilai'])) {
					foreach ($request['detail_nilai'] as $key => $val) {
						DB::table('sis_audit_tahap1_detail')
							->where('aud_thp1_det_id', $key)
							->update([
								'aud_thp1_det_nilai'       => $val,
								'aud_thp1_det_satuan'      => isset($request['detail_satuan'][$key]) ? $request['detail_satuan'][$key] : NULL,
								'aud_thp1_det_hasil_tinjauan' => isset($request['detail_hasil_tinjauan'][$key]) ? $request['detail_hasil_tinjauan'][$key] : NULL,
								'aud_thp1_det_keterangan'     => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : NULL,
							]);
					}
				}
			}
			
			if($request['tutup_audit'] == 'ya'){
				// Notifikasi
				$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
				// Send Push
				$notifStruct            = new NotifStruct();
				$notifStruct->title     = "Proses Audit Tahap I";
				$notifStruct->message   = sprintf("Proses Audit Tahap I telah ditentukan untuk jadwal audit tahap 1 no #%s, silahkan klarifikasi temuan.", $request['aud_thp1_id']);
				$notifStruct->user_id   = $data_pelanggan?->user_id;
				$notifStruct->click_url = url('/pelanggan/jadwal');
				sendNotification($notifStruct);

				// Send Email
				$structEmail          = new EmailStruct();
				$structEmail->subject = "Proses Audit Tahap I";
				$structEmail->body    = view("$this->view.mails.publish")
					->with([
						'nama'       => $data_pelanggan?->cust_nama,
						'message'       => sprintf("Proses Audit Tahap I telah ditentukan untuk jadwal audit tahap 1 no #%s, silahkan klarifikasi temuan.", $request['aud_thp1_id']),
						'link_verif'        => url('/pelanggan/jadwal'),
					])->render();
				$structEmail->to      = $data_pelanggan?->cust_email;
				sendEmail($structEmail);
			}
            

            DB::commit();
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
			return view("$this->view.print_hasil_tinjauan_sni")->with($parser);
		}
		else{
			$dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
			$dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
			$dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
			$dataTim->select('*');
			$dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
			
			$parser['dataTim'] = $dataTim->get();
			return view("$this->view.print_hasil_tinjauan_pusat")->with($parser);
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
		
        return view("$this->view.print_audit_tahap1")->with($parser);
	}
}
