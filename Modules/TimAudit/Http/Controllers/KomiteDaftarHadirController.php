<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\TimAudit\Http\Traits\AuditorTraits;

class KomiteDaftarHadirController extends Controller
{
    use AuditorTraits;

    public $module = self::class;
    private $url = 'timaudit/komite/daftar-hadir';
    private $view = "timaudit::komite_daftar_hardir";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Komite', url($this->url)),
            new BreadcrumbsStruct('Daftar Hadir'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function edit(Request $request)
    {
        try {
            $breadcrumbs = [
                new BreadcrumbsStruct('Tim Audit'),
                new BreadcrumbsStruct('Komite', url($this->url)),
                new BreadcrumbsStruct('Daftar Hadir'),
            ];
            $dataJadwal  = $this->isKepalaKomite($request['id_jadwal']);

            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $dataJadwal, 'jadwal_id' => $request['id_jadwal']];

            return view("$this->view.unggah")->with($parser);
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) {
                log_error($e, $request->except("_token"));
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $newFilePath = [];
        $oldFilePath = [];
        try {
            $dataJadwal = $this->isKepalaKomite($request['jadw_id']);
            if (!empty($dataJadwal->jadw_file_kehadiran_komite)) array_push($oldFilePath, $dataJadwal->jadw_file_kehadiran_komite);

            $baseFileUpload = sprintf(config("app.path_file_audit"), $dataJadwal->jadw_id);
            if ($request->hasFile('jadw_file_kehadiran_komite')) {
                $fileKehadiran     = $request->file('jadw_file_kehadiran_komite');
                $fileKehadiranName = Str::slug('file-kehadiran-komite-'. $fileKehadiran->getClientOriginalName()) . '-' . time() . '.' . $fileKehadiran->getClientOriginalExtension();
                $fileKehadiranPath = sprintf("%s/%s", $baseFileUpload, $fileKehadiranName);
                $fileKehadiran->move($baseFileUpload, $fileKehadiranName);

                $dataJadwal->jadw_file_kehadiran_komite = $fileKehadiranPath;
                $newFilePath[]                          = public_path($fileKehadiranPath);
            }

            $dataJadwal->save();
            foreach ($oldFilePath as $path) { // remove old file
                @unlink($path);
            }

            return redirect(url($this->url))->with('message', "Unggah berhasil");
        } catch (Exception $e) {
            foreach ($newFilePath as $path) { // remove new file uploaded
                @unlink($path);
            }
            if (!($e instanceof ExpectedException)) {
                log_error($e, $request->except("_token"));
            }
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
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
        $data = SisJadwal::with('sis_jadwal_audits.sis_audit_lks');
        $data->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join("sis_audit_tim_komite", "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->whereIn('sis_audit_tim_komite.komite_posisi', ['ketua']);
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', "=", "submited");
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if($f->field == 'jadw_id')
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
                if($sort[$i] == 'jadw_id')
					$data->orderBy('sis_jadwal.jadw_id', $order[$i]);
				else
					$data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(distinct sis_jadwal.jadw_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $isUploaded = !empty($d->jadw_file_kehadiran_komite);

            $x['is_uploaded']          = $isUploaded;
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = ucwords($d->jadw_jenis);
            $x['total_jadwal']         = $d->sis_jadwal_audits->count();
            $result[] = $x;
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }
}
