@extends("layouts.layout_app")

@section('title', 'Upload Laporan PPC')

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
												<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}, {{$dataJadwal->kec_nama}}, {{$dataJadwal->kab_nama}}, {{$dataJadwal->prov_nama}}</td></tr><tr><td></td><td>
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
						<div class="dt-card__heading"><h3 class="dt-card__title">Upload Laporan PPC</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div id="vueUpload">
							<div id="frmLap" style="display:none;">
								<fieldset style="border: 1px #eee solid;padding:20px;">
								<legend>Form Upload:</legend>
								<div class="form-group form-row">
									<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Jenis Lpoaran</label>
									<div class="col-xl-9">
										  <div class="form-check mb-2">
											<input value="19" aria-describedby="jenis_lapHelp" type="radio" id="jenis_lap" name="jenis_lap" class="form-check-input" @click="setJenisLaporan('19')">
											<label class="form-check-label" for="jenis_lap">19. RENCANA PENGAMBILAN CONTOH </label>
										  </div>
										  
										  <div class="form-check mb-2">
											<input value="20" aria-describedby="jenis_lapHelp" type="radio" id="jenis_lap2" name="jenis_lap" class="form-check-input" @click="setJenisLaporan('20')">
											<label class="form-check-label" for="jenis_lap2">20. BERITA ACARA PENGAMBILAN CONTOH</label>
										  </div>
										  
										  <div class="form-check mb-2">
											<input value="21" aria-describedby="jenis_lapHelp" type="radio" id="jenis_lap3" name="jenis_lap" class="form-check-input" @click="setJenisLaporan('21')">
											<label class="form-check-label" for="jenis_lap3">21. LABEL CONTOH UJI</label>
										  </div>
										  
										  <div class="form-check mb-2">
											<input value="22" aria-describedby="jenis_lapHelp" type="radio" id="jenis_lap4" name="jenis_lap" class="form-check-input" @click="setJenisLaporan('22')">
											<label class="form-check-label" for="jenis_lap4">22. LAPORAN KEGIATAN PENGAMBILAN CONTOH</label>
										  </div>
										  
										   <div class="form-check mb-2">
											<input value="pengantar-uji" aria-describedby="jenis_lapHelp" type="radio" id="jenis_lap5" name="jenis_lap" class="form-check-input" @click="setJenisLaporan('pengantar-uji')">
											<label class="form-check-label" for="jenis_lap5">SURAT PENGANTAR UJI</label>
										  </div>
										  <small id="jenis_lapHelp" class="form-text" style="color:red;">Note: Silahkan pilih jenis laporan, jika anda mengupload file dengan jenis laporan yang sama dengan data yang ada di eksisting maka akan menimpa atau merubah data yang ada.</small>
									</div>
								</div>
								<div class="form-group form-row" id="data_permohonan">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >File Laporan</label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Laporan" @change="validateUpload" accept="application/pdf" name="laporan_filepath" id="laporan_filepath">
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
												@click="submitLaporan"
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
											<a href="#" class="btn btn-outline-success btn-xs" @click="addData">
												<i class="fas fa-plus"></i> Tambah
											</a>
										</div>
										&nbsp;&nbsp;&nbsp;<div class="datagrid-btn-separator"></div>
										&nbsp;&nbsp;&nbsp;
										<div>
											<a href="#" class="btn btn-outline-danger btn-xs" @click="deleteItem()">
												<i class="fas fa-trash"></i> Hapus
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
		
		
		
        $(document).ready(function () {
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    laporan_jenis: null,
                    laporan_filepath: null,
                    agreement: false,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						let dg = $('#ttData').datagrid({
							method: 'get',
							width: $(".tab-content").width()-20,
							url: `{{ url("$url/ajax?action=datagrid-ppc-laporan") }}&jadw_id={{$dataJadwal->jadw_id}}`,
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
								{field: 'audit_ppc_filepath', title: '', width: 100, sortable: false},
							]],
							columns: [[
								{field: 'audit_ppc_jenis_file', title: 'Jenis', width: 300, sortable: true,
									formatter: function (val, row) {
										if(row.audit_ppc_jenis_file == '19')
											return `19. RENCANA PENGAMBILAN CONTOH`
										else if(row.audit_ppc_jenis_file == '20')
											return `20. BERITA ACARA PENGAMBILAN CONTOH`
										else if(row.audit_ppc_jenis_file == '21')
											return `21. LABEL CONTOH UJI`
										else if(row.audit_ppc_jenis_file == '22')
											return `22. LAPORAN KEGIATAN PENGAMBILAN CONTOH`
										else if(row.audit_ppc_jenis_file == 'pengantar-uji')
											return `SURAT PENGANTAR UJI`
									}
								},
								{field: 'created_at', title: 'created at', width: 100, sortable: true},
								{field: 'updated_at', title: 'updated at', width: 100, sortable: true},
							]],
						});	
						
						dg.datagrid(
							'enableFilter', [
								{field: 'action', type: 'label'},
								{field: 'audit_ppc_filepath', type: 'label'},
								{field: 'created_at', type: 'label'},
								{field: 'updated_at', type: 'label'},
								{
									field: 'audit_ppc_jenis_file',
									type: 'combobox',
									options: {
										panelHeight: 'auto',
										value: '',
										data: [
											{value: '19', text: 'RENCANA PENGAMBILAN CONTOH'},
											{value: '20', text: 'BERITA ACARA PENGAMBILAN CONTOH'},
											{value: '21', text: 'LABEL CONTOH UJI'},
											{value: '22', text: 'LAPORAN KEGIATAN PENGAMBILAN CONTOH'},
											{value: 'pengantar-uji', text: 'SURAT PENGANTAR UJI'},
											{value: '', text: 'Semua'}
										],
										onChange: function (value) {
											dg.datagrid('addFilterRule', {
												field: 'audit_ppc_jenis_file',
												op: 'equal',
												value: value
											});

											dg.datagrid('doFilter');
										}
									}
								},
							]);
					})
				},
                methods: {
					setJenisLaporan(dt) {
						this.laporan_jenis = dt;
					},
					async addData() {
						setTimeout(async () => {
							this.laporan_jenis = null;
							this.agreement = false;
							this.loading_submit = false;
							$("#laporan_filepath").val(null);
							$("#frmLap").show();
							$(".tab-content").height("100%");
						}, 500);						
					},
					async deleteItem() {
                        swalWithBootstrapButtons({
                            title: `Hapus Item ?`,
                            text: `Anda yakin menghapus data laporan ppc yang telah anda pilih ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								var idData = []; 
								var data = $('#ttData').datagrid('getData');
								var opts = $('#ttData').datagrid('options');
								for (var i = 0; i < data.rows.length; i++) {
									var tr = opts.finder.getTr($('#ttData')[0],i);
									var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
									if(atLeastOneIsChecked == true){
										idData.push(data.rows[i].audit_ppc_id);
									}
								}
								$.ajax({
									url: `{{url("$url/update")}}`,
									data: { 'ids[]': idData, 'tipe': 'delete-laporan' },
									type: 'POST',
									success: function (response) {
										toastCenter({
											type: 'success',
											title: response.message
										})
										setTimeout(async () => {
											$('#ttData').datagrid('reload');
											this.laporan_jenis = null;
											$("#frmLap").hide();
											$(".tab-content").height("100%");
										}, 500);	
										
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

                            $("#laporan_filepath").val("")
                        }
						else{
							this.agreement = true
						}
                    },
                    submitLaporan() {
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
								if ($.trim($("#laporan_filepath").val()) === "") {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Laporan"
											})
								}
								else if (this.laporan_jenis == null) {
									toastCenter({
												type: 'warning',
												title: "Silahkan Pilih Jenis File Laporan"
											})
								}
								else{
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("audit_ppc_jenis_file", this.laporan_jenis);
									formData.append("tipe", `upload-laporan`);
									const file = document.querySelector("#laporan_filepath").files[0];
									formData.append("audit_ppc_filepath", file)
									
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
											});
											
											setTimeout(async () => {
												$('#ttData').datagrid('reload');
												this.laporan_jenis = null;
												$("#frmLap").hide();
												$(".tab-content").height("100%");
											}, 500);
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
