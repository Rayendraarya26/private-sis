<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Models\BbkkpSis\MasterKodeEa;
use App\Models\BbkkpSis\MasterKodeNace;
use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
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

class PenjadwalanController extends Controller
{

    public $module = self::class;
    private $url = 'operatorls/penjadwalan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tanggal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::penjadwalan.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal'               => $this->ajax_datagrid_jadwal($request),
            'datagrid-jadwal-audit'         => $this->ajax_datagrid_jadwal_audit($request),
            'combogrid-pelanggan'           => $this->ajax_combogrid_pelanggan($request),
            'combogrid-permohonan'          => $this->ajax_combogrid_permohonan($request),
            'combogrid-permohonan-komoditi' => $this->ajax_combogrid_permohonan_komoditi($request),
            'combogrid-sertifikat'          => $this->ajax_combogrid_sertifikat($request),
            'combobox-ea'                   => $this->ajax_combobox_kode_ea($request),
            'combobox-nace'                 => $this->ajax_combobox_kode_nace($request),
            'data-list-komoditi'                 => $this->ajax_data_list_komoditi($request),
            default                         => null,
        };
    }

    private function ajax_data_list_komoditi(Request $request)
	{
		$data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan.mohon_id", "=", "sis_permohonan_detail.mohon_id")
			->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
			->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
			->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
			->select('sis_permohonan_komoditi.*');
		$data->select(DB::raw("
							group_concat(distinct master_komoditi.komodt_id SEPARATOR ';') AS komodt_id,
							group_concat(distinct master_komoditi.komodt_nama SEPARATOR ';') AS komoditi_nama,
							group_concat(distinct mohon_kmditi_merk SEPARATOR ';') AS merk,
							group_concat(distinct mohon_kmditi_tipe SEPARATOR ';') AS tipe,
							group_concat(distinct mohon_kmditi_ukuran SEPARATOR ';') AS ukuran,
							group_concat(distinct mohon_kmditi_nace SEPARATOR ';') AS nace,
							group_concat(distinct mohon_kmditi_ea SEPARATOR ';') AS ea,
							group_concat(distinct mohon_kmditi_ruang_lingkup SEPARATOR ';') AS ruang_lingkup,
							group_concat(distinct mohon_kmditi_kapasitas_produksi_tahunan SEPARATOR ';') AS kapasitas_produksi,
							group_concat(distinct mohon_kmditi_kapasitas_produksi_tahunan_satuan SEPARATOR ';') AS satuan
							"));
		$data->where('sis_permohonan_komoditi.mohon_det_id', '=', $request['mohon_det_id']);
		$data->groupBy('sis_permohonan_detail.mohon_det_id');
		$result = [];
		foreach ($data->get() as $d) {
            $x['komodt_id'] = $d->komodt_id;
            $x['komoditi_nama'] = $d->komoditi_nama;
            $x['merk'] = $d->merk;
            $x['tipe'] = $d->tipe;
            $x['ukuran'] = $d->ukuran;
            $x['nace'] = $d->nace;
            $x['ea'] = $d->ea;
            $x['ruang_lingkup'] = $d->ruang_lingkup;
            $x['kapasitas_produksi'] = $d->kapasitas_produksi;
            $x['satuan'] = $d->satuan;
            array_push($result, $x);
        }

		return response()->json($x);
	}
    
	private function ajax_datagrid_jadwal(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_billing', "sis_jadwal.bill_id", "=", "sis_billing.bill_id");

        // Filter
        $data->where('jadw_tanggal_status', '!=', 'accepted');
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
        $data->select("*");

        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');
        // $data->havingRaw('total_audit_belum_selesai > ?', [0]);
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['cust_nama']            = $d->cust_nama;
            $x['bill_nomor_billing']            = $d->bill_nomor_billing;
            $x['jadw_tanggal_status']  = $d->jadw_tanggal_status;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");

        // Filter
        $data->where('sis_jadwal_audit.jadw_id', '=', $request['jadw_id']);
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
        $total = $data->select(DB::raw('count(distinct sis_jadwal_audit.jadw_audit_id) as total'))->first()->total;

        // Pagination
        $data->select("*");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_audit_id']               = $d->jadw_audit_id;
            $x['jadw_audit_jenis']            = $d->jadw_audit_jenis;
            $x['mohon_id']                    = $d->mohon_id;
            $x['mohon_det_id']                    = $d->mohon_det_id;
            $x['sert_id']                     = $d->sert_id;
            $x['sert_nama']                   = $d->sert_nama;
            $x['komodt_id']                   = $d->komodt_id;
            $x['komodt_nama']                 = $d->komodt_nama;
            $x['cust_sert_id']                = $d->cust_sert_id;
            $x['jadw_audit_nomor_sertifikat'] = $d->jadw_audit_nomor_sertifikat;
            $x['jadw_audit_nomor_referensi']  = $d->jadw_audit_nomor_referensi;
            $x['jadw_audit_kode_nace']        = $d->jadw_audit_kode_nace;
            $x['jadw_audit_kode_ea']          = $d->jadw_audit_kode_ea;
            $x['jadw_audit_standart_acuan']   = $d->jadw_audit_standart_acuan;
            $x['jadw_audit_ruang_lingkup']    = $d->jadw_audit_ruang_lingkup;
            $x['jadw_audit_kegiatan']         = $d->jadw_audit_kegiatan;
            $x['jadw_audit_tujuan_audit']     = $d->jadw_audit_tujuan_audit;
            $x['jadw_audit_sni']              = $d->jadw_audit_sni;
            $x['jadw_audit_merk']             = $d->jadw_audit_merk;
            $x['jadw_audit_tipe']             = $d->jadw_audit_tipe;
            $x['jadw_audit_ukuran']           = $d->jadw_audit_ukuran;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_pelanggan(Request $request)
    {
        $data = SisPelanggan::join('sis_billing', "sis_pelanggan.cust_id", "=", "sis_billing.cust_id");
        $data->orderBy("cust_nama");
        // Filter
        if (!empty($request->q)) {
            $data->where('cust_nama', 'LIKE', '%' . $request->q . '%');
        }

        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_id']            = $d->cust_id;
			$x['cust_nama']          = $d->cust_nama;
			$x['bill_nomor_billing'] = $d->bill_nomor_billing;
			$x['bill_id'] = $d->bill_id;
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
        $data->where('sis_permohonan.cust_id', '=', $request->cust_id);
        $cust_id = $request->cust_id;
        $data->whereNotIn('sis_permohonan_detail.mohon_det_id', function ($query) use ($cust_id) {
            $query->select(DB::raw('IFNULL(sis_jadwal_audit.mohon_det_id, 0)'))
                ->from('sis_jadwal_audit')
                ->join('sis_jadwal', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id")
                ->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted')
                ->where('sis_jadwal.jadw_team_status', '=', 'accepted')
                ->where('sis_jadwal.cust_id', '=', $cust_id);
        });
		
		 $data->whereNotIn('sis_permohonan_detail.mohon_det_id', function ($query) use ($cust_id) {
            $query->select(DB::raw('sis_permohonan_detail.mohon_det_id AS mohon_det_id'))
                ->from('sis_permohonan')
                ->join('sis_permohonan_detail', "sis_permohonan.mohon_id", "=", "sis_permohonan_detail.mohon_id")
                ->join('sis_audit_tahap1', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
                ->where('sis_permohonan.cust_id', '=', $cust_id)
                ->where('sis_audit_tahap1.aud_thp1_ditutup', '=', 'tidak');
        });

        if ($request->jenis_status == 're-sertifikasi') {
            $data->where('mohon_det_jenis_status', '=', 'lama');
            $data->leftJoin('sis_pelanggan_sertifikasi', "sis_pelanggan_sertifikasi.cust_sert_id", "=", "sis_permohonan_detail.cust_sert_id");
            $data->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_pelanggan_sertifikasi.komodt_id");
        } else if ($request->jenis_status == 'sertifikasi') {
            $data->where('mohon_det_jenis_status', '=', 'baru');
        } else {
            $data->leftJoin('sis_pelanggan_sertifikasi', "sis_pelanggan_sertifikasi.cust_sert_id", "=", "sis_permohonan_detail.cust_sert_id");
            $data->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_pelanggan_sertifikasi.komodt_id");
        }

        if (!empty($request->q)) {
            $data->where('master_sertifikasi.sert_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(distinct sis_permohonan_detail.mohon_det_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "sis_permohonan.mohon_id AS id", "sis_permohonan.mohon_id AS mohon_id", "master_sertifikasi.sert_id AS sert_id", 'sis_permohonan_detail.mohon_det_id AS mohon_det_id')->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_permohonan_detail.mohon_det_id');

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['nomor_sni']                = $d->sert_sni;
			
            if ($request->jenis_status == 're-sertifikasi') {
                $x['komodt_id']        = $d->komodt_id;
                $x['komodt_nama']      = $d->komodt_nama;
                $x['kode_ea']          = $d->kode_ea_nama;
                $x['kode_nace']        = $d->kode_nace_nama;
                $x['tipe']             = $d->cust_sert_tipe;
                $x['merk']             = $d->cust_sert_merk;
                $x['nomor_sertifikat'] = $d->cust_sert_nomor_sertifikat;
                $x['nomor_referensi']  = $d->cust_sert_nomor_referensi;
                $x['nomor_sni']        = $d->cust_sert_nomor_sni;
                $x['lingkup']          = $d->cust_sert_lingkup;
                $x['cust_sert_id']     = $d->cust_sert_id;
				$x['produksi_tahunan']           = $d->cust_sert_produksi_tahunan;
				$x['satuan']           = $d->cust_sert_produksi_tahunan_satuan;
            }
            $x['id']                 = $d->id;
            $x['deskripsi']                = "Permohonan nomor #" . $d->mohon_id . " " . $d->sert_nama;
            $x['mohon_det_id']				= $d->mohon_det_id;
            $x['nama']                     = $d->sert_nama;
            $x['cust_sert_id']             = $d->cust_sert_id;
            $x['mohon_id']                 = $d->id;
            $x['cust_id']                  = $d->cust_id;
            $x['user_id']                  = $d->user_id;
            $x['sert_id']                  = $d->sert_id;
            $x['sert_nama']                = $d->sert_nama;
            $x['mohon_harga_permohonan']   = $d->mohon_harga_permohonan;
            $x['mohon_harus_lunas_status'] = $d->mohon_harus_lunas_status;
            $x['mohon_cust_nama']          = $d->mohon_cust_nama;
            $x['sert_is_product']          = $d->sert_is_product;
            $x['mohon_jenis_status']       = ($d->mohon_det_jenis_status == 'lama') ? 're-sertifikasi' : 'sertifikasi baru';
            $x['created_at']               = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']                = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_permohonan_komoditi(Request $request)
    {
        $data = SisPermohonanKomoditi::join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id");
		$data->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id");
		$data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_permohonan_detail.sert_id");
		$data->join('sis_permohonan', "sis_permohonan.mohon_id", "=", "sis_permohonan_detail.mohon_id");
        // Filter
        $data->where('sis_permohonan_komoditi.mohon_det_id', '=', $request->mohon_det_id);

        if (!empty($request->q)) {
            $data->where('komodt_nama', 'LIKE', '%' . $request->q . '%');
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['sert_is_product']           = $d->sert_is_product;
            $x['mohon_kmditi_id']           = $d->mohon_kmditi_id;
            $x['komodt_id']           = $d->komodt_id;
            $x['komodt_nama']         = $d->komodt_nama;
            $x['mohon_kmditi_sni']    = $d->mohon_kmditi_sni;
            $x['mohon_kmditi_merk']   = $d->mohon_kmditi_merk;
            $x['mohon_kmditi_tipe']   = $d->mohon_kmditi_tipe;
            $x['mohon_kmditi_ukuran'] = $d->mohon_kmditi_ukuran;
            $x['mohon_kmditi_ea'] = $d->mohon_kmditi_ea;
            $x['mohon_kmditi_nace'] = $d->mohon_kmditi_nace;
            $x['mohon_kmditi_ruang_lingkup'] = $d->mohon_kmditi_ruang_lingkup;
            $x['mohon_kmditi_kapasitas_produksi_tahunan_satuan'] = $d->mohon_kmditi_kapasitas_produksi_tahunan_satuan;
            $x['mohon_kmditi_kapasitas_produksi_tahunan'] = $d->mohon_kmditi_kapasitas_produksi_tahunan;
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
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['deskripsi'] = "Survailan untuk sertifikat " . $d->sert_nama . " nomor referensi " . $d->cust_sert_nomor_referensi;
            $x['id']        = $d->cust_sert_id;
            $x['nama']      = $d->sert_nama;


            $x['sert_nama']        = $d->sert_nama;
            $x['komodt_id']        = $d->komodt_id;
            $x['komodt_nama']      = $d->komodt_nama;
            $x['kode_ea']          = $d->kode_ea_nama;
            $x['kode_nace']        = $d->kode_nace_nama;
            $x['tipe']             = $d->cust_sert_tipe;
            $x['merk']             = $d->cust_sert_merk;
            $x['nomor_sertifikat'] = $d->cust_sert_nomor_sertifikat;
            $x['nomor_referensi']  = $d->cust_sert_nomor_referensi;
            $x['nomor_sni']        = $d->cust_sert_nomor_sni;
            $x['lingkup']          = $d->cust_sert_lingkup;
            $x['produksi_tahunan']           = $d->cust_sert_produksi_tahunan;
            $x['satuan']           = $d->cust_sert_produksi_tahunan_satuan;
			
            $x['cust_sert_id']                       = $d->cust_sert_id;
            $x['sert_id']                            = $d->sert_id;
            $x['cust_sert_nomor_referensi']          = $d->cust_sert_nomor_referensi;
            $x['cust_sert_tgl_sertifikat_awal']      = $d->cust_sert_tgl_sertifikat_awal?->format("Y-m-d");
            $x['cust_sert_tgl_sertifikat_perubahan'] = $d->cust_sert_tgl_sertifikat_perubahan?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_kode_ea(Request $request)
    {
        $data   = MasterKodeEa::select('*');
        $result = [];
        foreach ($data->get() as $d) {
            $x['id']   = $d->kode_ea_id;
            $x['nama'] = $d->kode_ea_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    private function ajax_combobox_kode_nace(Request $request)
    {
        $data   = MasterKodeNace::select('*');
        $result = [];
        foreach ($data->get() as $d) {
            $x['id']   = $d->kode_nace_id;
            $x['nama'] = $d->kode_nace_nama;
            array_push($result, $x);
        }

        return response()->json($result);
    }

    public function create()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tanggal'),
            new BreadcrumbsStruct('Input Penjadwalan'),
        ];

        $masterKodeEa   = MasterKodeEa::all();
        $masterKodeNace = MasterKodeNace::all();

        $parser = [
            'module'      => $this->module,
            'url'         => $this->url,
            'breadcrumbs' => $breadcrumbs,
        ];
        return view("operatorls::penjadwalan.create")->with($parser);
    }

    public function store(Request $request)
    {

        $request->validate([
            "cust_id"              => 'required',
            "jadw_tanggal_status"  => 'required',
            "jadw_tanggal_mulai"   => 'required',
            "jadw_tanggal_selesai" => 'required',
            "jadw_jenis"           => 'required',
            "jadwal_items"         => 'required',
        ]);

        try {
            DB::beginTransaction();
            $newSisJadwal                       = new SisJadwal();
            $newSisJadwal->cust_id              = $request['cust_id'];
            $newSisJadwal->bill_id              = $request['bill_id'];
            $newSisJadwal->jadw_tanggal_status  = $request['jadw_tanggal_status'];
            $newSisJadwal->jadw_tanggal_mulai   = $request['jadw_tanggal_mulai'];
            $newSisJadwal->jadw_tanggal_selesai = $request['jadw_tanggal_selesai'];
            $newSisJadwal->jadw_jenis           = $request['jadw_jenis'];
            $newSisJadwal->created_at           = Carbon::now();
            $newSisJadwal->updated_at           = Carbon::now();
            $newSisJadwal->save();

            // add items
            $dataItems = json_decode($request['jadwal_items']);
			$mohon_id = [];
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
					'jadw_audit_status' => 'on-going',
					'jadw_audit_jenis' => $itm->jenis,
					'mohon_id' => ($itm->mohon_id != '') ? $itm->mohon_id : null,
					'mohon_det_id' => ($itm->mohon_det_id != '') ? $itm->mohon_det_id : null,
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

				if ($itm->mohon_id != '') {
					if(!in_array($itm->mohon_id, $mohon_id, true)){
						array_push($mohon_id, $itm->mohon_id);
					}
				}
            }
			
			if (!empty($mohon_id)) {
				foreach ($mohon_id as $val) {
					SisPermohonanStatus::create([
						"status_mohon_id" => $val,
						"status_tipe"     => "informasi",
						"status_judul"    => "Informasi Pengajuan",
						"status_pesan"    => sprintf("Permohonan dengan nomor #%s telah diinputkan pada jadwal, silahkan lihat pada menu jadwal anda.", $itm->mohon_id),
						"created_at"      => Carbon::now(),
						"updated_at"      => Carbon::now(),
					]);
				}
			}

            DB::commit();
			
			$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
			// Send Push
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = 'Penjadwalan Audit Tahap II';
			$notifStruct->message   = sprintf("Penjadwalan Audit tahap II telah diterbitkan , yang akan dilakukan pada tanggal %s s/d %s, silahkan konfirmasi tanggal.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']);
			$notifStruct->user_id   = $data_pelanggan?->user_id;
			$notifStruct->click_url = url('/pelanggan/jadwal');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Penjadwalan Audit Tahap II";
			$structEmail->body    = view('operatorls::penjadwalan.mails.publish')
				->with([
					'nama'       => $data_pelanggan?->cust_nama,
					'message'       => sprintf("Penjadwalan Audit tahap II telah diterbitkan , yang akan dilakukan pada tanggal %s s/d %s, silahkan konfirmasi tanggal.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']),
					'link_verif'        => url('/pelanggan/jadwal'),
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
            'log-jadwal' => $this->log_jadwal($request),
            default      => null,
        };
    }

    private function log_jadwal(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penjadwalan Tanggal'),
            new BreadcrumbsStruct('Log Revisi'),
        ];

        $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->select('*');

        $dataLog = SisJadwalLog::where('jadw_id', $request['jadw_id']);
        $dataLog->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_jadwal' => $dataJadwal->get()[0], 'dataLog' => $dataLog->get()];
        return view("operatorls::penjadwalan.log_jadwal")->with($parser);
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
            new BreadcrumbsStruct('Penjadwalan Tanggal'),
            new BreadcrumbsStruct('Edit Jadwal'),
        ];

        $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_billing', 'sis_billing.bill_id', '=', 'sis_jadwal.bill_id');
        $dataJadwal->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("operatorls::penjadwalan.edit_jadwal")->with($parser);
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
            'edit-jadwal'        => $this->update_jadwal($request),
            'update-item-jadwal' => $this->update_item_jadwal($request),
            default              => null,
        };
    }

    private function update_jadwal(Request $request)
    {
        $request->validate([
            "cust_id"              => 'required',
            "jadw_id"              => 'required',
            "jadw_tanggal_status"  => 'required',
            "jadw_tanggal_mulai"   => 'required',
            "jadw_tanggal_selesai" => 'required',
            "jadw_jenis"           => 'required',
        ]);

        try {
            DB::beginTransaction();
            $dt_update = [
                'jadw_tanggal_status'  => 'on-going',
                'jadw_tanggal_mulai'   => $request['jadw_tanggal_mulai'],
                'jadw_tanggal_selesai' => $request['jadw_tanggal_selesai'],
                'jadw_jenis'           => $request['jadw_jenis'],
            ];
            SisJadwal::findOrFail($request['jadw_id'])->update($dt_update);

            if ($request['jadw_tanggal_status'] == 'rejected') {
                $newSisJadwalLog             = new SisJadwalLog();
                $newSisJadwalLog->jadw_id    = $request['jadw_id'];
                $newSisJadwalLog->jlog_tipe  = 'revisi-tanggal';
                $newSisJadwalLog->jlog_judul = 'Koreksi Data Tanggal';
                $newSisJadwalLog->jlog_pesan = 'Telah dilakukan update untuk tanggal sesuai dengan kesepakatan.';
                $newSisJadwalLog->created_at = Carbon::now();
                $newSisJadwalLog->updated_at = Carbon::now();
                $newSisJadwalLog->save();
            }
            DB::commit();
			
			$data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
			// Send Push
			$notifStruct            = new NotifStruct();
			$notifStruct->title     = 'Penjadwalan Audit Tahap II';
			$notifStruct->message   = sprintf("Penjadwalan Audit tahap II telah diterbitkan dan direvisi, yang akan dilakukan pada tanggal %s s/d %s, silahkan konfirmasi tanggal.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']);
			$notifStruct->user_id   = $data_pelanggan?->user_id;
			$notifStruct->click_url = url('/pelanggan/jadwal');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Penjadwalan Audit Tahap II";
			$structEmail->body    = view('operatorls::penjadwalan.mails.publish')
				->with([
					'nama'       => $data_pelanggan?->cust_nama,
					'message'       => sprintf("Penjadwalan Audit tahap II telah diterbitkan dan direvisi, yang akan dilakukan pada tanggal %s s/d %s, silahkan konfirmasi tanggal.", $request['jadw_tanggal_mulai'], $request['jadw_tanggal_selesai']),
					'link_verif'        => url('/pelanggan/jadwal'),
				])->render();
			$structEmail->to      = $data_pelanggan?->cust_email;
			sendEmail($structEmail);
			
            return responseJSON(200, null, "Data jadwal berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function update_item_jadwal(Request $request)
    {
        $request->validate([
            "jadw_id"                     => 'required',
            "jadw_audit_id"               => 'nullable',
            "jadw_audit_jenis"            => 'required',
            "sert_id"                     => 'required',
            "komodt_id"                   => 'nullable',
            "mohon_id"                    => 'nullable',
            "mohon_det_id"                    => 'nullable',
            "cust_sert_id"                => 'nullable',
            "jadw_audit_nomor_sertifikat" => 'nullable',
            "jadw_audit_nomor_referensi"  => 'nullable',
            "jadw_audit_kode_nace"        => 'required',
            "jadw_audit_kode_ea"          => 'required',
            "jadw_audit_standart_acuan"   => 'required',
            "jadw_audit_ruang_lingkup"    => 'required',
            "jadw_audit_kegiatan"         => 'required',
            "jadw_audit_tujuan_audit"     => 'required',
            "jadw_audit_sni"              => 'nullable',
            "jadw_audit_merk"             => 'nullable',
            "jadw_audit_tipe"             => 'nullable',
            "jadw_audit_ukuran"           => 'nullable',
            "jadw_audit_kapasitas_produksi_tahunan"           => 'nullable',
            "jadw_audit_kapasitas_produksi_tahunan_satuan"           => 'nullable',
        ]);

        try {
            
            if ($request['jadw_audit_id'] != '') {
				$dt_update = [
					'jadw_audit_standart_acuan'   => $request['jadw_audit_standart_acuan'],
					'jadw_audit_kegiatan'         => $request['jadw_audit_kegiatan'],
					'jadw_audit_tujuan_audit'     => $request['jadw_audit_tujuan_audit'],
				];
                SisJadwalAudit::findOrFail($request['jadw_audit_id'])->update($dt_update);
            } else {
				$dt_insert = [
					'jadw_audit_jenis'            => $request['jadw_audit_jenis'],
					'jadw_id'                     => $request['jadw_id'],
					'sert_id'                     => $request['sert_id'],
					'komodt_id'                   => $request['komodt_id'],
					'mohon_id'                    => $request['mohon_id'],
					'mohon_det_id'                    => $request['mohon_det_id'],
					'cust_sert_id'                => $request['cust_sert_id'],
					'jadw_audit_nomor_sertifikat' => $request['jadw_audit_nomor_sertifikat'],
					'jadw_audit_nomor_referensi'  => $request['jadw_audit_nomor_referensi'],
					'jadw_audit_kode_nace'        => $request['jadw_audit_kode_nace'],
					'jadw_audit_kode_ea'          => $request['jadw_audit_kode_ea'],
					'jadw_audit_standart_acuan'   => $request['jadw_audit_standart_acuan'],
					'jadw_audit_ruang_lingkup'    => $request['jadw_audit_ruang_lingkup'],
					'jadw_audit_kegiatan'         => $request['jadw_audit_kegiatan'],
					'jadw_audit_tujuan_audit'     => $request['jadw_audit_tujuan_audit'],
					'jadw_audit_sni'              => $request['jadw_audit_sni'],
					'jadw_audit_merk'             => $request['jadw_audit_merk'],
					'jadw_audit_tipe'             => $request['jadw_audit_tipe'],
					'jadw_audit_ukuran'           => $request['jadw_audit_ukuran'],
					'jadw_audit_kapasitas_produksi_tahunan'           => $request['jadw_audit_kapasitas_produksi_tahunan'],
					'jadw_audit_kapasitas_produksi_tahunan_satuan'           => $request['jadw_audit_kapasitas_produksi_tahunan_satuan'],
				];
                SisJadwalAudit::create($dt_insert);
            }
            return responseJSON(200, null, "Data jadwal berhasil disimpan.");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'data-jadwal'       => $this->delete_data_jadwal($request),
            'data-jadwal-audit' => $this->delete_data_jadwal_audit($request),
            default             => null,
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

    private function delete_data_jadwal_audit(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisJadwalAudit::where("jadw_audit_id", $id)->firstOrFail();
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
