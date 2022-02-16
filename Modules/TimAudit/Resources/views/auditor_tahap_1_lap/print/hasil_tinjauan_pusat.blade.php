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
		<h3 class="center" style="margin:0px;">LAPORAN AUDIT KECUKUPAN DOKUMEN</h3>
		<h4 class="center" style="margin:0px;">Nomor : ..................................... </h4>
	</div>
	<table style="border:0px;">
		<tbody>
			<tr><td>Nama Perusahaan</td><td>:</td><td>{{$restAudit->cust_nama}}</td></tr>
			<tr><td>Nomor Pendaftaran</td><td>:</td><td>{{  $restAudit->mohon_id}}</td></tr>
			<tr><td>Tanggal Pelaksanaan Audit</td><td>:</td><td>{{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_mulai))}} s/d {{date('d M Y', strtotime($restAudit->aud_thp1_tanggal_selesai))}}</td></tr>
			<tr><td>Tahap Audit</td><td>:</td><td>@if($restAudit->mohon_det_jenis_status == 'baru') Sertifikasi Awal @else Sertifikasi Ulang @endif</td></tr>
			<tr><td>Auditor</td><td>:</td><td>
				@foreach($dataTim as $tim)
					- {{$tim->peg_nama}} ({{$tim->thp1_tim_posisi}})<br/>
				@endforeach
			</td></tr>
			<tr><td>Alamat Perusahaan</td><td>:</td><td>{{$restAudit->cust_alamat}}</td></tr>
			<tr><td>Alamat Pabrik</td><td>:</td><td>{{$restAudit->cust_alamat}}</td></tr>
			<tr><td>No Telp/Fax</td><td>:</td><td>{{$restAudit->cust_nomor_telp}} / {{$restAudit->cust_nomor_fax}}</td></tr>
			<tr><td>Standar Acuan</td><td>:</td><td>{{$restAudit->aud_thp1_standart_acuan}}</td></tr>
			<tr><td>Sistem Manajemen yang di Miliki perusahaan</td><td>:</td><td>-</td></tr>
		</tbody>
	</table>
	<br/>
	<b>Persyaratan Teknis</b>
	<table>
		<thead>
			<tr>
				<th class="center data">No.</th>
				<th class="center data">Persyaratan</th>
				<th class="center data">Satuan</th>
				<th class="center data">Status Evaluasi</th>
			</tr>
		</thead>
		<tbody>
			@foreach($dataAuditKlausul as $kla)
			<tr>
				<td class="data">{{$kla->aud_thp1_det_thp1_nomor}}</td>
				<td class="data">{{$kla->aud_thp1_det_persyaratan}} @if($kla->aud_thp1_det_is_tinjauan == 'ya') = {{$kla->aud_thp1_det_nilai}} @endif</td>
				<td class="data">{{$kla->aud_thp1_det_satuan}}</td>
				<td class="data">@if($kla->aud_thp1_det_is_tinjauan == 'ya') {{$kla->aud_thp1_det_hasil_tinjauan}}<br/><p>{{$kla->aud_thp1_det_keterangan}}</p> @endif</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<br/>
	<table style="border-top:0px;">
		<tbody>
			<tr>
				<td width="80%" class="data"><b>Kesimpulan Audit Kecukupan Dokumen : </b><br/>{!! $restAudit->aud_thp1_kolom_xii !!}</td>
				<td width="20%" class="data"></td>
			</tr>
			<tr>
				<td width="80%" class="data"><b>Pernyataan Auditor : </b><br/><p>Laporan Audit kecukupan dokumen ini dibuat dengan sesuangguhnya dan dilaksanakan terhadap seluruh persyaratan teknis dan acuan yang berlaku.</p></td>
				<td width="20%" class="data"></td>
			</tr>
			@foreach($dataTim as $tim)
				<tr>
				<td width="80%" class="data">
					<b>{{ucfirst($tim->thp1_tim_posisi)}} : </b>
					<br/>
					<p>Nama : {{$tim->peg_nama}}</p>
					<p>Tanda Tangan : </p>
					<p>Tanggal : </p>
				</td>
				<td width="20%" class="data"></td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endsection