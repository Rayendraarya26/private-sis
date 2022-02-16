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
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Pegawai</label>
							<div class="col-xl-8">
								<input class="form-control" id="peg_id" value="">
								<input type="hidden" class="form-control" id="peg_nama">
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode</label>
							<div class="col-xl-8">
								<input type="text" class="form-control" id="kode">
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Posisi</label>
							<div class="col-xl-8">
								<select class="form-control" id="posisi">
									<option value=""></option>
									<option value="ketua">Ketua</option>
									<option value="anggota">Anggota</option>
									<option value="observer">Observer</option>
								</select>
							</div>
						</div>
							
						<div class="col-md-12 komoditi-button">
							<template v-if="form_type == 'add'">
								<a href="#" class="btn btn-sm btn-success" @click="addTim">
									<i class="fas fa-plus"></i> Simpan
								</a>
								<a href="#" class="btn btn-sm btn-danger" @click="cancelAction">
									<i class="fas fa-close"></i> Batal
								</a>
							</template>
							<template v-else>
								<a href="#" class="btn btn-sm btn-primary" @click="updateTim">
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
									<i class="fas fa-plus"></i> Input Tim
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
								<th>Kode</th>
								<th>Nama</th>
								<th>Posisi</th>
							</tr>
                        </thead>
                        <tbody>
							<template v-for="(itm, idx) in jadwal_tims">
								<tr>
									<td>
										<div class="btn-group" role="group">
											<button id="btnGroupDrop@{{ itm.id }}" type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
											  <a class="dropdown-item" href="javascript:void(0)" @click="editTim(itm.id)"> <i class="fad fa-pencil"></i> Edit</a>
											  <a class="dropdown-item" href="javascript:void(0)" @click="deleteTim(itm.id)"> <i class="fad fa-trash"></i> Hapus</a>
											</div>
										</div>
									</td>
									<td>@{{ itm.kode }}</td>
									<td>@{{ itm.peg_nama }}</td>
									<td>@{{ itm.posisi }}</td>
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
                    mohon_det_id: null,
					
                    jadwal_tims: [],
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
								const currentaData = await idb.tahap1_data.where({name: "penjadwalan"}).first();
								if (currentaData != null) {
									formData.append("aud_thp1_tanggal_mulai", currentaData.tanggal_mulai)
									formData.append("aud_thp1_tanggal_selesai", currentaData.tanggal_selesai)
									formData.append("cust_id", currentaData.cust_id)
									formData.append("mohon_id", currentaData.mohon_id)
									formData.append("mohon_det_id", currentaData.mohon_det_id)
									formData.append("sert_tahap1_jenis", currentaData.jenis_sertifikasi)
									formData.append("bill_id", currentaData.bill_id)
									formData.append("aud_thp1_tujuan", currentaData.tujuan)
									formData.append("aud_thp1_standart_acuan", currentaData.standart)
								}
								// Step 2
								const dataTims = this.jadwal_tims;
								formData.append("jadwal_tims", JSON.stringify(dataTims))
								
								console.log(dataTims);
								
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
										});
										
										await idb.tahap1_data.clear();
										await idb.tahap1_data_tim.clear();
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
                        let currentData = await idb.tahap1_data
							.where({name: "penjadwalan"})
							.first();
						
						if (currentData != null) {
							setTimeout(async () => {
								this.mohon_det_id = currentData.mohon_det_id;
								this.jadwal_tims = await window.idb.tahap1_data_tim.toArray();
								if (typeof this.jadwal_tims !== 'undefined' && this.jadwal_tims.length > 0) {
									console.log(this.jadwal_tims);
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
							await this.setForm();
							$("#form-tambah").show();
							$(".tab-content").height("100%");
						}, 500);						
					},
					async setForm(){
						 $('#peg_id').combogrid({
                            pageSize: '50',
                            panelWidth: 600,
                            pagination: true,
                            nowrap: false,
                            idField: 'peg_id',
                            textField: 'peg_nama',
                            editable: true,
							url:`{{ url("$url/ajax?action=combogrid-pegawai") }}&mohon_det_id=${this.mohon_det_id}`,
                            method: 'get',
                            mode: 'remote',
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'peg_id', hidden: true},
                                {field: 'peg_nip', title: 'NIP', width: 200, sortable: true,},
                                {field: 'peg_kode', title: 'Kode', width: 120, sortable: true,},
                                {field: 'peg_nama', title: 'Nama', width: 390, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
								$("#peg_nama").val(`${row.peg_nama}`);
								$("#kode").val(`${row.peg_kode}`);
                            },
                        });
						
						if(this.form_edited_id != null){
							let selectedItem = await window.idb.tahap1_data_tim.get(this.form_edited_id);
							$('#peg_id').combogrid('setValue', selectedItem.peg_id);
							$("#peg_nama").val(`${selectedItem.peg_nama}`);
							$("#kode").val(`${selectedItem.kode}`);
							$("#posisi").val(`${selectedItem.posisi}`);
						}
					},
					validateTim() {
                        	
                    },
					async deleteTim(id) {
                        let selectedTim = await window.idb.tahap1_data_tim.get(id);
                        swalWithBootstrapButtons({
                            title: `Hapus Tim ?`,
                            text: `Anda yakin menghapus data tim ini ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								await window.idb.tahap1_data_tim.where('id').equals(id).delete();
								this.jadwal_tims = await window.idb.tahap1_data_tim.toArray();
								if (typeof this.jadwal_tims !== 'undefined' && this.jadwal_tims.length > 0) {
									this.status_submit = true;
								}
								else{
									this.status_submit = false;
								}
                            }
                        });
                    },
					async editTim(id) {
						setTimeout(async () => {
							// Load to set initial Index DB
							$('#form_id').trigger("reset");
							this.form_type = 'update';
							this.form_edited_id = id;
							$("#form-tambah").show();
							$(".tab-content").height("100%");
							await this.setForm();
						}, 500);
					},
					async updateTim() {
						if(this.form_edited_id != null){
							let selectedTim = await window.idb.tahap1_data_tim.get(this.form_edited_id);
							try {
								this.validateTim()
								await window.idb.tahap1_data_tim.update(this.form_edited_id, {
									"peg_id": $('#peg_id').combogrid('getValue'),
									"peg_nama": $("#peg_nama").val(),
									"kode": $("#kode").val(),
									"posisi": $("#posisi").val(),
								});
								this.jadwal_tims = await window.idb.tahap1_data_tim.toArray();
								await this.cancelAction();
								
							} catch (message) {
								swalWithBootstrapButtons({
									title: `Validasi Tim`,
									text: message,
									type: 'warning',
								})
							}
							
						}
						else{
							await this.cancelAction();
						}
					},
					async addTim() {
						try {
                            this.validateTim()
                            let newTim = {
								"peg_id": $('#peg_id').combogrid('getValue'),
								"peg_nama": $("#peg_nama").val(),
								"kode": $("#kode").val(),
								"posisi": $("#posisi").val(),
                            };
							
                            await idb.tahap1_data_tim.put(newTim);
							this.jadwal_tims = await window.idb.tahap1_data_tim.toArray();
							if (typeof this.jadwal_tims !== 'undefined' && this.jadwal_tims.length > 0) {
								this.status_submit = true;
							}
							else{
								this.status_submit = false;
							}
                            await this.cancelAction();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Tim`,
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
