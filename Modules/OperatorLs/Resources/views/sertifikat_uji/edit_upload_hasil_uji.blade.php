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
<div id="vueUpload">
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
						<div class="dt-card__heading"><h3 class="dt-card__title">Upload Hasil Uji Sertifikat Produk</h3></div>
					  </div>
					  <div class="dt-card__body">
							<div id="frmSertifikat" style="display:none;">
								<fieldset style="border: 1px #eee solid;padding:20px;">
								<legend>Form Upload:</legend>
								<div class="form-group form-row" id="">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >Sertifikat No.</label>
									<div class="col-xl-8">
										<input type="text" class="form-control" v-on:keyup="validateSertifikat" name="prod_sert_nomor" id="prod_sert_nomor">
									</div>
								</div>
								
								<div class="form-group form-row" id="">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >Tanggal</label>
									<div class="col-xl-8">
										<input type="text" class="form-control" v-on:keyup="validateSertifikat" name="prod_sert_tanggal" id="prod_sert_tanggal">
									</div>
								</div>
								
								<div class="form-group form-row" id="">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >Nama Laboratorium</label>
									<div class="col-xl-8">
										<input type="text" class="form-control" v-on:keyup="validateSertifikat" name="prod_sert_lab_nama" id="prod_sert_lab_nama">
									</div>
								</div>
								
								<div class="form-group form-row" id="">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >File Sertifikasi Hasil Uji <span id="labelForm"></span></label>
									<div class="col-xl-8">
										<input type="file" class="form-control" aria-label="File Sertifikasi Hasil Uji" @change="validateUpload" accept="application/pdf" name="prod_sert_filepath" id="prod_sert_filepath">
										<small><span>Upload file harus berjenis PDF</span></small>
									</div>
								</div>
								
								<div class="form-group form-row" id="">
									<label class="col-xl-3 col-form-label text-sm-left" for="id" >Status</label>
									<div class="form-row">
										<div class="col-md-8 col-sm-8 offset-md-2 offset-sm-3">
										  <div class="custom-control custom-radio mb-3">
											<input type="radio" id="prod_sert_status_hasil1" name="prod_sert_status_hasil" class="custom-control-input" value="memenuhi" checked>
											<label class="custom-control-label" for="prod_sert_status_hasil1">Memenuhi</label>
										  </div>
										  <div class="custom-control custom-radio mb-3">
											<input type="radio" id="prod_sert_status_hasil2" name="prod_sert_status_hasil" value="tidak memenuhi" class="custom-control-input">
											<label class="custom-control-label" for="prod_sert_status_hasil2">Tidak Memenuhi</label>
										  </div>
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
										<button :disabled="!agreement"
												:class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
												@click="submitSertifikat"
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
											<a href="#" class="btn btn-outline-primary btn-xs" @click="uploadItem()">
												<i class="fas fa-cloud-upload"></i> Upload
											</a>
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
		
		function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
		
		function uploadItem() {
			setTimeout(async () => {
				$('#prod_sert_tanggal').datebox({
					required:true,
					editable:false,
					formatter:myformatter,
					parser:myparser,
					value:'',
					onSelect: async function(date){
						
					}
				});
				
				$("#prod_sert_nomor").val('');
				$("#prod_sert_filepath").val('');
				$("#prod_sert_lab_nama").val('');
				$("#frmSertifikat").show();
				$(".tab-content").height("100%");
			}, 500);						
		}
		
        $(document).ready(function () {
            window.vueUpload = new Vue({
                el: "#vueUpload",
                data: {
                    prod_sert_nomor: null,
                    prod_sert_filepath: null,
                    agreement: false,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						let dg = $('#ttData').datagrid({
							method: 'get',
							width: $(".tab-content").width()-20,
							url: `{{ url("$url/ajax?action=datagrid-sertifikat-uji") }}&jadw_id={{$dataJadwal->jadw_id}}`,
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
								{field: 'prod_sert_id', hidden: true},
								{field: 'prod_sert_filepath', title: 'File Sertifikat', width: 130, sortable: false, align:'center'},
								{field: 'prod_sert_nomor', title: 'Nomor Sertifikat Uji', width: 160, sortable: true},
							]],
							columns: [[
								{field: 'prod_sert_tanggal', title: 'Tanggal', width: 100, sortable: true},
								{field: 'prod_sert_lab_nama', title: 'Nama Laboratorium', width: 350, sortable: true},
								{field: 'prod_sert_status_hasil', title: 'Status', width: 100, sortable: true, align:'center'},
							]],
						});	
						
						dg.datagrid(
							'enableFilter', [
								{field: 'action', type: 'label'},
								{field: 'prod_sert_filepath', type: 'label'},
								{
									field: 'prod_sert_status_hasil',
									type: 'combobox',
									options: {
										panelHeight: 'auto',
										data: [
											{value: '', text: 'Semua'},
											{value: 'memenuhi', text: 'memenuhi'},
											{value: 'tidak memenuhi', text: 'tidak memenuhi'},
										],
										onChange: function (value) {
											dg.datagrid('addFilterRule', {
												field: 'prod_sert_status_hasil',
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
										idData.push(data.rows[i].prod_sert_id);
										fileData.push(data.rows[i].file);
									}
								}
								
								$.ajax({
									url: `{{url("$url/update")}}`,
									data: { 'ids[]': idData, 'filepath[]': fileData,  'tipe': 'delete-hasil-uji' },
									type: 'POST',
									success: function (response) {
										toastCenter({
											type: 'success',
											title: response.message
										});
										
										setTimeout(async () => {
											this.agreement = false;
											this.loading_submit = false;
											$('#ttData').datagrid('reload');
											$("#frmSertifikat").hide();
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

                            $("#prod_sert_filepath").val("")
                        }
						else{
							this.agreement = true
						}
                    },
					validateSertifikat() {
                        this.prod_sert_nomor = $("#prod_sert_nomor").val();
                    },
                    submitSertifikat() {
						if ($.trim($("#prod_sert_filepath").val()) === "") {
							toastCenter({
										type: 'warning',
										title: "Silahkan Unggah File Sertifikat"
									});
						}
						else if($("#prod_sert_nomor").val() === ''){
							toastCenter({
										type: 'warning',
										title: "Silahkan Isi Sertifikat Nomor"
									});
						}
						else{
							swalWithBootstrapButtons({
								title: `Upload Laporan ?`,
								text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
								type: 'info',
								showCancelButton: true,
								confirmButtonText: 'Upload',
								cancelButtonText: 'Batal',
								reverseButtons: true
							}).then(async (result) => {
								if (result.value) {
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("tipe", `upload-hasil-uji`);
									const file = document.querySelector("#prod_sert_filepath").files[0];
									formData.append("prod_sert_filepath", file)
									formData.append("prod_sert_nomor", this.prod_sert_nomor)
									formData.append("prod_sert_tanggal", $('#prod_sert_tanggal').datebox('getValue') );
									formData.append("prod_sert_lab_nama", $("#prod_sert_lab_nama").val());
									formData.append("prod_sert_status_hasil", document.querySelector('input[name="prod_sert_status_hasil"]:checked').value);
									
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
											setTimeout(async () => {
												self.agreement = false;
												self.loading_submit = false;
												$('#ttData').datagrid('reload');
												$("#frmSertifikat").hide();
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
							});
						}
                    },
                }
            })
        });
    </script>
@endpush
