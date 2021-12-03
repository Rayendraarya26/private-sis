<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisAuditTimKomite;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;

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

class PenjadwalanPencabutanController extends Controller
{

    public $module = self::class;
    private $url = 'operatorls/jadwal-pencabutan';
    private $view = "operatorls::penjadwalan_pencabutan";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Pencabutan Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal'      => $this->ajax_datagrid_jadwal($request),
            'combogrid-pelanggan'  => $this->ajax_combogrid_pelanggan($request),
            'combobox-posisi'      => $this->ajax_combobox_posisi($request),
            'combogrid-sertifikat' => $this->ajax_combogrid_sertifikat($request),
            'combogrid-pegawai'     => $this->ajax_combogrid_pegawai($request),
            'datagrid-jadwal-komite' => $this->ajax_datagrid_jadwal_komite($request),
            default                => null,
        };
    }
	
	private function ajax_datagrid_jadwal_komite(Request $request)
    {
        $data = SisAuditTimKomite::join('sis_jadwal', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        $data->where('sis_jadwal.jadw_id', '=', $request['jadw_id']);
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

        // Pagination
        $data->select("*");
        $data->groupBy('sis_audit_tim_komite.komite_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['tipe']          = 'data-komite';
            $x['komite_id']     = $d->komite_id;
            $x['peg_id']        = $d->peg_id;
            $x['peg_nip']       = $d->peg_nip;
            $x['peg_nama']      = $d->peg_nama;
            $x['jadw_id']       = $d->jadw_id;
            $x['komite_posisi'] = $d->komite_posisi;
            // $x['komite_tgl_surat'] = $d->komite_tgl_surat?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }
	
	private function ajax_combogrid_pegawai(Request $request)
    {
        $data = MasterPegawai::leftJoin('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        if (!empty($request->q)) {
            $data->where('master_pegawai.peg_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(distinct peg_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "master_pegawai.user_id AS user_id")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['user_id']   = $d->user_id;
            $x['peg_id']   = $d->peg_id;
            $x['peg_kode']   = $d->peg_kode == '' ? '' : $d->peg_kode ;
            $x['peg_nama'] = $d->peg_nama;
            $x['peg_telp'] = $d->peg_telp;
            $x['peg_nip']  = $d->peg_nip;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	private function ajax_combogrid_sertifikat(Request $request)
    {
        $data = SisPelangganSertifikasi::join('master_sertifikasi', "sis_pelanggan_sertifikasi.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_pelanggan_sertifikasi.komodt_id");
        $data->where('cust_id', '=', $request->cust_id);

        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*", "master_sertifikasi.sert_nama AS sert_nama")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['id'] = $d->cust_sert_id;
            $x['nama'] = $d->sert_nama;
            $x['sert_nama'] = $d->sert_nama;
            $x['mohon_id'] = $d->mohon_id;
            $x['komodt_id'] = $d->komodt_id;
            $x['komodt_nama'] = $d->komodt_nama;
            $x['kode_ea'] = $d->kode_ea_nama;
            $x['kode_nace'] = $d->kode_nace_nama;
            $x['tipe'] = $d->cust_sert_tipe;
            $x['merk'] = $d->cust_sert_merk;
            $x['nomor_sertifikat'] = $d->cust_sert_nomor_sertifikat;
            $x['nomor_referensi'] = $d->cust_sert_nomor_referensi;
            $x['nomor_sni'] = $d->cust_sert_nomor_sni;
            $x['lingkup'] = $d->cust_sert_lingkup;
            $x['produksi_tahunan'] = $d->cust_sert_produksi_tahunan;
            $x['satuan'] = $d->cust_sert_produksi_tahunan_satuan;
            $x['cust_sert_id'] = $d->cust_sert_id;
            $x['sert_id'] = $d->sert_id;
            $x['cust_sert_nomor_referensi'] = $d->cust_sert_nomor_referensi;
            $x['cust_sert_tgl_sertifikat_awal'] = $d->cust_sert_tgl_sertifikat_awal?->format("Y-m-d");
            $x['cust_sert_tgl_sertifikat_perubahan'] = $d->cust_sert_tgl_sertifikat_perubahan?->format("Y-m-d");
            $x['deskripsi'] = "Pencabutan untuk sertifikat " . $d->sert_nama . " nomor referensi " . $d->cust_sert_nomor_referensi;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_jadwal(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
		$data->where('sis_jadwal.jadw_is_khusus_komite', '=', 'ya');
		$data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        
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
        $total = $data->select(DB::raw('count(distinct sis_jadwal.jadw_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama) SEPARATOR ',<br/>') as sert_nama"));

        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);

        $data->groupBy('sis_jadwal.jadw_id');
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status'] = ($d->total_detail > 0) ? 'closed' : 'open';
            $x['jadw_id'] = $d->jadw_id;
            $x['mohon_id'] = $d->mohon_id;
            $x['cust_nama'] = $d->cust_nama;
            $x['sert_nama'] = $d->sert_nama;
            $x['jadw_tanggal_mulai'] = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_pelanggan(Request $request)
    {
        $data = SisPelanggan::join('sis_pelanggan_sertifikasi', "sis_pelanggan.cust_id", "=", "sis_pelanggan_sertifikasi.cust_id");
        $data->orderBy("cust_nama");
        // Filter
        if (!empty($request->q)) {
            $data->where('cust_nama', 'LIKE', '%' . $request->q . '%');
        }

        // Total
        $total = $data->select(DB::raw('count(distinct sis_pelanggan.cust_id) as total'))->first()->total;
        // Pagination
        $data->select("sis_pelanggan.*", DB::raw('count(distinct sis_pelanggan_sertifikasi.cust_sert_id) as total_sert'))->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_pelanggan.cust_id');
		
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_id']            = $d->cust_id;
            $x['cust_nama']          = $d->cust_nama;
            $x['total_sert']          = $d->total_sert;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_posisi(Request $request)
    {
        $data = [
            ['id' => '', 'name' => 'Silahkan Pilih'],
            ['id' => 'ketua', 'name' => 'Ketua'],
            ['id' => 'anggota', 'name' => 'Anggota'],
        ];

        return response()->json($data);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Pencabutan Sertifikasi'),
            new BreadcrumbsStruct('Input Penjadwalan'),
        ];

        $parser = [
            'view'      => $this->view,
            'module'      => $this->module,
            'url'         => $this->url,
            'breadcrumbs' => $breadcrumbs,
        ];
        return view("$this->view.create")->with($parser);
    }

    public function store(Request $request)
    {
        $request->validate([
            "cust_id"              => 'required',
            "jadw_tanggal_mulai"   => 'required',
            "jadw_tanggal_selesai" => 'required',
            "jadw_jenis"           => 'required',
            "jadwal_items"         => 'required',
            "jadwal_tims"         => 'required',
        ]);

        try {
            DB::beginTransaction();
            $newSisJadwal                       = new SisJadwal();
            $newSisJadwal->cust_id              = $request['cust_id'];
            $newSisJadwal->jadw_tanggal_status  = 'accepted';
            $newSisJadwal->jadw_team_status  = 'accepted';
            $newSisJadwal->jadw_setujui_temuan  = 'setuju';
            $newSisJadwal->jadw_is_khusus_komite  = 'ya';
            $newSisJadwal->jadw_tanggal_mulai   = $request['jadw_tanggal_mulai'];
            $newSisJadwal->jadw_tanggal_selesai = $request['jadw_tanggal_selesai'];
            $newSisJadwal->jadw_jenis           = $request['jadw_jenis'];
            $newSisJadwal->created_at           = Carbon::now();
            $newSisJadwal->updated_at           = Carbon::now();
            $newSisJadwal->save();

            // add items
            $dataItems = json_decode($request['jadwal_items']);
			if(!empty($dataItems)){
				foreach ($dataItems as $itm) {
					if (strpos($itm->komodt_id, ';') !== false) {
						$komoditi_id= DB::table('master_komoditi')->insertGetId([
							'komodt_nama' => $itm->komodt_nama
						]);
					}
					else{
						$komoditi_id= $itm->komodt_id;
					}
					
					DB::table('sis_jadwal_audit')->insert([
						'jadw_id' => $newSisJadwal->jadw_id,
						'jadw_audit_status_komite' => 'submited',
						'jadw_audit_status' => 'on-going',
						'jadw_audit_jenis' => $itm->jenis,
						'mohon_id' => ($itm->mohon_id != '') ? $itm->mohon_id : null,
						'sert_id' => $itm->sert_id,
						'komodt_id' => $komoditi_id,
						'cust_sert_id' => ($itm->cust_sert_id != '') ? $itm->cust_sert_id : null,
						'jadw_audit_nomor_sertifikat' => $itm->nomor_sertifikat,
						'jadw_audit_nomor_referensi' => $itm->nomor_referensi,
						'jadw_audit_kode_nace' => $itm->kode_nace,
						'jadw_audit_kode_ea' => $itm->kode_ea,
						'jadw_audit_standart_acuan' => $itm->standart_acuan,
						'jadw_audit_ruang_lingkup' => $itm->ruang_lingkup,
						'jadw_audit_kegiatan' => $itm->kegiatan,
						'jadw_audit_tujuan_audit' => $itm->tujuan_audit,
						'jadw_audit_sni' => $itm->sni,
						'jadw_audit_merk' => $itm->merk,
						'jadw_audit_tipe' => $itm->tipe,
						'jadw_audit_ukuran' => $itm->ukuran,
						'jadw_audit_kapasitas_produksi_tahunan' => $itm->kapasitas_produksi,
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
					]);
				}
			}
            
			
			$dataTims = json_decode($request['jadwal_tims']);
			if(!empty($dataTims)){
				foreach ($dataTims as $tim) {				
					DB::table('sis_audit_tim_komite')->insert([
						'jadw_id' => $newSisJadwal->jadw_id,
						'peg_id' => ($tim->peg_id != '') ? $tim->peg_id : null,
						'komite_posisi' => ($tim->posisi != '') ? $tim->posisi : null,
						'komite_tgl_surat' => $request['jadw_tanggal_mulai'],
						'created_at' => Carbon::now(),
						'updated_at' => Carbon::now()
					]);
					
					$notifStruct            = new NotifStruct();
					$notifStruct->title     = "Penjadwalan Pencabutan Sertifikasi";
					$notifStruct->message   = sprintf("Penjadwalan Pencabutan sertifikasi anda telah diterbikan, yang akan dilakukan pada tanggal %s s/d %s, silahkan lakukan persetujuan Tim.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']);
					$notifStruct->user_id   = $tim?->user_id;
					$notifStruct->click_url = url('/timaudit/persetujuan-tim/auditor');
					sendNotification($notifStruct);
				}
            }

            DB::commit();
			
			$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
			// Send Push
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = "Penjadwalan Pencabutan Sertifikasi";
			$notifStruct->message   = sprintf("Penjadwalan Pencabutan sertifikasi anda telah diterbikan, yang akan dilakukan pada tanggal %s s/d %s, silahkan ajukan pengajuan untuk memperbaharui sertifikasi anda.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']);
			$notifStruct->user_id   = $data_pelanggan?->user_id;
			$notifStruct->click_url = url('/pelanggan/sertifikasi/permohonan');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Penjadwalan Pencabutan Sertifikasi";
			$structEmail->body    = view("$this->view.mails.publish")
				->with([
					'nama'       => $data_pelanggan?->cust_nama,
					'message'       => sprintf("Penjadwalan Pencabutan sertifikasi anda telah diterbikan, yang akan dilakukan pada tanggal %s s/d %s, silahkan ajukan pengajuan untuk memperbaharui sertifikasi anda.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']),
					'link_verif'        => url('/pelanggan/sertifikasi/permohonan'),
				])->render();
			$structEmail->to      = $data_pelanggan?->cust_email;
			sendEmail($structEmail);
			
            return responseJSON(200, null, "Data jadwal berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function detail(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'detail-tim' => $this->detail_tim($request),
            default      => null,
        };
    }

    private function detail_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Pencabutan Sertifikasi'),
            new BreadcrumbsStruct('Detail Komite'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
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
        return view("$this->view.detail_tim")->with($parser);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'edit-tim'   => $this->edit_tim($request),
            'detail-tim' => $this->detail_tim($request),
            default      => null,
        };
    }

    private function edit_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Pencabutan Sertifikasi'),
            new BreadcrumbsStruct('Edit Komite'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
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
        return view("operatorls::komite.edit_tim")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-komite'   => $this->update_data_tim($request),
            'ajukan-komite' => $this->update_pengajuan_tim($request),
            default         => null,
        };
    }

    private function update_data_tim(Request $request)
    {
        $request->validate([
            "jadw_id"       => 'required',
            "komite_id"     => 'nullable',
            "peg_id"        => 'required',
            "komite_posisi" => 'required',
        ]);

        try {
            if ($request->komite_id != '') {
                $dataUpdate = [
                    'jadw_id'          => $request->jadw_id,
                    'peg_id'           => $request->peg_id,
                    'komite_posisi'    => $request->komite_posisi,
                    'komite_tgl_surat' => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ];
                SisAuditTimKomite::findOrFail($request['komite_id'])->update($dataUpdate);
            } else {
                $dataInsert = [
                    'jadw_id'                => $request->jadw_id,
                    'peg_id'                 => $request->peg_id,
                    'komite_posisi'          => $request->komite_posisi,
                    'komite_tgl_kesanggupan' => Carbon::now(),
                    'komite_tgl_surat'       => Carbon::now(),
                    'created_at'             => Carbon::now(),
                    'updated_at'             => Carbon::now(),
                ];
                SisAuditTimKomite::create($dataInsert);
            }
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function update_pengajuan_tim(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
        ]);


        try {
            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];

            DB::beginTransaction();
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-jadwal' => $this->delete_data_jadwal($request),
            'data-komite' => $this->delete_data_komite($request),
            default       => null,
        };
    }
	
	private function delete_data_jadwal(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisJadwal::where("jadw_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
	
    private function delete_data_komite(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisAuditTimKomite::where("komite_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = FALSE;
                    break;
                }
            }

            if ($status_return == TRUE) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
