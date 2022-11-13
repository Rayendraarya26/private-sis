<?php

namespace App\Http\Structs;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertYok3Struct
{
    public string $noReg;              // No. Reg : 00/65
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
        $this->noReg            = $noReg;
        $this->lembaga          = $lembaga;
        $this->perusahaanNama   = $perusahaanNama;
        $this->perusahaanAlamat = $perusahaanAlamat;
        $this->sertifikasiTipe  = $sertifikasiTipe;
        $this->ruangLingkup     = $ruangLingkup;
        $this->kodeEA           = $kodeEA;
        $this->kodeNACE         = $kodeNACE;
        $this->tglTerbit        = $tglTerbit;
        $this->tglPerubahan     = $tglPerubahan;
        $this->tglKadaluarsa    = $tglKadaluarsa;
    }

    /**
     * @throws \ImagickException
     */
    public function generate()
    {
        $img = Image::make(public_path('images/sertifikasi-asset/cert_yok3.png'));

        // Nomer Registrasi
        $img->text("No. Reg : $this->noReg", 300, 830, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(50);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Jenis Cert
        $img->text("$this->lembaga", 300, 930, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(120);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Declaration text
        $img->text("Kami menyatakan bahwa :", 300, 1090, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });


        // PT Name
        $img->text("$this->perusahaanNama", 300, 1210, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            if (Str::length($this->perusahaanNama) > 50) {
                $font->size(120);
            } else {
                $font->size(150);
            }
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Alamat
        $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
        renderMultiline(
            $img,
            [
                'xAxis'    => 300,
                'yAxis'    => 1310,
                'color'    => '#000000',
                'size'     => 65,
                'align'    => 'left',
                'valign'   => 'top',
                'fontType' => $fontType,
            ],
            $this->perusahaanAlamat,
            50);

        // Telah menerapkan
        $img->text("telah menerapkan Sistem Manajemen Mutu sesuai dengan", 300, 1650, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // JENIS SERTIFIKAT
        $img->text($this->sertifikasiTipe, 300, 1800, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(190);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // ======================= BOTTOM =================================================
        $xBot = 300;
        $yBot = 2000;
        // RUANG LINGKUP
        $img->text("Ruang lingkup : ", 300, $yBot, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // RUANG LINGKUP VALUE
        $img->text($this->ruangLingkup, 850, $yBot, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Kode EA
        $img->text("Kode EA : ", 300, $yBot + 80, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Kode EA VALUE
        $img->text($this->kodeEA, 640, $yBot + 80, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Kode NACE
        $img->text("Kode NACE : ", 300, $yBot + 160, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Kode NACE VALUE
        $img->text($this->kodeNACE, 720, $yBot + 160, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // TGL TERBIT
        $img->text("Tanggal terbit : ", 300, $yBot + 240, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // TGL TERBIT VALUE
        $img->text($this->tglTerbit, 850, $yBot + 240, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Change date
        $img->text("Tanggal perubahan : ", 300, $yBot + 320, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Change date VALUE
        $img->text($this->tglPerubahan, 970, $yBot + 320, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Valid Until
        $img->text("Berlaku hingga : ", 300, $yBot + 400, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });
        // Valid Until VALUE
        $img->text($this->tglKadaluarsa, 870, $yBot + 400, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(65);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Generate QRCODE
        $qrCodeText = "$this->perusahaanNama
$this->perusahaanAlamat
Ruang lingkup: $this->ruangLingkup
Tanggal terbit: $this->tglTerbit
Tanggal perubahan: $this->tglPerubahan
Berlaku hingga: $this->tglKadaluarsa";

        $qrName = sprintf('%s.png', Str::uuid());
        $qrPath = public_path($qrName);
        QrCode::format('png')->size(300)->generate($qrCodeText, $qrPath);
        $img->insert($qrName, "bottom-left", 150, 150);
        @unlink($qrPath);

        // Generate sertifikat
        $certImageName = sprintf("YOK3-%s.png", Str::uuid());
        $certImagePath = public_path($certImageName);
        $img->save($certImagePath);

        // Save AS PDF
        $listImagePath = [$certImagePath];
        return certMergeImage(sprintf("YOK3-%s.pdf", $this->perusahaanNama), $listImagePath);
    }
}
