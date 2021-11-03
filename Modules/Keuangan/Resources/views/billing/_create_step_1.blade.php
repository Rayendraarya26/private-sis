<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<form action="#">
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Data Pelanggan</label>
			<div class="col-xl-7">
			  <input class="form-control" name="cust_id" id="cust_id" aria-describedby="cust_idHelp" style="min-width:300px;">
				<small id="cust_idHelp" class="form-text">Note: Silahkan pilih pelanggan.</small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="bill_nomor_billing" >Nomor Billing</label>
			<div class="col-xl-5">
			  <input type="text" class="form-control" id="bill_nomor_billing" v-model="bill_nomor_billing">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="bill_billing_date">Tanggal Billing</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="bill_billing_date" style="max-width:300px;">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="bill_due_date">Due Date/Tanggal Jatuh Tempo</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="bill_due_date" style="max-width:300px;">
			</div>
		</div>
		</form>
	</div>
</div>

@push('javascript')
    <script>
        // Vue Step One
		
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
		
        $(document).ready(function () {
            window.vueStepOne = new Vue({
                el: "#vueStepOne",
                data: {
                    cust_id: null,
                    cust_nama: null,
                    bill_nomor_billing: null,
                    bill_billing_date: null,
                    bill_due_date: null,
                },
                mounted() {
                    this.loadIdb();
                },
				watch: {
					bill_nomor_billing: function(val, oldVal) {
						this.setBillNomor(val);
					}
				},
                methods: {
                    setCustId(id) {
						this.cust_id = id
					},
					setBillingDate(data) {
						this.bill_billing_date = data
					},
					setDueDate(data) {
						this.bill_due_date = data
					},
					async setBillNomor(val) {
						this.bill_nomor_billing = val;
						const currentaData = await idb.bill_data.where({name: "billing"}).first();
						let dbData = {name: "billing", value: { cust_id:currentaData.value.cust_id, cust_nama: currentaData.value.cust_nama, bill_nomor_billing: val, bill_billing_date: currentaData.value.bill_billing_date, bill_due_date: currentaData.value.bill_due_date,}}
						if (currentaData == null) {
							await idb.bill_data.put(dbData);
						} else {
							await idb.bill_data.update(currentaData.id, dbData);
						}
					},
                    validate() {
                        if (this.cust_id == null) throw "Pilih Pelanggan"
                        if ($('#bill_nomor_billing').val() === '') throw "Isi Nomor Billing"
                        if (this.bill_billing_date == null) throw "Isi Tanggal Billing"
                        if (this.bill_due_date == null) throw "Isi Tanggal Jatuh Tempo"
                    },
                    async loadIdb() {
						let currentData = await idb.bill_data
                                .where({name: "billing"})
                                .first()
						
						await this.setForm()
                    },
                    async setForm() {
                        let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid-pelanggan") }}`
                        if (self.cust_id != null) {
                            url += "&q=" + this.cust_nama;
                        }
						
						const currentaData = await idb.bill_data
                            .where({name: "billing"})
                            .first();
							
						let dbData;
                        if (currentaData == null) {
							dbData = {name: "billing", value: { cust_id: null, cust_nama: null, bill_nomor_billing: null, bill_billing_date: null, bill_due_date: null,}};
                            await idb.bill_data.put(dbData);
                        } else {
							self.setBillingDate(currentaData.value.bill_billing_date);
							self.setDueDate(currentaData.value.bill_due_date);
							self.setCustId(currentaData.value.cust_id);
							
							$('#bill_nomor_billing').val(currentaData.value.bill_nomor_billing)
							dbData = {name: "billing", value: { cust_id: currentaData.value.cust_id, cust_nama: currentaData.value.cust_nama, bill_nomor_billing: currentaData.value.bill_nomor_billing, bill_billing_date: currentaData.value.bill_billing_date, bill_due_date: currentaData.value.bill_due_date,}}
                            await idb.bill_data.update(currentaData.id, dbData);
                        }
						
						$('#bill_due_date').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.bill_due_date,
							onSelect: async function(date){
								self.setBillingDate(date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate());
								
								const currentaData = await idb.bill_data.where({name: "billing"}).first();
                                let dbData = {name: "billing", value: { cust_id:currentaData.value.cust_id, cust_nama: currentaData.value.cust_nama, bill_nomor_billing: currentaData.value.bill_nomor_billing, bill_billing_date: currentaData.value.bill_billing_date, bill_due_date: date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate(),}}
                                if (currentaData == null) {
                                    await idb.bill_data.put(dbData);
                                } else {
                                    await idb.bill_data.update(currentaData.id, dbData);
                                }
							}
						});
						
						$('#bill_billing_date').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.bill_billing_date,
							onSelect: async function(date){
								self.setDueDate(date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate());
								
								const currentaData = await idb.bill_data.where({name: "billing"}).first();
                                let dbData = {name: "billing", value: { cust_id:currentaData.value.cust_id, cust_nama: currentaData.value.cust_nama, bill_nomor_billing: currentaData.value.bill_nomor_billing, bill_billing_date: date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate(), bill_due_date: currentaData.value.bill_due_date,}}
                                if (currentaData == null) {
                                    await idb.bill_data.put(dbData);
                                } else {
                                    await idb.bill_data.update(currentaData.id, dbData);
                                }
							}
						});
						

                        $('#cust_id').combogrid({
                            pageSize: '50',
                            panelWidth: 400,
                            pagination: true,
                            nowrap: false,
                            idField: 'cust_id',
                            textField: 'cust_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            value: self.cust_nama,
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'cust_id', hidden: true},
                                {field: 'cust_nama', title: 'Nama Pelanggan', width: 390, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
                                self.setCustId(row.cust_id);
								
								const currentaData = await idb.bill_data.where({name: "billing"}).first();
                                let dbData = {name: "billing", value: { cust_id:row.cust_id, cust_nama: row.cust_nama, bill_nomor_billing: currentaData.value.bill_nomor_billing, bill_billing_date: currentaData.value.bill_billing_date, bill_due_date: currentaData.value.bill_due_date,}}
                                if (currentaData == null) {
                                    await idb.bill_data.put(dbData);
                                } else {
                                    await idb.bill_data.update(currentaData.id, dbData);
                                }
                            },
                        });
						
                        $('#cust_id').combogrid('setValue', this.cust_id);
                    },
                }
            })
        })
    </script>
@endpush
