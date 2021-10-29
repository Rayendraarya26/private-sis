<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalTim;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SisAuditDaftarPeriksa;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditDetailTahap1;

use App\Models\BbkkpSis\MasterKlausulTahap1;

use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class AuTahap1Controller extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1';
    private $view = "timaudit::auditor_tahap_1";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
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
		$data->join('sis_jadwal_tim', function($join)
                         {
                             $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
                         });
						 
		$data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");		
		$data->leftJoin('sis_audit_daftar_periksa', function($join)
                         {
                             $join->on("sis_audit_daftar_periksa.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
                         });
			
        // Filter
		$data->where('master_pegawai.user_id', '=', auth()->id());
		$data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
		$data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
		$data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
		$data->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua']);
		$data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
		$data->whereIn('sis_jadwal_audit.jadw_audit_jenis', ['tahap-1']);
		$data->whereNotNull('sis_jadwal.jadw_file_jadwal');
		
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
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
		$data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
		$data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
		$data->selectRaw("GROUP_CONCAT(distinct jadw_tim_kesanggupan) AS jadw_tim_kesanggupan");
		$data->skip(($request->page - 1) * $request->rows);
		$data->take($request->rows);
		$data->groupBy('sis_jadwal_audit.jadw_audit_id');
		
        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id'] = $d->jadw_id;
            $x['jadw_audit_id'] = $d->jadw_audit_id;
            $x['jadw_tanggal_mulai'] = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama'] = $d->cust_nama;
            $x['sert_nama'] = $d->sert_nama;
            $x['jadw_jenis'] = $d->jadw_jenis;
            $x['jadw_audit_jenis'] = $d->jadw_audit_jenis;
			
            $x['dftr_periksa_file'] = ($d->dftr_periksa_file != '') ? '<a class="btn-xs btn-success btn-block" target="_blank" href = "'.url($d->dftr_periksa_file).'"><i class="fas fa-cloud-download"></i> Download</a>' : '';
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
	
	public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
		return match ($request['tipe']) {
            'audit-tahap1' => $this->edit_audit_tahap1($request),
            default => null,
        };
    }
	
	private function edit_audit_tahap1(Request $request)
    {
		$breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Proses Audit Tahap 1'),
        ];
		
		$dataJadwal = SisJadwal::join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
		$dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
		$dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");		$dataJadwal->join('sis_jadwal_tim', function($join) {
							$join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
						});
		$dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
		$dataJadwal->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
		$dataJadwal->where('sis_jadwal_audit.jadw_audit_id', $request['jadw_audit_id']);
		$dataJadwal->where('master_pegawai.user_id', '=', auth()->id());
		$dataJadwal->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua']);
        $dataJadwal->select("*", "sis_jadwal.jadw_id AS jadw_id");
		$dataJadwal->groupBy('sis_jadwal_audit.jadw_audit_id');
		
		$dataAudit = SisAuditTahap1::join('sis_jadwal_audit', "sis_audit_tahap1.jadw_audit_id", "=", "sis_jadwal_audit.jadw_audit_id");
		$dataAudit->where('sis_jadwal_audit.jadw_audit_id', $request['jadw_audit_id']);
		$status_entry = false;
		
		
		$dataAuditKlausul = SisAuditTahap1::join('sis_jadwal_audit', "sis_audit_tahap1.jadw_audit_id", "=", "sis_jadwal_audit.jadw_audit_id");
		$dataAuditKlausul->join('sis_audit_detail_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_detail_tahap1.aud_thp1_id");
		$dataAuditKlausul->where('sis_jadwal_audit.jadw_audit_id', $request['jadw_audit_id']);
		
		if ($dataAudit->exists()) {
			$status_entry = true;
		}
		
        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'statusEntry' => $status_entry, 'dataAuditKlausul' => $dataAuditKlausul->get() ];		
        return view("$this->view.edit_audit_tahap1")->with($parser);
    }

	public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
		return match ($request['tipe']) {
            'update-generate-tahap1' => $this->update_generate_tahap1($request),
            default => null,
        };
    }
	
	private function update_generate_tahap1(Request $request)
	{
		$request->validate([
			"jadw_audit_id" => 'required',
			"sert_id" => 'required',
			"mohon_id" => 'required',
		]);
		
		try {
			DB::beginTransaction();
			$restData = DB::table('sis_audit_tahap1')->where('jadw_audit_id', $request['jadw_audit_id'])->first();
			if ($restData !== null) {
				DB::table('sis_audit_tahap1')
				  ->where('jadw_audit_id', $request['jadw_audit_id'])
				  ->update(["created_at" => Carbon::now(),"updated_at" => Carbon::now(),]);
				$aud_thp1_id = $restData->aud_thp1_id;
			}
			else{
				$newTahap1 = new SisAuditTahap1();
				$newTahap1->jadw_audit_id = $request['jadw_audit_id'];
				$newTahap1->created_at = Carbon::now();
				$newTahap1->updated_at = Carbon::now();
				$newTahap1->save();
				$aud_thp1_id = $newTahap1->aud_thp1_id;
			}
			
			$restDataDet = DB::table('sis_audit_detail_tahap1')->where('aud_thp1_id', $request['aud_thp1_id'])->first();
			if ($restDataDet === null) {
				$dts = DB::table('master_klausul_tahap1')->where('sert_id', '=', $request['sert_id'])->orderBy(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 1), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 2), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 3), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 4), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 5), '.', -1) + 0"))->get();
				
				if ($dts !== null) {
					foreach ($dts as $dt) {
							DB::table('sis_audit_detail_tahap1')->insert([
								'aud_thp1_id' => intval($aud_thp1_id),
								'klausul_thp1_id' => intval($dt->klausul_thp1_id),
								'aud_thp1_det_thp1_nomor' => $dt->klausul_thp1_nomor,
								'aud_thp1_det_peryataan' => $dt->klausul_thp1_peryataan,
								'aud_thp1_det_is_tinjauan' => $dt->klausul_thp1_is_tinjauan,
								'aud_thp1_det_kode_dok' => NULL,
								'aud_thp1_det_judul_dok' => NULL,
								'aud_thp1_det_hasil_tinjauan' => NULL,
								'aud_thp1_det_keterangan' => NULL,
							]);
						}
				}
			}
			
			DB::commit();
			return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
	}
}
