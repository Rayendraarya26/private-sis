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
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Tipe Jadwal</label>
							<div class="col-xl-9">
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="surveilans" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe4" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('surveilans')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe4">Surveilans</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe2" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe2">Sertifikasi</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="re-sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe3" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('re-sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe3">Re-Sertifikasi</label>
								  </div>
								<small id="jadw_audit_jenis_tipeHelp" class="form-text">Note: Silahkan pilih tipe audit items.</small>
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cb_data_id" >Data Permohonan/Sertifikat</label>
							<div class="col-xl-8">
								<input type="text" class="form-control" id="cb_data_id">
								<small class="form-text">Note: Data re-sertifikasi dan sertifikasi untuk data permohonan; Data surveilans untuk data sertifikat.</small>
								
								<input type="hidden" id="mohon_id" value="">
								<input type="hidden" id="mohon_det_id" value="">
								<input type="hidden" id="sert_id" value="">
								<input type="hidden" id="sert_nama" value="">
								<input type="hidden" id="cust_sert_id" value="">
								<input type="hidden" id="nomor_sertifikat" value="">
								<input type="hidden" id="nomor_referensi" value="">
						
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Komoditi</label>
							<div class="col-xl-8">
								<input class="form-control" id="cb_komoditi" value="">
								<input type="hidden" id="mohon_komoditi_id" value="">
								<input type="hidden" id="komodt_id" value="">
								<input type="hidden" id="komodt_nama" value="">
								<input type="hidden" id="tipe" value="">
								<input type="hidden" id="merk" value="">
								<input type="hidden" id="sni" value="">
								<input type="hidden" id="ukuran" value="">
								<input type="hidden" id="satuan" value="">
								<input type="hidden" id="kapasitas_produksi" value="">
								
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi1">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode NACE</label>
							<div class="col-xl-8">
								<input id="kode_nace" value="">
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi2">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode EA</label>
							<div class="col-xl-8">
								<input id="kode_ea" value="">
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
							
						<div class="col-md-12 komoditi-button">
							<template v-if="form_type == 'add'">
								<a href="#" class="btn btn-sm btn-success" @click="addItem">
									<i class="fas fa-plus"></i> Simpan
								</a>
								<a href="#" class="btn btn-sm btn-danger" @click="cancelAction">
									<i class="fas fa-close"></i> Batal
								</a>
							</template>
							<template v-else>
								<a href="#" class="btn btn-sm btn-primary" @click="updateItem">
									<i class="fas fa-save"></i> Ubah
								</a>
								<a href="#" class="btn btn-sm btn-danger" @click="cancelAction">
									<i class="fas fa-close"></i> Batal
								</a>
							</template>
						</div>
						<hr/>
				</div>
			</div>
            <div class="col-md-12">
				<div id="toolbar" style="padding: 10px 0 10px 20px">
					<div class="row">
						@if(authorized("{$module}@create"))
							<div>
								<a href="#" class="btn btn-outline-info btn-xs" @click="tambahData">
									<i class="fas fa-plus"></i> Input Data Detail
								</a>
							</div>
						@endif
					</div>
				</div>
                <div class="table-responsive">
					<table class="table table-bordered mb-0">
                        <thead>
							<tr>
								<th>Aksi</th>
								<th>Jenis</th>
								<th>Sertifikasi</th>
								<th>Nomor Sertifikat</th>
								<th>Nomor<br>Referensi</th>
								<th>Kode NACE</th>
								<th>Kode EA</th>
								<th>Standart Acuan</th>
								<th>Ruang Lingkup</th>
								<th>Kegiatan</th>
								<th>Tujuan Audit</th>
								<th>Komoditi</th>
								<th>SNI</th>
								<th>Merk</th>
								<th>Tipe</th>
								<th>Ukuran</th>
							</tr>
                        </thead>
                        <tbody>
							<template v-for="(itm, idx) in jadwal_items">
								<tr>
									<td>
										<div class="btn-group" role="group">
											<button id="btnGroupDrop@{{ itm.id }}" type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
											  <a class="dropdown-item" href="javascript:void(0)" @click="editItem(itm.id)"> <i class="fad fa-pencil"></i> Edit</a>
											  <a class="dropdown-item" href="javascript:void(0)" @click="deleteItem(itm.id)"> <i class="fad fa-trash"></i> Hapus</a>
											</div>
										</div>
									</td>
									<td>@{{ itm.jenis }}</td>
									<td>@{{ itm.sert_nama }}</td>
									<td>@{{ itm.nomor_sertifikat }}</td>
									<td>@{{ itm.nomor_referensi }}</td>
									<td>@{{ itm.kode_nace }}</td>
									<td>@{{ itm.kode_ea }}</td>
									<td>@{{ itm.standart_acuan }}</td>
									<td>@{{ itm.ruang_lingkup }}</td>
									<td>@{{ itm.kegiatan }}</td>
									<td>@{{ itm.tujuan_audit }}</td>
									<td>@{{ itm.komodt_nama }}</td>
									<td>@{{ itm.sni }}</td>
									<td>@{{ itm.merk }}</td>
									<td>@{{ itm.tipe }}</td>
									<td>@{{ itm.ukuran }}</td>
								</tr>
							</template>
                        </tbody>
                    </table>
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
					form_type: "add",
                    form_edited_id: null,
                    status_submit: false,
                    loading_submit: false,
					
                    jadwal_items: [], // upload to server
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
								const currentaData = await idb.jadwal_data.where({name: "penjadwalan"}).first();
								if (currentaData != null) {
									formData.append("jadw_tanggal_status", 'on-going')
									formData.append("jadw_tanggal_mulai", currentaData.tanggal_mulai)
									formData.append("jadw_tanggal_selesai", currentaData.tanggal_selesai)
									formData.append("jadw_jenis", currentaData.jenis)
									formData.append("bill_id", currentaData.bill_id)
									formData.append("cust_id", currentaData.cust_id)
								}
								// Step 2
								const dataItems = this.jadwal_items;
								formData.append("jadwal_items", JSON.stringify(dataItems))

								
								// Submit Permohonan
								this.loading_submit = true;
								let self = this;
								$.ajax({
									url: `{{action("$module@store")}}`,
									type: 'post',
									processData: false,
									contentType: false,
									data: formData,
									success: async function (res) {
										toastCenter({
											type: 'success',
											title: res.message
										})

										await window.idb.jadwal_data.clear();
										await window.idb.jadwal_data_itms.clear();
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
                        let currentData = await idb.jadwal_data
							.where({name: "penjadwalan"})
							.first();
						
						if (currentData != null) {
							setTimeout(async () => {
								this.jadwal_items = await window.idb.jadwal_data_itms.toArray();
								if (typeof this.jadwal_items !== 'undefined' && this.jadwal_items.length > 0) {
									console.log(this.jadwal_items);
									this.status_submit = true;
								}
								else{
									this.status_submit = false;
								}
							}, 500);
							
							setTimeout(async () => {
								$(".tab-content").height("100%");
							}, 1000);
						}						
                    },
					async cancelAction() {
						setTimeout(async () => {
							this.form_type = 'add';
							this.form_edited_id = null;
							$('#form_id').trigger("reset");
							$("#form-tambah").hide();
							$(".tab-content").height("100%");
						}, 500);	
						return false;
                    },
					async tambahData() {
						setTimeout(async () => {
							// Load to set initial Index DB
							$('#form_id').trigger("reset");
							this.form_type = 'add';
							this.form_edited_id = null;
							$("#form-tambah").show();
							$("#sertifikasi_komoditi").hide();
							$("#sertifikasi_komoditi1").hide();
							$("#sertifikasi_komoditi2").hide();

							$(".tab-content").height("100%");
						}, 500);						
					},
					async setJenisAudit(dt_jenis){
						$("#mohon_id").val("");
						$("#mohon_det_id").val("");
						$("#cust_sert_id").val("");
						$("#sert_id").val("");
						$("#sert_nama").val("");
						$("#nomor_sertifikat").val("");
						$("#nomor_referensi").val("");
						$("#mohon_komoditi_id").val("");
						$("#komodt_id").val("");
						$("#komodt_nama").val("");
						$("#tipe").val("");
						$("#merk").val("");
						$("#sni").val("");
						$("#ukuran").val("");
						$("#standart_acuan").val("");
						$("#ruang_lingkup").val("");
						$("#kegiatan").val("");
						$("#tujuan_audit").val("");
						$("#kode_ea").val("");
						$("#kode_nace").val("");
						if(dt_jenis === 'sertifikasi'){
							$("#sertifikasi_komoditi").hide();
							// $("#sertifikasi_komoditi1").show();
							// $("#sertifikasi_komoditi2").show();
						}
						else{
							$("#sertifikasi_komoditi").hide();
							// $("#sertifikasi_komoditi1").hide();
							// $("#sertifikasi_komoditi2").hide();
						}
						
						let urlCombo = ``;	
						$('#cb_data_id').combogrid({
							url: urlCombo,
							value: ``,
						});
						
						const currentaData = await idb.jadwal_data.where({name: "penjadwalan"}).first();
						if(currentaData != null){
							if(dt_jenis !== 'surveilans'){
								urlCombo = `{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id=${currentaData.cust_id}&jenis_status=${dt_jenis}`;
								$('#cb_data_id').combogrid({
									pageSize: '50', panelWidth: 650, pagination: true, idField: 'mohon_det_id', nowrap: false, textField: 'nama', editable: true, url: urlCombo, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: false, required: false,
									columns: [[
										{field: 'mohon_jenis_status', title: 'Jenis<br>Permohonan', width: 120, sortable: true,},
										{field: 'id', title: 'No.<br>Permohonan', width: 120, sortable: true,},
										{field: 'nama', title: 'Permohonan', width: 250, sortable: true,},
									]],
									onSelect: function (index, row) {
										$("#cust_sert_id").val("");
										$("#sert_id").val(row.sert_id);
										$("#sert_nama").val(row.sert_nama);
										$("#mohon_id").val(row.mohon_id);
										$("#mohon_det_id").val(row.mohon_det_id);
										
										if(row.cust_sert_id != ''){
											$("#cust_sert_id").val(row.cust_sert_id);
										}
										if(dt_jenis === 're-sertifikasi'){
											$("#nomor_sertifikat").val(row.nomor_sertifikat);
											$("#nomor_referensi").val(row.nomor_referensi);
											$("#mohon_komoditi_id").val("");
											$("#komodt_id").val(row.komodt_id);
											$("#komodt_nama").val(row.komodt_nama);
											$("#tipe").val(row.tipe);
											$("#merk").val(row.merk);
											$("#sni").val(row.nomor_sni);
											$("#ukuran").val("");
											$("#standart_acuan").val("");
											$("#kegiatan").val("");
											$("#tujuan_audit").val("");
											$("#ruang_lingkup").val(row.lingkup);
											$('#kode_ea').val(`${row.kode_ea}`);
											$('#kode_nace').val(`${row.kode_nace}`);
											$('#kapasitas_produksi').val(`${row.produksi_tahunan}`);
											$('#satuan').val(`${row.satuan}`);
										}
										else{
											if(row.sert_is_product === 'ya'){
												$("#sertifikasi_komoditi").show();
												let urlComboKomoditi = `{{ url("$url/ajax?action=combogrid-permohonan-komoditi") }}&mohon_det_id=${row.mohon_det_id}`;
												$('#cb_komoditi').combogrid({
													pageSize: '50', panelWidth: 650, pagination: true, idField: 'mohon_kmditi_id', nowrap: false, textField: 'komodt_nama', editable: true, url: urlComboKomoditi, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: false, required: false,
													columns: [[
														{field: 'mohon_kmditi_id', hidden: true},
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
														$("#tipe").val(rowK.mohon_kmditi_tipe);
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
															$("#tipe").val(res.tipe);
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
								urlCombo = `{{ url("$url/ajax?action=combogrid-sertifikat") }}&cust_id=${currentaData.cust_id}`;
								$('#cb_data_id').combogrid({
									pageSize: '50', panelWidth: 650, pagination: true, idField: 'id', nowrap: false, textField: 'nama', editable: true, url: urlCombo, method: 'get', mode: 'remote', value: '', multiSort: true, fitColumns: false, required: false,
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
										$("#sert_nama").val(row.sert_nama);
										$("#mohon_id").val("");
										$("#mohon_det_id").val("");
										$("#nomor_sertifikat").val(row.nomor_sertifikat);
										$("#nomor_referensi").val(row.nomor_referensi);
										$("#mohon_komoditi_id").val(row.komodt_id);
										$("#komodt_id").val(row.komodt_id);
										$("#komodt_nama").val(row.komodt_nama);
										$("#tipe").val(row.tipe);
										$("#merk").val(row.merk);
										$("#sni").val(row.nomor_sni);
										$("#ukuran").val("");
										$("#standart_acuan").val("");
										$('#kode_ea').val(`${row.kode_ea}`);
										$('#kode_nace').val(`${row.kode_nace}`);
										$("#ruang_lingkup").val(row.lingkup);
										$("#kapasitas_produksi").val(row.produksi_tahunan);
										$("#satuan").val(row.satuan);
										$("#kegiatan").val("");
										$("#tujuan_audit").val("");
									},
								});
							}
							
							if(this.form_edited_id != null){
								let selectedItem = await window.idb.jadwal_data_itms.get(this.form_edited_id);
								if(dt_jenis !== 'surveilans'){
									$('#cb_data_id').combogrid('setValue', `${selectedItem.mohon_det_id}`);
								}
								else{
									$('#cb_data_id').combogrid('setValue', `${selectedItem.cust_sert_id}`);
								}
								
								$("#mohon_id").val(selectedItem.mohon_id);
								$("#mohon_det_id").val(selectedItem.mohon_det_id);
								$("#cust_sert_id").val(selectedItem.cust_sert_id);
								$("#sert_id").val(selectedItem.sert_id);
								$("#sert_nama").val(selectedItem.sert_nama);
								$("#nomor_sertifikat").val(selectedItem.nomor_sertifikat);
								$("#nomor_referensi").val(selectedItem.nomor_referensi);
								$("#komodt_id").val(selectedItem.komodt_id);
								$("#komodt_nama").val(selectedItem.komodt_nama);
								$("#tipe").val(selectedItem.tipe);
								$("#merk").val(selectedItem.merk);
								$("#sni").val(selectedItem.sni);
								$("#ukuran").val(selectedItem.ukuran);
								$("#standart_acuan").val(selectedItem.standart_acuan);
								$("#ruang_lingkup").val(selectedItem.ruang_lingkup);
								$("#kegiatan").val(selectedItem.kegiatan);
								$("#tujuan_audit").val(selectedItem.tujuan_audit);
								$("#kode_ea").val(selectedItem.kode_ea);
								$("#kode_nace").val(selectedItem.kode_nace);
								$("#satuan").val(selectedItem.satuan);
								$("#kapasitas_produksi").val(selectedItem.kapasitas_produksi);
								$("#mohon_komoditi_id").val(selectedItem.mohon_komoditi_id);
								if(dt_jenis !== 'surveilans'){
									if(selectedItem.mohon_komoditi_id != ''){
										$("#sertifikasi_komoditi").show();
										let urlComboKomoditi = `{{ url("$url/ajax?action=combogrid-permohonan-komoditi") }}&mohon_det_id=${selectedItem.mohon_det_id}`;
										$('#cb_komoditi').combogrid({
											pageSize: '50', panelWidth: 650, pagination: true, idField: 'mohon_kmditi_id', nowrap: false, textField: 'komodt_nama', editable: true, url: urlComboKomoditi, method: 'get', mode: 'remote', value: `${selectedItem.mohon_komoditi_id}`, multiSort: true, fitColumns: false, required: false,
											columns: [[
												{field: 'komodt_id', hidden: true},
												{field: 'komodt_nama', title: 'Komoditi', width: 250, sortable: true,},
												{field: 'mohon_kmditi_sni', title: 'SNI', width: 100, sortable: true,},
												{field: 'mohon_kmditi_merk', title: 'Merk', width: 100, sortable: true,},
												{field: 'mohon_kmditi_tipe', title: 'Tipe', width: 100, sortable: true,},
												{field: 'mohon_kmditi_ukuran', title: 'Ukuran', width: 100, sortable: true,},
											]],
											onSelect: function (index, rowK) {
												$("#mohon_komoditi_id").val(rowK.mohon_komoditi_id);
												$("#komodt_id").val(rowK.komodt_id);
												$("#komodt_nama").val(rowK.komodt_nama);
												$("#tipe").val(rowK.mohon_kmditi_tipe);
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
								}
							}
						}
					},
					validateItem() {
                        	
                    },
					async deleteItem(id) {
                        let selectedItem = await window.idb.jadwal_data_itms.get(id);
                        swalWithBootstrapButtons({
                            title: `Hapus Item ?`,
                            text: `Anda yakin menghapus data audit ini ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								await window.idb.jadwal_data_itms.where('id').equals(id).delete();
								this.jadwal_items = await window.idb.jadwal_data_itms.toArray();
								if (typeof this.jadwal_items !== 'undefined' && this.jadwal_items.length > 0) {
									this.status_submit = true;
								}
								else{
									this.status_submit = false;
								}
                            }
                        });
                    },
					async editItem(id) {
						setTimeout(async () => {
							// Load to set initial Index DB
							$('#form_id').trigger("reset");
							this.form_type = 'update';
							this.form_edited_id = id;
							$("#form-tambah").show();
							$("#sertifikasi_komoditi").hide();
							$("#sertifikasi_komoditi1").hide();
							$("#sertifikasi_komoditi2").hide();

							$(".tab-content").height("100%");
							
							let selectedItem = await window.idb.jadwal_data_itms.get(id);
							var $radios = $('input:radio[name=jadw_audit_jenis_tipe]');
							$radios.filter(`[value=${selectedItem.jenis}]`).prop('checked', true);
							console.log(selectedItem);
							await this.setJenisAudit(selectedItem.jenis);
						}, 500);
					},
					async updateItem() {
						if(this.form_edited_id != null){
							let selectedItem = await window.idb.jadwal_data_itms.get(this.form_edited_id);
							try {
								this.validateItem()
								await window.idb.jadwal_data_itms.update(this.form_edited_id, {
									"jenis": $('input[name=jadw_audit_jenis_tipe]:checked', '#form_id').val(),
									"mohon_id": $("#mohon_id").val(),
									"mohon_det_id": $("#mohon_det_id").val(),
									"cust_sert_id": $("#cust_sert_id").val(),
									"sert_id": $("#sert_id").val(),
									"sert_nama": $("#sert_nama").val(),
									"nomor_sertifikat": $("#nomor_sertifikat").val(),
									"nomor_referensi": $("#nomor_referensi").val(),
									"komodt_id": $("#komodt_id").val(),
									"komodt_nama": $("#komodt_nama").val(),
									"tipe": $("#tipe").val(),
									"merk": $("#merk").val(),
									"sni": $("#sni").val(),
									"ukuran": $("#ukuran").val(),
									"standart_acuan": $("#standart_acuan").val(),
									"ruang_lingkup": $("#ruang_lingkup").val(),
									"kegiatan": $("#kegiatan").val(),
									"tujuan_audit": $("#tujuan_audit").val(),
									"kode_ea": $('#kode_ea').val(),
									"kode_nace": $('#kode_nace').val(),
									"satuan": $('#satuan').val(),
									"kapasitas_produksi": $('#kapasitas_produksi').val(),
									"mohon_komoditi_id": $("#mohon_komoditi_id").val(),
								});
								this.jadwal_items = await window.idb.jadwal_data_itms.toArray();
								await this.cancelAction();
								
							} catch (message) {
								swalWithBootstrapButtons({
									title: `Validasi Item`,
									text: message,
									type: 'warning',
								})
							}
							
						}
						else{
							await this.cancelAction();
						}
					},
					async addItem() {
						try {
                            this.validateItem()
                            let newItem = {
                                "jenis": $('input[name=jadw_audit_jenis_tipe]:checked', '#form_id').val(),
                                "mohon_id": $("#mohon_id").val(),
                                "mohon_det_id": $("#mohon_det_id").val(),
								"cust_sert_id": $("#cust_sert_id").val(),
								"sert_id": $("#sert_id").val(),
								"sert_nama": $("#sert_nama").val(),
								"nomor_sertifikat": $("#nomor_sertifikat").val(),
								"nomor_referensi": $("#nomor_referensi").val(),
								"komodt_id": $("#komodt_id").val(),
								"komodt_nama": $("#komodt_nama").val(),
								"tipe": $("#tipe").val(),
								"merk": $("#merk").val(),
								"sni": $("#sni").val(),
								"ukuran": $("#ukuran").val(),
								"standart_acuan": $("#standart_acuan").val(),
								"ruang_lingkup": $("#ruang_lingkup").val(),
								"kegiatan": $("#kegiatan").val(),
								"tujuan_audit": $("#tujuan_audit").val(),
								"kode_ea": $('#kode_ea').val(),
								"kode_nace": $('#kode_nace').val(),
								"satuan": $('#satuan').val(),
								"kapasitas_produksi": $('#kapasitas_produksi').val(),
								"mohon_komoditi_id": $("#mohon_komoditi_id").val(),
                            };
							
                            await idb.jadwal_data_itms.put(newItem);
							this.jadwal_items = await window.idb.jadwal_data_itms.toArray();
							if (typeof this.jadwal_items !== 'undefined' && this.jadwal_items.length > 0) {
								this.status_submit = true;
							}
							else{
								this.status_submit = false;
							}
                            await this.cancelAction();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Item`,
                                text: message,
                                type: 'warning',
                            })
                        }
					},
                }
            })
        })
    </script>
@endpush
