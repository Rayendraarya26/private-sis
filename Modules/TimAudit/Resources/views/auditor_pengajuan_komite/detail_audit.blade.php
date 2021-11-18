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
											s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}</td>
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
                                <div class="col-sm-8">
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
                                <div class="col-sm-8">
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
					
					</div>
				</div>
			</div>
        </div>
    </div>
@endsection

