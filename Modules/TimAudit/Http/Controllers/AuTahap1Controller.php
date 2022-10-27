<?php

namespace Modules\TimAudit\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Models\BbkkpSis\SisAuditTahap1;
use App\Models\BbkkpSis\SisAuditTahap1Tim;
use App\Models\BbkkpSis\SisAuditTahap1Detail;
use App\Models\BbkkpSis\SisAuditTahap1Revisi;
use App\Models\BbkkpSis\SisAuditTahap1RevisiFile;
use App\Models\BbkkpSis\SisPelanggan;

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
use Barryvdh\DomPDF\Facade as PDF;

class AuTahap1Controller extends Controller
{
    public $module = self::class;
    private $url = 'timaudit/auditor/tahap1';
    private $view = "timaudit::auditor_tahap_1";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
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

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_audit_tahap1.aud_thp1_status_temuan', '!=', 'setuju');
        $data->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya');
        $data->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua');
        $data->whereNotNull('sis_audit_tahap1.aud_thp1_file_jadwal');
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
        $data->select("*", "sis_audit_tahap1.aud_thp1_id AS aud_thp1_id");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, ' (' , UPPER(IF(sis_permohonan_detail.cust_sert_id IS NULL, 'baru', 'lama')), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_audit_tahap1.aud_thp1_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['aud_thp1_status_temuan']   = $d->aud_thp1_status_temuan;
            $x['aud_thp1_status']          = $d->aud_thp1_status;
            $x['aud_thp1_id']              = $d->aud_thp1_id;
            $x['aud_thp1_tanggal_mulai']   = $d->aud_thp1_tanggal_mulai;
            $x['aud_thp1_tanggal_selesai'] = $d->aud_thp1_tanggal_selesai;
            $x['cust_nama']                = $d->cust_nama;
            $x['sert_nama']                = $d->sert_nama;
            $x['jadw_jenis']               = $d->jadw_jenis;
            $x['aud_thp1_jenis']           = $d->aud_thp1_jenis;
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

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'audit-tahap1' => $this->edit_audit_tahap1($request),
            default        => null,
        };
    }

    private function edit_audit_tahap1(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Tim Audit'),
            new BreadcrumbsStruct('Kepala Auditor', url($this->url)),
            new BreadcrumbsStruct('Audit Tahap 1', url($this->url)),
            new BreadcrumbsStruct('Proses Audit Tahap 1'),
        ];

        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
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

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id")
            ->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id")
            ->where('master_pegawai.user_id', '=', auth()->id())
            ->where('sis_audit_tahap1_tim.thp1_tim_kesanggupan', '=', 'ya')
            ->where('sis_audit_tahap1_tim.thp1_tim_posisi', '=', 'ketua')
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $status_entry = false;
        if ($dataAuditKlausul->exists()) {
            $status_entry = true;
        }

        $dataRevisi = SisAuditTahap1::join('sis_audit_tahap1_persetujuan_revisi', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_persetujuan_revisi.aud_thp1_id")
            ->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id'])
            ->select('*')->get();

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'statusEntry' => $status_entry, 'dataAuditKlausul' => $dataAuditKlausul->get(), 'dataAudit' => $restAudit, 'dataRevisi' => $dataRevisi];
        return view("$this->view.edit_audit_tahap1")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'update-generate-tahap1' => $this->update_generate_tahap1($request),
            'update-audit-tahap1'    => $this->update_audit_tahap1($request),
            default                  => null,
        };
    }

    private function update_generate_tahap1(Request $request)
    {
        $request->validate([
            "aud_thp1_id"       => 'required',
            "sert_id"           => 'required',
            "mohon_id"          => 'required',
            "sert_tahap1_jenis" => 'required',
        ]);

        try {
            DB::beginTransaction();
            $restData = DB::table('sis_audit_tahap1')->where('aud_thp1_id', $request['aud_thp1_id'])->first();
            if ($restData !== null) {
                DB::table('sis_audit_tahap1')
                    ->where('aud_thp1_id', $request['aud_thp1_id'])
                    ->update(["created_at" => Carbon::now(), "updated_at" => Carbon::now(),]);
                $aud_thp1_id = $restData->aud_thp1_id;
            } else {
                $newTahap1              = new SisAuditTahap1();
                $newTahap1->aud_thp1_id = $request['aud_thp1_id'];
                $newTahap1->created_at  = Carbon::now();
                $newTahap1->updated_at  = Carbon::now();
                $newTahap1->save();
                $aud_thp1_id = $newTahap1->aud_thp1_id;
            }

            $restDataDet = DB::table('sis_audit_tahap1_detail')->where('aud_thp1_id', $request['aud_thp1_id'])->first();
            if ($restDataDet === null) {
                if ($request['sert_tahap1_jenis'] == 'sni') {
                    $dts = DB::table('master_klausul_tahap1')->where('sert_id', '=', $request['sert_id'])->orderBy(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 1), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 2), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 3), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 4), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 5), '.', -1) + 0"))->get();

                    if ($dts !== null) {
                        foreach ($dts as $dt) {
                            DB::table('sis_audit_tahap1_detail')->insert([
                                'aud_thp1_id'                 => intval($aud_thp1_id),
                                'klausul_thp1_id'             => intval($dt->klausul_thp1_id),
                                'aud_thp1_det_thp1_nomor'     => $dt->klausul_thp1_nomor,
                                'aud_thp1_det_peryataan'      => $dt->klausul_thp1_peryataan,
                                'aud_thp1_det_is_tinjauan'    => $dt->klausul_thp1_is_tinjauan,
                                'aud_thp1_det_kode_dok'       => null,
                                'aud_thp1_det_judul_dok'      => null,
                                'aud_thp1_det_hasil_tinjauan' => null,
                                'aud_thp1_det_keterangan'     => null,
                            ]);
                        }
                    }
                } else if ($request['sert_tahap1_jenis'] == 'pusat') {
                    $dts = DB::table('master_klausul_tahap1')->where('sert_id', '=', $request['sert_id'])->orderBy(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 1), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 2), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 3), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 4), '.', -1) + 0, SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(klausul_thp1_nomor, '.'), '.', 5), '.', -1) + 0"))->get();

                    if ($dts !== null) {
                        foreach ($dts as $dt) {
                            DB::table('sis_audit_tahap1_detail')->insert([
                                'aud_thp1_id'              => intval($aud_thp1_id),
                                'klausul_thp1_id'          => intval($dt->klausul_thp1_id),
                                'aud_thp1_det_thp1_nomor'  => $dt->klausul_thp1_nomor,
                                'aud_thp1_det_persyaratan' => $dt->klausul_thp1_peryataan,
                                'aud_thp1_det_is_tinjauan' => $dt->klausul_thp1_is_tinjauan,
                                'aud_thp1_det_nilai'       => null,
                                'aud_thp1_det_satuan'      => null,
                            ]);
                        }
                    }
                }

            }

            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function update_audit_tahap1(Request $request)
    {
        $request->validate([
            "aud_thp1_id"           => 'required',
            "cust_id"               => 'required',
            "sert_id"               => 'required',
            "user_id"               => 'required',
            "cust_email"            => 'required',
            "cust_nama"             => 'required',
            "mohon_id"              => 'required',
            "jenis"                 => 'required',
            "detail_hasil_tinjauan" => 'required',
            "detail_keterangan"     => 'required',
            "detail_judul_dok"      => 'nullable',
            "detail_kode_dok"       => 'nullable',
            "detail_nilai"          => 'nullable',
            "detail_satuan"         => 'nullable',
            "is_revisi"             => 'required',
        ]);

        try {
            if ($request['is_revisi'] == 'ya') {
                DB::table('sis_audit_tahap1')
                    ->where('aud_thp1_id', $request['aud_thp1_id'])
                    ->update(['aud_thp1_status_temuan' => 'proses']);
            }

            if ($request['jenis'] == 'sni') {
                if (!empty($request['detail_kode_dok'])) {
                    foreach ($request['detail_kode_dok'] as $key => $val) {
                        DB::table('sis_audit_tahap1_detail')
                            ->where('aud_thp1_det_id', $key)
                            ->update([
                                'aud_thp1_det_kode_dok'       => $val,
                                'aud_thp1_det_judul_dok'      => isset($request['detail_judul_dok'][$key]) ? $request['detail_judul_dok'][$key] : null,
                                'aud_thp1_det_hasil_tinjauan' => isset($request['detail_hasil_tinjauan'][$key]) ? $request['detail_hasil_tinjauan'][$key] : null,
                                'aud_thp1_det_keterangan'     => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                            ]);

                        if (isset($request['detail_hasil_tinjauan'][$key])) {
                            if ($request['detail_hasil_tinjauan'][$key] == 'no') {
                                DB::table('sis_audit_tahap1_detail')
                                    ->where('aud_thp1_det_id', $key)
                                    ->update([
                                        'aud_thp1_det_is_temuan' => 'ya',
                                        'aud_thp1_det_status'    => 'proses',
                                    ]);


                                $check_data = DB::table('sis_audit_tahap1_revisi')
                                    ->selectRaw('*')
                                    ->whereIn('thp1_revisi_status', ['open', 'fixed'])
                                    ->where('aud_thp1_det_id', $key)
                                    ->get();
                                if ($check_data->isEmpty()) {
                                    DB::table('sis_audit_tahap1_revisi')->insert([
                                        'aud_thp1_det_id'     => $key,
                                        'thp1_revisi_catatan' => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                                        'thp1_revisi_status'  => 'open'
                                    ]);
                                } else {
                                    foreach ($check_data as $ck) {
                                        DB::table('sis_audit_tahap1_revisi')
                                            ->where('thp1_revisi_id', $ck->thp1_revisi_id)
                                            ->update([
                                                'thp1_revisi_catatan' => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                                            ]);
                                    }

                                }
                            } else {
                                DB::table('sis_audit_tahap1_detail')
                                    ->where('aud_thp1_det_id', $key)
                                    ->update([
                                        'aud_thp1_det_is_temuan' => 'tidak',
                                        'aud_thp1_det_status'    => 'closed',
                                    ]);
                                DB::table('sis_audit_tahap1_revisi')
                                    ->where('aud_thp1_det_id', $key)
                                    ->delete();
                            }
                        }
                    }
                }
            } else if ($request['jenis'] == 'pusat') {
                if (!empty($request['detail_nilai'])) {
                    foreach ($request['detail_nilai'] as $key => $val) {
                        DB::table('sis_audit_tahap1_detail')
                            ->where('aud_thp1_det_id', $key)
                            ->update([
                                'aud_thp1_det_nilai'          => $val,
                                'aud_thp1_det_satuan'         => isset($request['detail_satuan'][$key]) ? $request['detail_satuan'][$key] : null,
                                'aud_thp1_det_hasil_tinjauan' => isset($request['detail_hasil_tinjauan'][$key]) ? $request['detail_hasil_tinjauan'][$key] : null,
                                'aud_thp1_det_keterangan'     => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                            ]);

                        if (isset($request['detail_hasil_tinjauan'][$key])) {
                            if ($request['detail_hasil_tinjauan'][$key] == 'no') {
                                DB::table('sis_audit_tahap1_detail')
                                    ->where('aud_thp1_det_id', $key)
                                    ->update([
                                        'aud_thp1_det_is_temuan' => 'ya',
                                        'aud_thp1_det_status'    => 'proses',
                                    ]);


                                $check_data = DB::table('sis_audit_tahap1_revisi')
                                    ->selectRaw('*')
                                    ->whereIn('thp1_revisi_status', ['open', 'fixed'])
                                    ->where('aud_thp1_det_id', $key)
                                    ->get();
                                if ($check_data->isEmpty()) {
                                    DB::table('sis_audit_tahap1_revisi')->insert([
                                        'aud_thp1_det_id'     => $key,
                                        'thp1_revisi_catatan' => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                                        'thp1_revisi_status'  => 'open'
                                    ]);
                                } else {
                                    foreach ($check_data as $ck) {
                                        DB::table('sis_audit_tahap1_revisi')
                                            ->where('thp1_revisi_id', $ck->thp1_revisi_id)
                                            ->update([
                                                'thp1_revisi_catatan' => isset($request['detail_keterangan'][$key]) ? $request['detail_keterangan'][$key] : null,
                                            ]);
                                    }

                                }
                            } else {
                                DB::table('sis_audit_tahap1_detail')
                                    ->where('aud_thp1_det_id', $key)
                                    ->update([
                                        'aud_thp1_det_is_temuan' => 'tidak',
                                        'aud_thp1_det_status'    => 'closed',
                                    ]);

                                DB::table('sis_audit_tahap1_revisi')
                                    ->where('aud_thp1_det_id', $key)
                                    ->delete();
                            }
                        }
                    }
                }
            }

            DB::commit();
            if ($request['is_revisi'] == 'ya') {
            }
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'hasil-tinjauan' => $this->print_hasil_tinjauan($request),
            default          => null,
        };
    }

    private function print_hasil_tinjauan(Request $request)
    {
        $dataJadwal = SisAuditTahap1::where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);
        $dataJadwal->select(
            '*',
            DB::raw("'tunggal' as jadw_jenis"),
            DB::raw("'tahap-1' as jadw_audit_jenis"),
            DB::raw("sis_audit_tahap1.aud_thp1_id as jadw_id"),
            DB::raw("sis_audit_tahap1.aud_thp1_tujuan as jadw_audit_tujuan_audit"),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_nama) as komodt_nama'),
            DB::raw('GROUP_CONCAT(DISTINCT master_komoditi.komodt_sni) as sni'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_detail.mohon_det_no_referensi) as no_referensi'),
            DB::raw('GROUP_CONCAT(DISTINCT sis_permohonan_komoditi.mohon_kmditi_ruang_lingkup) as ruang_lingkup'),
        );

        $dataJadwal->join('sis_audit_tahap1_tim', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id")
            ->join('sis_permohonan', "sis_audit_tahap1.mohon_id", "=", "sis_permohonan.mohon_id")
            ->join('sis_permohonan_detail', "sis_permohonan_detail.mohon_det_id", "=", "sis_audit_tahap1.mohon_det_id")
            ->join('sis_permohonan_komoditi', "sis_permohonan_detail.mohon_det_id", "=", "sis_permohonan_komoditi.mohon_det_id")
            ->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_permohonan_komoditi.komodt_id")
            ->join('master_sertifikasi', "sis_permohonan_detail.sert_id", "=", "master_sertifikasi.sert_id")
            ->join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_permohonan.cust_id")
            ->join('sis_billing', "sis_billing.bill_id", "=", "sis_audit_tahap1.bill_id")
            ->groupBy('sis_audit_tahap1.aud_thp1_id');

        $restAudit = $dataJadwal->get()[0];

        $dataAuditKlausul = SisAuditTahap1::join('sis_audit_tahap1_detail', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_detail.aud_thp1_id");
        $dataAuditKlausul->where('sis_audit_tahap1.aud_thp1_id', $request['aud_thp1_id']);

        $parser = [
            'module'           => $this->module,
            'url'              => $this->url,
            'restAudit'        => $restAudit,
            'dataAuditKlausul' => $dataAuditKlausul->get(),
        ];

        $dataTim = SisAuditTahap1Tim::join('sis_audit_tahap1', "sis_audit_tahap1.aud_thp1_id", "=", "sis_audit_tahap1_tim.aud_thp1_id");
        $dataTim->join('master_pegawai', "sis_audit_tahap1_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        $dataTim->select('*');
        $dataTim->where('sis_audit_tahap1_tim.aud_thp1_id', '=', $request['aud_thp1_id']);
        foreach ($dataTim->get() as $tim) {
            if ($tim->thp1_tim_posisi == 'ketua') {
                $parser['ketua_tim'] = $tim->peg_nama;
                break;
            }
        }
        $parser['dataTim'] = $dataTim->get();
        if ($restAudit['sert_tahap1_jenis'] == 'sni') {
            // return view("$this->view.print.hasil_tinjauan_sni")->with($parser);
            $pdf = PDF::loadView("$this->view.print.hasil_tinjauan_sni", $parser)->setPaper('a4', 'portrait');
        } else {
            $pdf = PDF::loadView("$this->view.print.hasil_tinjauan_pusat", $parser)->setPaper('a4', 'portrait');
            // return view("$this->view.print.hasil_tinjauan_pusat")->with($parser);
        }
        return $pdf->stream();
    }
}
