@extends("layouts.layout_app")

@section('title', 'Rekomendasi untuk Persetujuan')

@push("css")
    <!-- HTML -->
    <link rel="stylesheet" href="{{asset("assets/plugins/smartwizard/css/smart_wizard_all.min.css")}}">
@endpush

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
						<div class="accordion" id="accordion-example">
						  <div class="card">
								<div class="card-header" id="headingOne"><h5 class="mb-0"><button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-one" aria-expanded="true" aria-controls="collapse-one">Diajukan untuk</button></h5></div>
								<div id="collapse-one" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion-example">
								  <div class="card-body">
									<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table mb-0">
											<tbody>
												<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
												<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
												<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
												<tr><td>SM/SNI yang diacu</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
												<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
											</tbody>
										</table>
								  </div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" id="headingTwo">
								  <h5 class="mb-0"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-two" aria-expanded="false" aria-controls="collapse-two">Kronologis kegiatan *)</button></h5>
								</div>
								<div id="collapse-two" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-example">
								  <div class="card-body">
									<table class="table">
										<tbody>
											<tr><td>-</td><td>Audit dilaksanakan pada {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}</td></tr>
										</tbody>
									</table>
								  </div>
								</div>
							</div>
						  </div>
						</div>
					  </div>
					</div>
				</div>
				
				<div class="col-xl-12">
					<div class="dt-card">
					  <div class="dt-card__header">
						<div class="dt-card__heading"><h3 class="dt-card__title">Upload Hasil Uji</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div id="vueRekomendasi">
							<div class="form-group form-row" id="data_permohonan">
								<label class="col-xl-3 col-form-label text-sm-left" for="id" >Isi Rekomendasi</label>
								<div class="col-xl-8">
									<textarea class="form-control" v-on:keyup="validateSertifikat" name="rekmd_komte_isi" id="rekmd_komte_isi">@if(isset($dataJadwal->rekmd_komte_isi)) {{$dataJadwal->rekmd_komte_isi}} @endif</textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="label-form">Tutup Rekomendasi?</label>
								<div class="col-md-12">
								  <div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="rekmd_komte_status" id="aud_thp1_status1" value="ditutup" @click="setTutup('ditutup')">
									<label class="form-check-label" for="aud_thp1_status1">Tutup</label>
								  </div>
								  <div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="rekmd_komte_status" id="aud_thp1_status2" value="on-going" @click="setTutup('on-going')" >
									<label class="form-check-label" for="aud_thp1_status2">Tidak</label>
								  </div>
								</div>
							</div>
							
							<div style="padding-top: 20px">
								<template v-if="loading_submit">
									<div class="fa-3x" style="text-align: center">
										<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
									</div>
								</template>
								<template v-else>
									<button :disabled="!status_submit" :class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}" @click="submitRekomendasi">
										<i class="fas fa-save"></i> Simpan Rekomendasi
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
            window.vueRekomendasi = new Vue({
                el: "#vueRekomendasi",
                data: {
                    rekmd_komte_status: null,
                    rekmd_komte_isi: null,
                    jadw_audit_sertifikat_filepath: null,
                    status_submit: false,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						$('textarea#rekmd_komte_isi').tinymce({
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
					validateSertifikat() {
						tinyMCE.triggerSave();
                        this.rekmd_komte_isi = tinyMCE.get('rekmd_komte_isi').getContent();
                    },
					setTutup(val){
						this.rekmd_komte_status = `${val}`;
						this.status_submit = true;
                    },
                    submitRekomendasi() {
						tinyMCE.triggerSave();
						if(tinyMCE.get('rekmd_komte_isi').getContent() === ''){
							toastCenter({
										type: 'warning',
										title: "Silahkan Isi Rekomendasi"
									});
						}
						else{
							swalWithBootstrapButtons({
								title: `Submit Rekomendasi ?`,
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
									formData.append("tipe", `rekomendasi`)
									formData.append("rekmd_komte_isi", tinyMCE.get('rekmd_komte_isi').getContent())
									formData.append("rekmd_komte_status", this.rekmd_komte_status)
									
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
