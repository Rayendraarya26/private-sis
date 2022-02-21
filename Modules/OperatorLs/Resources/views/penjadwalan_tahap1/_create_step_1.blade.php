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
			<label class="col-xl-3 col-form-label text-sm-left" for="mohon_id">Pilih Data Permohonan</label>
			<div class="col-xl-7">
			  <input class="form-control" name="mohon_id" id="mohon_id" aria-describedby="mohon_idHelp" style="min-width:300px;">
				<small id="cust_idHelp" class="form-text">Note: Silahkan pilih permohonan.</small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="aud_thp1_tanggal_selesai">Tujuan Audit</label>
			<div class="col-xl-7">
			  <textarea class="form-control" id="aud_thp1_tujuan" v-on:keyup="tujuanSet" v-model="aud_thp1_tujuan"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="aud_thp1_tanggal_selesai">Standart Acuan</label>
			<div class="col-xl-7">
			  <textarea class="form-control" id="aud_thp1_standart_acuan" v-on:keyup="standartSet" v-model="aud_thp1_standart_acuan"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="aud_thp1_tanggal_mulai">Tanggal Mulai</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="aud_thp1_tanggal_mulai" style="max-width:300px;">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="aud_thp1_tanggal_selesai">Tanggal Selesai</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="aud_thp1_tanggal_selesai" style="max-width:300px;">
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
                    bill_id: null,
                    mohon_id: null,
                    mohon_det_id: null,
                    jenis_sertifikasi: null,
                    aud_thp1_tanggal_mulai: null,
                    aud_thp1_tanggal_selesai: null,
                    aud_thp1_tujuan: ``,
                    aud_thp1_standart_acuan: ``,
                },
                mounted() {
                    this.loadIdb();
                },
                methods: {
					tujuanSet: async function(event) {
						this.aud_thp1_tujuan = $('#aud_thp1_tujuan').val();
						await this.updateDatabase()
					},
					standartSet: async function(event) {
						this.aud_thp1_standart_acuan = $('#aud_thp1_standart_acuan').val();
						await this.updateDatabase()
					},
					async setCustId(id) {
						this.cust_id = id
						await this.updateDatabase()
					},
					async setBillId(id) {
						this.bill_id = id
						await this.updateDatabase()
					},
					async setMohonId(id) {
						this.mohon_id = id
						await this.updateDatabase()
					},
					async setMohonDetailId(id) {
						this.mohon_det_id = id
						await this.updateDatabase()
					},
					async setJenis(val) {
						this.jenis_sertifikasi = val;
						await this.updateDatabase()
					},
					async setTanggalMulai(date) {
						this.aud_thp1_tanggal_mulai = date;
						await this.updateDatabase()
					},
					async setTanggalSelesai(date) {
						this.aud_thp1_tanggal_selesai = date;
						await this.updateDatabase()
					},
                    validate() {
                        if (this.cust_id == null) throw "Pilih Pelanggan"
                        if (this.bill_id == null) throw "Pilih Pelanggan"
                        if (this.mohon_id == null) throw "Pilih Permohonan"
                        if (this.mohon_det_id == null) throw "Pilih Permohonan"
                        if (this.jenis_sertifikasi == null) throw "Pilih Permohonan"
                        if (this.aud_thp1_tujuan == '' || this.aud_thp1_tujuan == null) throw "Isi Tujuan Audit"
                        if (this.aud_thp1_standart_acuan == '' || this.aud_thp1_standart_acuan == null) throw "Isi Tujuan Audit"
                        if (this.aud_thp1_tanggal_mulai == null) throw "Isi Tanggal Mulai"
                        if (this.aud_thp1_tanggal_selesai == null) throw "Isi Tanggal Selesai"
                    },
					async updateDatabase() {
						let dbData = {name: "penjadwalan", tanggal_mulai: this.aud_thp1_tanggal_mulai, tanggal_selesai: this.aud_thp1_tanggal_selesai, bill_id: this.bill_id, cust_id: this.cust_id, mohon_id: this.mohon_id, tujuan : this.aud_thp1_tujuan, standart : this.aud_thp1_standart_acuan , mohon_det_id : this.mohon_det_id, jenis_sertifikasi : this.jenis_sertifikasi};
						const currentData = await idb.tahap1_data.where({name: "penjadwalan"}).first();
						if (currentData == null) {
							await idb.tahap1_data.put(dbData);
                        } else {
							await idb.tahap1_data.update(currentData.id, dbData);
                        }
					},
                    async loadIdb() {
						let currentData = await idb.tahap1_data.where({name: "penjadwalan"}).first();
						await this.setForm(currentData);
						
                    },
                    async setForm(currentData) {
                        // tahap1_data_itms
						if (currentData == null) {
							let dbData = {name: "penjadwalan", tanggal_mulai: null, tanggal_selesai: null, bill_id: null, cust_id: null, mohon_id: null, tujuan : null, mohon_det_id : null, jenis_sertifikasi : null, standart : null};
							currentData = dbData;
                            await idb.tahap1_data.put(dbData);
                        }
						else{
							this.cust_id= currentData.cust_id;
							this.bill_id= currentData.bill_id;
							this.mohon_id= currentData.mohon_id;
							this.mohon_det_id= currentData.mohon_det_id;
							this.jenis_sertifikasi= currentData.jenis_sertifikasi;
							this.aud_thp1_tanggal_mulai= currentData.tanggal_mulai;
							this.aud_thp1_tanggal_selesai= currentData.tanggal_selesai;
							this.aud_thp1_tujuan= currentData.tujuan;
							this.aud_thp1_standart_acuan= currentData.standart;
						}
						$('#aud_thp1_tujuan').val(`${this.aud_thp1_tujuan}`);
						$('#aud_thp1_standart_acuan').val(`${this.aud_thp1_standart_acuan}`);
						let self = this;
						let url_permohonan = `{{ url("$url/ajax?action=combogrid-permohonan") }}`;
                        $('#cust_id').combogrid({
                            pageSize: '50',
                            panelWidth: 600,
                            pagination: true,
                            nowrap: false,
                            idField: 'bill_id',
                            textField: 'cust_nama',
                            editable: true,
                            url: `{{ url("$url/ajax?action=combogrid-pelanggan") }}`,
                            method: 'get',
                            mode: 'remote',
                            value: self.bill_id,
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'cust_id', hidden: true},
                                {field: 'bill_id', hidden: true},
                                {field: 'cust_nama', title: 'Nama Pelanggan', width: 390, sortable: true,},
                                {field: 'bill_nomor_billing', title: 'Nomor Billing', width: 200, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
								await self.setCustId(row.cust_id);
								await self.setBillId(row.bill_id);
								$('#mohon_id').combogrid({url : `{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id=${self.cust_id}&bill_id=${self.bill_id}`});
                            },
                        });
						
						$('#mohon_id').combogrid({
                            pageSize: '50',
                            panelWidth: 600,
                            pagination: true,
                            nowrap: false,
                            idField: 'mohon_det_id',
                            textField: 'sert_nama',
                            editable: true,
                            method: 'get',
                            mode: 'remote',
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'mohon_det_id', hidden: true,},
                                {field: 'sert_tahap1_jenis', hidden: true,},
                                {field: 'mohon_id', title: 'No.<br>Permohonan', width: 120, sortable: true,},
                                {field: 'mohon_jenis_status', title: 'Jenis<br>Permohonan', width: 120, sortable: true,},
                                {field: 'sert_nama', title: 'Serifikasi', width: 360, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
								await self.setMohonId(row.mohon_id);
								await self.setMohonDetailId(row.mohon_det_id);
								await self.setJenis(row.sert_tahap1_jenis);
                            },
                        });
						
						if(self.cust_id != null){
							$('#mohon_id').combogrid({url : `{{ url("$url/ajax?action=combogrid-permohonan") }}&cust_id=${self.cust_id}&bill_id=${self.bill_id}`});
						}
						
						if(self.mohon_det_id != null){
							setTimeout(() => {
								$('#mohon_id').combogrid('setValue', `${self.mohon_det_id}`);
							}, 400);
						}
						
						$('#aud_thp1_tanggal_mulai').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.aud_thp1_tanggal_mulai,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
								await self.setTanggalMulai(data_date);
							}
						});
						
						$('#aud_thp1_tanggal_selesai').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.aud_thp1_tanggal_selesai,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
								await self.setTanggalSelesai(data_date);
							}
						});
						
						
						
                    },
                }
            })
        })
    </script>
@endpush
