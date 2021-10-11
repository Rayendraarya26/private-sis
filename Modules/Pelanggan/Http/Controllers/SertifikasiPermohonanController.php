<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Http\Structs\EmailStruct;
use App\Http\Structs\NotifStruct;
use App\Models\BbkkpSis\MasterBadanHukum;
use App\Models\BbkkpSis\MasterJenisPerusahaan;
use App\Models\BbkkpSis\MasterKabupaten;
use App\Models\BbkkpSis\MasterKecamatan;
use App\Models\BbkkpSis\MasterKomoditi;
use App\Models\BbkkpSis\MasterNegara;
use App\Models\BbkkpSis\MasterProvinsi;
use App\Models\BbkkpSis\MasterSertifikasi;
use App\Models\BbkkpSis\MasterSertifikasiDokumen;
use App\Models\BbkkpSis\SisPelanggan;
use App\Models\BbkkpSis\SisPelangganDokumen;
use App\Models\BbkkpSis\SisPelangganPabrik;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use App\Models\BbkkpSis\SisPermohonan;
use App\Models\BbkkpSis\SisPermohonanDokumen;
use App\Models\BbkkpSis\SisPermohonanKomoditi;
use App\Models\BbkkpSis\SisPermohonanPabrik;
use App\Models\BbkkpSis\SisPermohonanStatus;
use App\Models\BbkkpSis\SysUserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SertifikasiPermohonanController extends Controller
{
    public $module = self::class;
    private $url = 'pelanggan/sertifikasi/permohonan';

    public function index()
    {
        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Permohonan Sertifikasi'),
        ];

        $parser = ['module' => $this->module, 'url' => $this->url, 'breadcrumbs' => $breadcrumbs];
        return view('pelanggan::sertifikasi_permohonan.index')->with($parser);
    }

    public function create()
    {
        $dataPelanggan         = SisPelanggan::where("user_id", auth()->id())->first();
        $masterBadanHukum      = MasterBadanHukum::all();
        $masterJenisPerusahaan = MasterJenisPerusahaan::all();
        $breadcrumbs           = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Permohonan Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Tambah'),
        ];
        $parser                = [
            'module'                => $this->module,
            'url'                   => $this->url,
            'dataPelanggan'         => $dataPelanggan,
            'masterBadanHukum'      => $masterBadanHukum,
            'masterJenisPerusahaan' => $masterJenisPerusahaan,
            'breadcrumbs'           => $breadcrumbs
        ];
        return view('pelanggan::sertifikasi_permohonan.create')->with($parser);
    }

    public function store(Request $request)
    {
        $request->validate([
            "pertanyaan_tambahan" => 'required',
            "jenis_permohonan"    => 'required',
            "jenis_sertifikasi"   => 'required',
        ]);

        // Set data uploaded file path (digunakan untuk delete file yang diupload ketika catch error)
        $uploadedPath = [];
        $custID       = auth()->user()?->sis_pelanggan->cust_id;
        try {
            if (!$request->hasFile('pertanyaan_tambahan')) throw new Exception("Mohon unggah pertanyaan tambahan", 400);
            if ($request['jenis_permohonan'] == "lama" && empty($request['sertifikat_lama_id'])) throw new Exception("Sertifikat lama belum dipilih", 400);

            /* TODO:
             * 1. FIND: data sis_pelanggan, sis_pelanggan_dokumens, sis_pelanggan_pabrik
             * 2. FIND: master_sertifikasi dan dukumen yang dibutuhkan master_sertifikasi_dokumens (cek juga apakah semua dokumen sudah terupload)
             * 2. ADD: sis_permohonan, sis_pelanggan_pabrik, sis_permohonan_dokumens (sesuai jenis sertifikasi), sis_permohonan_komoditi (jika ada)
             * 3. COPY: Jika sukses copy file sis_pelanggan_dokumens ke lokasi sis_permohonan_dokumens
             *  */

            DB::beginTransaction();
            // 1
            $dataSisPelanggan = SisPelanggan::with(["sis_pelanggan_dokumens", "sis_pelanggan_pabriks"])->find($custID)->first();

            // 2
            $dataMasterSertifiaksi = MasterSertifikasi::with('master_sertifikasi_dokumens.master_jenis_dok_perusahaan')->findOrFail($request['jenis_sertifikasi']);
            $uploadedDocID         = [];
            $requiredDocID         = [];
            foreach ($dataSisPelanggan->sis_pelanggan_dokumens as $dokumen) {
                array_push($uploadedDocID, $dokumen->jenis_dok_perusahaan_id);
            }
            foreach ($dataMasterSertifiaksi->master_sertifikasi_dokumens as $dms) {
                array_push($requiredDocID, $dms->jenis_dok_perusahaan_id);
                if (!in_array($dms->jenis_dok_perusahaan_id, $uploadedDocID)) throw new Exception(sprintf("Dokumen %s belum di unggah", $dataMasterSertifiaksi->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text), 400);
            }

            if ($dataMasterSertifiaksi->sert_is_product == "ya" && empty($request['data_komoditas'])) throw new Exception("Data komoditas belum di inputkan", 400);

            // 3.1 add sis_permohonan
            $newSisPermohonan                                    = new SisPermohonan();
            $newSisPermohonan->cust_id                           = $dataSisPelanggan->cust_id;
            $newSisPermohonan->user_id                           = $dataSisPelanggan->user_id;
            $newSisPermohonan->sert_id                           = $request['jenis_sertifikasi'];
            $newSisPermohonan->mohon_jenis_status                = $request['jenis_permohonan'];
            $newSisPermohonan->cust_sert_id                      = $request['sertifikat_lama_id'];
            $newSisPermohonan->mohon_kajian_permohonan_file      = null;
            $newSisPermohonan->mohon_pernyataan_persetujuan_file = null;
            $newSisPermohonan->mohon_spk_file                    = null;
            $newSisPermohonan->mohon_cust_email                  = $dataSisPelanggan->cust_email;
            $newSisPermohonan->mohon_cust_nomor_telp             = $dataSisPelanggan->cust_nomor_telp;
            $newSisPermohonan->mohon_cust_nomor_fax              = $dataSisPelanggan->cust_nomor_fax;
            $newSisPermohonan->mohon_cust_nomor_hp               = $dataSisPelanggan->cust_nomor_hp;
            $newSisPermohonan->mohon_cust_nama                   = $dataSisPelanggan->cust_nama;
            $newSisPermohonan->jenis_perusahaan_id               = $dataSisPelanggan->jenis_perusahaan_id;
            $newSisPermohonan->badan_hukum_id                    = $dataSisPelanggan->badan_hukum_id;
            $newSisPermohonan->cust_asing                        = $dataSisPelanggan->cust_asing;
            $newSisPermohonan->negara_id                         = $dataSisPelanggan->negara_id;
            $newSisPermohonan->kec_id                            = $dataSisPelanggan->kec_id;
            $newSisPermohonan->kab_id                            = $dataSisPelanggan->kab_id;
            $newSisPermohonan->prov_id                           = $dataSisPelanggan->prov_id;
            $newSisPermohonan->mohon_cust_alamat                 = $dataSisPelanggan->cust_alamat;
            $newSisPermohonan->mohon_cust_nomor_akta_pendirian   = $dataSisPelanggan->cust_nomor_akta_pendirian;
            $newSisPermohonan->mohon_cust_nama_pemilik           = $dataSisPelanggan->cust_nama_pemilik;
            $newSisPermohonan->mohon_cust_nama_pimpinan          = $dataSisPelanggan->cust_nama_pimpinan;
            $newSisPermohonan->mohon_cust_nama_wakil_manajemen   = $dataSisPelanggan->cust_nama_wakil_manajemen;
            $newSisPermohonan->mohon_cust_jumlah_bagian          = $dataSisPelanggan->cust_jumlah_bagian;
            $newSisPermohonan->mohon_cust_jumlah_manajemen       = $dataSisPelanggan->cust_jumlah_manajemen;
            $newSisPermohonan->mohon_cust_jumlah_administrasi    = $dataSisPelanggan->cust_jumlah_administrasi;
            $newSisPermohonan->mohon_cust_jumlah_part_time       = $dataSisPelanggan->cust_jumlah_part_time;
            $newSisPermohonan->mohon_cust_jumlah_operasional     = $dataSisPelanggan->cust_jumlah_operasional;
            $newSisPermohonan->mohon_cust_jumlah_shift_1         = $dataSisPelanggan->cust_jumlah_shift_1;
            $newSisPermohonan->mohon_cust_jumlah_shift_2         = $dataSisPelanggan->cust_jumlah_shift_2;
            $newSisPermohonan->mohon_cust_jumlah_shift_3         = $dataSisPelanggan->cust_jumlah_shift_3;
            $newSisPermohonan->mohon_cust_jumlah_non_permanen    = $dataSisPelanggan->cust_jumlah_non_permanen;
            $newSisPermohonan->mohon_cust_shif_kerja             = $dataSisPelanggan->cust_shif_kerja;
            $newSisPermohonan->mohon_cust_luas_tanah             = $dataSisPelanggan->cust_luas_tanah;
            $newSisPermohonan->mohon_cust_luas_bangunan          = $dataSisPelanggan->cust_luas_bangunan;
            $newSisPermohonan->mohon_pertanyaan_filepath         = null;
            $newSisPermohonan->created_at                        = Carbon::now();
            $newSisPermohonan->updated_at                        = Carbon::now();
            $newSisPermohonan->save();

            // DEFINE BASE UPLOAD AND UPDATE mohon_pertanyaan_filepath
            $baseFileUpload     = sprintf(config("app.path_file_pengajuan"), $newSisPermohonan->mohon_id);
            $filePertanyaan     = $request->file('pertanyaan_tambahan');
            $filePertanyaanName = Str::slug('pertanyaan-tambahan' . $filePertanyaan->getClientOriginalName()) . '-' . time() . '.' . $filePertanyaan->getClientOriginalExtension();
            $filePertanyaanPath = sprintf("%s/%s", $baseFileUpload, $filePertanyaanName);
            $filePertanyaan->move($baseFileUpload, $filePertanyaanName);
            array_push($uploadedPath, $filePertanyaanPath);
            $newSisPermohonan->mohon_pertanyaan_filepath = $filePertanyaanPath;
            $newSisPermohonan->save();

            // 3.2 add sis_permohonan_pabrik
            if (!empty($dataSisPelanggan?->sis_pelanggan_pabriks)) {
                foreach ($dataSisPelanggan?->sis_pelanggan_pabriks as $pabrik) {
                    $newSisPermohonanPabrik                               = new SisPermohonanPabrik();
                    $newSisPermohonanPabrik->mohon_id                     = $newSisPermohonan->mohon_id;
                    $newSisPermohonanPabrik->mohon_pabrik_nomor_telp      = $pabrik->pabrik_nomor_telp;
                    $newSisPermohonanPabrik->mohon_pabrik_nomor_fax       = $pabrik->pabrik_nomor_fax;
                    $newSisPermohonanPabrik->mohon_pabrik_nomor_hp        = $pabrik->pabrik_nomor_hp;
                    $newSisPermohonanPabrik->mohon_pabrik_nama            = $pabrik->pabrik_nama;
                    $newSisPermohonanPabrik->negara_id                    = $pabrik->negara_id;
                    $newSisPermohonanPabrik->kec_id                       = $pabrik->kec_id;
                    $newSisPermohonanPabrik->kab_id                       = $pabrik->kab_id;
                    $newSisPermohonanPabrik->prov_id                      = $pabrik->prov_id;
                    $newSisPermohonanPabrik->mohon_pabrik_alamat          = $pabrik->pabrik_alamat;
                    $newSisPermohonanPabrik->mohon_pabrik_kode_pos        = $pabrik->pabrik_kode_pos;
                    $newSisPermohonanPabrik->mohon_pabrik_jumlah_karyawan = $pabrik->pabrik_jumlah_karyawan;
                    $newSisPermohonanPabrik->mohon_pabrik_kegiatan_utama  = $pabrik->pabrik_kegiatan_utama;
                    $newSisPermohonanPabrik->mohon_pabrik_luas_tanah      = $pabrik->pabrik_luas_tanah;
                    $newSisPermohonanPabrik->mohon_pabrik_luas_bangunan   = $pabrik->pabrik_luas_bangunan;
                    $newSisPermohonanPabrik->created_at                   = Carbon::now();
                    $newSisPermohonanPabrik->updated_at                   = Carbon::now();
                    $newSisPermohonanPabrik->save();
                }
            }

            // 3.3 add sis_permohonan_dokumens
            if (!empty($dataSisPelanggan?->sis_pelanggan_dokumens)) {
                foreach ($dataSisPelanggan?->sis_pelanggan_dokumens as $dokumen) {
                    if (in_array($dokumen->jenis_dok_perusahaan_id, $requiredDocID)) {
                        // Get data pelanggan dokumen
                        $pelangganFilePath = public_path($dokumen->cust_dok_filepath);

                        // Copy to pengajuan
                        $dokumenName   = basename($pelangganFilePath);
                        $dokumenFolder = sprintf("%s/dokumen", $baseFileUpload);
                        $dokumenPath   = sprintf("%s/%s", $dokumenFolder, $dokumenName);
                        if (!File::exists($dokumenFolder)) {
                            File::makeDirectory($dokumenFolder, 0777, true, true);
                        }
                        copy($pelangganFilePath, $dokumenPath);
                        array_push($uploadedPath, $dokumenPath);

                        $newSisPermohonanDokumen                          = new SisPermohonanDokumen();
                        $newSisPermohonanDokumen->mohon_id                = $newSisPermohonan->mohon_id;
                        $newSisPermohonanDokumen->jenis_dok_perusahaan_id = $dokumen->jenis_dok_perusahaan_id;
                        $newSisPermohonanDokumen->mohon_dok_deskripsi     = $dokumen->cust_dok_deskripsi;
                        $newSisPermohonanDokumen->mohon_dok_filepath      = $dokumenPath;
                        $newSisPermohonanDokumen->created_at              = Carbon::now();
                        $newSisPermohonanDokumen->updated_at              = Carbon::now();
                        $newSisPermohonanDokumen->save();
                    }
                }
            }

            // 3.4 add sis_permohonan_komoditi
            if ($dataMasterSertifiaksi->sert_is_product == "ya") {
                $dataKomoditas = json_decode($request['data_komoditas']);
                foreach ($dataKomoditas as $komoditi) {
                    $newSisPermohonanKomoditas                                                 = new SisPermohonanKomoditi();
                    $newSisPermohonanKomoditas->mohon_id                                       = $newSisPermohonan->mohon_id;
                    $newSisPermohonanKomoditas->komodt_id                                      = $komoditi->komoditi_id;
                    $newSisPermohonanKomoditas->mohon_kmditi_sni                               = $komoditi->sni;
                    $newSisPermohonanKomoditas->mohon_kmditi_merk                              = $komoditi->merk;
                    $newSisPermohonanKomoditas->mohon_kmditi_tipe                              = $komoditi->tipe;
                    $newSisPermohonanKomoditas->mohon_kmditi_ukuran                            = $komoditi->ukuran;
                    $newSisPermohonanKomoditas->mohon_kmditi_kapasitas_produksi_tahunan        = $komoditi->produksi_tahunan;
                    $newSisPermohonanKomoditas->mohon_kmditi_kapasitas_produksi_tahunan_satuan = $komoditi->satuan_produksi;
                    $newSisPermohonanKomoditas->created_at                                     = Carbon::now();
                    $newSisPermohonanKomoditas->updated_at                                     = Carbon::now();
                    $newSisPermohonanKomoditas->save();
                }
            }
            DB::commit();

            // Send Notification to Marketing
            $groupMarketing = SysUserGroup::with('user')->where('ug_group_id', 4)->get();
            if ($groupMarketing) {
                foreach ($groupMarketing as $marketing) {
                    // Send Push
                    $notifStruct            = new NotifStruct();
                    $notifStruct->title     = $newSisPermohonan->mohon_jenis_status == 'baru' ? "Permohonan Pengajuan Sertifikasi " : "Permohonan Perpanjangan sertifikasi";
                    $notifStruct->message   = sprintf("%s mengajukan permohonan %s", $newSisPermohonan->mohon_cust_nama, $dataMasterSertifiaksi->sert_nama);
                    $notifStruct->user_id   = $marketing?->user?->user_id;
                    $notifStruct->click_url = url('/marketing/verifikasi-permohonan');
                    sendNotification($notifStruct);

                    // Send Email
                    $structEmail          = new EmailStruct();
                    $structEmail->subject = "Verifikasi Akun";
                    $structEmail->body    = view('pelanggan::sertifikasi_permohonan.mails.marketing_permohonan_baru')
                        ->with([
                            'pemohonNama'       => $newSisPermohonan->mohon_cust_nama,
                            'pemohonSertifNama' => $dataMasterSertifiaksi->sert_nama,
                            'link_verif'        => url('/marketing/verifikasi-permohonan'),
                        ])->render();
                    $structEmail->to      = $marketing?->user?->user_email;
                    sendEmail($structEmail);
                }
            }

            // Add Pengajuan Status
            SisPermohonanStatus::create([
                "status_mohon_id" => $newSisPermohonan->mohon_id,
                "status_tipe"     => "informasi",
                "status_judul"    => "Pengajuan Permohonan",
                "status_pesan"    => sprintf("%s mengajukan permohonan sertifikasi %s", auth()->user()->user_fullname, $dataMasterSertifiaksi->sert_nama),
                "created_at"      => Carbon::now(),
                "updated_at"      => Carbon::now(),
            ]);

            return responseJSON(200, null, "Permohonan berhasil dan sedang tahap verifikasi");
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($uploadedPath as $delPath) { // delete uploaded file
                @unlink($delPath);
            }

            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function edit($mohonID)
    {
        $dataPemohon           = SisPermohonan::with([
            'sis_pelanggan_sertifikasi',
            'sis_permohonan_dokumens.master_jenis_dok_perusahaan',
            'sis_permohonan_komoditis.master_komoditi',
            'sis_permohonan_pabriks.master_kabupaten',
            'sis_permohonan_pabriks.master_kecamatan',
            'sis_permohonan_pabriks.master_provinsi',
            'master_sertifikasi',
            'master_jenis_perusahaan',
            'master_badan_hukum',
            'master_negara',
            'master_provinsi',
            'master_kabupaten',
            'master_kecamatan',
        ])->where("user_id", auth()->id())->findOrFail($mohonID);
        $masterBadanHukum      = MasterBadanHukum::all();
        $masterJenisPerusahaan = MasterJenisPerusahaan::all();

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Permohonan Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Ubah'),
        ];

        $parser = [
            'breadcrumbs'           => $breadcrumbs,
            'module'                => $this->module,
            'url'                   => $this->url,
            'dataPemohon'           => $dataPemohon,
            'masterBadanHukum'      => $masterBadanHukum,
            'masterJenisPerusahaan' => $masterJenisPerusahaan,
        ];
        // dd($parser);
        return view('pelanggan::sertifikasi_permohonan.edit')->with($parser);
    }

    public function update(Request $request)
    {
        $uploadedPath = [];
        try {
            $request->validate([
                "mohon_id"            => 'required|integer',
                "pertanyaan_tambahan" => 'sometimes|mimetypes:application/pdf',
                'data_komoditas'      => 'sometimes'
            ]);

            /* TODO:
             * 1. FIND: data sis_permohonan dengan id mohon_id
             * 2. UPDATE: data komoditas (jika sert_produk = ya)
             * 3. UPDATE: pertanyaan tambahan (jika di upload)
             *  */

            $dataPemohon = SisPermohonan::with(['master_sertifikasi'])
                ->where('mohon_id', $request['mohon_id'])
                ->where('cust_id', auth()->user()->sis_pelanggan->cust_id)->first();

            if ($dataPemohon->master_sertifikasi->sert_is_product == "ya") {
                if (empty($request['data_komoditas'])) throw new Exception("Data komoditas dibutuhkan", 500);
                $dataKomoditas = json_decode($request['data_komoditas']);
                SisPermohonanKomoditi::where('mohon_id', $dataPemohon->mohon_id)->delete(); // Delete ROW
                foreach ($dataKomoditas as $komoditas) {
                    SisPermohonanKomoditi::firstOrCreate(
                        [
                            'komodt_id'                                      => $komoditas->komoditi_id,
                            'mohon_kmditi_sni'                               => $komoditas->sni,
                            'mohon_kmditi_merk'                              => $komoditas->merk,
                            'mohon_kmditi_tipe'                              => $komoditas->tipe,
                            'mohon_kmditi_ukuran'                            => $komoditas->ukuran,
                            'mohon_kmditi_kapasitas_produksi_tahunan'        => $komoditas->produksi_tahunan,
                            'mohon_kmditi_kapasitas_produksi_tahunan_satuan' => $komoditas->satuan_produksi,
                        ],
                        [
                            'mohon_id'   => $dataPemohon->mohon_id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
                }
            }

            $baseFileUpload = sprintf(config("app.path_file_pengajuan"), $dataPemohon->mohon_id);
            if ($request->hasFile('pertanyaan_tambahan')) {
                $filePertanyaan     = $request->file('pertanyaan_tambahan');
                $filePertanyaanName = Str::slug('pertanyaan-tambahan' . $filePertanyaan->getClientOriginalName()) . '-' . time() . '.' . $filePertanyaan->getClientOriginalExtension();
                $filePertanyaanPath = sprintf("%s/%s", $baseFileUpload, $filePertanyaanName);
                $filePertanyaan->move($baseFileUpload, $filePertanyaanName);
                array_push($uploadedPath, $filePertanyaanPath);
                $dataPemohon->mohon_pertanyaan_filepath = $filePertanyaanPath;
                $dataPemohon->save();
            }

            DB::commit();

            return responseJSON(200, null, "Pembaruan data permohonan berhasil");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedPath as $delPath) { // delete uploaded file
                @unlink($delPath);
            }
            return responseJSON(500, null, $e->getMessage());
        }

    }

    public function detail(Request $request, $mohonID)
    {
        $dataPemohon = SisPermohonan::with([
            'sis_pelanggan_sertifikasi',
            'sis_permohonan_dokumens.master_jenis_dok_perusahaan',
            'sis_permohonan_komoditis.master_komoditi',
            'sis_permohonan_pabriks.master_kabupaten',
            'sis_permohonan_pabriks.master_kecamatan',
            'sis_permohonan_pabriks.master_provinsi',
            'master_sertifikasi',
            'master_jenis_perusahaan',
            'master_badan_hukum',
            'master_negara',
            'master_provinsi',
            'master_kabupaten',
            'master_kecamatan',
        ])
            ->findOrFail($mohonID);

        $breadcrumbs = [
            new BreadcrumbsStruct('Pelanggan'),
            new BreadcrumbsStruct('Permohonan Sertifikasi', url($this->url)),
            new BreadcrumbsStruct('Detail'),
        ];
        $parser      = [
            'breadcrumbs' => $breadcrumbs,
            'module'      => $this->module,
            'url'         => $this->url,
            'dataPemohon' => $dataPemohon,
        ];
        return view('pelanggan::sertifikasi_permohonan.detail')->with($parser);
    }

    public function destroy(Request $request)
    {
        $request->validate(['mohon_id' => 'required|integer']);

        try {
            DB::beginTransaction();
            $dataPemohon = SisPermohonan::where('user_id', auth()->id())->findOrFail($request['mohon_id']);
            if ($dataPemohon->mohon_approved_status != "on-progress") throw new Exception("Permohonan tidak dapat dihapus, anda hanya dapat menghapus permohonan dengan status Proses");
            $deletedPath = sprintf(config("app.path_file_pengajuan"), $dataPemohon->mohon_id);
            $dataPemohon->delete();
            File::deleteDirectory($deletedPath);

            DB::commit();
            return responseJSON(200, null, "Data berhasil dihapus");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    public function ajax(Request $request)
    {
        $request->validate(['action' => 'required']);
        return match ($request['action']) {
            'datagrid'                             => $this->ajax_datagrid($request),
            'combogrid_sertifikat_lama'            => $this->ajax_combogrid_sertifikat_lama($request),
            'combogrid_sertifikasi'                => $this->ajax_combogrid_sertifikasi($request),
            'combogrid_komoditas'                  => $this->ajax_combogrid_komoditas($request),
            'combogrid_negara'                     => $this->ajax_combogrid_negara($request),
            'combogrid_provinsi'                   => $this->ajax_combogrid_provinsi($request),
            'combogrid_kabupaten'                  => $this->ajax_combogrid_kabupaten($request),
            'combogrid_kecamatan'                  => $this->ajax_combogrid_kecamatan($request),
            'dokumen_sertifikat'                   => $this->ajax_dokumen_sertifikat($request),
            "upload_dokumen"                       => $this->ajax_upload_dokumen($request),
            "data_pemohon"                         => $this->ajax_data_pemohon($request),
            "update_data_pemohon"                  => $this->ajax_update_data_pemohon($request),
            "pabrik_data"                          => $this->ajax_pabrik_data($request),
            "pabrik_add"                           => $this->ajax_pabrik_add($request),
            "pabrik_update"                        => $this->ajax_pabrik_update($request),
            "pabrik_delete"                        => $this->ajax_pabrik_delete($request),
            "permohonan_get_dokumen"               => $this->ajax_permohonan_get_dokumen($request),
            "permohonan_unggah_dokumen"            => $this->ajax_permohonan_unggah_dokumen($request),
            "permohonan_kondisi_perusahaan"        => $this->ajax_permohonan_kondisi_perusahaan($request),
            "permohonan_update_kondisi_perusahaan" => $this->ajax_permohonan_update_kondisi_perusahaan($request),
            "permohonan_pabrik_data"               => $this->ajax_permohonan_pabrik_data($request),
            "permohonan_pabrik_add"                => $this->ajax_permohonan_pabrik_add($request),
            "permohonan_pabrik_update"             => $this->ajax_permohonan_pabrik_update($request),
            "permohonan_pabrik_delete"             => $this->ajax_permohonan_pabrik_delete($request),
            default                                => responseJSON(404, null, "Invalid url"),
        };
    }

    private function ajax_datagrid(Request $request)
    {
        $data = SisPermohonan::join('master_sertifikasi', "sis_permohonan.sert_id", "=", "master_sertifikasi.sert_id");
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
        $data->select("sis_permohonan.*", "master_sertifikasi.sert_nama", "master_sertifikasi.sert_id")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['cust_sert_id']          = $d->cust_sert_id;
            $x['mohon_id']              = $d->mohon_id;
            $x['cust_id']               = $d->cust_id;
            $x['user_id']               = $d->user_id;
            $x['sert_id']               = $d->sert_id;
            $x['sert_nama']             = $d->sert_nama;
            $x['mohon_approved_status'] = $d->mohon_approved_status;
            $x['mohon_jenis_status']    = $d->mohon_jenis_status;
            $x['created_at']            = $d->created_at?->format("Y-m-d H:i:s"); // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            $x['update_at']             = $d->update_at?->format("Y-m-d H:i:s");  // ? adalah nullsafe operator, jika data tidak ada maka akan return NULL (fitur php 8)
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_sertifikat_lama(Request $request)
    {
        $data = SisPelangganSertifikasi::with("master_sertifikasi")->join("master_sertifikasi", "master_sertifikasi.sert_id", '=', "sis_pelanggan_sertifikasi.sert_id");
        // Filter
        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_tipe', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_merk', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_nomor_sertifikat', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_nomor_referensi', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_nomor_sni', 'LIKE', '%' . $request->q . '%')
                ->orWhere('cust_sert_lingkup', 'LIKE', '%' . $request->q . '%');
        }

        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        } else {
            $data->orderBy("cust_sert_expired_date");
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['sert_id']                    = $d->sert_id;
            $x['sert_nama']                  = $d->sert_nama;
            $x['sert_deskripsi']             = $d->sert_deskripsi;
            $x['sert_expired']               = $d->sert_expired;
            $x['sert_format_referensi']      = $d->sert_format_referensi;
            $x['sert_is_product']            = $d->sert_is_product;
            $x['cust_sert_id']               = $d->cust_sert_id;
            $x['cust_sert_nomor_sertifikat'] = $d->cust_sert_nomor_sertifikat;
            $x['cust_sert_expired_date']     = $d->created_at?->format("Y-m-d H:i:s");
            $x['cust_sert_nomor_referensi']  = $d->cust_sert_nomor_referensi;
            $x['cust_sert_nomor_sni']        = $d->cust_sert_nomor_sni;
            $x['cust_sert_lingkup']          = $d->cust_sert_lingkup;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_sertifikasi(Request $request)
    {
        $data = MasterSertifikasi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('sert_nama', 'LIKE', '%' . $request->q . '%');
        }

        // Sorter
        if (!empty($request->sort) && !empty($request->order)) {
            $sort  = explode(",", $request->sort);
            $order = explode(",", $request->order);
            for ($i = 0; $i < count($sort); $i++) {
                $data->orderBy($sort[$i], $order[$i]);
            }
        } else {
            $data->orderBy("sert_is_product", "asc");
        }
        // Total
        $total = $data->select(DB::raw('count(*) as total'))->first()->total;
        // Pagination
        $data->select("*")->skip(($request->page - 1) * $request->rows)->take($request->rows);

        // Result
        $result = [];
        foreach ($data->get() as $d) {
            $x['sert_id']               = $d->sert_id;
            $x['sert_nama']             = $d->sert_nama;
            $x['sert_deskripsi']        = $d->sert_deskripsi;
            $x['sert_expired']          = $d->sert_expired;
            $x['sert_format_referensi'] = $d->sert_format_referensi;
            $x['sert_is_product']       = $d->sert_is_product;
            $x['created_at']            = $d->created_at?->format("Y-m-d H:i:s");
            $x['updated_at']            = $d->updated_at?->format("Y-m-d H:i:s");
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_komoditas(Request $request)
    {
        $data = MasterKomoditi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('komodt_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['komodt_id']   = $d->komodt_id;
            $x['komodt_nama'] = $d->komodt_nama;
            array_push($result, $x);
        }

        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_negara(Request $request)
    {
        $data = MasterNegara::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('negara_id', $request->q);
            $data->orWhere('negara_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['negara_id']   = $d->negara_id;
            $x['negara_kode'] = $d->negara_kode;
            $x['negara_nama'] = $d->negara_nama;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_provinsi(Request $request)
    {
        $data = MasterProvinsi::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('prov_id', $request->q);
            $data->orWhere('prov_nama', 'LIKE', '%' . $request->q . '%');
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
            $x['prov_id']   = $d->prov_id;
            $x['prov_nama'] = $d->prov_nama;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_kabupaten(Request $request)
    {
        $data = MasterKabupaten::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('kab_id', $request->q);
            $data->orWhere('kab_nama', 'LIKE', '%' . $request->q . '%');
        }
        if (!empty($request->prov_id)) {
            $data->where('prov_id', $request->prov_id);
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
            $x['kab_nama'] = $d->kab_nama;
            $x['kab_id']   = $d->kab_id;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_combogrid_kecamatan(Request $request)
    {
        $data = MasterKecamatan::select("*");
        // Filter
        if (!empty($request->q)) {
            $data->where('kec_id', $request->q);
            $data->orWhere('kec_nama', 'LIKE', '%' . $request->q . '%');
        }
        if (!empty($request->kab_id)) {
            $data->where('kab_id', $request->kab_id);
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
            $x['kec_id']   = $d->kec_id;
            $x['kec_nama'] = $d->kec_nama;
            array_push($result, $x);
        }
        return response()->json(["total" => $total, "rows" => $result]);
    }

    private function ajax_dokumen_sertifikat(Request $request)
    {
        try {
            $request->validate(['sert_id' => 'required|integer']);
            $dataDokumen = MasterSertifikasiDokumen::with("master_jenis_dok_perusahaan")->where("sert_id", $request['sert_id'])->get();
            $results     = [];
            foreach ($dataDokumen as $dt) {
                $findMyDoc = SisPelangganDokumen::where("cust_id", auth()->user()?->sis_pelanggan->cust_id)
                    ->where("jenis_dok_perusahaan_id", $dt->jenis_dok_perusahaan_id)->first();
                $results[] = [
                    'dt_id'        => $dt->sert_dok_id,
                    'dt_name'      => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text,
                    'dt_sample'    => !empty($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) ? asset($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) : null,
                    'dt_deskripsi' => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_deskripsi,
                    'my_document'  => !empty($findMyDoc) ? asset($findMyDoc->cust_dok_filepath) : null,
                ];
            }

            return responseJSON(200, $results, "data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_upload_dokumen(Request $request)
    {
        try {
            $request->validate([
                'sert_dok_id' => 'required|integer',
                'file'        => 'required|mimetypes:application/pdf|max:10000', // 10MB
            ]);

            $dataMasterSertDok = MasterSertifikasiDokumen::with('master_jenis_dok_perusahaan')->findOrFail($request['sert_dok_id']);

            $dataFile = $request->file("file");
            $filePath = sprintf(config("app.path_file_customer"), auth()->user()?->sis_pelanggan->cust_id);
            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            $fileName = Str::slug($dataMasterSertDok?->master_jenis_dok_perusahaan?->jenis_dok_perusahaan_text) . '-' . time() . '.' . $dataFile->getClientOriginalExtension();
            $dataFile->move($filePath, $fileName);

            $dokumen = SisPelangganDokumen::updateOrCreate(
                ['cust_id' => auth()->user()->sis_pelanggan->cust_id, 'jenis_dok_perusahaan_id' => $dataMasterSertDok->jenis_dok_perusahaan_id],
                ['cust_dok_filepath' => $filePath . '/' . $fileName]
            );

            return responseJSON(200, $dokumen, "Dokumen berhasil di unggah");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_data_pemohon(Request $request)
    {
        try {
            $dataPelanggan              = auth()->user()?->sis_pelanggan;
            $dataPelanggan->negara_nama = $dataPelanggan->master_negara->negara_nama;

            return responseJSON(200, $dataPelanggan, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_data(Request $request)
    {
        try {
            $dataPabrik = auth()->user()?->sis_pelanggan?->sis_pelanggan_pabriks;
            foreach ($dataPabrik as $pabrik) {
                $pabrik->negara_nama = $pabrik->master_negara?->negara_nama;
            }
            return responseJSON(200, $dataPabrik, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_add(Request $request)
    {
        try {
            $dataPelanggan = auth()->user()?->sis_pelanggan;
            $dataPabrik    = $dataPelanggan?->sis_pelanggan_pabriks;

            $newPabrik              = new SisPelangganPabrik();
            $newPabrik->cust_id     = $dataPelanggan->cust_id;
            $newPabrik->pabrik_nama = sprintf("Pabrik %d - (silakan ubah nama pabrik %s)", count($dataPabrik) + 1, Str::random(5));

            $allField = $newPabrik->getFillable();
            foreach ($allField as $field) {
                if (!in_array($field, ['cust_id', 'pabrik_nama', 'negara_id', 'kec_id', 'kab_id', 'prov_id',])) {
                    $newPabrik->$field = "-";
                }
            }
            $newPabrik->save();
            return responseJSON(200, $newPabrik, "Data pabrik ditambahkan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_update(Request $request)
    {
        try {
            $request->validate(['pabrik_id' => 'required|integer', "parameter" => "required", "value" => "required"]);
            $parameter = $request['parameter'];
            $value     = $request['value'] == '--' ? NULL : $request['value'];

            $dataPabrik             = SisPelangganPabrik::findOrFail($request['pabrik_id']);
            $dataPabrik->$parameter = $value;
            $dataPabrik->save();
            return responseJSON(200, null, "Data pabrik diperbarui");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_pabrik_delete(Request $request)
    {
        try {
            $request->validate(['pabrik_id' => 'required|integer']);

            $dataPabrik = SisPelangganPabrik::findOrFail($request['pabrik_id']);
            $dataPabrik->delete();
            return responseJSON(200, null, "Data pabrik dihapus");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_update_data_pemohon(Request $request)
    {
        try {
            $request->validate(["parameter" => "required", "value" => "required"]);
            $parameter = $request['parameter'];
            $value     = $request['value'] == '--' ? NULL : $request['value'];

            $dataPemohon                          = auth()->user()?->sis_pelanggan;
            $dataPemohon->$parameter              = $value;
            $dataPemohon->cust_jumlah_operasional = $dataPemohon->cust_jumlah_shift_1 + $dataPemohon->cust_jumlah_shift_2 + $dataPemohon->cust_jumlah_shift_3;
            $dataPemohon->save();
            return responseJSON(200, $dataPemohon, "Data diperbarui");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_get_dokumen(Request $request)
    {
        try {
            $request->validate(['mohon_id' => 'required|integer']);

            $dataPemohon = SisPermohonan::with(['sis_permohonan_dokumens.master_jenis_dok_perusahaan'])
                ->where("user_id", auth()->id())->findOrFail($request['mohon_id']);

            $dataDokumen = MasterSertifikasiDokumen::with("master_jenis_dok_perusahaan")->where("sert_id", $dataPemohon->sert_id)->get();
            $results     = [];
            foreach ($dataDokumen as $dt) {
                $findMyDoc = $dataPemohon->sis_permohonan_dokumens()->where('jenis_dok_perusahaan_id', $dt->jenis_dok_perusahaan_id)->first();
                $results[] = [
                    'dt_id'        => $dt->sert_dok_id,
                    'dt_name'      => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_text,
                    'dt_sample'    => !empty($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) ? asset($dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_sample_file) : null,
                    'dt_deskripsi' => $dt->master_jenis_dok_perusahaan->jenis_dok_perusahaan_deskripsi,
                    'my_document'  => !empty($findMyDoc) ? asset($findMyDoc->mohon_dok_filepath) : null,
                ];
            }

            return responseJSON(200, $results, "data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_unggah_dokumen(Request $request)
    {
        $uploadedPath = [];
        try {
            $request->validate([
                'mohon_id'    => 'required|integer',
                'sert_dok_id' => 'required|integer',
                'file'        => 'required|mimetypes:application/pdf|max:10000', // 10MB
            ]);

            DB::beginTransaction();
            $dataPemohon = SisPermohonan::where("user_id", auth()->id())->findOrFail($request['mohon_id']);

            $dataMasterSertDok = MasterSertifikasiDokumen::with('master_jenis_dok_perusahaan')->findOrFail($request['sert_dok_id']);

            // Update data utama
            $dataFile = $request->file("file");
            $filePath = sprintf(config("app.path_file_customer"), auth()->user()?->sis_pelanggan->cust_id);
            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            $fileName = Str::slug($dataMasterSertDok?->master_jenis_dok_perusahaan?->jenis_dok_perusahaan_text) . '-' . time() . '.' . $dataFile->getClientOriginalExtension();
            $dataFile->move($filePath, $fileName);
            $dataDokumen = SisPelangganDokumen::updateOrCreate(
                ['cust_id' => auth()->user()->sis_pelanggan->cust_id, 'jenis_dok_perusahaan_id' => $dataMasterSertDok->jenis_dok_perusahaan_id],
                ['cust_dok_filepath' => $filePath . '/' . $fileName]
            );
            $source      = public_path($dataDokumen->cust_dok_filepath);
            array_push($uploadedPath, $source);

            // Update data pemohon
            # Copy from pelanggan
            $dokumenName    = basename($source);
            $baseFileUpload = sprintf(config("app.path_file_pengajuan"), $dataPemohon->mohon_id);
            $dokumenFolder  = sprintf("%s/dokumen", $baseFileUpload);
            $destination    = sprintf("%s/%s", $dokumenFolder, $dokumenName);
            copy($source, $destination);
            array_push($uploadedPath, $destination);
            $dokumen = SisPermohonanDokumen::updateOrCreate(
                ['mohon_id' => $dataPemohon->mohon_id, 'jenis_dok_perusahaan_id' => $dataMasterSertDok->jenis_dok_perusahaan_id],
                ['mohon_dok_filepath' => $destination]
            );
            DB::commit();

            return responseJSON(200, $dokumen, "Dokumen berhasil di unggah");
        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedPath as $delPath) { // delete uploaded file
                @unlink($delPath);
            }
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_kondisi_perusahaan(Request $request)
    {
        try {
            $request->validate(["mohon_id" => "required|integer"]);
            $dataPemohon              = SisPermohonan::where("user_id", auth()->id())->findOrFail($request['mohon_id']);
            $dataPemohon->negara_nama = $dataPemohon->master_negara->negara_nama;

            return responseJSON(200, $dataPemohon, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_update_kondisi_perusahaan(Request $request)
    {
        try {
            $request->validate(["parameter_main" => "required", "parameter_permohonan" => "required", "value" => "required", 'mohon_id' => 'required']);
            $parameterPelanggan = $request['parameter_main'];
            $parameterPemohon   = $request['parameter_permohonan'];
            $value              = $request['value'] == '--' ? NULL : $request['value'];

            DB::beginTransaction();

            $dataPelanggan                          = auth()->user()?->sis_pelanggan;
            $dataPelanggan->$parameterPelanggan     = $value;
            $dataPelanggan->cust_jumlah_operasional = $dataPelanggan->cust_jumlah_shift_1 + $dataPelanggan->cust_jumlah_shift_2 + $dataPelanggan->cust_jumlah_shift_3;
            $dataPelanggan->save();

            $dataPemohon                                = SisPermohonan::where("user_id", auth()->id())->findOrFail($request['mohon_id']);
            $dataPemohon->$parameterPemohon             = $value;
            $dataPemohon->mohon_cust_jumlah_operasional = $dataPemohon->mohon_cust_jumlah_shift_1 + $dataPemohon->mohon_cust_jumlah_shift_2 + $dataPemohon->mohon_cust_jumlah_shift_3;
            $dataPemohon->save();

            DB::commit();
            return responseJSON(200, $dataPemohon, "Data diperbarui");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_pabrik_data(Request $request)
    {
        try {
            $request->validate(["mohon_id" => "required|integer"]);
            $dataPemohon = SisPermohonan::where("user_id", auth()->id())->findOrFail($request['mohon_id']);
            $dataPabrik  = $dataPemohon?->sis_permohonan_pabriks;
            foreach ($dataPabrik as $pabrik) {
                $pabrik->negara_nama = $pabrik->master_negara?->negara_nama;
            }
            return responseJSON(200, $dataPabrik, "Data ditemukan");
        } catch (Exception $e) {
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_pabrik_add(Request $request)
    {
        try {
            $request->validate(["mohon_id" => "required|integer"]);
            DB::beginTransaction();
            $randomName = Str::random(5);
            // Add Permohonan Data
            $dataPemohon       = SisPermohonan::where("user_id", auth()->id())->findOrFail($request['mohon_id']);
            $dataPabrikPemohon = $dataPemohon?->sis_permohonan_pabriks;

            $mohonNewPabrik                    = new SisPermohonanPabrik();
            $mohonNewPabrik->mohon_id          = $dataPemohon->mohon_id;
            $mohonNewPabrik->mohon_pabrik_nama = sprintf("Pabrik %d - (silakan ubah nama pabrik %s)", count($dataPabrikPemohon) + 1, $randomName);
            $allField                          = $mohonNewPabrik->getFillable();
            foreach ($allField as $field) {
                if (!in_array($field, ['mohon_id', 'mohon_pabrik_nama', 'negara_id', 'kec_id', 'kab_id', 'prov_id',])) {
                    $mohonNewPabrik->$field = "-";
                }
            }
            $mohonNewPabrik->save();


            // Add Main Data
            $dataPelanggan       = auth()->user()?->sis_pelanggan;
            $dataPabrikPelanggan = $dataPelanggan?->sis_pelanggan_pabriks;

            $newPabrik              = new SisPelangganPabrik();
            $newPabrik->cust_id     = $dataPelanggan->cust_id;
            $newPabrik->pabrik_nama = sprintf("Pabrik %d - (silakan ubah nama pabrik %s)", count($dataPabrikPelanggan) + 1, $randomName);

            $allField = $newPabrik->getFillable();
            foreach ($allField as $field) {
                if (!in_array($field, ['cust_id', 'pabrik_nama', 'negara_id', 'kec_id', 'kab_id', 'prov_id',])) {
                    $newPabrik->$field = "-";
                }
            }
            $newPabrik->save();

            DB::commit();
            return responseJSON(200, $newPabrik, "Data pabrik ditambahkan");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_pabrik_update(Request $request)
    {
        try {
            $request->validate([
                'mohon_id'             => 'required|integer',
                'mohon_pabrik_id'      => 'required|integer',
                "parameter_pelanggan"  => "required",
                "parameter_permohonan" => "required",
                "value"                => "required"
            ]);
            DB::beginTransaction();
            $parameterPermohonan = $request['parameter_permohonan'];
            $parameterPelanggan  = $request['parameter_pelanggan'];
            $value               = $request['value'] == '--' ? NULL : $request['value'];
            // Update Permohonan Pabrik
            $dataPabrikPermohonan                       = SisPermohonanPabrik::where('mohon_id', $request['mohon_id'])->findOrFail($request['mohon_pabrik_id']);
            $namaPabrik                                 = $dataPabrikPermohonan->mohon_pabrik_nama;
            $dataPabrikPermohonan->$parameterPermohonan = $value;
            $dataPabrikPermohonan->save();

            // Update Pelanggan Pabrik
            $dataPabrikPelanggan                      = SisPelangganPabrik::where("pabrik_nama", $namaPabrik)->where("cust_id", auth()->user()->sis_pelanggan?->cust_id)->first();
            $dataPabrikPelanggan->$parameterPelanggan = $value;
            $dataPabrikPelanggan->save();
            DB::commit();
            return responseJSON(200, null, "Data pabrik diperbarui");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }

    private function ajax_permohonan_pabrik_delete(Request $request)
    {
        try {
            $request->validate([
                'mohon_pabrik_id' => 'required|integer',
                'mohon_id'        => 'required|integer',
            ]);
            DB::beginTransaction();

            // Delete pabrik permohonan
            $dataPabrikPermohonan = SisPermohonanPabrik::where('mohon_id', $request['mohon_id'])->findOrFail($request['mohon_pabrik_id']);
            $namaPabrik           = $dataPabrikPermohonan->mohon_pabrik_nama;
            $dataPabrikPermohonan->delete();

            // Delete pabrik pelanggan
            $dataPabrikPelanggan = SisPelangganPabrik::where("pabrik_nama", $namaPabrik)->where("cust_id", auth()->user()->sis_pelanggan?->cust_id)->first();
            $dataPabrikPelanggan->delete();

            DB::commit();
            return responseJSON(200, null, "Data pabrik dihapus");
        } catch (Exception $e) {
            DB::rollBack();
            return responseJSON(500, null, $e->getMessage());
        }
    }
}
