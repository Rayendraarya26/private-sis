<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisJadwal;
use App\Models\BbkkpSis\SisPelangganPabrik;
use App\Models\BbkkpSis\SisAuditSertifikatProduk;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SertifikatUjiController extends Controller
{
    public $module = self::class;
    private $url = 'operatorls/sertifikat-uji';
    private $view = "operatorls::sertifikat_uji";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Upload Sertifikat Hasil Uji'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid-jadwal-audit' => $this->ajax_datagrid_jadwal_audit($request),
            'datagrid-sertifikat-uji'    => $this->ajax_datagrid_sertifikat_uji($request),
            default                 => null,
        };
    }
	
    private function ajax_datagrid_sertifikat_uji(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_audit_sertifikat_produk', "sis_audit_sertifikat_produk.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_id', '=', $request['jadw_id']);
        $data->whereNotNull('sis_jadwal.jadw_file_jadwal');
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
				if($f->field == 'prod_sert_status_hasil')
					$data->where($f->field, $f->value);
				else
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

        $result = [];
        foreach ($data->get() as $d) {
            $x['jadw_id']               = $d->jadw_id;
            $x['prod_sert_id']               = $d->prod_sert_id;
            $x['prod_sert_tanggal']               = $d->prod_sert_tanggal;
            $x['prod_sert_nomor']               = $d->prod_sert_nomor;
            $x['prod_sert_lab_nama']               = $d->prod_sert_lab_nama;
            $x['prod_sert_status_hasil']               = $d->prod_sert_status_hasil;
            $x['prod_sert_filepath'] = ($d->prod_sert_filepath != '') ? '<a class=" " target="_blank" href = "' . url($d->prod_sert_filepath) . '"><i class="fas fa-download"></i> Download</a>' : '';
            $x['file']                           = ($d->prod_sert_filepath != '') ? $d->prod_sert_filepath : '';
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    private function ajax_datagrid_jadwal_audit(Request $request)
    {
        $data = SisJadwal::join('sis_pelanggan', "sis_pelanggan.cust_id", "=", "sis_jadwal.cust_id");
        $data->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $data->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $data->join('sis_jadwal_tim', "sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
        $data->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $data->join('sis_billing', "sis_jadwal.bill_id", "=", "sis_billing.bill_id");
        $data->join('sis_audit_sertifikat_produk', "sis_audit_sertifikat_produk.jadw_id", "=", "sis_jadwal.jadw_id");

        // Filter
        $data->where('master_pegawai.user_id', '=', auth()->id());
        $data->where('sis_jadwal.jadw_tanggal_status', '=', 'accepted');
        $data->where('sis_jadwal.jadw_team_status', '=', 'accepted');
        $data->where('sis_jadwal_tim.jadw_tim_kesanggupan', '=', 'ya');
        $data->where('sis_jadwal_tim.jadw_tim_posisi', '=', 'ketua');
        $data->where('sis_jadwal_audit.jadw_audit_status', '=', 'on-going');
        $data->where('sis_jadwal.jadw_is_tutup', '=', 'tidak');
		
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

        $data->select("*", "sis_jadwal.jadw_id AS jadw_id");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_status_komite) AS status_komite");
        $data->selectRaw("GROUP_CONCAT(DISTINCT CONCAT('-', sert_nama, '(' , UPPER(jadw_audit_jenis), ')') SEPARATOR ',<br/>') as sert_nama");
        $data->selectRaw("GROUP_CONCAT(distinct jadw_audit_jenis) AS jadw_audit_jenis");
        $data->selectRaw("COUNT(distinct prod_sert_id) AS total_hasil_uji");
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
            $x['total_hasil_uji']     = $d->total_hasil_uji;
            array_push($result, $x);
        }

        return response()->json(["rows" => $result]);
    }

    public function edit(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-hasil-uji' => $this->edit_upload_hasil_uji($request),
            default            => null,
        };
    }

    private function edit_upload_hasil_uji(Request $request)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Upload Sertifikat Hasil Uji', url($this->url)),
            new BreadcrumbsStruct('Upload Hasil Uji Jadwal #' . $request['jadw_id']),
        ];

        $dataJadwal = SisJadwal::where('sis_jadwal.jadw_id', $request['jadw_id']);
        $dataJadwal->join('sis_pelanggan', 'sis_pelanggan.cust_id', '=', 'sis_jadwal.cust_id');
        $dataJadwal->join('sis_jadwal_audit', "sis_jadwal.jadw_id", "=", "sis_jadwal_audit.jadw_id");
        $dataJadwal->join('master_sertifikasi', "master_sertifikasi.sert_id", "=", "sis_jadwal_audit.sert_id");
        $dataJadwal->join('master_komoditi', "master_komoditi.komodt_id", "=", "sis_jadwal_audit.komodt_id");
        $dataJadwal->join('sis_jadwal_tim', function ($join) {
            $join->on("sis_jadwal_tim.jadw_id", "=", "sis_jadwal.jadw_id");
            $join->on('sis_jadwal_tim.jadw_tim_posisi', '=', DB::raw("'ppc'"));
        });

        $dataJadwal->join('master_pegawai', "sis_jadwal_tim.peg_id", "=", "master_pegawai.peg_id");
        $dataJadwal->leftJoin('sis_audit_logbook', function ($join) {
            $join->on("sis_audit_logbook.jadw_tim_id", "=", "sis_jadwal_tim.jadw_tim_id");
            $join->on('logbook_jenis', '=', DB::raw("'ppc'"));
        });

        $dataJadwal->where('master_pegawai.user_id', '=', auth()->id());

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
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_posisi) AS jadw_tim_posisi");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.peg_id) AS peg_id");
        $dataJadwal->selectRaw("GROUP_CONCAT(distinct sis_jadwal_tim.jadw_tim_id) AS jadw_tim_id");
        $dataJadwal->groupBy('sis_jadwal.jadw_id');
        $restJadwal = $dataJadwal->get()[0];

        $dataPabrik = SisPelangganPabrik::where('cust_id', $restJadwal->cust_id);
        $dataPabrik->leftJoin('master_kabupaten', 'master_kabupaten.kab_id', '=', 'sis_pelanggan_pabrik.kab_id');
        $dataPabrik->leftJoin('master_kecamatan', 'master_kecamatan.kec_id', '=', 'sis_pelanggan_pabrik.kec_id');
        $dataPabrik->leftJoin('master_provinsi', 'master_provinsi.prov_id', '=', 'sis_pelanggan_pabrik.prov_id');
        $dataPabrik->select('*');

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'dataJadwal' => $dataJadwal->get()[0], 'dataPabrik' => $dataPabrik->get()];
        return view("$this->view.edit_upload_hasil_uji")->with($parser);
    }

    public function update(Request $request)
    {
        $request->validate(['tipe' => 'required']);
        return match ($request['tipe']) {
            'upload-hasil-uji' => $this->update_upload_hasil_uji($request),
            'delete-hasil-uji' => $this->delete_hasil_uji($request),
            default            => null,
        };
    }

    private function update_upload_hasil_uji(Request $request)
    {
        $request->validate([
            "jadw_id" => 'required',
            "prod_sert_filepath"         => 'required',
            "prod_sert_tanggal"         => 'required',
            "prod_sert_nomor"         => 'required',
            "prod_sert_lab_nama"         => 'required',
            "prod_sert_status_hasil"         => 'required',
        ]);
		
        try {
            if (!$request->hasFile('prod_sert_filepath')) throw new Exception("Mohon unggah file", 400);

            $dataJadwal = SisJadwal::where('jadw_id', $request['jadw_id']);
            $dataJadwal->select('*');

            $restJadwal = $dataJadwal->get()[0];
            // DEFINE BASE UPLOAD AND UPDATE prod_sert_filepath
            $baseFileUpload = sprintf(config("app.path_file_audit"), $restJadwal->jadw_id);
            $fileData       = $request->file('prod_sert_filepath');
            $fileName       = Str::slug('file-sertifikasi-uji-' . $request['jadw_id'] . '-' . $fileData->getClientOriginalName()) . '-' . time() . '.' . $fileData->getClientOriginalExtension();
            $filePath       = sprintf("%s/%s", $baseFileUpload, $fileName);
            $fileData->move($baseFileUpload, $fileName);

            DB::beginTransaction();
			DB::table('sis_audit_sertifikat_produk')->insert(
				[
					'jadw_id' => $request['jadw_id'], 
					'prod_sert_tanggal' => $request['prod_sert_tanggal'], 
					'prod_sert_nomor' => $request['prod_sert_nomor'], 
					'prod_sert_lab_nama' => $request['prod_sert_lab_nama'], 
					'prod_sert_status_hasil' => $request['prod_sert_status_hasil'],
					'prod_sert_filepath' => $filePath
				]
			);

            DB::commit();
            return responseJSON(200, [], 'Berhasil menyimpan data');
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }

    private function delete_hasil_uji(Request $request)
    {
        try {
            $status_return = TRUE;
            DB::beginTransaction();
			if(is_array($request->ids)){
				foreach ($request->ids as $key => $val) {
					if ($request['filepath'][$key] != '') {
						@unlink($request['filepath'][$key]);
					}
					DB::table('sis_audit_sertifikat_produk')->where('prod_sert_id', $val)->delete();
				}

				DB::commit();
				if ($status_return == TRUE) {
					return responseJSON(200, [], "Berhasil menghapus data");
				} else {
					return responseJSON(500, [], "Terjadi kesalahan saat menghapus data");
				}
			}
			else{
				return responseJSON(500, [], "Belum memilh data");
			}
        } catch (Exception $e) {
            return responseJSON(500, [], $e->getMessage());
        }
    }
}
