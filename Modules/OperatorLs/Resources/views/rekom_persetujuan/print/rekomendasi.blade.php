<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REKOMENDASI PERSETUJUAN KOMITE</title>
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
        <img src="{{public_path('/images/logos/sis_ls_bbkkp.png')}}" alt="Logo" style="max-width: 120px;">
    </div>
    <div class="text-center">
        <span style="font-weight: bold; font-size: 16px;font-family: Arial, Helvetica, sans-serif;">REKOMENDASI UNTUK PERSETUJUAN</span>
    </div>
</header>

<section>
	<table>
		<thead>
			<tr><th class="left" colspan="2">1. Diajukan untuk</th></tr>
		</thead>
		<tbody>
			<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
			<tr><td>Komoditas</td><td>: {{  $dataJadwal->komodt_nama}}</td></tr>
			<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_tipe}}</td></tr>
			<tr><td>SM/SNI yang diacu</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
			<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr><th class="left">2. Kronologis Kegiatan</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>
				- Audit dilaksanakan pada {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}
				<br/>
				- 
				</td>
			</tr>
			<tr>
				<td>
				Permohonan sertifikasi dari pemohon
				<ul>
					@foreach($dataMohon as $dp)
					<li>Surat pemohon No {{$dp->mohon_id}} tanggal {{ $dp->created_at?->format("d M Y") }}</li>
					@endforeach
				</ul>
				
				</td>
			</tr>
			
			@foreach($dataThp1 as $thp1)
			<tr>
				<td>
					Pelaksanaan Audit Tahap I
					<br/>
					Susunan Tim :<br/>{!! $thp1->tim_list !!}
					<br/>
					Jumlah Temuan : {{$thp1->total_temuan * $thp1->total_det/ $thp1->total_data}}
					<br/>
					Tanggal {{ date('d M Y', strtotime($thp1->aud_thp1_tanggal_mulai)) }}
				</td>
			</tr>
			@endforeach
			
			@foreach($dataAudit as $aud)
			<tr>
				<td>
					Pelaksanaan Audit {{$aud->jenis_jadwal}}
					<br/>
					Susunan Tim :<br/>{!! $aud->tim_list !!}
					</div>
					<div class="col-md-4">
						Tanggal {{ date('d M Y', strtotime($aud->jadw_tanggal_mulai)) }} s/d {{ date('d M Y', strtotime($aud->jadw_tanggal_selesai)) }}
					</div>
					<div class="col-md-12">
						  <table class="" style="border:0px;">
							<tbody>
							<tr>
							  <td>Status LKS :</td>
							  <td class="">Kritis</td>
							  <td class="">Mayor</td>
							  <td class="">Minor</td>
							  <td class="">Observasi</td>
							  <td class="">Total</td>
							</tr>
							<tr>
							  <td>LKS yang ditutup</td>
							  <td>{{$aud->total_kritis * $aud->lks_total/ $aud->total_data}}</td>
							  <td>{{$aud->total_mayor * $aud->lks_total/ $aud->total_data}}</td>
							  <td>{{$aud->total_minor * $aud->lks_total/ $aud->total_data}}</td>
							  <td>{{$aud->total_observasi * $aud->lks_total/ $aud->total_data}}</td>
							  <td>{{ ($aud->total_kritis * $aud->lks_total/ $aud->total_data) + ($aud->total_mayor * $aud->lks_total/ $aud->total_data) + ($aud->total_minor * $aud->lks_total/ $aud->total_data) + ($aud->total_observasi * $aud->lks_total/ $aud->total_data) }}</td>
							</tr>
							<tr>
							  <td>LKS yang tetap ada/baru</td>
							  <td>....</td>
							  <td>....</td>
							  <td>....</td>
							  <td>....</td>
							  <td>....</td>
							</tr>
							</tbody>
						  </table>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr><th class="left">3. LKS ditutup tanggal @if($dataJadwal->lks_expired_date_perbaikan != '') {{ date('d M Y', strtotime($dataJadwal->lks_expired_date_perbaikan)) }} @endif</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>
				@foreach($dataPPC as $ppc)
					Pengambilan Contoh*) untuk SPPT SNI
					<hr style="border:0.1px dotted  #000;" >
					Petugas Pengambil Contoh : {{$ppc->peg_nama}}
					<hr style="border:0.1px dotted  #000;" >
				@endforeach
					Sertifikat No :
					<?php
					foreach($dataSertifikat as $sert){
						$path = (isset($sert->prod_sert_filepath)) ? url($sert->prod_sert_filepath) : '#';
						echo '<a href="'.$path.'" target="_blank">'. $sert->prod_sert_nomor .'</a>, ';
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<table style="border-top:0px;">
		<thead>
			<tr><th class="left" >4. Isi rekomendasi</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>@if(isset($dataJadwal->rekmd_komte_isi)) {!! $dataJadwal->rekmd_komte_isi !!} @endif</td>
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
            $text = "F-KEP-2";
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
            $text = "Rev. 00";
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
