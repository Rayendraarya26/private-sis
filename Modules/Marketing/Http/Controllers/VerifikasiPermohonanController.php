<?php

namespace Modules\Marketing\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUser;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class VerifikasiPermohonanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public $module = self::class;
    private $url = 'marketing/verifikasi-permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("marketing::verifikasi_permohonan.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-permohonan' => $this->ajax_datagrid_permohonan($request),
            'tinymce-uploadimage' => $this->ajax_tinymce_uploadimage($request),
            default               => null,
        };
    }

    private function ajax_datagrid_permohonan(Request $request)
    {
        $data = SisPermohonan::join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");
        // Filter
        $data->whereIn('mohon_approved_status', ['on-progress', 'fix']);
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if($f->field == 'mohon_id')
                    $data->where('sis_permohonan.mohon_id', 'LIKE', '%' . $f->value . '%');
                else if($f->field == 'created_at')
                    $data->where('sis_permohonan.created_at', 'LIKE', '%' . $f->value . '%');
                else
                    $data->where($f->field, 'LIKE', '%' . $f->value . '%');
            }
        }
        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                if($sort[$i] == 'mohon_id')
                    $data->orderBy('sis_permohonan.mohon_id', $order[$i]);
                else if($sort[$i] == 'created_at')
                    $data->orderBy('sis_permohonan.created_at', $order[$i]);
                else
                    $data->orderBy($sort[$i], $order[$i]);
            }
        }
        // Total
        $total = $data->select(DB::raw('count(DISTINCT sis_permohonan.mohon_id) as total'))->first()->total;
        // Pagination
        $data->select("*", "sis_permohonan.created_at AS created_at", "sis_permohonan.updated_at AS updated_at", DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))->skip(($request->page - 1) * $request->rows)->take($request->rows);
        $data->groupBy('sis_permohonan.mohon_id');
        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['status_step'] = '';
            if (is_null($d->mohon_pernyataan_persetujuan_file)) {
                $x['status_step'] = 'verifikasi';
            }

            $x['cust_sert_id']           = $d->cust_sert_id;
            $x['mohon_id']               = $d->mohon_id;
            $x['cust_id']                = $d->cust_id;
            $x['user_id']                = $d->user_id;
            $x['sert_nama']              = $d->sert_nama;
            $x['mohon_cust_nama']        = $d->mohon_cust_nama;
            $x['mohon_det_jenis_status'] = $d->mohon_det_jenis_status;
            $x['created_at']             = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['updated_at']              = $d->updated_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
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
            $img->move(public_path(config('app.path_file_tinymce')), $imgName);
            $publicUrl = asset(config('app.path_file_tinymce') . '/' . $imgName);

            return response()->json(["location" => $publicUrl]);
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }

    }

    public function detail(Request $request, $mohonID)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'verifikasi' => $this->detail_verifikasi($request, $mohonID),
            default      => null,
        };

    }

    private function detail_verifikasi(Request $request, $mohonID)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $mohonID . '"'),
        ];

        $dataPermohon = SisPermohonan::where('mohon_id', $mohonID)->select('*')
            ->join('master_jenis_perusahaan', 'master_jenis_perusahaan.jenis_perusahaan_id', '=', 'sis_permohonan.jenis_perusahaan_id')
            ->leftJoin('master_negara', 'master_negara.negara_id', '=', 'sis_permohonan.negara_id')
            ->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan.kab_id')
            ->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan.kec_id')
            ->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan.prov_id');


        $dataPermohonSertifikasi = SisPermohonan::where('sis_permohonan_detail.mohon_id', $mohonID)->select('*')
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id");

        $dataPermohonKomoditi = SisPermohonanKomoditi::where('sis_permohonan_detail.mohon_id', $mohonID)->select('*')
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('master_komoditi', 'master_komoditi.komodt_id', '=', 'sis_permohonan_komoditi.komodt_id');


        $dataPermohonPabrik = SisPermohonanPabrik::where('mohon_id', $mohonID)->select('*')
            ->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_permohonan_pabrik.kab_id')
            ->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_permohonan_pabrik.kec_id')
            ->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_permohonan_pabrik.prov_id');

        $dataPermohonanDokumen = SisPermohonanDokumen::where('mohon_id', $mohonID)->select('*')
            ->join('master_jenis_dok_perusahaan', 'master_jenis_dok_perusahaan.jenis_dok_perusahaan_id', '=', 'sis_permohonan_dokumen.jenis_dok_perusahaan_id');


        $dataPermohonanStatus = SisPermohonanStatus::where('status_mohon_id', $mohonID)->where('status_tipe', 'revisi')->select('*');

        $parser = [
            'module'                  => $this->module,
            'url'                     => $this->url,
            'dataPermohon'            => $dataPermohon->get()[0],
            'dataPermohonKomoditi'    => $dataPermohonKomoditi->get(),
            'dataPermohonPabrik'      => $dataPermohonPabrik->get(),
            'dataPermohonanDokumen'   => $dataPermohonanDokumen->get(),
            'dataPermohonSertifikasi' => $dataPermohonSertifikasi->get(),
            'dataPermohonanStatus'    => $dataPermohonanStatus->get(),
            'breadcrumbs'             => $breadcrumbs
        ];
        return view('marketing::verifikasi_permohonan.detail_verifikasi')->with($parser);
    }

    public function edit(Request $request) // menerima parameter ID dari Modules\Master\Routes\web.php
    {
        $request->validate(['status' => 'required']);
        return match ($request['status']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'revisi'   => $this->edit_revisi($request),
            'accepted' => $this->edit_accepted($request),
            'rejected' => $this->edit_rejected($request),
            default    => null,
        };
    }

    private function edit_revisi(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Marketing'),
            new BreadcrumbsStruct('Verifikasi Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail Permohonan "#' . $request['mohon_id'] . '"', url($this->url . '/' . 'detail/' . $request['mohon_id'] . '?action=verifikasi')),
            new BreadcrumbsStruct('Revisi Permohonan "#' . $request['mohon_id'] . '"'),
        ];

        $dataPermohon = SisPermohonan::where('sis_permohonan.mohon_id', $request['mohon_id'])->select('*', DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->groupBy('sis_permohonan.mohon_id');

        $parser = [
            'module'       => $this->module,
            'url'          => $this->url,
            'dataPermohon' => $dataPermohon->get()[0],
            'breadcrumbs'  => $breadcrumbs
        ];
        return view("marketing::verifikasi_permohonan.edit_revisi")->with($parser);
    }

    private function edit_accepted(Request $request)
    {
        $data = $dataPermohon = SisPermohonan::where('sis_permohonan.mohon_id', $request['mohon_id'])->select('*', DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR ',<br/>') as sert_nama"))
                                    ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
                                    ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
                                    ->groupBy('sis_permohonan.mohon_id')->get()[0];

        $dataInsert = [
            'mohon_id'        => $request['mohon_id'],
            'status_mohon_id' => $request['mohon_id'],
            'status_tipe'     => 'Informasi',
            'status_pesan'    => 'Permohonan anda untuk nomor #' . $request->mohon_id . ' telah diterima.',
            'status_judul'    => 'Permohonan Diterima'
        ];

        DB::transaction(function () use ($request, $dataInsert) {
            SisPermohonanStatus::create([
                'status_mohon_id' => $dataInsert['status_mohon_id'],
                'status_tipe'     => $dataInsert['status_tipe'],
                'status_pesan'    => $dataInsert['status_pesan'],
                'status_judul'    => $dataInsert['status_judul']
            ]);
            // Delete User Group
            SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_approved_status' => 'accepted']);
        });


        // Send Push
        $notifStruct            = new NotifStruct();
        $notifStruct->title     = 'Permohonan anda untuk nomor #' . $request['mohon_id'] . ' telah diterima.';
        $notifStruct->message   = sprintf("%s mengajukan permohonan dengan nomor #%s telah melalui proses verifikasi dan diputuskan untuk diterima.", $data['mohon_cust_nama'], $data['mohon_id']);
        $notifStruct->user_id   = $data['user_id'];
        $notifStruct->click_url = url('/pelanggan/sertifikasi/permohonan');
        sendNotification($notifStruct);

        // Send Push TIM LS dan Marketing
        $dataUser = SysUser::whereIn('ug_group_id', ['6', '4'])->select('*')->join('sys_user_group', 'ug_user_id', '=','user_id');
        foreach ($dataUser->get() as $us) {
            $notifUsr          = new NotifStruct();
            $notifUsr->title   = 'Upload Kajian Permohonan #' . $request['mohon_id'];
            $notifUsr->message = sprintf("Upload Kajian Permohonan untuk permohonan nomor #%s untuk %s ,yang telah melalui proses verifikasi dan diputuskan diterima.", $data['mohon_id'], $data['mohon_cust_nama']);
            $notifUsr->user_id = $us->user_id;

            if($us->ug_group_id == '4' )
                $notifUsr->click_url = url('/marketing/kajian-permohonan');
            else
                $notifUsr->click_url = url('/operatorls/kajian-permohonan');

            sendNotification($notifUsr);
        }

        // Send Email
        $structEmail          = new EmailStruct();
        $structEmail->subject = "Pengajuan permohonan sertifikasi";
        $structEmail->body    = view('marketing::verifikasi_permohonan.mails.accepted')
            ->with([
                'pemohonNama'       => $data['mohon_cust_nama'],
                'pemohonSertifNama' => $data['sert_nama'],
                'link_verif'        => url('/pelanggan/sertifikasi/permohonan'),
            ])->render();
        $structEmail->to      = $data['mohon_cust_email'];
        sendEmail($structEmail);

        return redirect($this->url)->with('message', "Data permohonan #" . $request['mohon_id'] . " sudah diverifikasi dengan status '<strong>Diterima</strong>'.");
    }

    private function edit_rejected(Request $request)
    {
        $data = SisPermohonan::select('*', DB::raw("GROUP_CONCAT(DISTINCT CONCAT(sert_nama, IF(mohon_det_jenis_status = 'baru', '(Baru)', '(Perpanjang)')) SEPARATOR '; ') as sert_nama"))
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->groupBy('sis_permohonan.mohon_id')->findOrFail($request['mohon_id']);

        $dataInsert = [
            'mohon_id'        => $request['mohon_id'],
            'status_mohon_id' => $request['mohon_id'],
            'status_tipe'     => 'Informasi',
            'status_pesan'    => 'Permohonan anda untuk nomor #' . $request->mohon_id . ' telah ditolak.',
            'status_judul'    => 'Permohonan Ditolak'
        ];

        DB::transaction(function () use ($request, $dataInsert) {
            SisPermohonanStatus::create([
                'status_mohon_id' => $dataInsert['status_mohon_id'],
                'status_tipe'     => $dataInsert['status_tipe'],
                'status_pesan'    => $dataInsert['status_pesan'],
                'status_judul'    => $dataInsert['status_judul']
            ]);
            SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_approved_status' => 'rejected', 'mohon_kajian_permohonan_file' => null]);
        });

        // Send Push
        $notifStruct            = new NotifStruct();
        $notifStruct->title     = 'Permohonan anda untuk nomor #' . $request->mohon_id . ' telah ditolak.';
        $notifStruct->message   = sprintf("%s mengajukan permohonan dengan nomor #%s telah melalui proses verifikasi dan diputuskan untuk ditolak", $data->mohon_cust_nama, $data->mohon_id);
        $notifStruct->user_id   = $data?->user_id;
        $notifStruct->click_url = url('/pelanggan/sertifikasi/permohonan');
        sendNotification($notifStruct);

        // Send Email
        $structEmail          = new EmailStruct();
        $structEmail->subject = "Pengajuan permohonan sertifikasi";
        $structEmail->body    = view('marketing::verifikasi_permohonan.mails.reject')
            ->with([
                'pemohonNama'       => $data->mohon_cust_nama,
                'pemohonSertifNama' => $data->sert_nama,
                'link_verif'        => url('/pelanggan/sertifikasi/permohonan'),
            ])->render();
        $structEmail->to      = $data?->mohon_cust_email;
        sendEmail($structEmail);

        return redirect($this->url)->with('message', "Data permohonan #" . $request->mohon_id . " sudah diverifikasi dengan status '<strong>Ditolak</strong>'.");
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) { // Match fitur mirip switch case tetapi lebih simple (PHP 8 keatas)
            'update-revisi' => $this->update_revisi($request),
            default         => null,
        };
    }

    private function update_revisi(Request $request)
    {
        $request->validate([
            'mohon_id'         => 'required|integer',
            'mohon_cust_email' => 'required|string',
            'user_id'          => 'required|string',
            'status_tipe'      => 'required|string',
            'status_pesan'     => 'required|string'
        ]);
        $dataInsert = [
            'status_mohon_id' => $request->mohon_id,
            'status_tipe'     => $request->status_tipe,
            'status_pesan'    => $request->status_pesan,
            'status_judul'    => 'Revisi pengajuan #' . $request->mohon_id
        ];

        try {
            DB::beginTransaction();
            SisPermohonanStatus::create($dataInsert);
            SisPermohonan::findOrFail($request['mohon_id'])->update(['mohon_approved_status' => 'revisi']);
            DB::commit();

            // Send Push
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Revisi pengajuan #' . $request->mohon_id;
            $notifStruct->message   = $dataInsert['status_pesan'];
            $notifStruct->user_id   = $request->user_id;
            $notifStruct->click_url = url("/pelanggan/sertifikasi/permohonan/edit/$request->mohon_id");
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = $dataInsert['status_judul'];
            $structEmail->body    = view('marketing::verifikasi_permohonan.mails.revisi')
                ->with([
                    'message'    => $dataInsert['status_pesan'],
                    'link_verif' => url("/pelanggan/sertifikasi/permohonan/edit/$request->mohon_id")
                ])->render();
            $structEmail->to      = $request?->mohon_cust_email;
            sendEmail($structEmail);


            return redirect($this->url)->with('message', "Tambah informasi revisi berhasil");
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
        }
    }
}
