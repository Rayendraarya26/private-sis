@extends("layouts.layout_app")

@section('title', 'Upload Hasil Uji Sertifikasi')

@push("css")
    <style>
        legend { 
		  display: block;
		  padding-left: 2px;
		  padding-right: 2px;
		  border: none;
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
												<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
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
												  <td>Fax : {{$dpp->pabrik_nomor_fax}}; Telp : {{$dpp->pabrik_nomor_telp}}; Hp : {{$dpp->pabrik_nomor_hp}}</td>
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
						<div class="dt-card__heading"><h3 class="dt-card__title">Upload Hasil Uji</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div id="vueUpload">
							<div id="frmLap" style="display:none;">
								<fieldset style="border: 1px #eee solid;padding:20px;">
								<legend>Form Upload:</legend>
								<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >File Sertifikasi Hasil Uji <span id="labelForm"></span></label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Sertifikasi Hasil Uji" @change="validateUpload" accept="application/pdf" name="jadw_audit_sertifikat_filepath" id="jadw_audit_sertifikat_filepath">
										<input type="hidden" id="jadw_audit_id">
										<input type="hidden" id="jadw_audit_sertifikat_filepath_lama">
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
								</fieldset>
							</div>
							
							<div id="ttData" style="width:100%; min-width: 310px; min-height: 300px"></div>
							<div id="toolbar" style="padding: 10px 0 10px 20px">
								<div class="row">
									@if(authorized("{$module}@edit"))
										<div>
											<a href="#" class="btn btn-outline-danger btn-xs" @click="deleteItem()">
												<i class="fas fa-trash"></i> Hapus File
											</a>
										</div>
									@endif
								</div>
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
		
		function uploadData(id) {
			setTimeout(async () => {
				$.ajax({
					url: `{{ url("$url/ajax?action=data-list-uji") }}&jadw_audit_id=${id}`,
					type: 'get',
					processData: false,
					contentType: false,
					success: async function (res) {
						setTimeout(() => {
							$("#jadw_audit_sertifikat_filepath_lama").val(res.jadw_audit_sertifikat_filepath);
							$("#labelForm").html(res.sert_nama);
						}, 400)
					},
					error: function (xhr) {
						self.loading_submit = false;
						if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
						else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
					}
				});
				$("#jadw_audit_id").val(id);
				$("#frmLap").show();
				$(".tab-content").height("100%");
			}, 500);						
		}
		
        $(document).ready(function () {
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    jadw_audit_sertifikat_filepath: null,
                    agreement: false,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						let dg = $('#ttData').datagrid({
							method: 'get',
							width: $(".tab-content").width()-20,
							url: `{{ url("$url/ajax?action=datagrid-hasil-uji") }}&jadw_id={{$dataJadwal->jadw_id}}`,
							rownumbers: false,
							nowrap: false,
							singleSelect: false,
							remoteFilter: true,
							multiSort: true,
							toolbar: '#toolbar',
							pagination: false,
							clientPaging: false,
							frozenColumns: [[
								{field: 'ck', checkbox: true, sortable: false},
								{
									field: 'action',
									title: "<br/><br/><br/>",
									width: 80,
									align: 'center',
									formatter: function (val, row) {
										let dom = `dropdownMenu_${row.jadw_audit_id}`;
										let btnEdit = ``;			
										btnEdit += `<a href="#" class="btn btn-outline-info btn-xs btn-block" onclick="uploadData(${row.jadw_audit_id})"><i class="fas fa-cloud-upload"></i>Upload</a>`;
										
										return `@if(authorized("{$module}@edit")) ${btnEdit} @endif`
									}
								},
								{field: 'jadw_audit_sertifikat_filepath', title: 'File<br>Sertifikat', width: 100, sortable: false},
							]],
							columns: [[
								{field: 'jadw_audit_jenis', title: 'Jenis<br/>Pengajuan<br/>', width: 100, sortable: true},
								{field: 'sert_nama', title: 'Sertifikasi<br/>', width: 400, sortable: true},
								{field: 'komodt_nama', title: 'Komoditi<br/>Nama', width: 150, sortable: true},
								{field: 'jadw_audit_sni', title: 'SNI', width: 150, sortable: true},
								{field: 'jadw_audit_ruang_lingkup', title: 'Ruang<br>Linkup', width: 400, sortable: true},
							]],
						});	
						
						dg.datagrid(
							'enableFilter', [
								{field: 'action', type: 'label'},
								{field: 'jadw_audit_sertifikat_filepath', type: 'label'},
								{field: 'jadw_audit_jenis', type: 'label'},
							]);
					})
				},
                methods: {
					async deleteItem() {
                        swalWithBootstrapButtons({
                            title: `Hapus Item ?`,
                            text: `Anda yakin menghapus data sertifikat hasil uji yang telah anda pilih ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								var idData = []; 
								var fileData = []; 
								var data = $('#ttData').datagrid('getData');
								var opts = $('#ttData').datagrid('options');
								for (var i = 0; i < data.rows.length; i++) {
									var tr = opts.finder.getTr($('#ttData')[0],i);
									var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
									if(atLeastOneIsChecked == true){
										idData.push(data.rows[i].jadw_audit_id);
										fileData.push(data.rows[i].file);
									}
								}
								
								console.log(idData);
								$.ajax({
									url: `{{url("$url/update")}}`,
									data: { 'ids[]': idData, 'filepath[]': fileData,  'tipe': 'delete-hasil-uji' },
									type: 'POST',
									success: function (response) {
										toastCenter({
											type: 'success',
											title: response.message
										})

										let dg = $('#ttData');
										dg.datagrid('reload');
									},
									error: function (err) {
										if (err.responseJSON.message) {
											toastCenter({
												type: 'error',
												title: err.responseJSON.message
											})
										}
									}
								});
                            }
                        });
                    },
					validateUpload(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#jadw_audit_sertifikat_filepath").val("")
                        }
						else{
							this.agreement = true
						}
                    },
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Upload Laporan ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								if ($.trim($("#jadw_audit_sertifikat_filepath").val()) === "") {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Laporan"
											})
								}
								else{
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("jadw_audit_id", $("#jadw_audit_id").val());
									formData.append("jadw_audit_sertifikat_filepath_lama", $("jadw_audit_sertifikat_filepath_lama").val());
									formData.append("tipe", `upload-hasil-uji`);
									const file = document.querySelector("#jadw_audit_sertifikat_filepath").files[0];
									formData.append("jadw_audit_sertifikat_filepath", file)
									
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
											setTimeout(() => location.href = "{{url("$url")}}/edit?tipe=upload-hasil-uji&jadw_id={{$dataJadwal->jadw_id}}", 1000)
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
