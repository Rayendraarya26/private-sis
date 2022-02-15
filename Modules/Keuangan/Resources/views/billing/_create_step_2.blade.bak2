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
										<input value="permohonan" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe1" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe1">Permohonan</label>
									  </div>

									  <!-- Radio Button -->
									  <div class="custom-control custom-radio custom-control-inline">
										<input value="surveilans" aria-describedby="itms_bil_tipeHelp" type="radio" id="itms_bil_tipe4" name="itms_bil_tipe" class="custom-control-input" @click="setComboDataPermohonan()">
										<label class="custom-control-label" for="itms_bil_tipe4">Surveilans</label>
									  </div>
									  <!-- /radio button -->
									<small id="itms_bil_tipeHelp" class="form-text">Note: Silahkan pilih tipe billing items.</small>
								</div>
							</div>
							<div class="form-group form-row" id="data_permohonan" style="display:none;">
								<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id" >Data Permohonan/Sertifikat</label>
								<div class="col-xl-8">
								  <input type="text" class="form-control" id="mohon_id">
								  <small id="itms_bil_tipeHelp" class="form-text">Note: Data re-sertifikasi dan sertifikasi untuk data permohonan; Data surveilans untuk data sertifikat.</small>
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
					</div>
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
                    mohon_det_id: null,
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
									$(".tab-content").height("100%");
								}, 500)
                            }
							
							$(".tab-content").height("100%");
                        }, 500)
						
                    },
                    validate() {
                        let itms_bil_desc = $.trim($("#itms_bil_desc").val());
                        let itms_bil_total = $.trim($("#itms_bil_total").val());
                        let itms_bil_tipe = $('input[name=itms_bil_tipe]:checked', '#myForm').val();
						
                        if (itms_bil_tipe === "") throw "Tipe Item Belum dipilih";
                        if (itms_bil_total === "") throw "Tuliskan Total(Rp.)";
                        if (itms_bil_desc === "") throw "Tuliskan deskripsi";
						if (this.mohon_id === "") throw "Pilih Data permohonan";
                    },
					async setComboDataPermohonan(){
						let self = this;
                        let itms_bil_tipe = $('input[name=itms_bil_tipe]:checked', '#myForm').val();
						$(".tab-content").height("100%");
						
						const currentaData = await idb.bill_data
                            .where({name: "billing"})
                            .first();
						
						if(currentaData != null){
							let url = ``;
							this.mohon_id = null;
							this.mohon_det_id = null;
							this.bil_lunas = null;
							$("#itms_bil_desc").val('');
							$("#itms_bil_total").val('');
										
							if(itms_bil_tipe === 'permohonan'){
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
										{field: 'id', title: 'No.<br/>Permohonan', width: 100, sortable: true,},
										{field: 'nama', title: 'Permohonan', width: 250, sortable: true,},
									]],
									onSelect: function (index, row) {
										self.mohon_id = row.id;
										self.mohon_det_id = null;
										self.bil_lunas = row.mohon_harus_lunas_status;
										self.mohon_text = row.deskripsi;
										$("#itms_bil_desc").val(row.deskripsi)
										$("#itms_bil_total").val(`${row.mohon_harga_permohonan}`)
									},
								});
							}
							else if(itms_bil_tipe === 'surveilans'){
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
										{field: 'nomor_referensi', title: 'No. Referensi', width: 250, sortable: true,},
										{field: 'cust_sert_tgl_sertifikat_awal', title: 'Tgl. Awal', width: 100, sortable: true,},
										{field: 'cust_sert_tgl_sertifikat_perubahan', title: 'Tgl. Perubahan', width: 100, sortable: true,},
									]],
									onSelect: function (index, row) {
										self.mohon_id = row.id;
										self.mohon_det_id = null;
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
								this.mohon_det_id = null;
								this.bil_lunas = null;
							}
						}
                        
					},
                }
            })
        })
    </script>
@endpush
