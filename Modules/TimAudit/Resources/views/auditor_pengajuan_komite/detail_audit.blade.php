@extends('layouts.layout_app')

@section('title', 'Pengajuan Komite')

@push('css')
    <style>
        .borderless tr td, .borderless th {
            border: none;
        }
    </style>
@endpush

@section('content')
    <div class="dt-content" id="laporanPage">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                Informasi Audit Jadwal No #{{ $data->jadw_id }}
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <table class="table borderless">
									<tr>
										<td>Nama Perusahaan</td>
										<td>: {{$data->sis_pelanggan->cust_nama}}
										</td>
									</tr>
									<tr>
										<td>Tanggal Pelaksanaan</td>
										<td>
											: {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
											s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}
											@if($data->jadw_file_jadwal != '')<br/><a href="{{ url($data->jadw_file_jadwal) }}" target="_blank">Download Jadwal</a>@endif
											</td>
									</tr>
									<tr>
										<td>Ruang Lingkup <i>(Nace Code)</i></td>
										<td>:
											@if($data->sis_jadwal_audits->count() > 1)
												<ol>
													@foreach($data->sis_jadwal_audits as $audit)
														<li>{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}</li>
													@endforeach
												</ol>
											@else
												@foreach($data->sis_jadwal_audits as $audit)
													{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}
												@endforeach
											@endif
										</td>
									</tr>

									<tr>
										<td>Komoditas</td>
										<td>:
											@foreach($data->sis_jadwal_audits as $audit)
												@if($audit->master_komoditi->komodt_nama != "")
													{{$audit->master_komoditi->komodt_nama . (!$loop->last ? ' ; ' : '.')}}
												@endif
											@endforeach
										</td>
									</tr>

									<tr>
										<td>Jenis Audit</td>
										<td>:
											@foreach($data->sis_jadwal_audits()->groupBy('jadw_audit_jenis')->get() as $audit)
												Audit {{ucwords($audit->jadw_audit_jenis) . (!$loop->last ? ' ; ' : '.')}}
											@endforeach
										</td>
									</tr>
								</table>
                            </div>


                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    Susunan TIM Audit
                                </label>
                                <div class="col-sm-9">
                                    <ol>
                                        @foreach($data->sis_jadwal_tims as $tim)
                                            <li>
                                                {{ucwords($tim->jadw_tim_posisi)}}:
                                                {{$tim->master_pegawai->peg_nama}}
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    Jumlah Temuan LKS
                                </label>
                                <div class="col-sm-9">
                                    <ul>
                                        <li>Kritis: {{$dataLKS['jumlah']['kritis']}}</li>
                                        <li>Mayor: {{$dataLKS['jumlah']['mayor']}}</li>
                                        <li>Minor: {{$dataLKS['jumlah']['minor']}}</li>
                                        <br>
                                        <li>Total: {{$dataLKS['jumlah']['total']}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-xl-12">	
				<div class="dt-card">
					<div class="dt-card__header">
						<div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">Detail Audit</h3>
						</div>
					</div>
					<div class="dt-card__body">
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						LKS :
					</label>
					<div class="col-sm-9">
						@if($data->jadw_file_lks != '')<a href="{{ url($data->jadw_file_lks) }}" target="_blank"><i class="fad fa-download"></i> Download</a><br/><small>Sudah ditanda tangani basah</small>@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan Ringkas :
					</label>
					<div class="col-sm-9">
						@if($data->jadw_file_laporan_ringkas != '')<a href="{{ url($data->jadw_file_laporan_ringkas) }}" target="_blank"><i class="fad fa-download"></i> Download</a><br/><small>Sudah ditanda tangani basah</small>@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan Lengkap :
					</label>
					<div class="col-sm-9">
						<a href="{{ url("$url/detail/$data->jadw_id/lap-lengkap") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
						<br/><small>Sudah diverifikasi</small>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Tanggal Rapat :
					</label>
					<div class="col-sm-9">
					{!! $data->jadw_tanggal_rapat_akhir?->isoFormat("LL") !!}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Hadir :
					</label>
					<div class="col-sm-9">
						@if($data->jadw_file_kehadiran != '')<a href="{{ url($data->jadw_file_kehadiran) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Notulen Rapat :
					</label>
					<div class="col-sm-9">
					{!! $data->jadw_notulen_rapat !!}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Periksa File Upload Tim :
					</label>
					<div class="col-sm-9">
						<table class="table table-bordered mb-0">
							<tr>
								<th>Nama</th>
								<th>Posisi</th>
								<th>File Daftar Periksa</th>
							</tr>
							@foreach($dataAuditTim as $tim)
							<tr>
								<td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
								<td>{{ucwords($tim->jadw_tim_posisi)}}</td>
								<td>@if($tim->dftr_periksa_file != '')<a href="{{ url($tim->dftr_periksa_file) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Logbook Tim :
					</label>
					<div class="col-sm-9">
						<table class="table table-bordered mb-0">
							<tr>
								<th>Nama</th>
								<th>Posisi</th>
								<th>File Logbook</th>
							</tr>
							@foreach($dataTimLogbook as $tim)
							<tr>
								<td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
								<td>{{ucwords($tim->jadw_tim_posisi)}}</td>
								<td>@if($tim->logbook_filepath != '')<a href="{{ url($tim->logbook_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
				@if(!empty($data->sis_audit_ppcs))
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan PPC :
					</label>
					<div class="col-sm-9">
						<table class="table table-bordered mb-0">
							<tr>
								<th>Jenis File Laporan</th>
								<th>Download File</th>
							</tr>
							@foreach($data->sis_audit_ppcs as $ppc)
							<tr>
								<td>
								@if($ppc->audit_ppc_jenis_file == '19')
									19. RENCANA PENGAMBILAN CONTOH
								@elseif($ppc->audit_ppc_jenis_file == '20')
									20. BERITA ACARA PENGAMBILAN CONTOH
								@elseif($ppc->audit_ppc_jenis_file == '21')
									21. LABEL CONTOH UJI
								@elseif($ppc->audit_ppc_jenis_file == '22')
									22. LAPORAN KEGIATAN PENGAMBILAN CONTOH
								@endif
								</td>
								<td>@if($ppc->audit_ppc_filepath != '')<a href="{{ url($ppc->audit_ppc_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
				@endif
				
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Uji Sertifikat :
					</label>
					<div class="col-sm-9">
						<table class="table table-bordered mb-0">
							<tr>
								<th>Sertifikasi</th>
								<th>Nomor</th>
								<th>File</th>
							</tr>
							@foreach($dataSertifikat as $ser)
							<tr>
								<td>{{ucwords($ser->sert_nama)}}</td>
								<td>{{ucwords($ser->jadw_audit_sertifikat_nomor)}}</td>
								<td>@if($tim->jadw_audit_sertifikat_filepath != '')<a href="{{ url($tim->jadw_audit_sertifikat_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
			</div>
				</div>
			</div>
        </div>
    </div>
@endsection

