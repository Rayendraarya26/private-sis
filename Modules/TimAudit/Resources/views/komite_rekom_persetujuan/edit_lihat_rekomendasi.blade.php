@extends("layouts.layout_app")

@section('title', 'Rekomendasi untuk Persetujuan')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="dt-card">
					  <div class="dt-card__header">
						<div class="dt-card__heading"><h3 class="dt-card__title">Informasi Data Jadwal No. #{{$dataJadwal->jadw_id}}</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div class="table-responsive col-xl-12 col-md-12 col-12">
							<table class="table mb-0">
								<thead>
									<tr><th scope="col" colspan="2">1. Diajukan untuk</th></tr>
								</thead>
								<tbody>
									<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
									<tr><td>Komoditas</td><td>: {{  $dataJadwal->komodt_nama}}</td></tr>
									<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
									<tr><td>SM/SNI yang diacu</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
									<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								</tbody>
							</table>
							
							<table class="table mb-0">
								<thead>
									<tr><th scope="col">2. Kronologis Kegiatan</th></tr>
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
											<div class="col-xl-12">
												<div class="row">
													<div class="col-md-8">
													Pelaksanaan Audit Tahap I
													<br/>
													Susunan Tim :<br/>{!! $thp1->tim_list !!}
													<br/>
													Jumlah Temuan : {{$thp1->total_temuan * $thp1->total_det/ $thp1->total_data}}
													</div>
													<div class="col-md-4">
														Tanggal {{ date('d M Y', strtotime($thp1->aud_thp1_tanggal_mulai)) }}
													</div>
												</div>
											</div>
										</td>
									</tr>
									@endforeach
									
									@foreach($dataAudit as $aud)
									<tr>
										<td>
											<div class="col-xl-12">
												<div class="row">
													<div class="col-md-8">
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
															  <td>{{$thp1->total_kritis * $thp1->lks_total/ $thp1->total_data}}</td>
															  <td>{{$thp1->total_mayor * $thp1->lks_total/ $thp1->total_data}}</td>
															  <td>{{$thp1->total_minor * $thp1->lks_total/ $thp1->total_data}}</td>
															  <td>{{$thp1->total_observasi * $thp1->lks_total/ $thp1->total_data}}</td>
															  <td>{{ ($thp1->total_kritis * $thp1->lks_total/ $thp1->total_data) + ($thp1->total_mayor * $thp1->lks_total/ $thp1->total_data) + ($thp1->total_minor * $thp1->lks_total/ $thp1->total_data) + ($thp1->total_observasi * $thp1->lks_total/ $thp1->total_data) }}</td>
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
													</div>
												</div>
											</div>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							
							<table class="table mb-0">
								<thead>
									<tr><th colspan="2">3. LKS ditutup tanggal @if($aud->lks_expired_date_perbaikan != '') {{ date('d M Y', strtotime($aud->lks_expired_date_perbaikan)) }} @endif</th></tr>
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
							
							<table class="table mb-0">
								<thead>
									<tr><th colspan="2">4. Isi rekomendasi</th></tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="2">@if(isset($dataJadwal->rekmd_komte_isi)) {!! $dataJadwal->rekmd_komte_isi !!} @endif</td>
									</tr>
								</tbody>
							</table>
						</div>
					  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection