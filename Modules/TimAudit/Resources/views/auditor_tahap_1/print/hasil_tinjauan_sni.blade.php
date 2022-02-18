<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hasil Tinjauan</title>
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
	<table style="border:0px;">
		<tbody>
			<tr><td>NAMA PERUSAHAAN</td><td>: {!! $restAudit->cust_nama !!}</td></tr>
			<tr><td>NOMOR REFERENSI</td><td>: {!! $restAudit->no_referensi !!}</td></tr>
			<tr><td>RUANG LINGKUP</td><td>: {!! $restAudit->ruang_lingkup !!}</td></tr>
			<tr><td>TANGGAL</td><td>: {{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_rapat_akhir))}}</td></tr>
			<tr><td>TIM</td><td>: 
			@foreach($dataTim as $dlb)
			- {{$dlb->peg_nama}} ( {{$dlb->thp1_tim_posisi}} )<br/>
			@endforeach
			</td></tr>
		</tbody>
	</table>
	<br/>
	<table id="rekap-lks">
		<thead>
			<tr>
				<th class="center data" rowspan="2">NO KLAUSUL</th>
				<th class="center data" rowspan="2">PERSYARATAN</th>
				<th class="center data" colspan="2">DOKUMEN TERKAIT<br/>(dapat berupa manual, prosedur, instruksi kerja atau rekaman)</th>
				<th class="center data" rowspan="2">PEMENUHAN<br>(OK/NO)</th>
				<th class="center data" rowspan="2">KETERANGAN</th>
			</tr>
			<tr>
				<th class="center data">Kode Dokumen</th>
				<th class="center data">Judul Dokumen</th>
			</tr>
		</thead>
		<tbody>
			@foreach($dataAuditKlausul as $kla)
			<tr>
				<td class="data">{{$kla->aud_thp1_det_thp1_nomor}}</td>
				<td class="data">{{$kla->aud_thp1_det_peryataan}}</td>
				<td class="data">@if($kla->aud_thp1_det_is_tinjauan == 'ya') {{$kla->aud_thp1_det_kode_dok}} @endif</td>
				<td class="data">@if($kla->aud_thp1_det_is_tinjauan == 'ya') {{$kla->aud_thp1_det_judul_dok}} @endif</td>
				<td class="center data">@if($kla->aud_thp1_det_is_tinjauan == 'ya') {{strtoupper($kla->aud_thp1_det_hasil_tinjauan)}} @endif</td>
				<td class="data">{{$kla->aud_thp1_det_keterangan}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<br/>
	<table style="border:0px;">
		<tbody>
			<tr>
				<td>Kesimpulan : </td>
				<td>Dokumen yang disusun oleh {{strtoupper($restAudit->mohon_cust_nama)}} <strong>{{$restAudit->aud_thp1_status}}</strong> kecukupan minimal terhadap Standar {!! $restAudit->aud_thp1_standart_acuan !!}.</td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
	<table style="border:0px;">
		<tbody>
			<tr>
				<td class="center">Mengetahui</td>
				<td class="center">Yogyakarta, {{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_rapat_akhir))}}</td>
			</tr>
			<tr>
				<td class="center">Kepala Seksi Sertifikasi</td>
				<td class="center">Lead Auditor</td>
			</tr>
			<tr>
				<td class="center"><br/><br/><br/><br/></td>
				<td class="center"><br/><br/><br/><br/></td>
			</tr>
			<tr>
				<td class="center">Rambat</td>
				<td class="center">{{$ketua_tim}}</td>
			</tr>
		</tbody>
	</table>
</section>


<script type="text/php">
    if (isset($pdf)) {
        // FTM
        $pdf->page_script('
            $x = 60;
            $y = 810;
            $text = "F.K3-08/Rev.0";
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
            $text = "";
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
