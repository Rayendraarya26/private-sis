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
                <h3>Form Input Billing Items <small aria-label="Setiap perubahan akan tersimpan di storage browser"
                                          class="custom-cooltipz"
                                          data-cooltipz-size="large"
                                          data-cooltipz-dir="right"><i class="fal fa-database"></i></small></h3>
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" id="myForm">
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Tipe Item</label>
								<div class="col-xl-9">
									<!-- Radio Button -->
									  <div class="custom-control custom-radio custom-control-inline">
										<input value="lain-lain" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe1" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe1">Lain-Lain</label>
									  </div>
									  <!-- /radio button -->

									  <!-- Radio Button -->
									  <div class="custom-control custom-radio custom-control-inline">
										<input value="sertifikasi" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe2" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe2">Sertifikasi</label>
									  </div>
									  <!-- /radio button -->

									  <!-- Radio Button -->
									  <div class="custom-control custom-radio custom-control-inline">
										<input value="re-sertifikasi" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe3" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe3">Re-Sertifikasi</label>
									  </div>
									  <!-- /radio button -->

									  <!-- Radio Button -->
									  <div class="custom-control custom-radio custom-control-inline">
										<input value="survailan" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe4" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe4">Survailent</label>
									  </div>
									  <!-- /radio button -->
									<small id="itms_bil_tipeHelp" class="form-text">Note: Silahkan pilih tipe billing items.</small>
								</div>
							</div>
							<div class="form-group form-row" id="data_permohonan" style="display:none;">
								<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >Data Permohonan/Sertifikat</label>
								<div class="col-xl-8">
								  <input type="text" class="form-control" id="mohon_id">
								  <small id="itms_bil_tipeHelp" class="form-text">Note: Data re-sertifikasi dan sertifikasi untuk data permohonan; Data survailan untuk data sertifikat.</small>
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" for="itms_bil_desc">Keterangan Item</label>
								<div class="col-xl-9">
								  <textarea class="form-control" id="itms_bil_desc"></textarea>
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-xl-3 col-form-label text-sm-left" for="itms_bil_total">Harga</label>
								<div class="col-xl-4">
								  <input type="number" class="form-control" id="itms_bil_total">
								</div>
							</div>
							</form>
						<div class="col-md-12 komoditi-button">
							<template v-if="jenis_item_form_type == 'add'">
								<button class="btn btn-sm btn-success" @click="addItem">
									<i class="fas fa-plus"></i> Tambah
								</button>
							</template>
							<template v-else>
								<button class="btn btn-sm btn-primary" @click="updateItem">
									<i class="fas fa-save"></i> Simpan
								</button>
								<button class="btn btn-sm btn-danger" @click="calcelUpdateItem">
									<i class="fas fa-close"></i> Batal
								</button>
							</template>
						</div>
					</div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(itm, idx) in bill_items">
                            <tr>
                                <td>@{{ itm.bil_tipe }}</td>
                                <td>@{{ itm.bil_desc }}</td>
                                <td>@{{ itm.bil_total }}</td>
                                <td>
									<div class="btn-group" role="group">
										<button id="btnGroupDrop@{{ itm.id }}" type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
										<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
										  <a class="dropdown-item" href="javascript:void(0)" @click="editItem(itm.id)"> <i class="fad fa-pencil"></i> Edit</a>
										  <a class="dropdown-item" href="javascript:void(0)" @click="deleteItem(itm.id)"> <i class="fad fa-trash"></i> Hapus</a>
										</div>
									</div>
                                </td>
                            </tr>
                        </template>
                        </tbody>
						<tfoot>
							<tr>
								<th></th>
								<th>Total</th>
								<th><span id="total_biaya"></span></th>
								<th></th>
							</tr>
						</tfoot>
                    </table>
                </div>
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
                    jenis_item_form_type: "add",
                    jenis_item_form_edited_id: null,
					
                    total_biaya: 0,
                    mohon_id: null,
                    bil_lunas: null,
                    mohon_text: "-- Pilih Data --",
                    bill_items: [], // upload to server
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
                    start() {
                        setTimeout(async () => {
                            // Load to set initial Index DB
                            let currentData = await idb.bill_data
                                .where({name: "billing"})
                                .first()

                            if (currentData != null) {								
								setTimeout(async () => {
									this.bill_items = await window.idb.bill_data_itms.toArray();
									this.total_biaya = this.bill_items.reduce(function(sum, current) {
									  return sum + parseInt(current.bil_total);
									}, 0);
									$('#total_biaya').html(this.total_biaya);
								}, 500)
                            }
                        }, 500)
                    },
                    validate() {
                        // if (this.jenis_sertifikasi_id == null) throw "Pilih Jenis Sertifikasi"
                        if (this.bill_items.length === 0) throw "Mohon isikan data item billing"
                    },
					async resetFooterTabel() {
						this.total_biaya = this.bill_items.reduce(function(sum, current) {
						  return sum + parseInt(current.bil_total);
						}, 0);
						$('#total_biaya').html(this.total_biaya);
					},
                    async resetFormItem() {
                        $("#itms_bil_desc").val("");
                        $("#itms_bil_total").val("");
                        await this.setComboDataPermohonan()
                        $("#mohon_id").combogrid('clear');
                        this.jenis_item_form_type = 'add';
                        this.jenis_item_form_edited_id = null;
                        this.mohon_id = null;
                        this.bil_lunas = null;
						$("#data_permohonan").hide();
						var $radios = $('input:radio[name=itms_bil_tipe]');
						if($radios.is(':checked') === false) {
							$radios.filter('[value=lain-lain]').prop('checked', true);
						}
                    },
                    validateItem() {
                        let itms_bil_desc = $.trim($("#itms_bil_desc").val());
                        let itms_bil_total = $.trim($("#itms_bil_total").val());
                        let itms_bil_tipe = $('input[name=itms_bil_tipe]:checked', '#myForm').val();
						
                        if (itms_bil_tipe === "") throw "Tipe Item Belum dipilih";
                        if (itms_bil_total === "") throw "Tuliskan Total(Rp.)";
                        if (itms_bil_desc === "") throw "Tuliskan Ddeskripsi";
						if(itms_bil_tipe === 'sertifikasi' || itms_bil_tipe === 're-sertifikasi'){
							if (mohon_id === "") throw "Pilih Data permohonan";
						}
                    },
                    async addItem() {
                        try {
                            this.validateItem()
                            let newItem = {
                                "mohon_id": this.mohon_id,
                                "bil_lunas": this.bil_lunas,
                                "bil_desc": $.trim($("#itms_bil_desc").val()),
                                "bil_total": $.trim($("#itms_bil_total").val()),
                                "bil_tipe": $('input[name=itms_bil_tipe]:checked', '#myForm').val(),
                            };
							
                            await idb.bill_data_itms.put(newItem);
                            this.bill_items = await window.idb.bill_data_itms.toArray()
                            this.resetFormItem();
                            this.resetFooterTabel();
                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Item`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    async deleteItem(id) {
                        let selectedItem = await window.idb.bill_data_itms.get(id);
                        swalWithBootstrapButtons({
                            title: `Hapus Item ?`,
                            text: `Anda yakin menghapus data item billing ${selectedItem.bil_desc} ?`,
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
                                await window.idb.bill_data_itms.where('id').equals(id).delete();
                                this.bill_items = await window.idb.bill_data_itms.toArray()
								this.resetFooterTabel();
                            }
                        });
                    },
                    async editItem(id) {
                        let selectedItem = await window.idb.bill_data_itms.get(id);
						var $radios = $('input:radio[name=itms_bil_tipe]');
						$radios.filter(`[value=${selectedItem.bil_tipe}]`).prop('checked', true);
						
                        await this.setComboDataPermohonan()
                        $("#mohon_id").combogrid('setValue', selectedItem.mohon_id);
                        this.jenis_item_form_type = "update";
                        this.jenis_item_form_edited_id = selectedItem.id;
						$("#itms_bil_desc").val(selectedItem.bil_desc);
                        $("#itms_bil_total").val(selectedItem.bil_total);
                    },
                    async updateItem() {
                        try {
                            this.validateItem()
                            await window.idb.bill_data_itms.update(this.jenis_item_form_edited_id, {
								"mohon_id": this.mohon_id,
								"bil_lunas": this.bil_lunas,
                                "bil_desc": $.trim($("#itms_bil_desc").val()),
                                "bil_total": $.trim($("#itms_bil_total").val()),
                                "bil_tipe": $('input[name=itms_bil_tipe]:checked', '#myForm').val(),
                            });
                            this.bill_items = await window.idb.bill_data_itms.toArray()
                            this.resetFormItem();
                            this.resetFooterTabel();

                        } catch (message) {
                            swalWithBootstrapButtons({
                                title: `Validasi Item`,
                                text: message,
                                type: 'warning',
                            })
                        }
                    },
                    calcelUpdateItem() {
                        this.jenis_item_form_type = "add";
                        this.resetFormItem();
                    },
					async setComboDataPermohonan(){
						let self = this;
                        let itms_bil_tipe = $('input[name=itms_bil_tipe]:checked', '#myForm').val();
						
						const currentaData = await idb.bill_data
                            .where({name: "billing"})
                            .first();
							
						if(currentaData != null){
							let url = ``;
							this.mohon_id = null;
							this.bil_lunas = null;
							$("#itms_bil_desc").val('');
							$("#itms_bil_total").val('');
										
							if(itms_bil_tipe === 'sertifikasi' || itms_bil_tipe === 're-sertifikasi'){
								$("#data_permohonan").show();
								url = `{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id=${currentaData.value.cust_id}&jenis_status=${itms_bil_tipe}`;
								$('#mohon_id').combogrid({
									pageSize: '50',
									panelWidth: 650,
									pagination: true,
									idField: 'id',
									nowrap: false,
									textField: 'nama',
									editable: true,
									url: url,
									method: 'get',
									mode: 'remote',
									value: '',
									multiSort: true,
									fitColumns: true,
									required: false,
									columns: [[
										{field: 'id', hidden: true},
										{field: 'id', title: 'No.<br/>Permohonan', width: 150, sortable: true,},
										{field: 'nama', title: 'Permohonan', width: 250, sortable: true,},
									]],
									onSelect: function (index, row) {
										self.mohon_id = row.id;
										self.bil_lunas = row.mohon_harus_lunas_status;
										self.mohon_text = row.deskripsi;
										$("#itms_bil_desc").val(row.deskripsi)
										$("#itms_bil_total").val(`${row.mohon_harga_permohonan}`)
									},
								});
							}
							else if(itms_bil_tipe === 'survailan'){
								$("#data_permohonan").show();
								url = `{{ url("$url/ajax?action=combogrid-sertifikat") }}&cust_id=${currentaData.value.cust_id}`;
								$('#mohon_id').combogrid({
									pageSize: '50',
									panelWidth: 650,
									pagination: true,
									idField: 'id',
									nowrap: false,
									textField: 'nama',
									editable: true,
									url: url,
									method: 'get',
									mode: 'remote',
									multiSort: true,
									fitColumns: true,
									required: false,
									value: '',
									columns: [[
										{field: 'id', hidden: true},
										{field: 'nama', title: 'Nama Sertifikat', width: 250, sortable: true,},
										{field: 'cust_sert_nomor_referensi', title: 'No. Referensi', width: 250, sortable: true,},
										{field: 'cust_sert_tgl_sertifikat_awal', title: 'Tgl. Awal', width: 100, sortable: true,},
										{field: 'cust_sert_tgl_sertifikat_perubahan', title: 'Tgl. Perubahan', width: 100, sortable: true,},
									]],
									onSelect: function (index, row) {
										self.mohon_id = row.id;
										self.bil_lunas = 'ya';
										self.mohon_text = row.deskripsi;
										$("#itms_bil_desc").val(row.deskripsi)
										$("#itms_bil_total").val(0)
									},
								});
							}
							else{
								$("#data_permohonan").hide();
								this.mohon_id = null;
								this.bil_lunas = null;
							}
							
							
							
						}
                        
					},
                }
            })
        })
    </script>
@endpush
