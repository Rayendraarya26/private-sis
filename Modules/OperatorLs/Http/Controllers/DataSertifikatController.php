<?php

namespace Modules\OperatorLs\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\CertJecaStruct;
use App\Http\Structs\CertJpaStruct;
use App\Http\Structs\CertYok3Struct;
use App\Http\Structs\CertYqStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\OperatorLs\Http\Traits\SertifikatTrait;

class DataSertifikatController extends Controller
{
    use SertifikatTrait;

    public $module = self::class;
    private $url = 'operatorls/data-sertifikat';
    private $view = "operatorls::data_sertifikat";

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Data Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view("$this->view.index")->with($parser);
    }

    public function uploadSertifikat($sertifikatId)
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Operator Lembaga Sertifikasi'),
            new BreadcrumbsStruct('Data Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Upload Sertifikasi #' . $sertifikatId),
        ];

        $dataSertifikat = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi', 'sis_pelanggan'])->where('sis_pelanggan_sertifikasi.cust_sert_id', '=', $sertifikatId)->first();

        if (isset($dataSertifikat)) {
            $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs, 'data_sertifikat' => $dataSertifikat];
            return view("$this->view.upload_sertifikat")->with($parser);
        } else {
            return view('errors.custom')->with([
                'code'    => 404,
                'info'    => "NOT FOUND",
                'message' => "Invalid URL",
            ]);
        }
    }

    public function saveSertifikat(Request $request)
    {
        $request->validate([
            'cust_id'            => 'required',
            'sert_nama'          => 'required',
            'cust_sert_id'       => 'required|integer',
            'cust_sert_filepath' => 'required|mimes:pdf'
        ]);
        $dataInsert = [];
        if ($request->hasFile("cust_sert_filepath")) {
            $file     = $request->file('cust_sert_filepath');
            $namaFile = Str::slug($request->sert_nama) . '_sertifikat_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = sprintf(config("app.path_file_sertifikat"), $request['cust_id']);
            if (!File::exists($filePath)) File::makeDirectory($filePath, 0777, true, true);
            $file->move(public_path($filePath), $namaFile);
            $dataInsert['cust_sert_filepath'] = $filePath . '/' . $namaFile;

            DB::transaction(function () use ($request, $dataInsert) {
                SisPelangganSertifikasi::findOrFail($request['cust_sert_id'])->update(['cust_sert_filepath' => $dataInsert['cust_sert_filepath']]);
            });

            $data_pelanggan = SisPelanggan::where('cust_id', $request['cust_id'])->select('user_id', 'cust_nama', 'cust_email')->first();
            // Send Push
            $notifStruct            = new NotifStruct();
            $notifStruct->title     = 'Penerbitan Sertifikat';
            $notifStruct->message   = sprintf("Selamat, Sertifikat %s anda telah diterbitkan.", $request['sert_nama']);
            $notifStruct->user_id   = $data_pelanggan?->user_id;
            $notifStruct->click_url = url('/pelanggan/sertifikasi/data');
            sendNotification($notifStruct);

            // Send Email
            $structEmail          = new EmailStruct();
            $structEmail->subject = "Penerbitan Sertifikat";
            $structEmail->body    = view('operatorls::penjadwalan.mails.publish')
                ->with([
                    'nama'       => $data_pelanggan?->cust_nama,
                    'message'    => sprintf("Selamat, Sertifikat %s anda telah diterbitkan.", $request['sert_nama']),
                    'link_verif' => url('/pelanggan/sertifikasi/data'),
                ])->render();
            $structEmail->to      = $data_pelanggan?->cust_email;
            sendEmail($structEmail);

            return redirect($this->url)->with('message', "Upload file sertifikat sudah berhasil disimpan.");
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'File tidak dapat di upload.']);
        }
    }

    public function cetak(Request $request, $sertifikatId)
    {
        $qrySertifikat = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi', 'sis_pelanggan'])->where('sis_pelanggan_sertifikasi.cust_sert_id', '=', $sertifikatId);
        if ($qrySertifikat->first() !== null) {
            try {
                $dataSertifikat = $qrySertifikat->first();
                $qryPelanggan   = SisPelanggan::with(['master_kecamatan', 'master_kabupaten', 'master_provinsi', 'master_negara', 'master_provinsi'])->where('cust_id', '=', $dataSertifikat->cust_id);
                $dataPelanggan  = $qryPelanggan->first();
                if (empty($dataPelanggan->negara_id)) throw new ExpectedException("Tidak dapat mencetak karena data tidak lengkap (Negara tidak ditemukan)");

                $tglTerbit          = $dataSertifikat->cust_sert_tgl_sertifikat_awal->format('d F  Y');
                $tglPerubahan       = ($dataSertifikat->cust_sert_tgl_sertifikat_perubahan != '') ? $dataSertifikat->cust_sert_tgl_sertifikat_perubahan->format('d F  Y') : '';
                $tglKadaluarsa      = $dataSertifikat->cust_sert_expired_date->format('d F  Y');
                $tglSertifikasiAwal = $dataSertifikat->cust_sert_tgl_sertifikat_awal->format('d F Y');
                if ($dataPelanggan?->master_negara?->negara_nama == 'Indonesia') {
                    $perusahaanAlamat = $dataPelanggan->cust_alamat . ', ' . $dataPelanggan->master_kecamatan->kec_nama . ', ' . $dataPelanggan->master_kabupaten->kab_nama . ', ' . $dataPelanggan->master_provinsi->prov_nama . ', ' . $dataPelanggan->master_negara->negara_nama;
                } else {
                    $perusahaanAlamat = $dataPelanggan->cust_alamat . ', ' . $dataPelanggan->master_negara->negara_nama;
                }

                if ($dataSertifikat->master_sertifikasi->sert_id == '1') {
                    $cert = new CertYqStruct(
                        noReg: $dataSertifikat->cust_sert_nomor_referensi,
                        tglSertifikasiAwal: $tglSertifikasiAwal,
                        lembaga: $dataSertifikat->cust_sert_nomor_referensi,
                        perusahaanNama: $dataSertifikat->sis_pelanggan->cust_nama,
                        perusahaanAlamat: $perusahaanAlamat,
                        sertifikasiTipe: $dataSertifikat->cust_sert_nomor_sni,
                        ruangLingkup: $dataSertifikat->cust_sert_lingkup,
                        kodeEA: $dataSertifikat->kode_ea_nama,
                        kodeNACE: $dataSertifikat->kode_nace_nama,
                        tglTerbit: $tglTerbit,
                        tglPerubahan: $tglPerubahan,
                        tglKadaluarsa: $tglKadaluarsa,
                    );
                } else if ($dataSertifikat->master_sertifikasi->sert_id == '2') {
                    $cert = new CertJecaStruct(
                        noReg: $dataSertifikat->cust_sert_nomor_referensi,
                        tglSertifikasiAwal: $tglSertifikasiAwal,
                        lembaga: $dataSertifikat->cust_sert_nomor_referensi,
                        perusahaanNama: $dataSertifikat->sis_pelanggan->cust_nama,
                        perusahaanAlamat: $perusahaanAlamat,
                        sertifikasiTipe: $dataSertifikat->cust_sert_nomor_sni,
                        ruangLingkup: $dataSertifikat->cust_sert_lingkup,
                        kodeEA: $dataSertifikat->kode_ea_nama,
                        kodeNACE: $dataSertifikat->kode_nace_nama,
                        tglTerbit: $tglTerbit,
                        tglPerubahan: $tglPerubahan,
                        tglKadaluarsa: $tglKadaluarsa,
                    );
                } else if (in_array($dataSertifikat->master_sertifikasi->sert_id, ['5', '6', '9'])) {
                    $cert = new CertJpaStruct(
                        noReg: $dataSertifikat->cust_sert_nomor_referensi,
                        tglSertifikasiAwal: $tglSertifikasiAwal,
                        lembaga: $dataSertifikat->cust_sert_nomor_referensi,
                        perusahaanNama: $dataSertifikat->sis_pelanggan->cust_nama,
                        perusahaanAlamat: $perusahaanAlamat,
                        produkJenis: $dataSertifikat->master_komoditi->komodt_nama,
                        produkTipe: $dataSertifikat->cust_sert_tipe,
                        produkMerk: $dataSertifikat->cust_sert_merk,
                        produkStandar: $dataSertifikat->cust_sert_nomor_sni,
                        produkSistemSertifikasi: 'Tipe 5',
                        tglTerbit: $tglTerbit,
                        tglPerubahan: $tglPerubahan,
                        tglKadaluarsa: $tglKadaluarsa,
                    );
                } else {
                    $cert = new CertYok3Struct(
                        noReg: $dataSertifikat->cust_sert_nomor_referensi,
                        lembaga: $dataSertifikat->cust_sert_nomor_referensi,
                        perusahaanNama: $dataSertifikat->sis_pelanggan->cust_nama,
                        perusahaanAlamat: $perusahaanAlamat,
                        sertifikasiTipe: $dataSertifikat->cust_sert_nomor_sni,
                        ruangLingkup: $dataSertifikat->cust_sert_lingkup,
                        kodeEA: $dataSertifikat->kode_ea_nama,
                        kodeNACE: $dataSertifikat->kode_nace_nama,
                        tglTerbit: $tglTerbit,
                        tglPerubahan: $tglPerubahan,
                        tglKadaluarsa: $tglKadaluarsa,
                    );
                }


                $path = $cert->generate();
                return response()->download($path)->deleteFileAfterSend(true);
            } catch (Exception $e) {
                if (!($e instanceof ExpectedException)) log_error($e, $request->except("_token"));
                return redirect()->back()->withInput($request->all())->withErrors(['message' => $e->getMessage()]);
            }

        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['message' => 'Data tidak ditemukan.']);
        }
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
        // $data = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi', 'sis_pelanggan']);
        $data = SisPelangganSertifikasi::join('sis_pelanggan', "sis_pelanggan_sertifikasi.cust_id", "=", "sis_pelanggan.cust_id");
        $data->join('master_sertifikasi', "sis_pelanggan_sertifikasi.sert_id", "=", "master_sertifikasi.sert_id");
        $data->leftJoin('master_komoditi', "master_komoditi.komodt_id", "=", "sis_pelanggan_sertifikasi.komodt_id");
        // Filter
        if (!empty($request->filterRules)) {
            foreach (json_decode($request->filterRules) as $f) {
                if ($f->value != '') {
                    if ($f->field == 'cust_sert_file') {
                        if ($f->value == 'ya') {
                            $data->whereNotNull('cust_sert_filepath');
                        } else {
                            $data->whereNull('cust_sert_filepath');
                        }
                    } else {
                        $data->where($f->field, 'LIKE', '%' . $f->value . '%');
                    }
                }

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
            $x['cust_sert_id']                  = $d->cust_sert_id;
            $x['cust_nama']                     = $d->sis_pelanggan->cust_nama;
            $x['cust_sert_nomor_sertifikat']    = $d->cust_sert_nomor_sertifikat;
            $x['cust_sert_nomor_referensi']     = $d->cust_sert_nomor_referensi;
            $x['cust_sert_nomor_sni']           = $d->cust_sert_nomor_sni;
            $x['cust_sert_status']              = $d->cust_sert_status;
            $x['cust_sert_tgl_sertifikat_awal'] = $d->cust_sert_tgl_sertifikat_awal;
            $x['cust_sert_expired_date']        = $d->cust_sert_expired_date->format("Y-m-d H:i:s");
            $x['cust_sert_status_survailen']    = $d->cust_sert_status_survailen;
            $x['cust_sert_file']                = !empty($d->cust_sert_filepath) ? 'ya' : 'tidak';
            $x['cust_sert_filepath']            = !empty($d->cust_sert_filepath) ? asset($d->cust_sert_filepath) : null;
            $x['komodt_nama']                   = $d->master_komoditi->komodt_nama;
            $result[]                           = $x;
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }
}
