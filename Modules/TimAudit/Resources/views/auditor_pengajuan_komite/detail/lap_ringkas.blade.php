@extends("layouts.layout_blank")

@section('title', 'Laporan Ringkas')
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
		<h3 class="center" style="margin:0px;">LAPORAN RINGKAS</h3>
	</div>
	<table>
		<tbody>
			<tr><td>Nama Perusahaan : </td><td>{{$dataJadwal->cust_nama}}</td><td>Kegiatan : </td><td>{!! $dataJadwal->jadw_audit_kegiatan !!}</td></tr>
			<tr><td>No. Ref : </td><td>{!! $dataJadwal->jadw_audit_nomor_referensi !!}</td><td>Tanggal : </td><td><?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_mulai))?> s/d <?=date('d M Y', strtotime($dataJadwal->jadw_tanggal_selesai))?></td></tr>
			<tr><td>Komoditas : </td><td>{!! $dataJadwal->komodt_nama !!}</td><td>Standart Acuan : </td><td>{!! $dataJadwal->jadw_audit_standart_acuan !!}</td></tr>
			<tr><td>Alamat : </td><td>{{$dataJadwal->cust_alamat}}</td><td>Ketua : </td><td>{!! $dataJadwal->peg_nama !!}</td></tr>
		</tbody>
	</table>
	
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >Kategori</th>
				<th class="left" >Jumlah</th>
				<th class="left" >No. LKS</th>
				<th class="left" >Klausul</th>
				<th class="left" >Target Penyelesaian</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$total_lks = 0;
			?>
			@foreach($dataLks as $lks)
			<?php
				$total_lks = $total_lks+$lks->lks_jumlah;
			?>
			<tr>
				<td>{{$lks->lks_kategori_ketidaksesuaian}}</td>
				<td>{{$lks->lks_jumlah}}</td>
				<td>{{$lks->lks_nomor}}</td>
				<td>{{$lks->lks_klausul_ketidaksesuaian}}</td>
				<td>{{$lks->lks_expired_date_perbaikan}}</td>
			</tr>
			@endforeach
			<tr>
				<td>Total</td>
				<td><?=$total_lks;?></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >Ringkasan Hasil</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_ringkas_kesimpulan !!}</td>
			</tr>
		</tbody>
	</table>
	<table style="border-top:0px;">
		<thead>
			<tr>
				<th class="left" >Rekomendasi</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{!! $dataJadwal->lap_ringkas_rekomendasi !!}</td>
			</tr>
		</tbody>
	</table>
@endsection