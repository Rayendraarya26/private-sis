<?php
use Illuminate\Support\Facades\Crypt;
?>
@extends('layouts.layout_public')

@section('title', "Tracking Sertifikasi")

@push('css')
<style>
	.card {
		background-color: white !important;
		/*border: 1px solid #9e9e9e;*/
		border-radius: 4px;
		width: 100%;
		min-height: 20px;
		padding: 12px;
		box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
	}
</style>
@endpush

@section('content')

<div class="parallax filter-gradient orange" data-color="orange" style="height: auto; min-height: 400vh;">
    <div class= "container">
        <div class="row">
            <div class="col-12">
            	<br><br><br><br><br><br><br>
				<div class="card">
					<h5><i class="fa fa-file-text"></i>&nbsp;Tracking Sertifikasi</h5>
					<hr>
					<form class="form-horizontal justify-content-center">
						<div class="form-group">
						    <label class="col-sm-offset-2 col-sm-2 control-label" style="padding-top: 0;">Perusahaan</label>
						    <div class="col-sm-6">
						    	{{ $data->mohon_cust_nama }}
						    </div>
						</div>
						<div class="form-group">
						    <label class="col-sm-offset-2 col-sm-2 control-label" style="padding-top: 0;">Alamat Perusahaan</label>
						    <div class="col-sm-6">
						    	{{ $data->mohon_cust_alamat }}
						    </div>
						</div>
					</form>
					<br>
					<br>
					<h6><i class="fa fa-clock"></i>&nbsp;Daftar Sertifikat ({{ count($data->sis_permohonan_details) }})</h6>
					<hr>
					<div class="table-responsive">
						<table class="table table-striped table-bordered" cellpadding="3" cellspacing="5">
							<tbody>
								@foreach($data->sis_permohonan_details as $row)
									<tr>
										<td>
											<h6>{{ $row->master_sertifikasi->sert_nama }}</h6>
											<div style="display: flex; justify-content: space-between;">
												<div style="font-size: 12px;">
													<b>Tipe:</b>&nbsp;
													@if($row->mohon_det_jenis_status == 'baru')
													<span>Sertifikat baru</span>
													@endif
													@if($row->mohon_det_jenis_status == 'lama')
													<span>Perpanjangan sertifikat</span>
													@endif
													<br>
													<b>No.Sertifikat:</b>&nbsp;
													@if($row->cust_sert_id)
													<a href="{{ url('/track/certificate/'.Crypt::encryptString($row->cust_sert_id)) }}" target="_blank" rel="noopener noreferrer">
														<b>{{ $row->sis_pelanggan_sertifikasi->cust_sert_nomor_sertifikat }}</b>
													</a>
													@else
													<span>-</span>
													@endif

												</div>
												<div>
												</div>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<br>
					<br>
					<h6><i class="fa fa-clock"></i>&nbsp;Riwayat</h6>
					<hr>
					<div class="table-responsive">
						<table class="table table-striped table-bordered" cellpadding="3" cellspacing="5">
							<tbody>
								@foreach($data->sis_permohonan_statuses as $key => $row)
									<tr>
										<td>
											<h6>{{ $row->status_judul }}</h6>
											<div style="display: flex; justify-content: space-between;">
												<p style="font-size: 12px;">{{ $row->status_pesan }}</p>
												<small style="color: #9e9e9e;">{{ date('d F Y H:i', strtotime($row->created_at)) }}</small>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

@endsection