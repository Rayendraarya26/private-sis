<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Lengkap</title>
    <style>
        .text-center {
            text-align: center;
            justify-content: center;
        }


        section, span, table, tr, th, td, #rekap-lks {
            font-size: 12px;
        }

        #rekap-lks {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #rekap-lks td, #rekap-lks th {
            border: 1px solid black;
        }

        #rekap-lks tr:hover {
            background-color: #ddd;
        }

        #rekap-lks th {
            padding-top: 5px;
            padding-bottom: 5px;
            background-color: #FBD4B4;
            color: black;
        }

        #rekap-lks td {
            padding: 0 10px 0 10px
        }

        header {
            /*position: absolute;*/
            right: 0px;
            /*background-color: lightblue;*/
            height: 50px;
        }

		table{
            font-family: Arial, Helvetica, sans-serif;
			width:100%;
			border-collapse:collapse;
			border:0.5px solid #000;
		}
		thead{
			vertical-align:middle !important;
			// background:#ECF0F5;
		}
		th, td{
			padding:5px !important;
		}
		.center{
			text-align:center;
		}
		.right{
			text-align:right; padding:10px;
		}
		.left{
			text-align:left; padding:10px;
		}
    </style>
</head>
<body style="margin-top: 50px">
<header>
    <div style="float: left">
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo"
             style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 16px;font-family: Arial, Helvetica, sans-serif;">HASIL TINJAUAN DOKUMEN<br>{!! $restAudit->aud_thp1_standart_acuan !!}</span>
    </div>
</header>

<section>
	<table>
		<thead>
			<tr>
				<th class="left" colspan="2">I. Pengesahan</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Dibuat oleh : Lead Auditor</td><td>Disetujui oleh : Plt. Kepala Seksi Sertifikasi</td></tr>
			<tr>
				<td>@if(!empty($peg_ttd_base64))
						<img src="{{ $peg_ttd_base64 }}" alt="ttd ketua"
							 style="max-height: 100px;">
                    @elseif(!empty($peg_ttd_file))
						<img src="{{public_path($peg_ttd_file)}}" alt="ttd ketua"
							 style="max-height: 100px;">
					@else
						<br/>
						<br/>
						<br/>
					@endif</td>
				<td></td>
			</tr>
			<tr><td>Nama : {{$ketua_tim}}</td><td>Nama : Rambat</td></tr>
			<tr><td>Tanggal : <?=date('d M Y', strtotime($restAudit->aud_thp1_tanggal_rapat_akhir))?></td><td>Tanggal : <?=date('d M Y', strtotime($restAudit->aud_thp1_tanggal_rapat_akhir))?></td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left data" colspan="3">II. Umum</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Tahap Kegiatan</td><td>:</td><td>Audit Tahap 1 @if($restAudit->mohon_det_jenis_status == 'baru') Sertifikasi Awal @else Sertifikasi Ulang @endif @if($restAudit->sert_is_product == 'ya'){{$restAudit->sni}} @else {{$restAudit->sert_sni}} @endif</td></tr>
			<tr><td>Tanggal Pelaksanaan Audit</td><td>:</td><td>{{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_mulai))}} s/d {{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_selesai))}}</td></tr>
			<tr><td>Nama Perusahaan</td><td>:</td><td>{{$restAudit->cust_nama}}</td></tr>
			<tr><td>Nama Referensi</td><td>:</td><td>{{$restAudit->mohon_det_no_referensi}}</td></tr>
			<tr><td>Jumlah Karyawan</td><td>:</td><td>{{$restAudit->mohon_cust_jumlah_operasional}}</td></tr>
			<tr><td>Komoditas</td><td>:</td><td>{{$restAudit->komodt_nama}}</td></tr>
			<tr><td>Kapasitas Produksi</td><td>:</td><td>{{$restAudit->produksi}} / Tahun</td></tr>
			<tr><td>Alamat Perusahaan</td><td>:</td><td>{{$restAudit->cust_alamat}}</td></tr>
			<tr><td>Tujuan Audit</td><td>:</td><td>{{$restAudit->aud_thp1_tujuan}}</td></tr>
			<tr><td>Jenis Audit</td><td>:</td><td>{{ucfirst($restAudit->aud_thp1_jenis)}}</td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left data" style="border-top:0px;">III. Susunan Tim Audit</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					@foreach($dataTim as $tim)
						{{ucfirst($tim->thp1_tim_posisi)}} : {{$tim->peg_nama}}<br/>
					@endforeach
				</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left data" style="border-top:0px;">IV. Jumlah Temuan LKS</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Jumlah Temuan Audit Tahap I : {{$jmlTemuan}}</td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;"><tbody>
		<tbody>
			<tr><td>Uraian Temuan Dapat Dilihat Pada LKS(Hasil)</td></tr>
		</tbody>
	</table>
		</tbody>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">V. Audit kecukupan informasi terdokumentasi</th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_v !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">VI. Kondisi Lapangan </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_vi !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">VII. Status dan pemahaman persyaratan standar </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_vii !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">VIII. Informasi yang diperlukan yang berkenaan dengan (lingkup sistem manajemen K3, proses dan lokasi perusahaan, identifikasi bahaya dan risiko dan perundang-undangan/peraturan K3, dari operasi perusahaan dan risiko) tersedia. </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_viii !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">IX. Sumber daya yang tersedia </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_ix !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">X. Konfirmasi program audit sertifikasi tahap 2 </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_x !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">XI. Informasi pelaksanaan audit internal dan kaji ulang manajemen </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_xi !!}</td></tr></tbody>
	</table>
	<table style="border-top:0px;">
		<thead><tr><th class="left data" style="border-top:0px;">XII. Kesimpulan </th></tr></thead>
		<tbody><tr><td>{!! $restAudit->aud_thp1_kolom_xii !!}</td></tr></tbody>
	</table>
</section>


<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 810;
            $text = "F-TI-3";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // FTM
        $pdf->page_script('
            $x = 160;
            $y = 810;
            $text = "Rev.0";
            $font = $fontMetrics->get_font("helvetica");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');

        // Halaman (ID)
        $pdf->page_script('
            $x = 750;
            $y = 570;
            $text = "{$PAGE_NUM} dari {$PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "italic");
            $size = 10;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');
    }

</script>
</body>
</html>
