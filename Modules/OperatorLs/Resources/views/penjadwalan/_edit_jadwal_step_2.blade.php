@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
        }

        @media screen and (max-width: 450px) {
            .komoditi-button {
                padding-top: 0;
            }
        }
    </style>
@endpush

<div class="row" id="vueStepTwo">
    <div class="col-md-12" style="padding-bottom: 20px">
        <div class="row">
		
            <div class="col-md-12">
				<div id="form-tambah" style="display:none;">
					<form action="#" id="form_id">
						<div id="sertifikasi_permohonan">
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Tipe Jadwal</label>
							<div class="col-xl-9">
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="surveilans" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe4" name="jadw_audit_jenis_tipe" class="custom-control-input" onClick="setJenisAudit('surveilans')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe4">Surveilans</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe2" name="jadw_audit_jenis_tipe" class="custom-control-input" onClick="setJenisAudit('sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe2">Sertifikasi</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="re-sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe3" name="jadw_audit_jenis_tipe" class="custom-control-input" onClick="setJenisAudit('re-sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe3">Re-Sertifikasi</label>
								  </div>
								<small id="jadw_audit_jenis_tipeHelp" class="form-text">Note: Silahkan pilih tipe audit items.</small>
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cb_data_id" >Data Permohonan/Sertifikat</label>
							<div class="col-xl-8">
								<input type="text" class="form-control" id="cb_data_id">
								<small class="form-text">Note: Data tahap-I, re-sertifikasi dan sertifikasi untuk data permohonan; Data surveilans untuk data sertifikat.</small>
								
								<input type="hidden" id="jadw_audit_id" value="">
								<input type="hidden" id="data_update" value="">
								<input type="hidden" id="tipe" value="">
								<input type="hidden" id="mohon_id" value="">
								<input type="hidden" id="mohon_det_id" value="">
								<input type="hidden" id="sert_id" value="">
								<input type="hidden" id="cust_sert_id" value="">
								<input type="hidden" id="nomor_sertifikat" value="">
								<input type="hidden" id="nomor_referensi" value="">
								<input type="hidden" id="komodt_id" value="">
								<input type="hidden" id="tipe_komoditi" value="">
								<input type="hidden" id="merk" value="">
								<input type="hidden" id="sni" value="">
								<input type="hidden" id="ukuran" value="">
								<input type="hidden" id="kode_nace" value="">
								<input type="hidden" id="kode_ea" value="">
								<input type="hidden" id="satuan" value="">
								<input type="hidden" id="kapasitas_produksi" value="">
								<input type="hidden" id="mohon_komoditi_id" value="">
						
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Komoditi</label>
							<div class="col-xl-8">
								<input class="form-control" id="cb_komoditi" value="">
							</div>
						</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Standart Acuan</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="standart_acuan"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row" style="display:none;">
							<label class="col-xl-3 col-form-label text-sm-left" >Ruang Lingkup</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="ruang_lingkup"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Kegiatan Audit</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="kegiatan"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Tujuan Audit</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="tujuan_audit" value=""></textarea>
							</div>
						</div>
							
						<div class="col-md-12 aksi-button">
							<a href="#" class="btn btn-xs btn-primary" onClick="simpanAction()">
								<i class="fas fa-save"></i> Simpan
							</a>
							<a href="#" class="btn btn-xs btn-danger" onClick="cancelAction()">
								<i class="fas fa-close"></i> Batal
							</a>
						</div>
						<hr/>
				</div>
			</div>
            <div class="col-md-12">
				<div id="ttData" style="width:100%; min-width: 310px"></div>
				<div id="toolbar" style="padding: 10px 0 10px 20px">
					<div class="row">
						@if(authorized("{$module}@create"))
							<div>
								<a href="#" class="btn btn-outline-success btn-xs" onClick="addData()">
									<i class="fas fa-plus"></i> Tambah
								</a>
							</div>
							&nbsp;&nbsp;&nbsp;
						@endif							
						@if(authorized("{$module}@destroy"))
							<div class="datagrid-btn-separator"></div>
							&nbsp;&nbsp;&nbsp;
							<div>
								<a href="#" class="btn btn-outline-danger btn-xs" onClick="confirmDelete()">
									<i class="fas fa-trash"></i> Hapus
								</a>
							</div>
						@endif
					</div>
				</div>
            </div>
			<div class="col-md-12">
				<hr/>
				<template v-if="loading_submit">
					<div class="fa-3x" style="text-align: center">
						<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
					</div>
				</template>
				<template v-else>
					<button :disabled="!status_submit"
							:class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}"
							@click="submitJadwal">
						<i class="fad fa-disk"></i> Simpan jadwal
					</button>
				</template>
			</div>
		</div>
    </div>
</div>

@push('javascript')
    <script>
		$(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {	
                    status_submit: true,
                    loading_submit: false,
                },
                mounted() {
                    setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 1) {
                            this.start();
                        }
                    }, 400)
                },
                methods: {
					async submitJadwal() {
                        swalWithBootstrapButtons({
                            title: `Simpan Data ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								let formData = new FormData();
								// Step 1
								formData.append("tipe", 'edit-jadwal')
								formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`)
								formData.append("jadw_tanggal_status", `{{$dataJadwal->jadw_tanggal_status}}`)
								formData.append("jadw_tanggal_mulai", window.vueStepOne.jadw_tanggal_mulai)
								formData.append("jadw_tanggal_selesai", window.vueStepOne.jadw_tanggal_selesai)
								formData.append("jadw_jenis", window.vueStepOne.jenis)
								formData.append("cust_id", window.vueStepOne.cust_id)

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
                    },
                    async start() {
                        setTimeout(async () => {
							$(".tab-content").height("100%");
							$('#ttData').datagrid({
								method: 'get',
								width: $(".tab-content").width()-20,
								height: document.documentElement.scrollHeight - 300,
								url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}&jadw_id={{$dataJadwal->jadw_id}}`,
								rownumbers: false,
								nowrap: false,
								singleSelect: false,
								remoteFilter: true,
								multiSort: true,
								// fitColumns: true,
								toolbar: '#toolbar',
								pagination: true,
								pageSize: 50,
								clientPaging: false,
								frozenColumns: [[
									{field: 'ck', checkbox: true, sortable: false},
									{
										field: 'action',
										title: "Aksi",
										width: 80,
										align: 'center',
										formatter: function (val, row, index) {
											@if(authorized("{$module}@edit"))
											var e = `<a onClick="editItem(${index})" href="javascript:void(0)" class="btn btn-xs btn-primary btn-block">Edit</a>`;
											return e;
											@endif
										}
									}
								]],
								columns: [[
									{field: 'jadw_audit_jenis', title: 'Jenis', width: 150, sortable: true},
									{field: 'mohon_det_id', hidden: true},
									{field: 'mohon_id', title: 'Permohonan No', width: 100, sortable: true},
									{field: 'sert_nama', title: 'Serifikasi', width: 250, sortable: true},
									{field: 'jadw_audit_nomor_sertifikat', title: 'No. Sertifikat', width: 120, sortable: true},
									{field: 'jadw_audit_nomor_referensi', title: 'No. Ref', width: 100, sortable: true},
									{field: 'jadw_audit_kode_nace', title: 'NACE', width: 150, sortable: true},
									{field: 'jadw_audit_kode_ea', title: 'EA', width: 150, sortable: true},
									{field: 'jadw_audit_standart_acuan', title: 'Standart Acuan', width: 250, sortable: true},
									{field: 'jadw_audit_ruang_lingkup', title: 'Ruang Lingkup', width: 250, sortable: true},
									{field: 'jadw_audit_kegiatan', title: 'Kegiatan', width: 250, sortable: true},
									{field: 'jadw_audit_tujuan_audit', title: 'Tujuan Audit', width: 250, sortable: true},
									{field: 'jadw_audit_sni', title: 'SNI', width: 100, sortable: true},
									{field: 'jadw_audit_merk', title: 'Merk', width: 100, sortable: true},
									{field: 'jadw_audit_tipe', title: 'Tipe', width: 100, sortable: true},
									{field: 'jadw_audit_ukuran', title: 'Ukuran', width: 100, sortable: true},
								]],
							});
						}, 1000);					
                    },
                }
            });
			
			
        });
		
		function setJenisAudit(dt_jenis){
			const jadw_audit_id = $('#jadw_audit_id').val();
			$("#mohon_id").val("");
			$("#mohon_det_id").val("");
			$("#cust_sert_id").val("");
			$("#sert_id").val("");
			$("#nomor_sertifikat").val("");
			$("#nomor_referensi").val("");
			$("#komodt_id").val("");
			$("#tipe_komoditi").val("");
			$("#merk").val("");
			$("#sni").val("");
			$("#ukuran").val("");
			$("#standart_acuan").val("");
			$("#ruang_lingkup").val("");
			$("#kegiatan").val("");
			$("#tujuan_audit").val("");
			$("#kode_ea").val("");
			$("#kode_nace").val("");
			$("#mohon_komoditi_id").val("");
			
			if(dt_jenis === 'sertifikasi' || dt_jenis === 'tahap-1'){
				$("#sertifikasi_komoditi").show();
			}
			else{
				$("#sertifikasi_komoditi").hide();
			}
			
			let urlCombo = ``;										
			if(dt_jenis !== 'surveilans'){
				urlCombo = `{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id={{$dataJadwal->cust_id}}&jenis_status=${dt_jenis}`;
				$('#cb_data_id').combogrid({
					pageSize: '50', panelWidth: 650, pagination: true, idField: 'id', nowrap: false, textField: 'nama', editable: true, url: urlCombo, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: true, required: false,
					columns: [[
						{field: 'id', hidden: true},
						{field: 'mohon_jenis_status', title: 'Jenis', width: 250, sortable: true,},
						{field: 'nama', title: 'Permohonan', width: 250, sortable: true,},
					]],
					onSelect: function (index, row) {
						$("#cust_sert_id").val("");
						$("#sert_id").val(row.sert_id);
						$("#mohon_id").val(row.mohon_id);
						$("#mohon_det_id").val(row.mohon_det_id);
						$("#nomor_referensi").val(row.nomor_referensi);
						if(row.cust_sert_id != ''){
							$("#cust_sert_id").val(row.cust_sert_id);
						}
						if(dt_jenis === 're-sertifikasi'){
							$("#nomor_sertifikat").val(row.nomor_sertifikat);
							$("#nomor_referensi").val(row.nomor_referensi);
							$("#komodt_id").val(row.komodt_id);
							$("#tipe_komoditi").val(row.tipe);
							$("#merk").val(row.merk);
							$("#sni").val(row.nomor_sni);
							$("#ukuran").val("");
							$("#standart_acuan").val("");
							$("#ruang_lingkup").val(row.lingkup);
							$("#kode_ea").val(row.kode_ea);
							$("#kode_nace").val(row.kode_nace);
							$("#kegiatan").val("");
							$("#tujuan_audit").val("");
						}
						else{
							if(row.sert_is_product === 'ya'){
								$("#sertifikasi_komoditi").show();
								let urlComboKomoditi = `{{ url("$url/ajax?action=combogrid-permohonan-komoditi") }}&mohon_det_id=${row.mohon_det_id}`;
								$('#cb_komoditi').combogrid({
									pageSize: '50', panelWidth: 650, pagination: true, idField: 'komodt_id', nowrap: false, textField: 'komodt_nama', editable: true, url: urlComboKomoditi, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: true, required: false,
									columns: [[
										{field: 'komodt_id', hidden: true},
										{field: 'komodt_nama', title: 'Komoditi', width: 250, sortable: true,},
										{field: 'mohon_kmditi_sni', title: 'SNI', width: 100, sortable: true,},
										{field: 'mohon_kmditi_merk', title: 'Merk', width: 100, sortable: true,},
										{field: 'mohon_kmditi_tipe', title: 'Tipe', width: 100, sortable: true,},
										{field: 'mohon_kmditi_ukuran', title: 'Ukuran', width: 100, sortable: true,},
									]],
									onSelect: function (index, rowK) {
										$("#mohon_komoditi_id").val(rowK.mohon_kmditi_id);
										$("#komodt_id").val(rowK.komodt_id);
										$("#komodt_nama").val(rowK.komodt_nama);
										$("#tipe_komoditi").val(rowK.mohon_kmditi_tipe);
										$("#merk").val(rowK.mohon_kmditi_merk);
										$("#sni").val(rowK.mohon_kmditi_sni);
										$("#ukuran").val(rowK.mohon_kmditi_ukuran);
										
										$("#ruang_lingkup").val(rowK.mohon_kmditi_ruang_lingkup);
										$('#kode_ea').val(`${rowK.mohon_kmditi_ea}`);
										$('#kode_nace').val(`${rowK.mohon_kmditi_nace}`);
										$('#satuan').val(`${rowK.mohon_kmditi_kapasitas_produksi_tahunan_satuan}`);
										$('#kapasitas_produksi').val(`${rowK.mohon_kmditi_kapasitas_produksi_tahunan}`);
									},
								});
							}
							else{
								$.ajax({
									url: `{{ url("$url/ajax?action=data-list-komoditi") }}&mohon_det_id=${row.mohon_det_id}`,
									type: 'get',
									processData: false,
									contentType: false,
									success: async function (res) {
										setTimeout(() => {
											$("#mohon_komoditi_id").val('');
											$("#komodt_id").val(res.komodt_id);
											$("#komodt_nama").val(res.komoditi_nama);
											$("#tipe_komoditi").val(res.tipe);
											$("#merk").val(res.merk);
											$("#ukuran").val(res.ukuran);
											
											$("#ruang_lingkup").val(res.ruang_lingkup);
											$('#kode_ea').val(`${res.ea}`);
											$('#kode_nace').val(`${res.nace}`);
											$('#satuan').val(`${res.satuan}`);
											$('#kapasitas_produksi').val(`${res.kapasitas_produksi}`);
										}, 400)
									},
									error: function (xhr) {
										self.loading_submit = false;
										if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
										else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
									}
								});
								$("#sni").val(row.nomor_sni);
								$("#sertifikasi_komoditi").hide();
							}
							
						}
					},
				});
			}
			else if(dt_jenis === 'surveilans'){
				urlCombo = `{{ url("$url/ajax?action=combogrid-sertifikat") }}&cust_id={{$dataJadwal->cust_id}}`;
				$('#cb_data_id').combogrid({
					pageSize: '50', panelWidth: 650, pagination: true, idField: 'id', nowrap: false, textField: 'nama', editable: true, url: urlCombo, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: true, required: false,
					columns: [[
						{field: 'id', hidden: true},
						{field: 'nama', title: 'Nama Sertifikat', width: 250, sortable: true,},
						{field: 'nomor_referensi', title: 'No. Referensi', width: 250, sortable: true,},
						{field: 'cust_sert_tgl_sertifikat_awal', title: 'Tgl. Awal', width: 100, sortable: true,},
						{field: 'cust_sert_tgl_sertifikat_perubahan', title: 'Tgl. Perubahan', width: 100, sortable: true,},
					]],
					onSelect: function (index, row) {
						$("#cust_sert_id").val(row.cust_sert_id);
						$("#sert_id").val(row.sert_id);
						$("#mohon_id").val("");
						$("#mohon_det_id").val("");
						$("#nomor_sertifikat").val(row.nomor_sertifikat);
						$("#nomor_referensi").val(row.nomor_referensi);
						$("#komodt_id").val(row.komodt_id);
						$("#tipe_komoditi").val(row.tipe);
						$("#merk").val(row.merk);
						$("#sni").val(row.nomor_sni);
						$("#ukuran").val("");
						$("#standart_acuan").val("");
						$("#ruang_lingkup").val(row.lingkup);
						$("#kode_ea").val(row.kode_ea);
						$("#kode_nace").val(row.kode_nace);
						$("#kegiatan").val("");
						$("#tujuan_audit").val("");
					},
				});
			}
			
			if(jadw_audit_id != ''){
				let data_update = $('#data_update').val();
				const obj = JSON.parse(`${data_update}`);
				

				if(dt_jenis !== 'surveilans'){
					$('#cb_data_id').combogrid({
						value:`${obj.mohon_det_id}`
					});
					if(dt_jenis !== 're-sertifikasi'){
						let urlComboKomoditi = `{{ url("$url/ajax?action=combogrid-permohonan-komoditi") }}&mohon_id=${obj.mohon_det_id}`;
						$('#cb_komoditi').combogrid({
							pageSize: '50', panelWidth: 650, pagination: true, idField: 'komodt_id', nowrap: false, textField: 'komodt_nama', editable: true, url: urlComboKomoditi, method: 'get', mode: 'remote', value: `${obj.komodt_id}`, multiSort: true, fitColumns: true, required: false,
							columns: [[
								{field: 'komodt_id', hidden: true},
								{field: 'komodt_nama', title: 'Komoditi', width: 250, sortable: true,},
								{field: 'mohon_kmditi_sni', title: 'SNI', width: 100, sortable: true,},
								{field: 'mohon_kmditi_merk', title: 'Merk', width: 100, sortable: true,},
								{field: 'mohon_kmditi_tipe', title: 'Tipe', width: 100, sortable: true,},
								{field: 'mohon_kmditi_ukuran', title: 'Ukuran', width: 100, sortable: true,},
							]],
						});
					}
				}
				else{
					$('#cb_data_id').combogrid({
						value:`${obj.cust_sert_id}`
					});
				}
				
				$("#mohon_id").val(obj.mohon_id);
				$("#mohon_det_id").val(obj.mohon_det_id);
				$("#cust_sert_id").val(obj.cust_sert_id);
				$("#sert_id").val(obj.sert_id);
				$("#nomor_sertifikat").val(obj.jadw_audit_nomor_sertifikat);
				$("#nomor_referensi").val(obj.jadw_audit_nomor_referensi);
				$("#komodt_id").val(obj.komodt_id);
				$("#tipe_komoditi").val(obj.jadw_audit_tipe);
				$("#merk").val(obj.jadw_audit_merk);
				$("#sni").val(obj.jadw_audit_sni);
				$("#ukuran").val(obj.jadw_audit_ukuran);
				$("#standart_acuan").val(obj.jadw_audit_standart_acuan);
				$("#ruang_lingkup").val(obj.jadw_audit_ruang_lingkup);
				$("#kegiatan").val(obj.jadw_audit_kegiatan);
				$("#tujuan_audit").val(obj.jadw_audit_tujuan_audit);
				$("#kode_ea").val(obj.jadw_audit_kode_ea);
				$("#kode_nace").val(obj.jadw_audit_kode_nace);
				$('#kapasitas_produksi').val(`${obj.mohon_kmditi_kapasitas_produksi_tahunan}`);
				$('#satuan').val(`${obj.mohon_kmditi_kapasitas_produksi_tahunan_satuan}`);
			}
		}
		
		function simpanAction() {
			$.messager.progress(); 
			let formDataItem = new FormData();
			formDataItem.append("jadw_audit_jenis", $('input[name=jadw_audit_jenis_tipe]:checked', '#form_id').val());
			formDataItem.append("jadw_audit_id", $("#jadw_audit_id").val());
			formDataItem.append("tipe", $("#tipe").val());
			formDataItem.append("sert_id", $("#sert_id").val());
			formDataItem.append("komodt_id", $("#komodt_id").val())
			formDataItem.append("mohon_id", $("#mohon_id").val());
			formDataItem.append("mohon_det_id", $("#mohon_det_id").val());
			formDataItem.append("cust_sert_id", $("#cust_sert_id").val());
			formDataItem.append("jadw_audit_nomor_sertifikat", $("#nomor_sertifikat").val());
			formDataItem.append("jadw_audit_nomor_referensi", $("#nomor_referensi").val());
			formDataItem.append("jadw_audit_kode_nace", $("#kode_nace").val());
			formDataItem.append("jadw_audit_kode_ea", $("#kode_ea").val());
			formDataItem.append("jadw_audit_standart_acuan", $("#standart_acuan").val());
			formDataItem.append("jadw_audit_ruang_lingkup", $("#ruang_lingkup").val());
			formDataItem.append("jadw_audit_kegiatan", $("#kegiatan").val());
			formDataItem.append("jadw_audit_tujuan_audit", $("#tujuan_audit").val());
			formDataItem.append("jadw_audit_sni", $("#sni").val());
			formDataItem.append("jadw_audit_merk", $("#merk").val());
			formDataItem.append("jadw_audit_tipe", $("#tipe_komoditi").val());
			formDataItem.append("jadw_audit_ukuran", $("#ukuran").val());
			formDataItem.append("mohon_kmditi_kapasitas_produksi_tahunan_satuan", $("#satuan").val());
			formDataItem.append("mohon_kmditi_kapasitas_produksi_tahunan", $("#kapasitas_produksi").val());
			formDataItem.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
			
			$.ajax({
				url: `{{action("$module@update")}}`,
				type: 'post',
				processData: false,
				contentType: false,
				data: formDataItem,
				success: async function (res) {
					toastCenter({
						type: 'success',
						title: res.message
					})

					$('#ttData').datagrid('reload');
					$.messager.progress('close');
					cancelAction();
				},
				error: function (xhr) {
					if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
					else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
					$.messager.progress('close');
				}
			});
		}
		
		function cancelAction() {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$('#jadw_audit_id').val("");
				$('#data_update').val("");
				$('#tipe').val("");
				$("#form-tambah").hide();
				$("#sertifikasi_permohonan").hide();
				$("#sertifikasi_komoditi").hide();

				$(".tab-content").height("100%");
			}, 500);	
			return false;
		}
		
		function editItem(index) {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$("#form-tambah").show();
				$("#sertifikasi_permohonan").hide();
				$("#sertifikasi_komoditi").hide();

				$(".tab-content").height("100%");
				
				var row = $('#ttData').datagrid('getRows')[index];
				const myJSON = JSON.stringify(row); 
				
				$('#jadw_audit_id').val(`${row.jadw_audit_id}`);
				$('#data_update').val(`${myJSON}`);
				$('#tipe').val("update-item-jadwal");
				var $radios = $('input:radio[name=jadw_audit_jenis_tipe]');
				$radios.filter(`[value=${row.jadw_audit_jenis}]`).prop('checked', true);
				setJenisAudit(`${row.jadw_audit_jenis}`);
			}, 500);
		}
		
		function addData() {
			setTimeout(async () => {
				$('#form_id').trigger("reset");
				$("#form-tambah").show();
				$("#sertifikasi_permohonan").show();
				$(".tab-content").height("100%");
				$('#jadw_audit_id').val("");
				$('#data_update').val("");
				$('#tipe').val("update-item-jadwal");
			}, 500);						
		}
		
		function confirmDelete() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Menghapus Data ?`,
                text: "Menghapus data bersifat permanen dan tidak dapat di kembalikan",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					var idData = []; 
					var data = $('#ttData').datagrid('getData');
					var opts = $('#ttData').datagrid('options');
					for (var i = 0; i < data.rows.length; i++) {
						var tr = opts.finder.getTr($('#ttData')[0],i);
						var atLeastOneIsChecked = tr.find('input[type=checkbox]:checked').length > 0;
						if(atLeastOneIsChecked == true){
							idData.push(data.rows[i].jadw_audit_id);
						}
					}
                    $.ajax({
                        url: `{{url("$url/delete")}}`,
						data: { 'ids[]': idData, 'tipe': 'data-jadwal-audit' },
						type: 'DELETE',
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
        }
		
		
    </script>
@endpush
