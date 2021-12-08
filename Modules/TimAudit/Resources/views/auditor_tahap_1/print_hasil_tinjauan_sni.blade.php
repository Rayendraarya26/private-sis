@extends("layouts.layout_blank")

@section('title', 'Hasil Tinjauan Audit Tahap 1')
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
		th.data  {
			border :1px solid #000000;
			border-collapse: collapse; 
		}
		td.data  {
			border :1px solid #000000;
			border-collapse: collapse; 
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
		<h3 class="center" style="margin:0px;">HASIL TINJAUAN DOKUMEN @if($restAudit->sert_is_product == 'ya'){{$restAudit->sni}} @else {{$restAudit->sert_sni}} @endif</h3>
	</div>
	<table>
		<thead>
			<tr>
				<th class="center data" rowspan="2">Klausul</th>
				<th class="center data" rowspan="2">Persyaratan</th>
				<th class="center data" colspan="2">Dokumen {{$restAudit->cust_nama}}</th>
				<th class="center data" rowspan="2">Hasil Tinjauan<br>(OK/NO)</th>
				<th class="center data" rowspan="2">Keterangan</th>
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
				<td class="data">@if($kla->aud_thp1_det_is_tinjauan == 'ya') {{$kla->aud_thp1_det_hasil_tinjauan}} @endif</td>
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
				<td>{!! $restAudit->aud_thp1_kolom_xii !!}</td>
			</tr>
		</tbody>
	</table>
@endsection