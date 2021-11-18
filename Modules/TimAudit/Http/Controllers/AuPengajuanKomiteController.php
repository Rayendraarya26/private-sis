<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisAuditLapLengkap;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\TimAudit\Http\Traits\AuditorTraits;

class AuPengajuanKomiteController extends Controller
{
	use AuditorTraits;
	
    public $module = self::class;
    private $url = 'timaudit/auditor/pengajuan-komite';
    private $view = "timaudit::auditor_pengajuan_komite";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Pengajuan Komite'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }
	
	public function detail(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'detail-audit' => $this->detail_audit($request),
            default         => null,
        };
    }
	
	public function detail_audit(Request $request)
    {
		try {
            $dataJadwal  = $this->isKepalaAuditDetail($request['jadw_id']);
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
				new BreadcrumbsStruct('Pengajuan Komite', url($this->url)),
				new BreadcrumbsStruct('Detail Audit'),
			];

            $dataLKS = [
                'jumlah' => ['kritis' => 0, 'mayor' => 0, 'minor' => 0, 'total' => 0],
            ];
            foreach ($dataJadwal->sis_jadwal_audits as $ja) {
                foreach ($ja->sis_audit_lks as $lks) {
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

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'dataLKS' => $dataLKS];

            return view("$this->view.detail_audit")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
	
	public function edit(Request $request)
    {
		try {
            $dataJadwal  = $this->isKepalaAudit($request['jadw_id']);
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
				new BreadcrumbsStruct('Pengajuan Komite', url($this->url)),
				new BreadcrumbsStruct('Proses Ajukan'),
			];

            $dataLKS = [
                'jumlah' => ['kritis' => 0, 'mayor' => 0, 'minor' => 0, 'total' => 0],
            ];
            foreach ($dataJadwal->sis_jadwal_audits as $ja) {
                foreach ($ja->sis_audit_lks as $lks) {
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

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'dataLKS' => $dataLKS];

            return view("$this->view.edit")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
	
	public function update(Request $request)
    {
        $request->validate([
            "jadw_id"          => 'required',
        ]);

        $uploadedPath = [];
        try {
            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];
            DB::beginTransaction();

            DB::table('sis_jadwal_audit')
                ->where('jadw_id', $request['jadw_id'])
                ->update(['jadw_audit_status_komite' => 'submited']);

            // Notifikasi
            /*
			ke LS dan Pelanggan
			*/
            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
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
        $data->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sis_billing', "sis_jadwal.bill_id", "=", "sis_billing.bill_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ketua');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ketua');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				if($f->field == 'jadw_id')
					$data->where('sis_jadwal.jadw_id', 'LIKE', '%' . $f->value . '%');
				else if($f->field == 'status_komite')
					$data->where('sis_jadwal_audit.jadw_audit_status_komite', 'LIKE', '%' . $f->value . '%');
				else
					$data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if($sort[$i] == 'jadw_id')
					$data->orderBy('sis_jadwal.jadw_id', $order[$i]);
				else if($sort[$i] == 'status_komite')
					$data->orderBy('sis_jadwal_audit.jadw_audit_status_komite', $order[$i]);
				else
					$data->orderBy($sort[$i], $order[$i]);
            }
        }
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_status_komite) AS status_komite");
        $data->selectRaw("GROUP_CONCAT(distinct sert_nama) AS sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_tim_kesanggupan) AS jadw_tim_kesanggupan");
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;
            $x['status_komite']     = ($d->status_komite);
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }
}
