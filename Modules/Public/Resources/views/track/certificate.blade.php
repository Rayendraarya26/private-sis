@extends('layouts.layout_public')

@section('title', "Verifikasi Sertifikat")

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
	.badge-success {
		font-size: 1.2rem;
		background-color: green;
		color: white;
		padding: 4px;
		border-radius: 6px;
	}
	.badge-danger {
		font-size: 1.2rem;
		background-color: red;
		color: white;
		padding: 4px;
		border-radius: 6px;
	}
	.badge-warning {
		font-size: 1.2rem;
		background-color: orange;
		color: white;
		padding: 4px;
		border-radius: 6px;
	}
</style>
@endpush

@section('content')

<div class="parallax filter-gradient orange" data-color="orange" style="height: auto; min-height: 200vh;">
    <div class= "container">
        <div class="row">
            <div class="col-12">
            	<br><br><br><br><br><br><br>
				<div class="card">
					<h5><i class="fa fa-award"></i>&nbsp;Verifikasi Sertifikat</h5>
					<hr>
					<form role="form" class="form-horizontal" method="post" action="{{ url('track/certificate') }}">
						@csrf
						<div class="form-group">
						    <label for="cert_number" class="col-sm-offset-2 col-sm-2 control-label">Nomor Sertifikat</label>
						    <div class="col-sm-6">
						      	<input type="text" required class="form-control" id="cert_number" name="cert_number" placeholder="Masukkan nomor sertifikat" value="{{ @$cert_number }}">
						    </div>
						</div>
						<div class="form-group">
						    <div class="col-sm-offset-4 col-sm-10">
						      	<button type="submit" class="btn btn-primary">
						      		<i class="fa fa-search"></i>&nbsp;
						      		Verifikasi
						      	</button>
						    </div>
						</div>
					</form>
					@if(isset($data) || (isset($error) && $error))
						<h6><i class="fa fa-search"></i>&nbsp;Hasil pencarian</h6>
						<hr>
						@if(empty($data) || $data === null || (isset($error) && $error))
							<div style="padding: 20px 0px; text-align: center;">
								<h6>Tidak ditemukan</h6>
								<small>{{ @$error }}</small>
							</div>
						@else
							<div class="table-responsive">
								<table class="table table-striped table-bordered" cellpadding="3" cellspacing="5">
									<tbody>
										<tr>
											<td style="text-align: right;">Perusahaan</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->sis_pelanggan->cust_nama }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Alamat Perusahaan</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->sis_pelanggan->cust_alamat }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Nomor Sertifikat</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_nomor_sertifikat }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Status</td>
											<td style="text-align: center;">:</td>
											<td>
												<div style="display: inline-flex;">
													@if($data->cust_sert_status == 'on_going')
													<div class="badge-success">
														Masih berlaku
													</div>
													@endif
													@if($data->cust_sert_status == 'dibekukan')
													<div class="badge-danger">
														Dibekukan
													</div>
													@endif
													@if($data->cust_sert_status == 'expired')
													<div class="badge-warning">
														Kadaluwarsa
													</div>
													@endif
												</div>
											</td>
										</tr>
										<tr>
											<td style="text-align: right;">Nomor Referensi</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_nomor_referensi }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Nomor SNI</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_nomor_sni }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Tipe Sertifikat</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_tipe }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Lingkup</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_lingkup }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Merk</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_merk }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Ukuran</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_ukuran }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Produksi Tahunan</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_produksi_tahunan }} {{ $data->cust_sert_produksi_tahunan_satuan }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Tanggal Awal Sertifikat</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_tgl_sertifikat_awal ? date('d F Y', strtotime($data->cust_sert_tgl_sertifikat_awal)) : '-' }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Tanggal Perubahan Sertifikat</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_tgl_sertifikat_perubahan ? date('d F Y', strtotime($data->cust_sert_tgl_sertifikat_perubahan)) : '-' }}</td>
										</tr>
										<tr>
											<td style="text-align: right;">Tanggal Kadaluwarsa</td>
											<td style="text-align: center;">:</td>
											<td>{{ $data->cust_sert_expired_date ? date('d F Y', strtotime($data->cust_sert_expired_date)) : '-' }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						@endif
					@endif
				</div>
            </div>
        </div>
    </div>
</div>

@endsection