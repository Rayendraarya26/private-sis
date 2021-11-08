@extends("layouts.layout_app")

@section('title', 'Penyusunan Komite')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->jadw_audit_kode_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->jadw_audit_kode_ea}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
								<tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->jadw_audit_ruang_lingkup}}</td></tr>
								<tr><td>Standar Acuan</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
								<tr><td>Kegiatan</td><td>: {{$dataJadwal->jadw_audit_kegiatan}}</td></tr>
								<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->jadw_audit_tujuan_audit}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Upload Jadwal</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="vueUpload">
								@if ($dataJadwal->jadw_file_jadwal != '')
									<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Jadwal Eksisting</label>
									<div class="col-xl-8">
										<a href="{{url($dataJadwal->jadw_file_jadwal)}}" class="btn btn-xs btn-info" target="_blank">Download File</a>
									</div>
								</div>
								@endif
								<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Jadwal Baru</label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Jadwal"
									   @change="validateUploadJadwal" accept="application/pdf"
									   name="jadw_file_jadwal" id="jadw_file_jadwal">
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
											<i class="fas fa-cloud-upload"></i> Upload
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
			@if ($dataJadwal->bill_payment_status != 'lunas')
				swalWithBootstrapButtons({
					title: `Informasi Audit Tahap 2`,
					text: `Data pengajuan belum lunas, anda tidak diperkenankan untuk melakukan proses audit(upload data jadwal) dalam pelaksanaan audit ini, silahkan konfirmasi ke keuangan apabila ada kekurangan dalam pembayaran?`,
					type: 'info',
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: 'OK',
					reverseButtons: true
				}).then(async (result) => {
					setTimeout(() => location.href = "{{url("$url")}}", 1000)
				});
			@endif
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    jadw_file_jadwal: null,
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

                            $("#jadw_file_jadwal").val("")
                        }
						else{
							this.agreement = true
						}
                    },
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Upload Jadwal ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								if ($.trim($("#jadw_file_jadwal").val()) === "") {
									toastCenter({
												type: 'success',
												title: "Silahkan Unggah File Jadwal"
											})
								}
								else{
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("tipe", `upload-jadwal`);
									const file = document.querySelector("#jadw_file_jadwal").files[0];
									formData.append("jadw_file_jadwal", file)
									
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
								
                            }
                        });
                    },
                }
            })
        });
    </script>
@endpush
