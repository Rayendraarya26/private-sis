<?php

namespace App\Http\Structs;

use Intervention\Image\Facades\Image;

class CertYqStruct
{
    public string $noReg;              // No. Reg : 00/70
    public string $tglSertifikasiAwal; // Sertifikasi awal:
    public string $lembaga;            // YQ 005 032
    public string $perusahaanNama;     // PT. Hok Tong
    public string $perusahaanAlamat;   // Jalan Raden fatah nomer 20....
    public string $sertifikasiTipe;    // SNI ISO 9001:2015
    public string $ruangLingkup;       // Proses Produksi crumb rubber (SIR 10, SIR 20)
    public string $kodeEA;
    public string $kodeNACE;
    public string $tglTerbit;
    public string $tglPerubahan;
    public string $tglKadaluarsa;

    public function __construct(
        $noReg = '',
        $tglSertifikasiAwal = '',
        $lembaga = '',
        $perusahaanNama = '',
        $perusahaanAlamat = '',
        $sertifikasiTipe = '',
        $ruangLingkup = '',
        $kodeEA = '',
        $kodeNACE = '',
        $tglTerbit = '',
        $tglPerubahan = '',
        $tglKadaluarsa = '',
    )
    {
        $this->noReg              = $noReg;
        $this->tglSertifikasiAwal = $tglSertifikasiAwal;
        $this->lembaga            = $lembaga;
        $this->perusahaanNama     = $perusahaanNama;
        $this->perusahaanAlamat   = $perusahaanAlamat;
        $this->sertifikasiTipe    = $sertifikasiTipe;
        $this->ruangLingkup       = $ruangLingkup;
        $this->kodeEA             = $kodeEA;
        $this->kodeNACE           = $kodeNACE;
        $this->tglTerbit          = $tglTerbit;
        $this->tglPerubahan       = $tglPerubahan;
        $this->tglKadaluarsa      = $tglKadaluarsa;
    }

    public function render()
    {
        $img = Image::make(public_path('images/sertifikasi-asset/cert_yq.png'));

        // Nomer Registrasi
        $img->text($this->noReg, 130, 885, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(50);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Tgl Sert Awal
        $img->text("Sertifikasi awal :  $this->tglSertifikasiAwal", 2350, 885, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(40);
            $font->color("#000000");
            $font->align("right");
            $font->valign("middle");
        });

        // Jenis Cert
        $img->text("$this->lembaga", 1250, 1120, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(120);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Declaration text
        $img->text("Kami menyatakan bahwa :", 1250, 1260, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(80);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });


        // PT Name
        $img->text("$this->perusahaanNama", 1250, 1400, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(160);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Alamat
        $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
        renderMultiline(
            $img,
            [
                'xAxis'    => 1250,
                'yAxis'    => 1500,
                'color'    => '#000000',
                'size'     => 65,
                'align'    => 'center',
                'valign'   => 'top',
                'fontType' => $fontType,
            ],
            $this->perusahaanAlamat,
            40);

        // Telah menerapkan
        $img->text("telah menerapkan Sistem Manajemen Mutu sesuai dengan", 1250, 1850, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(80);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // JENIS SERTIFIKAT
        $img->text($this->sertifikasiTipe, 1250, 2000, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(180);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // RUANG LINGKUP
        $img->text("Ruang lingkup : ", 143, 2230, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // RUANG LINGKUP VALUE
        $img->text($this->ruangLingkup, 730, 2230, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Kode EA
        $img->text("Kode EA : ", 143, 2300, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Kode EA VALUE
        $img->text($this->kodeEA, 470, 2300, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Kode NACE
        $img->text("Kode NACE : ", 143, 2380, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Kode NACE VALUE
        $img->text($this->kodeNACE, 550, 2380, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // TGL TERBIT
        $img->text("Tanggal terbit : ", 143, 2460, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // TGL TERBIT VALUE
        $img->text($this->tglTerbit, 680, 2460, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Change date
        $img->text("Tanggal perubahan : ", 143, 2540, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Change date VALUE
        $img->text($this->tglPerubahan, 850, 2540, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Valid Until
        $img->text("Berlaku hingga : ", 143, 2620, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Valid Until VALUE
        $img->text($this->tglKadaluarsa, 740, 2620, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(70);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });


        header('Content-Type: ' . $img->mime());
        header(sprintf('Content-Disposition: attachment; filename="%s.%s"', $img->filename, $img->extension));
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $img->filesize());
        header('Cache-Control: private, no-transform, no-store, must-revalidate');

        return $img->encode('png');
    }
}
