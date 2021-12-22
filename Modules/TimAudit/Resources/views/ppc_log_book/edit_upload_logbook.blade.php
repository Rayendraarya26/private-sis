@extends("layouts.layout_app")

@section('title', 'Upload Logbook PPC')

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
								<div class="card-header" id="headingOne">
								  <h5 class="mb-0">
									<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-one" aria-expanded="true" aria-controls="collapse-one">
									  Informasi Data Perusahaan
									</button>
								  </h5>
								</div>

								<div id="collapse-one" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion-example">
								  <div class="card-body">
									<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table mb-0">
											<tbody>
												<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
												<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}, {{$dataJadwal->kec_nama}}, {{$dataJadwal->kab_nama}}, {{$dataJadwal->prov_nama}}</td></tr><tr><td></td><td>
												<tr><td></td><td>
													Telp : {{$dataJadwal->cust_nomor_telp}}
													<br/>Hp : {{$dataJadwal->cust_nomor_hp}}
													<br/>Fax : {{$dataJadwal->cust_nomor_fax}}
												</td></tr>
											</tbody>
										</table>
										<table class="table mb-0">
											<thead>
												<tr>
												  <th class="text-uppercase" scope="col">Alamat</th>
												  <th class="text-uppercase" scope="col">Kode Pos</th>
												  <th class="text-uppercase" scope="col">Telp & Fax</th>
												  <th class="text-uppercase" scope="col">Kegiatan Utama</th>
												  <th class="text-uppercase" scope="col">Jumlah Karyawan</th>
												  <th class="text-uppercase" scope="col">Luas Tanah</th>
												  <th class="text-uppercase" scope="col">Luas Bangunan</th>
												</tr>
											</thead>
											<tbody>
												@foreach($dataPabrik as $dpp)
												<tr>
												  <td>{{$dpp->pabrik_nama}} {{$dpp->pabrik_alamat}}, {{$dpp->kec_nama}}, {{$dpp->kab_nama}}, {{$dpp->prov_nama}}</td>
												  <td>{{$dpp->pabrik_kode_pos}}</td>
												  <td>Fax : {{$dpp->pabrik_nomor_fax}};<br/>Telp : {{$dpp->pabrik_nomor_telp}};<br/>Hp : {{$dpp->pabrik_nomor_hp}}</td>
												  <td>{{$dpp->pabrik_kegiatan_utama}}</td>
												  <td>{{$dpp->pabrik_jumlah_karyawan}} Orang</td>
												  <td>{{$dpp->pabrik_luas_tanah}}</td>
												  <td>{{$dpp->pabrik_luas_bangunan}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>
								  </div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" id="headingTwo">
								  <h5 class="mb-0">
									<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-two" aria-expanded="false" aria-controls="collapse-two">
									  Informasi Jadwal Audit
									</button>
								  </h5>
								</div>
								<div id="collapse-two" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-example">
								  <div class="card-body">
									<table class="table">
										<tbody>
											<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
											<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}</td></tr>
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
						  </div>
						</div>
					  </div>
					</div>
				</div>
				
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Upload Logbook PPC</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="vueUpload">
								@if ($dataJadwal->logbook_filepath != '')
									<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Logbook Eksisting</label>
									<div class="col-xl-8">
										<a href="{{url($dataJadwal->logbook_filepath)}}" class="btn btn-xs btn-info" target="_blank">Download File</a>
									</div>
								</div>
								@endif
								<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >File Logbook Baru</label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Logbook"
									   @change="validateUploadJadwal" accept="application/pdf"
									   name="logbook_filepath" id="logbook_filepath">
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
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    logbook_filepath: null,
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

                            $("#logbook_filepath").val("")
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
								if ($.trim($("#logbook_filepath").val()) === "") {
									toastCenter({
												type: 'success',
												title: "Silahkan Unggah File Logbook"
											})
								}
								else{
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("jadw_tim_id", `{{$dataJadwal->jadw_tim_id}}`);
									formData.append("logbook_filepath_lama", `{{$dataJadwal->logbook_filepath}}`);
									formData.append("tipe", `upload-logbook`);
									const file = document.querySelector("#logbook_filepath").files[0];
									formData.append("logbook_filepath", file)
									
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
