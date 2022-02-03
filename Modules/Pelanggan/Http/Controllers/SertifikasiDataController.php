<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\CertJecaStruct;
use App\Http\Structs\CertJpaStruct;
use App\Http\Structs\CertYok3Struct;
use App\Http\Structs\CertYqStruct;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\OperatorLs\Http\Traits\SertifikatTrait;

class SertifikasiDataController extends Controller
{
    use SertifikatTrait;

    public $module = self::class;
    private $url = 'pelanggan/sertifikasi/data';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Data Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::sertifikasi_data.index')->with($parser);
    }

    public function preview(Request $request)
    {
        if ($request['type'] == 1) {
            $cert = new CertYqStruct(
                noReg: '00/70',
                tglSertifikasiAwal: '24 Februari 2022',
                lembaga: 'YQ 005 032',
                perusahaanNama: 'PT. HOK TONG',
                perusahaanAlamat: 'JI. Raden Patah, RT 07, Kel. Sijenjang, Kec. Jambi Timur, Jambi - 36149, Jambi - INDONESIA',
                sertifikasiTipe: 'SNI ISO 9001:2015',
                ruangLingkup: 'Proses produksi crumb rubber (SIR 10, SIR 20)',
                kodeEA: '[14] Karet dan produk plastik',
                kodeNACE: 'C.22.19 Pembuatan produk karet lainnya',
                tglTerbit: '21 Desember 2021',
                tglPerubahan: '-',
                tglKadaluarsa: '4 Oktober 2024',
            );
        } else if ($request['type'] == 2) {
            $cert = new CertJecaStruct(
                noReg: '00/70',
                tglSertifikasiAwal: '24 Februari 2022',
                lembaga: 'JECA 004 032',
                perusahaanNama: 'PT. ANEKA BUMI PRATAMA',
                perusahaanAlamat: 'JI. Raden Patah, RT 07, Kel. Sijenjang, Kec. Jambi Timur, Jambi - 36149, Jambi - INDONESIA',
                sertifikasiTipe: 'SNI ISO 9001:2015',
                ruangLingkup: 'Proses produksi crumb rubber (SIR 10, SIR 20)',
                kodeEA: '[14] Karet dan produk plastik',
                kodeNACE: 'C.22.19 Pembuatan produk karet lainnya',
                tglTerbit: '21 Desember 2021',
                tglPerubahan: '-',
                tglKadaluarsa: '4 Oktober 2024',
            );
        } else if ($request['type'] == 3) {
            $cert = new CertJpaStruct(
                noReg: "11/JPA/06",
                tglSertifikasiAwal: '27 April 2021',
                lembaga: 'JPA 009 010.11',
                perusahaanNama: 'PT. PENTASARI PRANAKARYA',
                perusahaanAlamat: 'JI. Tambak Aji I No. 1, Kel. Ngaliyan, Kec. Ngaliyan, Semarang - 50185, Jawa Tengah - INDONESIA',
                produkJenis: 'Ban Dalam',
                produkTipe: 'Karet Alam',
                produkMerk: 'DURATUBE',
                produkStandar: 'SNI 6700:2012',
                produkSistemSertifikasi: 'Tipe 5',
                tglTerbit: '11 Januari 2022',
                tglPerubahan: '-',
                tglKadaluarsa: '10 Januari 2026',
            );
        } else {
            $cert = new CertYok3Struct(
                noReg: '003/SMK3/21',
                lembaga: 'YO K3 009 003',
                perusahaanNama: 'PT. ABAISIAT RAYA',
                perusahaanAlamat: 'Jl. Raya Padang - Painan KM. 9, Sei Beremas,Kel. Gates Nan XX, Kec. Lubuk Begalung,Padang - 25227, Sumatera Barat - INDONESIA',
                sertifikasiTipe: 'SNI ISO 45001:2018',
                ruangLingkup: 'Proses produksi crumb rubber (SIR 10 & SIR 20)',
                kodeEA: '[14] Karet dan produk plastik',
                kodeNACE: 'C.22.19 Pembuatan produk karet lainnya',
                tglTerbit: '29 Oktober 2021',
                tglPerubahan: '-',
                tglKadaluarsa: '28 Oktober 2024',
            );
        }

        $path = $cert->generate();
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid' => $this->ajax_datagrid($request),
            default    => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi'])
            ->join('sis_pelanggan', 'sis_pelanggan.cust_id', 'sis_pelanggan_sertifikasi.cust_id')
            ->where('sis_pelanggan.user_id', '=', auth()->id());
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
            $x['cust_sert_nomor_sertifikat']    = $d->cust_sert_nomor_sertifikat;
            $x['cust_sert_nomor_referensi']     = $d->cust_sert_nomor_referensi;
            $x['cust_sert_nomor_sni']           = $d->cust_sert_nomor_sni;
            $x['cust_sert_status']              = $d->cust_sert_status;
            $x['cust_sert_tgl_sertifikat_awal'] = $d->cust_sert_tgl_sertifikat_awal;
            $x['cust_sert_expired_date']        = $d->cust_sert_expired_date->format("Y-m-d H:i:s");
            $x['cust_sert_status_survailen']    = $d->cust_sert_status_survailen;
            $x['cust_sert_filepath']            = !empty($d->cust_sert_filepath) ? asset($d->cust_sert_filepath) : null;
            $x['komodt_nama']                   = $d->master_komoditi->komodt_nama;
            $result[]                           = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
