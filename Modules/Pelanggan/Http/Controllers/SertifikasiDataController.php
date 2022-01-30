<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Structs\BreadcrumbsStruct;
use App\Models\BbkkpSis\SisPelangganSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
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
        $img = Image::make(public_path('images/sertifikasi-asset/cert_yq.png'));

        // Nomer Registrasi
        $img->text("No. Reg : 00/70", 130, 885, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(50);
            $font->color("#000000");
            $font->align("left");
            $font->valign("middle");
        });

        // Tgl Sert Awal
        $img->text("Sertifikasi awal :  24 Februari 2022", 2350, 885, function ($font) {
            $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
            $font->file($fontType);
            $font->size(40);
            $font->color("#000000");
            $font->align("right");
            $font->valign("middle");
        });

        // Jenis Cert
        $img->text("YQ 005 032", 1250, 1120, function ($font) {
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
        $img->text("PT. HOK TONG", 1250, 1400, function ($font) {
            $fontType = public_path('/assets/fonts/garibdttf/G_ari_bd.TTF');
            $font->file($fontType);
            $font->size(160);
            $font->color("#000000");
            $font->align("center");
            $font->valign("middle");
        });

        // Alamat
        $fontType = public_path('/assets/fonts/gothica1/GothicA1-Regular.ttf');
        $this->render_multiline(
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
            "JI. Raden Patah, RT 07, Kel. Sijenjang, Kec. Jambi Timur, Jambi - 36149, Jambi - INDONESIA",
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
        $img->text("SNI ISO 9001:2015", 1250, 2000, function ($font) {
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
        $img->text("Proses produksi crumb rubber (SIR 10, SIR 20)", 730, 2230, function ($font) {
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
        $img->text("[14] Karet dan produk plastik", 470, 2300, function ($font) {
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
        $img->text("C.22.19 Pembuatan produk karet lainnya", 550, 2380, function ($font) {
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
        $img->text("21 Desember 2021", 680, 2460, function ($font) {
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
        $img->text("-", 700, 2540, function ($font) {
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
        $img->text("4 Oktober 2024", 740, 2620, function ($font) {
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

        return $img->encode('png', 70);
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
        $data = SisPelangganSertifikasi::with(['master_sertifikasi', 'master_komoditi']);
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
