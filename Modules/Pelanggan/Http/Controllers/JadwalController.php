<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/jadwal';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::jadwal.index')->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid'            => $this->ajax_datagrid($request),
            'tinymce-uploadimage' => $this->ajax_tinymce_uploadimage($request),
            default               => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisJadwal::with(['sis_audit_tim_komites', 'sis_jadwal_tims'])
            ->with([
                'sis_jadwal_logs' => function ($query) {
                    $query->orderBy('jlog_tipe')->orderBy('jlog_id', 'desc')->whereIn('jlog_tipe', ['revisi-team', 'revisi-tanggal']);
                }
            ]);
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
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $logs = [];
            foreach ($d->sis_jadwal_logs as $log) {
                array_push($logs, [
                    'tipe'    => $log->jlog_tipe,
                    'judul'   => $log->jlog_judul,
                    'pesan'   => $log->jlog_pesan,
                    'tanggal' => $log->created_at->isoFormat('LLLL'),
                ]);
            }

            $x['jadw_id']              = $d->jadw_id;
            $x['jadw_tanggal_status']  = $d->jadw_tanggal_status;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['jadw_team_status']     = $d->jadw_team_status;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_file_jadwal']     = $d->jadw_file_jadwal;
            $x['enable_approval_tim']  = $d->sis_jadwal_tims->count() > 0;
            $x['logs']                 = $logs;
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
            $img->move(public_path(config('email.email_template_image_url')), $imgName);
            $publicUrl = asset(config('email.email_template_image_url') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }

    public function approveTanggal(Request $request, $jadwalID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal', url($this->url)),
            new BreadcrumbsStruct('Persetujuan Tanggal'),
        ];

        $data = SisJadwal::with(['sis_pelanggan', 'sis_jadwal_audits'])
            ->where('jadw_id', $jadwalID)
            ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
            ->firstOrFail();


        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view('pelanggan::jadwal.approve_tanggal')->with($parser);
    }

    public function processApproveTanggal(Request $request, $jadwalID)
    {
        $request->validate(['jadw_tanggal_status' => 'required', Rule::in(['revisi', 'accepted'])]);

        try {
            DB::beginTransaction();
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->firstOrFail();

            if ($request['jadw_tanggal_status'] == "revisi") {
                if (strip_tags($request['editor_revisi']) == "") throw new Exception("Anda harus mengisikan keterangan revisi");

                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-tanggal',
                    'jlog_judul' => sprintf('Revisi Tanggal Oleh %s', auth()->user()?->sis_pelanggan->cust_nama),
                    'jlog_pesan' => $request['editor_revisi'],
                ]);
            }

            $data->jadw_tanggal_status = $request['jadw_tanggal_status'];
            $data->save();

            DB::commit();
            return redirect(url($this->url))->with("message", sprintf("Persetujuan berhasil dikirim (%s)", $request['jadw_tanggal_status']));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function approveTim(Request $request, $jadwalID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Jadwal', url($this->url)),
            new BreadcrumbsStruct('Persetujuan Tim'),
        ];

        $data = SisJadwal::with(['sis_pelanggan', 'sis_jadwal_audits', 'sis_jadwal_tims.master_pegawai'])
            ->with([
                'sis_jadwal_tims' => function ($query) {
                    $query->orderBy(DB::raw("FIELD(jadw_tim_posisi, 'ketua', 'auditor', 'ppc', 'observer')"));
                }
            ])
            ->where('jadw_id', $jadwalID)
            ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
            ->firstOrFail();

        if ($data->sis_jadwal_tims->count() == 0) abort(404);


        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data' => $data];
        return view('pelanggan::jadwal.approve_tim')->with($parser);
    }

    public function processApproveTim(Request $request, $jadwalID)
    {
        $request->validate(['jadw_team_status' => 'required', Rule::in(['revisi', 'accepted'])]);

        try {
            DB::beginTransaction();
            $data = SisJadwal::where('jadw_id', $jadwalID)
                ->where('cust_id', auth()->user()?->sis_pelanggan->cust_id)
                ->firstOrFail();

            if ($request['jadw_team_status'] == "revisi") {
                if (strip_tags($request['editor_revisi']) == "") throw new Exception("Anda harus mengisikan keterangan revisi");

                SisJadwalLog::create([
                    'jadw_id'    => $data->jadw_id,
                    'jlog_tipe'  => 'revisi-team',
                    'jlog_judul' => sprintf('Revisi Tim Oleh %s', auth()->user()?->sis_pelanggan->cust_nama),
                    'jlog_pesan' => $request['editor_revisi'],
                ]);
            }

            $data->jadw_team_status = $request['jadw_team_status'];
            $data->save();

            DB::commit();
            return redirect(url($this->url))->with("message", sprintf("Persetujuan berhasil dikirim (%s)", $request['jadw_team_status']));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }

    }
}
