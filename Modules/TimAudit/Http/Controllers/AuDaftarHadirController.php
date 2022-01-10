<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalAudit;
use App\Models\BbkkpSis\SisJadwalLog;
use App\Models\BbkkpSis\SisAuditLks;
use App\Models\BbkkpSis\SysUserGroup;
use Modules\TimAudit\Http\Traits\AuditorTraits;

use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Http\Structs\BreadcrumbsStruct;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\TimAudit\Http\Traits\LksTrait;


class AuDaftarHadirController extends Controller
{
    use AuditorTraits, LksTrait;

    public $module = self::class;
    private $url = 'timaudit/auditor/daftar-hadir';
    private $view = "timaudit::auditor_daftar_hardir";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Auditor', url($this->url)),
            new BreadcrumbsStruct('Rapat Akhir'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function unggah(Request $request, $jadwalID)
    {
		try {
            $dataJadwal  = $this->isKepalaAuditDetail($jadwalID);
            $breadcrumbs = [
				new BreadcrumbsStruct('Tim Audit'),
				new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
                new BreadcrumbsStruct('Rapat Akhir'),
				new BreadcrumbsStruct('Kelengkapan'),
			];

            $dataLKS = $this->calculateTemuanLKS($dataJadwal);

			$dataAuditTim = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
							->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
							->leftJoin('sis_audit_daftar_periksa', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_daftar_periksa.jadw_tim_id")
							->where('sis_jadwal.jadw_id', '=', $jadwalID)->where('sis_jadwal_tim.jadw_tim_posisi', '!=', 'ppc')->select('*');


			$dataTimLogbook = SisJadwal::join('sis_jadwal_tim', "sis_jadwal.jadw_id", "=", "sis_jadwal_tim.jadw_id")
							->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
							->leftJoin('sis_audit_logbook', "sis_jadwal_tim.jadw_tim_id", "=", "sis_audit_logbook.jadw_tim_id")
							->where('sis_jadwal.jadw_id', '=', $jadwalID)->select('*');



			$SisJadwalLog = SisJadwalLog::where('jadw_id', $jadwalID)->where('jlog_tipe', 'revisi-temuan')->select('*');

            $parser = [
				'module' => $this->module,
				'url' => $this->url,
				'view' => $this->view,
				'breadcrumbs' => $breadcrumbs,
				'data' => $dataJadwal,
				'dataLKS' => $dataLKS,
				'dataAuditTim' => $dataAuditTim->get(),
				'dataTimLogbook' => $dataTimLogbook->get(),
				'SisJadwalLog' => $SisJadwalLog->get(),
			];

            return view("$this->view.unggah")->with($parser);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function storeUnggah(Request $request, $jadwalID)
    {
		$request->validate([
            'cust_nama' => 'required',
            'cust_email' => 'required',
            'user_id' => 'required',
            'cust_id' => 'required',
            'jadw_id' => 'required',
            'jadw_tanggal_rapat_akhir' => 'required|string',
            'jadw_notulen_rapat' => 'required|string',
            'jadw_setujui_temuan' => 'required|string',
        ]);

        $newFilePath = [];
        $oldFilePath = [];
        try {
            $dataJadwal = $this->isKepalaAudit($jadwalID);
            if (!empty($dataJadwal->jadw_file_kehadiran)) array_push($oldFilePath, $dataJadwal->jadw_file_kehadiran);

            $baseFileUpload = sprintf(config("app.path_file_audit"), $dataJadwal->jadw_id);
            if ($request->hasFile('jadw_file_kehadiran')) {
                $fileKehadiran     = $request->file('jadw_file_kehadiran');
                $fileKehadiranName = Str::slug('file-kehadiran-' . $request['jadw_id'] . '-' . $fileKehadiran->getClientOriginalName()) . '-' . time() . '.' . $fileKehadiran->getClientOriginalExtension();
                $fileKehadiranPath = sprintf("%s/%s", $baseFileUpload, $fileKehadiranName);
                $fileKehadiran->move($baseFileUpload, $fileKehadiranName);

                $dataJadwal->jadw_file_kehadiran = $fileKehadiranPath;
                array_push($newFilePath, public_path($fileKehadiranPath));
            }

			$dataJadwal->jadw_setujui_temuan = $request['jadw_setujui_temuan'];
			$dataJadwal->jadw_notulen_rapat = $request['jadw_notulen_rapat'];
			$dataJadwal->jadw_tanggal_rapat_akhir = $request['jadw_tanggal_rapat_akhir'];

            $dataJadwal->save();
            foreach ($oldFilePath as $path) { // remove old file
                @unlink($path);
            }

			$notifStruct            = new NotifStruct();
			$notifStruct->title     = 'Tinjauan Rapat Akhir';
			$notifStruct->message   = sprintf("Silahkan konfirmasi tinjauan Rapat akhir untuk temuan dari LKS yang sudah ditemukan pada jadwal No #%s.", $request['jadw_id']);
			$notifStruct->user_id   = $request['user_id'];
			$notifStruct->click_url = url('/pelanggan/tahap2/persetujuan-temuan');
			sendNotification($notifStruct);

			// Send Email
			$structEmail          = new EmailStruct();
			$structEmail->subject = "Tinjauan rapat Akhir";
			$structEmail->body    = view($this->view.'.mails.publish')
				->with([
					'nama'       => $request['cust_nama'],
					'message'       => sprintf("Silahkan konfirmasi tinjauan Rapat akhir untuk temuan dari LKS yang sudah ditemukan pada jadwal No #%s.", $request['jadw_id']),
					'link_verif'        => url('/pelanggan/tahap2/persetujuan-temuan'),
				])->render();
			$structEmail->to      = $request['cust_email'];
			sendEmail($structEmail);

            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            foreach ($newFilePath as $path) { // remove new file uploaded
                @unlink($path);
            }
            return responseJSON(500, [], $e->getMessage());
        }
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

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        });

        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->leftJoin('sis_audit_daftar_periksa', function ($join) {
            $join->on("sis_audit_daftar_periksa.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
        });

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'on-going');
        $data->whereIn('sis_jadwal_tim.jadw_tim_posisi', ['ketua']);
        $data->whereIn('sis_jadwal.jadw_setujui_temuan', [ 'revisi', 'none']);
		// 'diajukan',
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');

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
            $isUploaded = !empty($d->jadw_file_kehadiran);

            $x['is_uploaded']          = $isUploaded;
            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_setujui_temuan']              = $d->jadw_setujui_temuan;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = ucwords($d->jadw_jenis);
            $x['total_jadwal']         = $d->sis_jadwal_audits->count();
            array_push($result, $x);
        }


        return response()->json(["total" => $total, "rows" => $result]);
    }

	public function detail(Request $request, $jadwalID, $type)
    {
        try {
            $data = SisJadwal::join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id')
                ->with(['sis_jadwal_audits', 'sis_pelanggan', 'sis_jadwal_tims', 'sis_audit_lap_ringkas'])->find($jadwalID);
            if (empty($data)) throw new Exception('Data jadwal tidak ditemukan atau anda tidak mendapatkan akses');

            return match ($type) {
                'lap-ringkas'      => $this->cetak_lap_ringkas($request, $data),
                'lks'          => $this->cetak_lks($request, $data),
                default        => throw new Exception("Invalid URL"),
            };
        } catch (Exception $e) {
            return redirect($this->url)->withErrors(['message' => $e->getMessage()]);
        }
    }

	private function cetak_lap_ringkas(Request $request, SisJadwal $dataJadwal)
    {
        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $dataLKS = $this->calculateTemuanLKS($dataJadwal);

        $parser = ['dataJadwal' => $dataJadwal, 'dataKetua' => $dataKetua, 'dataLKS' => $dataLKS];
        $pdf    = PDF::loadView("$this->view.print.lap-ringkas", $parser)
            ->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    private function cetak_lks(Request $request, SisJadwal $dataJadwal)
    {
        $dataLKS = SisAuditLks::with(['sis_jadwal_tim', 'sis_audit_lks_files'])
            ->join("sis_jadwal", "sis_jadwal.jadw_id", "=", "sis_audit_lks.jadw_id")
            ->where('sis_jadwal.cust_id', auth()->user()->sis_pelanggan->cust_id)
            ->where('sis_jadwal.jadw_id', $dataJadwal->jadw_id)
            ->get();

        $dataKetua = $dataJadwal->sis_jadwal_tims->where('jadw_tim_posisi', 'ketua')->first();

        $parser = ['dataJadwal' => $dataJadwal, 'dataLks' => $dataLKS, 'dataKetua' => $dataKetua];

        $pdf = PDF::loadView("$this->view.print.lks", $parser)
            ->setPaper('a4', 'landscape');
        return $pdf->stream();
    }
}
