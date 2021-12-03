<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisAuditTimKomite;

use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PersetujuanTimAuditController extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/persetujuan-tim/auditor';
    private $view = "timaudit::persetujuan_tim_auditor";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Persetujuan Tim Auditor'),
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
        $result = [];
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'none');
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

        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->groupBy('sis_jadwal.jadw_id');

        foreach ($data->get() as $d) {
			if($d->jadw_id != ''){
				$x['jadw_status']              = 'tahap-2';
				$x['jadw_id']                  = $d->jadw_id;
				$x['jadw_tanggal_mulai']       = $d->jadw_tanggal_mulai;
				$x['jadw_tanggal_selesai']     = $d->jadw_tanggal_selesai;
				$x['cust_nama']                = $d->cust_nama;
				$x['sert_nama']                = $d->sert_nama;
				$x['jadw_jenis']               = $d->jadw_jenis;
				$x['jadw_audit_jenis']         = $d->jadw_audit_jenis;
				$x['jadw_tim_kesanggupan']     = $d->jadw_tim_kesanggupan;
				$x['jadw_tim_kesanggupan_tgl'] = $d->jadw_tim_kesanggupan_tgl;
				array_push($result, $x);
			}
        }

        // Tahap 1
        $dataTahap1 = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
			->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
			->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
			->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
			->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id")
			->where('master_pegawai.user_id', '=', auth()->id())
			->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'none');
			
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'jadw_id')
                    $dataTahap1->where('sis_audit_tahap1_tim.aud_thp1_id', 'LIKE', '%' . $f->value . '%');
                else
                    $dataTahap1->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if ($sort[$i] == 'jadw_id')
                    $dataTahap1->orderBy('sis_audit_tahap1_tim.aud_thp1_id', $order[$i]);
                else if ($sort[$i] == 'cust_nama')
                    $dataTahap1->orderBy('cust_nama', $order[$i]);
                else if ($sort[$i] == 'sert_nama')
                    $dataTahap1->orderBy('sert_nama', $order[$i]);
                else if ($sort[$i] == 'jadw_tanggal_mulai')
                    $dataTahap1->orderBy('sis_audit_tahap1_tim.aud_thp1_tanggal_mulai', $order[$i]);
                else if ($sort[$i] == 'jadw_tanggal_selesai')
                    $dataTahap1->orderBy('sis_audit_tahap1_tim.aud_thp1_tanggal_selesai', $order[$i]);
            }
        }

        $dataTahap1->select(
            "sis_audit_tahap1.aud_thp1_tanggal_mulai AS jadw_tanggal_mulai",
            "sis_audit_tahap1.aud_thp1_tanggal_selesai AS jadw_tanggal_selesai",
            "sis_audit_tahap1_tim.aud_thp1_id AS jadw_id",
            "sis_audit_tahap1_tim.thp1_tim_kesanggupan_tgl AS jadw_tim_kesanggupan_tgl",
            "cust_nama AS cust_nama",
        );
        $dataTahap1->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $dataTahap1->selectRaw("GROUP_CONCAT(distinct thp1_tim_kesanggupan) AS thp1_tim_kesanggupan");
        $dataTahap1->groupBy('sis_audit_tahap1_tim.aud_thp1_id');

        foreach ($dataTahap1->get() as $d) {
            $x['jadw_status']              = 'tahap-1';
            $x['jadw_id']                  = $d->jadw_id;
            $x['jadw_tanggal_mulai']       = $d->aud_thp1_tanggal_mulai;
            $x['jadw_tanggal_selesai']     = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['jadw_jenis']               = 'tunggal';
            $x['jadw_audit_jenis']         = 'tahap-1';
            $x['jadw_tim_kesanggupan']     = $d->thp1_tim_kesanggupan;
            $x['jadw_tim_kesanggupan_tgl'] = $d->thp1_tim_kesanggupan_tgl;
            array_push($result, $x);
        }
		
		$dataKomite = SisAuditTimKomite::join('sis_jadwal', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id")->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $dataKomite->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataKomite->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataKomite->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
        $dataKomite->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        $dataKomite->where('sis_audit_tim_komite.komite_kesanggupan', '=', 'none');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->field == 'jadw_id')
                    $dataKomite->where('sis_jadwal.jadw_id', 'LIKE', '%' . $f->value . '%');
                else
                    $dataKomite->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if ($sort[$i] == 'jadw_id')
                    $dataKomite->orderBy('sis_jadwal.jadw_id', $order[$i]);
                else
                    $dataKomite->orderBy($sort[$i], $order[$i]);
            }
        }
		
        $dataKomite->select("*");
        $dataKomite->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama) SEPARATOR ',<br/>') as sert_nama");
        $dataKomite->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $dataKomite->groupBy('sis_jadwal.jadw_id');

        foreach ($dataKomite->get() as $k) {
            $x['jadw_status'] = 'komite';
            $x['jadw_id']                  = $k->jadw_id;
            $x['jadw_tanggal_mulai']       = $k->jadw_tanggal_mulai;
            $x['jadw_tanggal_selesai']     = $k->jadw_tanggal_selesai;
            $x['cust_nama']                = $k->cust_nama;
            $x['sert_nama']                = $k->sert_nama;
            $x['jadw_jenis']               = $k->jadw_jenis;
            $x['jadw_audit_jenis']         = $k->jadw_audit_jenis;
            $x['jadw_tim_kesanggupan']     = $k->komite_kesanggupan;
            $x['jadw_tim_kesanggupan_tgl'] = $k->komite_tgl_kesanggupan;
            // $x['komite_tgl_surat'] = $d->komite_tgl_surat;
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'kesanggupan-tim' => $this->edit_kesanggupan_tim($request),
            default           => null,
        };
    }

    private function edit_kesanggupan_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Persetujuan Tim Auditor'),
            new BreadcrumbsStruct('Kesanggupan Form'),
        ];

        if ($request['jenis'] == 'tahap-1') {
            $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['jadw_id']);

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
			
			$dataJadwal->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
				->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
				->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
				->join('sis_permohonan_komoditi', "sis_permohonan_komoditi.mohon_det_id", "=", "sis_permohonan_detail.mohon_det_id")
				->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
				->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
				->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
				->join('sis_audit_tahap1_tim', "sis_audit_tahap1_tim.aud_thp1_id", "=", "sis_audit_tahap1.aud_thp1_id")
				->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
				->groupBy('sis_audit_tahap1.aud_thp1_id');
			
            $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());
        } 
		else if ($request['jenis'] == 'komite') {
            $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id'])
				->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
				->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id")
				->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id")
				->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id")
				->join('sis_audit_tim_komite', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id")
				->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");

            $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());
            $dataJadwal->groupBy('sis_jadwal.jadw_id');

            $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
			$dataJadwal->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- Penggunaan / Pembekuan Sertifikat ', sert_nama) SEPARATOR ',<br/>') as sert_nama");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct komodt_nama) AS komodt_nama");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_nomor_referensi) AS jadw_audit_nomor_referensi");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_nace) AS jadw_audit_kode_nace");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kode_ea) AS jadw_audit_kode_ea");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_standart_acuan) AS jadw_audit_standart_acuan");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_ruang_lingkup) AS jadw_audit_ruang_lingkup");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_tujuan_audit) AS jadw_audit_tujuan_audit");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct jadw_audit_kegiatan) AS jadw_audit_kegiatan");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_audit_tim_komite.komite_posisi) AS jadw_tim_posisi");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_audit_tim_komite.peg_id) AS peg_id");
        }
		else {
			$dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
            $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
            $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
            $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
            $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
            $dataJadwal->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");

            $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());

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
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_posisi) AS jadw_tim_posisi");
            $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.peg_id) AS peg_id");

            $dataJadwal->groupBy('sis_jadwal.jadw_id');
        }


        $parser = ['module' => $this->module, 'url' => $this->url, 'jenis' => $request['jenis'], 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("$this->view.edit_kesanggupan_tim")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'kesanggupan-tim' => $this->update_kesanggupan_tim($request),
            default           => null,
        };
    }

    private function update_kesanggupan_tim(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
            "peg_id"  => 'required',
            "jenis"   => 'required',
        ]);


        try {
            if ($request['jenis'] == 'tahap-1') {
				$dataJadwal = SisAuditTahap1::where('aud_thp1_id', $request['jadw_id']);
                $dataJadwal->select('*');

                $restJadwal = $dataJadwal->get()[0];

                DB::beginTransaction();

                DB::table('sis_audit_tahap1_tim')
                    ->where('aud_thp1_id', $request['jadw_id'])
                    ->where('peg_id', $request['peg_id'])
                    ->update(['thp1_tim_kesanggupan' => 'ya', 'thp1_tim_kesanggupan_tgl' => Carbon::now()]);
                DB::commit();
            } 
			else if($request['jenis'] == 'komite') {
                $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
                $dataJadwal->select('*');

                $restJadwal = $dataJadwal->get()[0];

                DB::beginTransaction();

                DB::table('sis_audit_tim_komite')
                    ->where('jadw_id', $request['jadw_id'])
                    ->where('peg_id', $request['peg_id'])
                    ->update(['komite_kesanggupan' => 'ya', 'komite_tgl_kesanggupan' => Carbon::now()]);
				
                DB::commit();
            }

			else {
                $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
                $dataJadwal->select('*');

                $restJadwal = $dataJadwal->get()[0];

                DB::beginTransaction();

                DB::table('sis_jadwal_tim')
                    ->where('jadw_id', $request['jadw_id'])
                    ->where('peg_id', $request['peg_id'])
                    ->update(['jadw_tim_kesanggupan' => 'ya', 'jadw_tim_kesanggupan_tgl' => Carbon::now()]);
				
                DB::commit();
            }

            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
