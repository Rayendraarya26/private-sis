@extends("layouts.layout_app")

@section('title', 'Upload Logbook Auditor Tahap I')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->aud_thp1_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->aud_thp1_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->aud_thp1_tanggal_selesai?->format("d M Y")}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->mohon_det_no_referensi}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-6">
					<div class="card">
					  <div class="card-body p-0">
							<table class="table">
								<tbody>
									<tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->mohon_kmditi_ruang_lingkup}}</td></tr>
									<tr><td>Standar Acuan</td><td>: {{$dataJadwal->aud_thp1_standart_acuan}}</td></tr>
									<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->aud_thp1_tujuan}}</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Upload File Logbook</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="vueUpload">
								@if ($dataJadwal->thp1_logbook_filepath != '')
									<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Logbook Eksisting</label>
									<div class="col-xl-8">
										<a href="{{url($dataJadwal->thp1_logbook_filepath)}}" target="_blank"><i class="fas fa-download"></i> Download File</a>
									</div>
								</div>
								@endif
								<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Logbook Baru</label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Logbook"
									   @change="validateUploadJadwal" accept="application/pdf"
									   name="thp1_logbook_filepath" id="thp1_logbook_filepath">
								<small><span>Upload file harus berjenis PDF</span></small>
									</div>
								</div>
								<div style="padding-top: 20px">
									<template v-if="loading_submit">
										<div class="fa-3x" style="text-align: center">
											<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
										</div>
									</template>
									<template v-else>
										<button :disabled="!agreement"
												:class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
												@click="submitPermohonan"
										>
											<i class="fas fa-cloud-upload"></i> Upload File Logbook
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
    <script>
	const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
		
        $(document).ready(function () {
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    thp1_logbook_filepath: null,
                    agreement: false,
                    loading_submit: false,
                },
                methods: {
					validateUploadJadwal(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#thp1_logbook_filepath").val("")
                        }
						else{
							this.agreement = true
						}
                    },
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Upload Logbook ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								if ($.trim($("#thp1_logbook_filepath").val()) === "") {
									toastCenter({
												type: 'success',
												title: "Silahkan Unggah File Logbook"
											})
								}
								else{
									// Submit Permohonan
									let formData = new FormData();
									formData.append("aud_thp1_id", `{{$dataJadwal->aud_thp1_id}}`);
									formData.append("thp1_tim_id", `{{$dataJadwal->thp1_tim_id}}`);
									formData.append("thp1_logbook_filepath_lama", `{{$dataJadwal->thp1_logbook_filepath}}`);
									const file = document.querySelector("#thp1_logbook_filepath").files[0];
									formData.append("thp1_logbook_filepath", file)
									
									this.loading_submit = true;
									let self = this;
									$.ajax({
										url: `{{action("$module@save")}}`,
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
								
                            }
                        });
                    },
                }
            })
        });
    </script>
@endpush
