<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SisBilling;
use App\Models\BbkkpSis\SisBillingItems;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalTim;
use App\Models\BbkkpSis\SisJadwalLog;

use App\Models\BbkkpSis\MasterKodeEa;
use App\Models\BbkkpSis\MasterKodeNace;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TimController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/tim';
	
    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Tim Audit'),
        ];
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::tim.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit'       => $this->ajax_datagrid_jadwal_audit($request),
            default                     => null,
        };
    }
	
	private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
		$data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
		$data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
		$data->leftJoin('sis_jadwal_tim', "sis_jadwal_tim.jadw_audit_id", "=", "sis_jadwal_audit.jadw_audit_id");
			
        // Filter
		$data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
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
		
		$data->selectRaw("count(distinct jadw_tim_id) AS total_tim");
		
        // Pagination
        $data->select("*", "sis_jadwal_audit.jadw_audit_id AS jadw_audit_id");
		$data->skip(($request->page - 1) * $request->rows);
		$data->take($request->rows);
		$data->groupBy('sis_jadwal_audit.jadw_audit_id');
		
        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_audit_id'] = $d->jadw_audit_id;
            $x['total_tim'] = $d->total_tim;
            $x['jadw_tanggal_mulai'] = $d->jadw_tanggal_mulai;
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai;
            $x['cust_nama'] = $d->cust_nama;
            $x['sert_id'] = $d->sert_id;
            $x['sert_nama'] = $d->sert_nama;
            $x['jadw_jenis'] = $d->jadw_jenis;
            $x['jadw_audit_team_status'] = $d->jadw_audit_team_status;
            $x['jadw_audit_jenis'] = $d->jadw_audit_jenis;
            $x['mohon_id'] = $d->mohon_id;
            $x['sert_id'] = $d->sert_id;
            $x['sert_nama'] = $d->sert_nama;
            $x['komodt_id'] = $d->komodt_id;
            $x['komodt_nama'] = $d->komodt_nama;
            $x['cust_sert_id'] = $d->cust_sert_id;
            $x['jadw_audit_nomor_sertifikat'] = $d->jadw_audit_nomor_sertifikat;
            $x['jadw_audit_nomor_referensi'] = $d->jadw_audit_nomor_referensi;
            $x['jadw_audit_kode_nace'] = $d->jadw_audit_kode_nace;
            $x['jadw_audit_kode_ea'] = $d->jadw_audit_kode_ea;
            $x['jadw_audit_standart_acuan'] = $d->jadw_audit_standart_acuan;
            $x['jadw_audit_ruang_lingkup'] = $d->jadw_audit_ruang_lingkup;
            $x['jadw_audit_kegiatan'] = $d->jadw_audit_kegiatan;
            $x['jadw_audit_tujuan_audit'] = $d->jadw_audit_tujuan_audit;
            $x['jadw_audit_sni'] = $d->jadw_audit_sni;
            $x['jadw_audit_merk'] = $d->jadw_audit_merk;
            $x['jadw_audit_tipe'] = $d->jadw_audit_tipe;
            $x['jadw_audit_ukuran'] = $d->jadw_audit_ukuran;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
