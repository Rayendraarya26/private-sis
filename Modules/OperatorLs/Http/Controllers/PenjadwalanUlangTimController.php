<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\MasterPegawai;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisJadwalTim;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PenjadwalanUlangTimController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/penjadwalan-ulang-tim';
    private $view = "operatorls::penjadwalan_ulang_tim";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Ulang Tim Audit'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-jadwal-tim'   => $this->ajax_datagrid_jadwal_tim($request),
            'combogrid-pegawai'     => $this->ajax_combogrid_pegawai($request),
            'combobox-posisi'       => $this->ajax_combobox_posisi($request),
            default                 => null,
        };
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");

        $data->leftJoin('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");


        // Filter
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->whereIn('sis_jadwal.jadw_team_status', ['accepted']);
        //$data->where('sis_jadwal_audit.jadw_audit_status_komite', '!=', 'submited');
        $data->where('sis_jadwal.jadw_is_khusus_komite', '=', 'tidak');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
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
        $total = $data->select(DB::raw('COUNT(DISTINCT sis_jadwal.jadw_id) as total'))->first()->total;


        // Pagination
        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("COUNT(DISTINCT jadw_tim_id) AS total_tim");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, jadw_audit_jenis) SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(DISTINCT jadw_audit_jenis) AS jadw_audit_jenis");
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
            $x['jadw_jenis']           = $d->jadw_jenis;
            $x['jadw_team_status']     = $d->jadw_team_status;
            $x['jadw_audit_jenis']     = $d->jadw_audit_jenis;
            $x['mohon_id']             = $d->mohon_id;
            $x['sert_id']              = $d->sert_id;
            $x['sert_nama']            = $d->sert_nama;
            $x['cust_sert_id']         = $d->cust_sert_id;
            $result[]                  = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_datagrid_jadwal_tim(Request $request)
    {
        $data = SisJadwalTim::join('sis_jadwal', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
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
        $data->groupBy('sis_jadwal_tim.jadw_tim_id');

        $result = [];
        foreach ($data->get() as $d) {
            $x['tipe']                     = 'data-tim';
            $x['jadw_tim_id']              = $d->jadw_tim_id;
            $x['peg_id']                   = $d->peg_id;
            $x['peg_nip']                  = $d->peg_nip;
            $x['peg_nama']                 = $d->peg_nama;
            $x['jadw_id']                  = $d->jadw_id;
            $x['jadw_tim_kode']            = $d->jadw_tim_kode;
            $x['jadw_tim_posisi']          = $d->jadw_tim_posisi;
            $x['jadw_tim_kesanggupan']     = $d->jadw_tim_kesanggupan;
            $x['jadw_tim_kesanggupan_tgl'] = $d->jadw_tim_kesanggupan_tgl?->format("Y-m-d");
            $x['can_delete']               = 'false';
            if ($d->jadw_tim_jenis == 'penambahan') {
                $x['can_delete'] = 'true';
            }
            $result[] = $x;
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
        // Total
        $total = $data->select(DB::raw('count(distinct peg_id) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['peg_id']   = $d->peg_id;
            $x['peg_kode'] = $d->peg_kode == '' ? '' : $d->peg_kode;
            $x['peg_nama'] = $d->peg_nama;
            $x['peg_telp'] = $d->peg_telp;
            $x['peg_nip']  = $d->peg_nip;
            $result[]      = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combobox_posisi(Request $request)
    {
        $data = [
            ['id' => 'ketua', 'name' => 'ketua'],
            ['id' => 'auditor', 'name' => 'auditor'],
            ['id' => 'observer', 'name' => 'observer'],
            ['id' => 'ppc', 'name' => 'ppc'],
        ];

        return response()->json($data);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'edit-tim' => $this->edit_tim($request),
            default    => null,
        };
    }

    private function edit_tim(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Penyusunan Tim'),
            new BreadcrumbsStruct('Edit Tim'),
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
        return view("$this->view.edit_tim")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-tim'   => $this->update_data_tim($request),
            'ajukan-tim' => $this->update_pengajuan_tim($request),
            default      => null,
        };
    }

    private function update_data_tim(Request $request)
    {
        $request->validate([
            "jadw_id"         => 'required',
            "jadw_tim_id"     => 'nullable',
            "peg_id"          => 'required',
            "jadw_tim_kode"   => 'required',
            "jadw_tim_posisi" => 'required',
        ]);

        try {
            if ($request->jadw_tim_id != '') {
                $dataUpdate = [
                    'peg_id'          => $request->peg_id,
                    'jadw_tim_kode'   => $request->jadw_tim_kode,
                    'jadw_tim_posisi' => $request->jadw_tim_posisi,
                    'updated_at'      => Carbon::now(),
                ];
                SisJadwalTim::findOrFail($request['jadw_tim_id'])->update($dataUpdate);
            } else {
                $dataInsert = [
                    'jadw_id'                  => $request->jadw_id,
                    'peg_id'                   => $request->peg_id,
                    'jadw_tim_kode'            => $request->jadw_tim_kode,
                    'jadw_tim_posisi'          => $request->jadw_tim_posisi,
                    'jadw_tim_jenis'           => 'penambahan',
                    'jadw_tim_kesanggupan'     => 'none',
                    'jadw_tim_kesanggupan_tgl' => null,
                    'created_at'               => Carbon::now(),
                    'updated_at'               => Carbon::now(),
                ];
                SisJadwalTim::create($dataInsert);
            }

            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function update_pengajuan_tim(Request $request)
    {
        $request->validate([
            "cust_id"              => 'required',
            "jadw_tanggal_mulai"   => 'required',
            "jadw_tanggal_selesai" => 'required',
            "jadw_id"              => 'required',
        ]);


        try {
            $data_tim = SisJadwalTim::join('sis_jadwal', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id")
                ->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id")
                ->select('*')
                ->where('sis_jadwal_tim.jadw_id', $request['jadw_id'])
                ->where('sis_jadwal_tim.jadw_tim_jenis', 'penambahan');

            foreach ($data_tim->get() as $d) {
                $notifStruct            = new NotifStruct();
                $notifStruct->title     = 'Penyusunan Ulang Tim Audit Tahap II';
                $notifStruct->message   = sprintf("Anda telah ditunjuk untuk melakukan Audit tahap II pada nomor jadwal #%s sebagai ", $request->jadw_id, $request->jadw_tim_posisi);
                $notifStruct->user_id   = $d?->user_id;
                $notifStruct->click_url = url('/timaudit/persetujuan-tim/auditor');
                sendNotification($notifStruct);

            }

            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'data-tim' => $this->delete_data_tim($request),
            default    => null,
        };
    }

    private function delete_data_tim(Request $request)
    {
        try {
            $status_return = true;
            foreach ($request->ids as $id) {
                $data = SisJadwalTim::where("jadw_tim_id", $id)->firstOrFail();
                if ($data->delete()) {

                } else {
                    $status_return = false;
                    break;
                }
            }

            if ($status_return) {
                return responseJSON(200, [], "Berhasil menghapus data");
            } else {
                return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
            }
        } catch (Exception $e) {
            if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
