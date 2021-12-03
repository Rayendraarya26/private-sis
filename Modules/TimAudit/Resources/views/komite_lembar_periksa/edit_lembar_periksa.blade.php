@extends("layouts.layout_app")

@section('title', 'Lembar Periksa Komite Sertifikasi')

@push("css")
    <!-- HTML -->
	<style>
		#label-form{
			font-weight:normal;
			/* color:#5BB6EA; */
		}
	</style>
@endpush

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="dt-card">
					  <div class="dt-card__header">
						<div class="dt-card__heading"><h3 class="dt-card__title">Jadwal No. #{{$dataJadwal->jadw_id}}</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div class="card-body">
							<div class="table-responsive col-xl-12 col-md-12 col-12">
								<table class="table mb-0">
									<tbody>
										<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
										<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
										<tr><td>No Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
										<tr><td>Acuan standar</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
										<tr><td>Jenis Produk</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
										<tr><td>Tipe produk</td><td>: {{$dataJadwal->jadw_audit_tipe}}</td></tr>
										<tr><td>Merek</td><td>: {{$dataJadwal->jadw_audit_merk}}</td></tr>
										<tr><td>Rekomendasi Komite</td><td>: <a href="{{ url("$url/edit") }}?tipe=lihat-rekomendasi&jadw_id={{$dataJadwal->jadw_id}}" target="blank">Lihat Data</a></td></tr>
									</tbody>
								</table>
							</div>
						  </div>
					  </div>
					</div>
				</div>
				
				<div id="vueLembarPeriksa">
				<div class="col-xl-12">
					<div class="dt-card">
						<div class="dt-card__header"><div class="dt-card__heading"><h3 class="dt-card__title">1. Penilaian</h3></div></div>
						<div class="dt-card__body">
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.1. Persyaratan Administrasi dan prosedur sertifikasi</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_1" id="komte_priksa_penilaian_1">@if(isset($dataJadwal->komte_priksa_penilaian_1)) {{$dataJadwal->komte_priksa_penilaian_1}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.2. Konfirmasi Hasil Pengkajian Permohonan</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_2" id="komte_priksa_penilaian_2">@if(isset($dataJadwal->komte_priksa_penilaian_2)) {{$dataJadwal->komte_priksa_penilaian_2}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.3. Evaluasi waktu audit yang direncanakan dengan realisasi pelaksanaan</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_3" id="komte_priksa_penilaian_3">@if(isset($dataJadwal->komte_priksa_penilaian_3)) {{$dataJadwal->komte_priksa_penilaian_3}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.4. Evaluasi kedalamam Laporan Audit yang dibuat oleh Auditor</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_4" id="komte_priksa_penilaian_4">@if(isset($dataJadwal->komte_priksa_penilaian_4)) {{$dataJadwal->komte_priksa_penilaian_4}} @endif</textarea>
								</div>
							</div>

							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.5. Komentar terhadap ketidaksesuaian, tindakan koreksi dan tindakan korektif</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_5" id="komte_priksa_penilaian_5">@if(isset($dataJadwal->komte_priksa_penilaian_5)) {{$dataJadwal->komte_priksa_penilaian_5}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.6. Hasil Inspeksi/ Asesmen Sistem Mutu/ Lingkungan*)</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_6" id="komte_priksa_penilaian_6">@if(isset($dataJadwal->komte_priksa_penilaian_6)) {{$dataJadwal->komte_priksa_penilaian_6}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.7. Konfirmasi terhadap ketercapaian tujuan audit</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_7" id="komte_priksa_penilaian_7">@if(isset($dataJadwal->komte_priksa_penilaian_7)) {{$dataJadwal->komte_priksa_penilaian_7}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.8. Rekaman Tahapan Sertifikasi</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_8" id="komte_priksa_penilaian_8">@if(isset($dataJadwal->komte_priksa_penilaian_8)) {{$dataJadwal->komte_priksa_penilaian_8}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.9. Hal-hal negative yang mempengaruhi penerbitan sertifikat</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_9" id="komte_priksa_penilaian_9">@if(isset($dataJadwal->komte_priksa_penilaian_9)) {{$dataJadwal->komte_priksa_penilaian_9}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.10. Hal-hal yang diperbaiki/ditambahkan</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_10" id="komte_priksa_penilaian_10">@if(isset($dataJadwal->komte_priksa_penilaian_10)) {{$dataJadwal->komte_priksa_penilaian_10}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.11. Hasil Perbaikan</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_11" id="komte_priksa_penilaian_11">@if(isset($dataJadwal->komte_priksa_penilaian_11)) {{$dataJadwal->komte_priksa_penilaian_11}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.12. Pelaksanaan Pengambilan contoh (khusus LS Produk)</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_12" id="komte_priksa_penilaian_12">@if(isset($dataJadwal->komte_priksa_penilaian_12)) {{$dataJadwal->komte_priksa_penilaian_12}} @endif</textarea>
								</div>
							</div>
							
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" id="label-form">1.13. Hasil Uji Laboratorium (khusus LS Produk)</label>
								<div class="col-xl-8">
									<textarea class="form-control" name="komte_priksa_penilaian_13" id="komte_priksa_penilaian_13">@if(isset($dataJadwal->komte_priksa_penilaian_13)) {{$dataJadwal->komte_priksa_penilaian_13}} @endif</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header bg-transparent">
							<h3 class="card-title">2. Keputusan/Rekomendasi</h3>
						</div>
						<div class="card-body pt-0">
							<div class="form-group row">
								<label class="col-form-label col-sm-3" for="jadw_file_kehadiran_komite">
									Kehadiran Komite*
									<br>
									<small>(pdf/excel)</small>
								</label>
								<div class="col-sm-8">
										<input type="file" class="form-control" aria-label="File Kehadiran Komite" name="jadw_file_kehadiran_komite" id="jadw_file_kehadiran_komite" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
										<input type="hidden" value="@if(isset($dataJadwal->jadw_file_kehadiran_komite)) {{$dataJadwal->jadw_file_kehadiran_komite}} @endif" id="jadw_file_kehadiran_komite_lama">
								</div>
							</div>
							<div class="table-responsive col-xl-12 col-md-12 col-12">
								<table class="table table-bordered mb-0">
									<thead>
										<tr>
										  <th scope="col">Status</th>
										  <th class="text-uppercase" scope="col">Jenis Audit</th>
										  <th class="text-uppercase" scope="col">Sertifikasi</th>
										  <th class="text-uppercase" scope="col">SNI</th>
										  <th class="text-uppercase" scope="col">Komoditas</th>
										  <th class="text-uppercase" scope="col">Merk</th>
										  <th class="text-uppercase" scope="col">Tipe</th>
										</tr>
									</thead>
									<tbody>
									@foreach($dataAudit as $dau)
										<tr>
										  <td scope="col">
										  @if($dau->jadw_audit_jenis == 'pengaktifan')
											  <div class="form-check mb-2">
												<input class="form-check-input" type="radio" name="status[{{$dau->jadw_audit_id}}]" id="rd2{{$dau->jadw_audit_id}}" value="ya" checked>
												<label class="form-check-label" for="rd2{{$dau->jadw_audit_id}}">tetap dapat menggunakan</label>
											  </div>
										  @elseif($dau->jadw_audit_jenis == 'pencabutan')
											  <div class="form-check mb-2">
												<input class="form-check-input" type="radio" name="status[{{$dau->jadw_audit_id}}]" id="rd2{{$dau->jadw_audit_id}}" value="tidak" checked>
												<label class="form-check-label" for="rd2{{$dau->jadw_audit_id}}">tidak berhak menggunakan</label>
											  </div>
										  @else
											  <div class="form-check mb-2">
												<input class="form-check-input" type="radio" name="status[{{$dau->jadw_audit_id}}]" id="rd1{{$dau->jadw_audit_id}}" value="ya">
												<label class="form-check-label" for="rd1{{$dau->jadw_audit_id}}">
												@if($dau->jadw_audit_jenis == 'sertifikasi')
													berhak memperoleh
												@elseif($dau->jadw_audit_jenis == 're-sertifikasi')
													berhak memperoleh kembali
												@else
													tetap dapat menggunakan
												@endif
												</label>
											  </div>
											  <!-- /radio button -->

											  <!-- Radio Button -->
											  <div class="form-check mb-2">
												<input class="form-check-input" type="radio" name="status[{{$dau->jadw_audit_id}}]" id="rd2{{$dau->jadw_audit_id}}" value="tidak">
												<label class="form-check-label" for="rd2{{$dau->jadw_audit_id}}">tidak berhak menggunakan</label>
											  </div>
											@endif
										  </td>
										  <td>{{$dau->jadw_audit_jenis}}</td>
										  <td>{{$dau->sert_nama}}</td>
										  <td>{{$dau->jadw_audit_sni}}</td>
										  <td>{{$dau->komodt_nama}}</td>
										  <td>{{$dau->jadw_audit_merk}}</td>
										  <td>{{$dau->jadw_audit_tipe}}</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							
							<div style="padding-top: 20px">
								<template v-if="loading_submit">
									<div class="fa-3x" style="text-align: center">
										<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
									</div>
								</template>
								<template v-else>
									<button :disabled="!status_submit" :class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}" @click="submitRekomendasi">
										<i class="fas fa-save"></i> Simpan Lembar Periksa
									</button>
								</template>
							</div>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
@endsection


@push("javascript")
	<script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
    <script>
		const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
			
        $(document).ready(function () {
            window.vueLembarPeriksa = new Vue({
                el: "#vueLembarPeriksa",
                data: {
                    status_submit: true,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						$('textarea#komte_priksa_penilaian_1').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_2').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_3').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_4').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_5').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_6').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_7').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_8').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
							
						$('textarea#komte_priksa_penilaian_9').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_10').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_11').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_12').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
						
						$('textarea#komte_priksa_penilaian_13').tinymce({
							height: 200,
							plugins: 'autosave link image code lists',
							relative_urls: false,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
							],
						});
					})
				},
                methods: {
                    submitRekomendasi() {
						tinyMCE.triggerSave();
						if(tinyMCE.get('komte_priksa_penilaian_1').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.1"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_2').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.2"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_3').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.3"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_4').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.4"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_5').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.5"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_6').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.6"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_7').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.7"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_8').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.8"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_9').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.9"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_10').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.10"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_11').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.11"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_12').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.12"});
						}
						else if(tinyMCE.get('komte_priksa_penilaian_13').getContent() === ''){
							toastCenter({type: 'warning',title: "Silahkan Isi Penilaian 1.12"});
						}
						@foreach($dataAudit as $dau)
						else if (!$("input[name='status[{{$dau->jadw_audit_id}}]']:checked").val()) {
						   toastCenter({type: 'warning',title: "Silahkan isikan keputusan untuk '{{$dau->sert_nama}}'"});
						}
						@endforeach
						else if ($.trim($("#jadw_file_kehadiran_komite").val()) === "") {
							toastCenter({
										type: 'warning',
										title: "Silahkan Unggah File Kehadiran Komite"
									})
						}
						else{
							swalWithBootstrapButtons({
								title: `Submit Lembar Periksa ?`,
								text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
								type: 'info',
								showCancelButton: true,
								confirmButtonText: 'Simpan',
								cancelButtonText: 'Batal',
								reverseButtons: true
							}).then(async (result) => {
								if (result.value) {
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("tipe", `lembar-periksa`)
									formData.append("komte_priksa_penilaian_1", tinyMCE.get('komte_priksa_penilaian_1').getContent())
									formData.append("komte_priksa_penilaian_2", tinyMCE.get('komte_priksa_penilaian_2').getContent())
									formData.append("komte_priksa_penilaian_3", tinyMCE.get('komte_priksa_penilaian_3').getContent())
									formData.append("komte_priksa_penilaian_4", tinyMCE.get('komte_priksa_penilaian_4').getContent())
									formData.append("komte_priksa_penilaian_5", tinyMCE.get('komte_priksa_penilaian_5').getContent())
									formData.append("komte_priksa_penilaian_6", tinyMCE.get('komte_priksa_penilaian_6').getContent())
									formData.append("komte_priksa_penilaian_7", tinyMCE.get('komte_priksa_penilaian_7').getContent())
									formData.append("komte_priksa_penilaian_8", tinyMCE.get('komte_priksa_penilaian_8').getContent())
									formData.append("komte_priksa_penilaian_9", tinyMCE.get('komte_priksa_penilaian_9').getContent())
									formData.append("komte_priksa_penilaian_10", tinyMCE.get('komte_priksa_penilaian_10').getContent())
									formData.append("komte_priksa_penilaian_11", tinyMCE.get('komte_priksa_penilaian_11').getContent())
									formData.append("komte_priksa_penilaian_12", tinyMCE.get('komte_priksa_penilaian_12').getContent())
									formData.append("komte_priksa_penilaian_13", tinyMCE.get('komte_priksa_penilaian_13').getContent())
									const file = document.querySelector("#jadw_file_kehadiran_komite").files[0];
									formData.append("jadw_file_kehadiran_komite", file)
									formData.append("jadw_file_kehadiran_komite_lama", $("#jadw_file_kehadiran_komite_lama").val())
									
									@foreach($dataAudit as $dau)
									formData.append('status[{{$dau->jadw_audit_id}}]', $("input[name='status[{{$dau->jadw_audit_id}}]']:checked").val());
									formData.append('tanggal[{{$dau->jadw_audit_id}}]', $("input[name='tanggal[{{$dau->jadw_audit_id}}]']").val());
									
									@endforeach
									this.loading_submit = true;
									let self = this;
									$.ajax({
										url: `{{action("$module@update")}}`,
										type: 'post',
										processData: false,
										contentType: false,
										data: formData,
										success: async function (res) {
											toastCenter({
												type: 'success',
												title: res.message
											})
											setTimeout(() => location.href = "{{url("$url")}}", 1000)
										},
										error: function (xhr) {
											self.loading_submit = false;
											if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
											else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
										}
									});
								}
							});
						}
                    },
                }
            })
        });
    </script>
@endpush
