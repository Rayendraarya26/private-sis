<?php

namespace App\Http\Structs;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CertJpaStruct
{
    public string $noReg;              // No. Reg : 00/70
    public string $tglSertifikasiAwal; // Sertifikasi awal:
    public string $lembaga;            // JPA 009 010.11
    public string $perusahaanNama;     // PT. PENTASARI PRANAKARYA
    public string $perusahaanAlamat;   // JI. Tambak Aji I No. 1, Kel. Ngaliyan,...

    public string $produkJenis;
    public string $produkTipe;
    public string $produkMerk;
    public string $produkStandar;
    public string $produkSistemSertifikasi;

    public string $tglTerbit;
    public string $tglPerubahan;
    public string $tglKadaluarsa;

    public function __construct(
        $noReg = '',
        $tglSertifikasiAwal = '',
        $lembaga = '',
        $perusahaanNama = '',
        $perusahaanAlamat = '',
        $produkJenis = '',
        $produkTipe = '',
        $produkMerk = '',
        $produkStandar = '',
        $produkSistemSertifikasi = '',
        $tglTerbit = '',
        $tglPerubahan = '',
        $tglKadaluarsa = '',
    )
    {
        $this->noReg                   = $noReg;
        $this->tglSertifikasiAwal      = $tglSertifikasiAwal;
        $this->lembaga                 = $lembaga;
        $this->perusahaanNama          = $perusahaanNama;
        $this->perusahaanAlamat        = $perusahaanAlamat;
        $this->produkJenis             = $produkJenis;
        $this->produkTipe              = $produkTipe;
        $this->produkMerk              = $produkMerk;
        $this->produkStandar           = $produkStandar;
        $this->produkSistemSertifikasi = $produkSistemSertifikasi;
        $this->tglTerbit               = $tglTerbit;
        $this->tglPerubahan            = $tglPerubahan;
        $this->tglKadaluarsa           = $tglKadaluarsa;
    }

    public function generate()
    {
        $img = Image::make(public_path('images/sertifikasi-asset/cert_jpa.png'));

        // Nomer Registrasi
        $img->text($this->noReg, 140, 930, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(45);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Tgl Sert Awal
        $img->text("Sertifikasi awal : $this->tglSertifikasiAwal", 2350, 930, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(45);
            $font->color("#000000");
            $font->align("right");
            $font->valign("middle");
        });

        // Jenis Cert
        $img->text("$this->lembaga", 1200, 1020, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(100);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Lembaga Declare
        $img->text("Lembaga Sertifikasi Produk LSPro - BBKKP JOGJA PRODUCT ASSURANCE", 1200, 1200, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Lembaga Declare2
        $img->text("memberikan sertifikat kepada : ", 1200, 1270, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Perusahaan Nama
        $img->text("$this->perusahaanNama", 1200, 1350, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Medium.ttf');
            $font->file($fontType);
            $font->size(120);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Alamat
        $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
        renderMultiline(
            $img,
            [
                'xAxis'    => 1200,
                'yAxis'    => 1430,
                'color'    => '#000000',
                'size'     => 60,
                'align'    => 'center',
                'valign'   => 'top',
                'fontType' => $fontType,
            ],
            "$this->perusahaanAlamat",
            40);

        // Jenis Produk
        $img->text("Jenis Produk : $this->produkJenis", 1200, 1750, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Tipe Produk
        $img->text("Tipe : $this->produkTipe", 1200, 1820, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Merk Produk
        $img->text("Merk : $this->produkMerk", 1200, 1890, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Standar Produk
        $img->text("Standar Produk : $this->produkStandar", 1200, 1960, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Sistem Sertitikasi
        $img->text("Sistem Sertifikasi Produk : $this->produkSistemSertifikasi", 1200, 2030, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });


        // Berhaak
        $img->text("Pemegang sertifikat in diberikan HAK menggunakan", 1200, 2230, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // Berhaak 2
        $img->text("logo LSPro - BBKKP JPA dan tanda SNI pada produk sesuai ketentuan.", 1200, 2300, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });


        // TGL Terbit
        $img->text("Tanggal terbit : $this->tglTerbit", 1200, 2500, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // TGL Perubahan
        $img->text("Tanggal perubahan: $this->tglPerubahan", 1200, 2570, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });
        // TGL Expire
        $img->text("Berlaku hingga : $this->tglKadaluarsa", 1200, 2640, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(60);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        $savePath   = sprintf("JPA-%s.png", Str::uuid());
        $publicPath = public_path($savePath);
        $img->save($publicPath);
        return $publicPath;
    }
}
