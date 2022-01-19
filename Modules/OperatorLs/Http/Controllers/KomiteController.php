<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SisAuditTimKomite;
use App\Models\BbkkpSis\SisJadwal;

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

class KomiteController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/komite';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Komite Audit'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("operatorls::komite.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit'  => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-jadwal-komite' => $this->ajax_datagrid_jadwal_komite($request),
            'combogrid-pegawai'      => $this->ajax_combogrid_pegawai($request),
            'combobox-posisi'        => $this->ajax_combobox_posisi($request),
            default                  => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_audit_komite_rekomendasi', "sis_audit_komite_rekomendasi.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->leftJoin('sis_audit_tim_komite', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('sis_audit_komite_rekomendasi.rekmd_komte_status', '=', 'ditutup');
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
        $data->where('sis_jadwal_audit.jadw_audit_status_komite', '=', 'submited');
		
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
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("count(distinct komite_id) AS total_tim");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('- ', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct CONCAT('- ', UPPER(jadw_audit_jenis) ) SEPARATOR ',<br/>') AS jadw_audit_jenis");
        $data->skip(($request->page - 1) * $request->rows);
        $data->take($request->rows);
        $data->groupBy('sis_jadwal.jadw_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']              = $d->jadw_id;
            $x['total_tim']            = $d->total_tim;
            $x['jadw_tanggal_mulai']   = $d->jadw_tanggal_mulai?->format("Y-m-d");
            $x['jadw_tanggal_selesai'] = $d->jadw_tanggal_selesai?->format("Y-m-d");
            $x['cust_nama']            = $d->cust_nama;
            $x['sert_nama']            = $d->sert_nama;
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_jadwal_komite(Request $request)
    {
        $data = SisAuditTimKomite::join('sis_jadwal', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        $data->where('sis_jadwal.jadw_id', '=', $request['jadw_id']);
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

        // Pagination
        $data->select("*");
        $data->groupBy('sis_audit_tim_komite.komite_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['tipe']          = 'data-komite';
            $x['komite_id']     = $d->komite_id;
            $x['peg_id']        = $d->peg_id;
            $x['peg_nip']       = $d->peg_nip;
            $x['peg_nama']      = $d->peg_nama;
            $x['jadw_id']       = $d->jadw_id;
            $x['komite_posisi'] = $d->komite_posisi;
            // $x['komite_tgl_surat'] = $d->komite_tgl_surat?->format("Y-m-d");
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    private function ajax_combogrid_pegawai(Request $request)
    {
        $data = MasterPegawai::leftJoin('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
        // Filter
        if (!empty($request->q)) {
            $data->where('master_pegawai.peg_nama', 'LIKE', '%' . $request->q . '%');
        }
        $data->whereNotIn('peg_id', function ($query) use ($request) {
            $query->select('peg_id')->from('sis_jadwal_tim')->where('jadw_id', '=', $request['jadw_id']);
        });
        // Total
        $total = $data->select(DB::raw('count(distinct peg_id) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['peg_id']   = $d->peg_id;
            $x['peg_nama'] = $d->peg_nama;
            $x['peg_telp'] = $d->peg_telp;
            $x['peg_nip']  = $d->peg_nip;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_posisi(Request $request)
    {
        $data = [
            ['id' => 'ketua', 'name' => 'ketua'],
            ['id' => 'anggota', 'name' => 'anggota'],
        ];

        return response()->json($data);
    }

    public function detail(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'detail-tim' => $this->detail_tim($request),
            default      => null,
        };
    }

    private function detail_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Komite'),
            new BreadcrumbsStruct('Detail Komite'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
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

        $dataJadwal->groupBy('sis_jadwal.jadw_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("operatorls::komite.detail_tim")->with($parser);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'edit-tim'   => $this->edit_tim($request),
            'detail-tim' => $this->detail_tim($request),
            default      => null,
        };
    }

    private function edit_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Komite'),
            new BreadcrumbsStruct('Edit Komite'),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
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

        $dataJadwal->groupBy('sis_jadwal.jadw_id');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0]];
        return view("operatorls::komite.edit_tim")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-komite'   => $this->update_data_tim($request),
            'ajukan-komite' => $this->update_pengajuan_tim($request),
            default         => null,
        };
    }

    private function update_data_tim(Request $request)
    {
        $request->validate([
            "jadw_id"       => 'required',
            "komite_id"     => 'nullable',
            "peg_id"        => 'required',
            "komite_posisi" => 'required',
        ]);

        try {
            if ($request->komite_id != '') {
                $dataUpdate = [
                    'jadw_id'          => $request->jadw_id,
                    'peg_id'           => $request->peg_id,
                    'komite_posisi'    => $request->komite_posisi,
                    'komite_tgl_surat' => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ];
                SisAuditTimKomite::findOrFail($request['komite_id'])->update($dataUpdate);
            } else {
                $dataInsert = [
                    'jadw_id'                => $request->jadw_id,
                    'peg_id'                 => $request->peg_id,
                    'komite_posisi'          => $request->komite_posisi,
                    'komite_tgl_kesanggupan' => Carbon::now(),
                    'komite_tgl_surat'       => Carbon::now(),
                    'created_at'             => Carbon::now(),
                    'updated_at'             => Carbon::now(),
                ];
                SisAuditTimKomite::create($dataInsert);
            }
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function update_pengajuan_tim(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
        ]);


        try {
            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];

            DB::beginTransaction();
            // Notifikasi ke Tim Komite yang ditunjuk
            /*
            $newSisJadwalLog = new SisJadwalLog();
            $newSisJadwalLog->jadw_id = $request['jadw_id'];
            $newSisJadwalLog->jlog_tipe = 'Informasi';
            $newSisJadwalLog->jlog_judul = 'Pengajuan Data Komite Audit';
            $newSisJadwalLog->jlog_pesan = 'Telah dilakukan pengajuan Komite untuk jadwal nomor #'.$request['jadw_id'].'.';
            $newSisJadwalLog->created_at = Carbon::now();
            $newSisJadwalLog->updated_at = Carbon::now();
            $newSisJadwalLog->save();
             */
            DB::commit();
			$dataTim = SisAuditTimKomite::join('sis_jadwal', "sis_audit_tim_komite.jadw_id", "=", "sis_jadwal.jadw_id");
			$dataTim->join('master_pegawai', "sis_audit_tim_komite.peg_id", "=", "master_pegawai.peg_id");
			$dataTim->join('sys_user', "master_pegawai.user_id", "=", "sys_user.user_id");
			$dataTim->where('sis_jadwal.jadw_id', '=', $request['jadw_id']);
			$dataTim->select("*");
			$dataTim->groupBy('sis_audit_tim_komite.komite_id');
			foreach ($dataTim->get() as $d) {
				$notifStruct            = new NotifStruct();
				$notifStruct->title     = 'Permohonan Penujukan Tim Komite';;
				$notifStruct->message   = sprintf("Penyusunan Tim Komite telah diterbitkan , yang telah dilakukan pada tanggal %s s/d %s, untuk jadwal nomor #%s, silahkan konfirmasi tim.", date("d-m-Y", strtotime($restJadwal->jadw_tanggal_mulai)) , date("d-m-Y", strtotime($restJadwal->jadw_tanggal_selesai)), $request['jadw_id'] );
				$notifStruct->user_id   = $d?->user_id;
				$notifStruct->click_url = url('/timaudit/persetujuan-tim/auditor');
				sendNotification($notifStruct);
			}
			
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-komite' => $this->delete_data_komite($request),
            default       => null,
        };
    }

    private function delete_data_komite(Request $request)
    {
        try {
            $status_return = TRUE;
            foreach ($request->ids as $id) {
                $data = SisAuditTimKomite::where("komite_id", $id)->firstOrFail();
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
