@extends("layouts.layout_blank")

@section('title', 'Rekomendasi untuk Persetujuan')
@push("css")
    <!-- HTML -->
    <style>
		body{
			font-family: helvetica;
			margin: 10px;
		}
		@page { margin: 40px; }
		body{
			font-size:14pt;
		}

		table{
			width:100%;
			border-collapse:collapse; 
			border:1px solid #000000;
		}
		thead{
			vertical-align:middle !important;
			background:#ECF0F5;
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
@endpush

@section('content')
	<div id="content">
		<h3 class="center" style="margin:0px;">REKOMENDASI UNTUK PERSETUJUAN</h3>
	</div>
	<table>
		<thead>
			<tr><th class="left" colspan="2">1. Diajukan untuk</th></tr>
		</thead>
		<tbody>
			<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
			<tr><td>Komoditas</td><td>: {{  $dataJadwal->komodt_nama}}</td></tr>
			<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
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
					<div class="table-responsive">
						  <table class="table table-bordered mb-0 p-0 no-margin">
							<thead>
							<tr>
							  <th scope="col">Status LKS :</th>
							  <th class="text-uppercase" scope="col">Kritis</th>
							  <th class="text-uppercase" scope="col">Mayor</th>
							  <th class="text-uppercase" scope="col">Minor</th>
							  <th class="text-uppercase" scope="col">Observasi</th>
							  <th class="text-uppercase" scope="col">Total</th>
							</tr>
							</thead>
							<tbody>
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
						</div>
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
					<hr/>
					Petugas Pengambil Contoh : {{$ppc->peg_nama}}
					<hr/>
					Sertifikat No :
					<?php
						$sertifikat_nomor = explode(", ", $ppc->jadw_audit_sertifikat_nomor);
						$sertifikat_filepath = explode("; ", $ppc->jadw_audit_sertifikat_filepath);
						if(!empty($sertifikat_nomor)){
							foreach($sertifikat_nomor as $key => $val){
								$path = (isset($sertifikat_filepath[$key])) ? url($sertifikat_filepath[$key]) : '#';
								echo '<a href="'.$path.'" target="_blank">'. $val .'</a>, ';
							}
						}
					?>
				@endforeach
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
@endsection