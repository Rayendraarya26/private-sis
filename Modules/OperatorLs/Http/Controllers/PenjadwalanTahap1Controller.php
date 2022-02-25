<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPermohonan;
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

class PenjadwalanTahap1Controller extends Controller
{

    public $module = self::class;
    private $url = 'operatorls/penjadwalan-tahap1';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tahap 1'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::penjadwalan_tahap1.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal'      => $this->ajax_datagrid_jadwal($request),
            'datagrid-jadwal-tim'  => $this->ajax_datagrid_jadwal_tim($request),
            'combogrid-pelanggan'  => $this->ajax_combogrid_pelanggan($request),
            'combogrid-permohonan' => $this->ajax_combogrid_permohonan($request),
            'combogrid-pegawai'    => $this->ajax_combogrid_pegawai($request),
            'combobox-posisi'      => $this->ajax_combobox_posisi($request),
            default                => null,
        };
    }

    private function ajax_datagrid_jadwal(Request $request)
    {
        $data = SisAuditTahap1::join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        $data->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id");
        $data->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id");
        $data->leftJoin('sis_audit_tahap1_detail', "sis_audit_tahap1_detail.aud_thp1_id", "=", "sis_audit_tahap1.aud_thp1_id");
        $data->where('aud_thp1_ditutup', '=', 'tidak');
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
        $total = $data->select(DB::raw('count(distinct sis_audit_tahap1.aud_thp1_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"));

        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);

        $data->groupBy('sis_audit_tahap1.aud_thp1_id');
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status']                   = ($d->total_detail > 0) ? 'closed' : 'open';
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['mohon_id']                 = $d->mohon_id;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['bill_nomor_billing']       = $d->bill_nomor_billing;
            $x['aud_thp1_tanggal_mulai']   = $d->aud_thp1_tanggal_mulai?->format("Y-m-d");
            $x['aud_thp1_tanggal_selesai'] = $d->aud_thp1_tanggal_selesai?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_jadwal_tim(Request $request)
    {
        $data = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
        $data->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        $data->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
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
        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id");
        $data->groupBy('sis_audit_tahap1_tim.thp1_tim_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['tipe']                     = 'data-tim';
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['thp1_tim_id']              = $d->thp1_tim_id;
            $x['peg_id']                   = $d->peg_id;
            $x['peg_nip']                  = $d->peg_nip;
            $x['peg_nama']                 = $d->peg_nama;
            $x['thp1_tim_kode']            = $d->thp1_tim_kode;
            $x['thp1_tim_posisi']          = $d->thp1_tim_posisi;
            $x['thp1_tim_kesanggupan']     = $d->thp1_tim_kesanggupan;
            $x['thp1_tim_kesanggupan_tgl'] = $d->thp1_tim_kesanggupan_tgl?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    private function ajax_combogrid_pelanggan(Request $request)
    {
        $data = SisPelanggan::join('sis_billing', "sis_pelanggan.cust_id", "=", "sis_billing.cust_id")
			->whereNotIn('sis_billing.bill_id', function ($query) use ($request) {
                $query->select('bill_id')->from('sis_audit_tahap1')->whereNotNull('bill_id');
            });

        $data->orderBy("cust_nama");
        // Filter
        if (!empty($request->q)) {
            $data->where('cust_nama', 'LIKE', '%' . $request->q . '%');
        }

        // Total
        $total = $data->select(DB::raw('count(distinct sis_billing.bill_id) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_id']            = $d->cust_id;
            $x['cust_nama']          = $d->cust_nama;
            $x['bill_id']            = $d->bill_id;
            $x['bill_nomor_billing'] = $d->bill_nomor_billing;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->whereIn('mohon_approved_status', ['accepted']);
        $data->whereIn('mohon_verif_kajian_permohonan_pjt', ['ya']);
        $data->whereIn('mohon_verif_kajian_permohonan_paskal', ['ya']);
        $data->whereIn('mohon_tagihan_biaya_status', ['setuju']);
        $data->whereNotNull('mohon_pernyataan_persetujuan_file');
        $data->where('mohon_det_perlu_tahap1', '=', 'ya');
        $data->where('sis_permohonan.cust_id', '=', $request->cust_id);
        $data->whereIn('sis_permohonan_detail.mohon_det_id', function ($query) use ($request) {
                $query->select('sis_permohonan_detail.mohon_det_id')->from('sis_billing_items')->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_billing_items.mohon_id")->where("sis_billing_items.bill_id", "=", $request->bill_id);
            });;
        $cust_id = $request->cust_id;
		$data->whereNotIn('sis_permohonan_detail.mohon_det_id', function ($query) use ($cust_id) {
            $query->select(DB::raw('IFNULL(sis_audit_tahap1.mohon_det_id, 0)'))
                ->from('sis_audit_tahap1')
                ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
                ->whereNotNull('sis_audit_tahap1.mohon_det_id')
                ->where('sis_permohonan.cust_id', '=', $cust_id);
        });

        if (!empty($request->q)) {
            $data->where('master_sertifikasi.sert_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(distinct sis_permohonan_detail.mohon_det_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "master_sertifikasi.sert_id AS sert_id")->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_permohonan_detail.mohon_det_id');

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['mohon_id']           = $d->mohon_id;
            $x['mohon_det_id']           = $d->mohon_det_id;
            $x['sert_tahap1_jenis']          = $d->sert_tahap1_jenis;
            $x['sert_nama']          = $d->sert_nama;
            $x['mohon_jenis_status'] = ($d->mohon_jenis_status == 'lama') ? 're-sertifikasi' : 'sertifikasi baru';
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_pegawai(Request $request)
    {
        $data = MasterPegawai::join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
		$data->join('pegawai_kompetensi_auditor', "pegawai_kompetensi_auditor.peg_id", "=", "master_pegawai.peg_id");
		$data->join('sis_permohonan_detail', "sis_permohonan_detail.sert_id", "=", "pegawai_kompetensi_auditor.sert_id");
        // Filter
        if (!empty($request->q)) {
            $data->where('master_pegawai.peg_nama', 'LIKE', '%' . $request->q . '%');
        }
		$data->where('sis_permohonan_detail.mohon_det_id', $request->mohon_det_id);
		$data->where('master_pegawai.is_auditor', 'yes');
        // Total
        $total = $data->select(DB::raw('COUNT(DISTINCT master_pegawai.peg_id) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);
		$data->groupBy("master_pegawai.peg_id");
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['peg_id']   = $d->peg_id;
            $x['peg_kode'] = $d->peg_kode == null ? '' : $d->peg_kode;
            $x['peg_nama'] = $d->peg_nama;
            $x['peg_telp'] = $d->peg_telp;
            $x['peg_nip']  = $d->peg_nip;
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
            ['id' => 'observer', 'name' => 'Observer'],
        ];

        return response()->json($data);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tahap 1'),
            new BreadcrumbsStruct('Input Penjadwalan'),
        ];

        $parser = [
            'module'      => $this->module,
            'url'         => $this->url,
            'breadcrumbs' => $breadcrumbs,
        ];
        return view("operatorls::penjadwalan_tahap1.create")->with($parser);
    }

    public function store(Request $request)
    {

        $request->validate([
            "cust_id"                  => 'required',
            "mohon_id"                 => 'required',
            "mohon_det_id"                 => 'required',
            "sert_tahap1_jenis"                 => 'required',
            "bill_id"                  => 'required',
            "aud_thp1_tanggal_mulai"   => 'required',
            "aud_thp1_tanggal_selesai" => 'required',
            "aud_thp1_tujuan"          => 'required',
            "aud_thp1_standart_acuan"          => 'required',
            "jadwal_tims"              => 'required',
        ]);

        try {
            DB::beginTransaction();
            $newSisAuditTahap1                           = new SisAuditTahap1();
            $newSisAuditTahap1->mohon_id                 = $request['mohon_id'];
            $newSisAuditTahap1->mohon_det_id                 = $request['mohon_det_id'];
            $newSisAuditTahap1->bill_id                  = $request['bill_id'];
            $newSisAuditTahap1->sert_tahap1_jenis   = $request['sert_tahap1_jenis'];
            $newSisAuditTahap1->aud_thp1_tanggal_mulai   = $request['aud_thp1_tanggal_mulai'];
            $newSisAuditTahap1->aud_thp1_tanggal_selesai = $request['aud_thp1_tanggal_selesai'];
            $newSisAuditTahap1->aud_thp1_tujuan          = $request['aud_thp1_tujuan'];
            $newSisAuditTahap1->aud_thp1_standart_acuan		= $request['aud_thp1_standart_acuan'];
            $newSisAuditTahap1->created_at               = Carbon::now();
            $newSisAuditTahap1->updated_at               = Carbon::now();
            $newSisAuditTahap1->save();

            if ($request['mohon_id'] != '') {
                SisPermohonanStatus::create([
                    "status_mohon_id" => $request['mohon_id'],
                    "status_tipe"     => "informasi",
                    "status_judul"    => "Informasi Audit Tahap 1",
                    "status_pesan"    => sprintf("Permohonan dengan nomor #%s telah diinputkan pada jadwal audit tahap 1.", $request['mohon_id']),
                    "created_at"      => Carbon::now(),
                    "updated_at"      => Carbon::now(),
                ]);
            }

            // add items
            $dataItems = json_decode($request['jadwal_tims']);
            foreach ($dataItems as $itm) {
                $newSisAuditTahap1Tim                  = new SisAuditTahap1Tim();
                $newSisAuditTahap1Tim->aud_thp1_id     = $newSisAuditTahap1->aud_thp1_id;
                $newSisAuditTahap1Tim->peg_id          = $itm->peg_id;
                $newSisAuditTahap1Tim->thp1_tim_kode   = $itm->kode;
                $newSisAuditTahap1Tim->thp1_tim_posisi = $itm->posisi;
                $newSisAuditTahap1Tim->created_at      = Carbon::now();
                $newSisAuditTahap1Tim->updated_at      = Carbon::now();
                $newSisAuditTahap1Tim->save();
            }

            DB::commit();

			$dataItems = json_decode($request['jadwal_tims']);
            foreach ($dataItems as $itm) {
				$data_pegawai = MasterPegawai::where('peg_id', $itm->peg_id)->select('user_id')->first();
				$notifStruct            = new NotifStruct();
				$notifStruct->title     = 'Penunjukan Tim Tahap 1';
				$notifStruct->message   = sprintf("Penjadwalan Tahap 1 telah diterbitkan dengan nomor permohonan #%s , yang akan dilakukan pada tanggal %s s/d %s.", $request['mohon_id'], $request['aud_thp1_tanggal_mulai'], $request['aud_thp1_tanggal_selesai']);
				$notifStruct->user_id   = $data_pegawai?->user_id;
				$notifStruct->click_url = url('/timaudit/persetujuan-tim/auditor');
				sendNotification($notifStruct);
            }

			$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
			// Send Push
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = 'Penjadwalan Tahap 1';
			$notifStruct->message   = sprintf("Penjadwalan Tahap 1 telah diterbitkan dengan nomor permohonan #%s , yang akan dilakukan pada tanggal %s s/d %s.", $request['mohon_id'], $request['aud_thp1_tanggal_mulai'], $request['aud_thp1_tanggal_selesai']);
			$notifStruct->user_id   = $data_pelanggan?->user_id;
			$notifStruct->click_url = url('/pelanggan/sertifikasi/permohonan');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Penjadwalan Tahap 1";
			$structEmail->body    = view('operatorls::penjadwalan_tahap1.mails.publish')
				->with([
					'nama'       => $data_pelanggan?->cust_nama,
					'message'       => sprintf("Penjadwalan Tahap 1 telah diterbitkan dengan nomor permohonan #%s , yang akan dilakukan pada tanggal %s s/d %s.", $request['mohon_id'], $request['aud_thp1_tanggal_mulai'], $request['aud_thp1_tanggal_selesai']),
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
            'detail-jadwal' => $this->detail_jadwal($request),
            default         => null,
        };
    }

    private function detail_jadwal(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tahap 1'),
            new BreadcrumbsStruct('Detail Jadwal'),
        ];

        $dataJadwal = SisAuditTahap1::where('aud_thp1_id', $request['aud_thp1_id'])
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        $dataJadwal->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id");
        $dataJadwal->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id");
        $dataJadwal->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id");
        $dataJadwal->groupBy('sis_audit_tahap1.aud_thp1_id');
        $dataJadwal->select('*', DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'), DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_nace) as mohon_kmditi_nace'), DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ea) as mohon_kmditi_ea'), DB::raw('GROUP_CONCAT(distinct sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as mohon_kmditi_ruang_lingkup'));

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("operatorls::penjadwalan_tahap1.detail_jadwal")->with($parser);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'edit-jadwal' => $this->edit_jadwal($request),
            default       => null,
        };
    }

    private function edit_jadwal(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tahap 1'),
            new BreadcrumbsStruct('Edit Jadwal'),
        ];

        $dataJadwal = SisAuditTahap1::where('aud_thp1_id', $request['aud_thp1_id'])
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        $dataJadwal->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id");
        $dataJadwal->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id");
        $dataJadwal->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("operatorls::penjadwalan_tahap1.edit_jadwal")->with($parser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     *
     * @return Renderable
     */
    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-jadwal' => $this->update_jadwal($request),
            'update-tim'    => $this->update_tim($request),
            default         => null,
        };
    }

    private function update_jadwal(Request $request)
    {
        $request->validate([
            "aud_thp1_id"              => 'required',
            "aud_thp1_tanggal_mulai"   => 'required',
            "aud_thp1_tanggal_selesai" => 'required',
            "aud_thp1_tujuan"          => 'required',
            "aud_thp1_standart_acuan"          => 'required',
        ]);

        try {
            DB::beginTransaction();
            $dt_update = [
                'aud_thp1_tanggal_mulai'   => $request['aud_thp1_tanggal_mulai'],
                'aud_thp1_tanggal_selesai' => $request['aud_thp1_tanggal_selesai'],
                'aud_thp1_tujuan'          => $request['aud_thp1_tujuan'],
                'aud_thp1_standart_acuan'          => $request['aud_thp1_standart_acuan'],
            ];
            SisAuditTahap1::findOrFail($request['aud_thp1_id'])->update($dt_update);
            DB::commit();

            return responseJSON(200, null, "Data jadwal berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function update_tim(Request $request)
    {
        $request->validate([
            "thp1_tim_id" => 'nullable',
            "aud_thp1_id" => 'required',
            "peg_id"      => 'required',
            "kode"        => 'required',
            "posisi"      => 'required',
        ]);

        try {
            $dt_update = [
                'aud_thp1_id'     => $request['aud_thp1_id'],
                'peg_id'          => $request['peg_id'],
                'thp1_tim_kode'   => $request['kode'],
                'thp1_tim_posisi' => $request['posisi'],
            ];
            if ($request['thp1_tim_id'] != '') {
                SisAuditTahap1Tim::findOrFail($request['thp1_tim_id'])->update($dt_update);
            } else {
                SisAuditTahap1Tim::create($dt_update);
            }
            return responseJSON(200, null, "Data tim berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'data-jadwal' => $this->delete_data_jadwal($request),
            'data-tim'    => $this->delete_data_tim($request),
            default       => null,
        };
    }

    private function delete_data_jadwal(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisAuditTahap1::where("aud_thp1_id", $id)->firstOrFail();
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

    private function delete_data_tim(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisAuditTahap1Tim::where("thp1_tim_id", $id)->firstOrFail();
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
