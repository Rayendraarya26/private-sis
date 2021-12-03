<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<form action="#">
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Data Pelanggan</label>
			<div class="col-xl-7">
			  <input class="form-control" name="cust_id" id="cust_id" aria-describedby="cust_idHelp" >
				<small id="cust_idHelp" class="form-text">Note: Silahkan pilih pelanggan.</small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Jenis Jadwal</label>
			<div class="col-xl-9">
				<!-- Radio Button -->
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="tunggal" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe1" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('tunggal')">
					<label class="custom-control-label" for="jadw_jenis_tipe1">Tunggal</label>
				  </div>
				  <!-- /radio button -->
				  <!-- Radio Button -->
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="integrasi" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe4" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('integrasi')">
					<label class="custom-control-label" for="jadw_jenis_tipe4">Integrasi</label>
				  </div>
				  <!-- /radio button -->
				  <!-- Radio Button -->
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="gabungan" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe3" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('gabungan')">
					<label class="custom-control-label" for="jadw_jenis_tipe3">Gabungan</label>
				  </div>
				  <!-- /radio button -->
				<small id="jadw_jenis_tipeHelp" class="form-text">Note: Silahkan pilih jenis jadwal.</small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="jadw_tanggal_mulai">Tanggal Mulai</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="jadw_tanggal_mulai" style="max-width:300px;">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="jadw_tanggal_selesai">Tanggal Selesai</label>
			<div class="col-xl-3">
			  <input type="text" class="form-control" id="jadw_tanggal_selesai" style="max-width:300px;">
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
                    jenis: null,
                    jadw_tanggal_mulai: null,
                    jadw_tanggal_selesai: null,
                },
                mounted() {
                    this.loadIdb();
                },
                methods: {
					async setCustId(id) {
						this.cust_id = id
						await this.updateDatabase()
					},
					async setTanggalMulai(date) {
						this.jadw_tanggal_mulai = date;
						await this.updateDatabase()
					},
					async setTanggalSelesai(date) {
						this.jadw_tanggal_selesai = date;
						await this.updateDatabase()
					},
					async setJenisJadwal(dt) {
						this.jenis = dt;
						await this.updateDatabase()
					},
                    validate() {
                        if (this.cust_id == null) throw "Pilih Pelanggan"
                        if (this.jenis == null) throw "Pilih jenis jadwal"
                        if (this.jadw_tanggal_mulai == null) throw "Isi Tanggal Mulai"
                        if (this.jadw_tanggal_selesai == null) throw "Isi Tanggal Selesai"
                    },
					async updateDatabase() {
						let dbData = {name: "pencabutan", tanggal_mulai: this.jadw_tanggal_mulai, tanggal_selesai: this.jadw_tanggal_selesai, jenis: this.jenis, cust_id: this.cust_id};
						const currentData = await idb.pencabutan_data.where({name: "pencabutan"}).first();
						if (currentData == null) {
							await idb.pencabutan_data.put(dbData);
                        } else {
							await idb.pencabutan_data.update(currentData.id, dbData);
                        }
					},
                    async loadIdb() {
						let currentData = await idb.pencabutan_data.where({name: "pencabutan"}).first();
						await this.setForm(currentData);
						
                    },
                    async setForm(currentData) {
                        // pencabutan_data_itms
						if (currentData == null) {
							let dbData = {name: "pencabutan", tanggal_mulai: null, tanggal_selesai: null, jenis: null, cust_id: null};
							currentData = dbData;
                            await idb.pencabutan_data.put(dbData);
                        }
						else{
							this.cust_id= currentData.cust_id;
							this.jenis= currentData.jenis;
							this.jadw_tanggal_mulai= currentData.tanggal_mulai;
							this.jadw_tanggal_selesai= currentData.tanggal_selesai;
							
							let $radios = $('input:radio[name=jadw_jenis_tipe]');
							if($radios.is(':checked') === false) {
								$radios.filter('[value='+ currentData.jenis +']').prop('checked', true);
							}
						}
						
						let self = this;
                        let url = `{{ url("$url/ajax?action=combogrid-pelanggan") }}`
                        $('#cust_id').combogrid({
                            pageSize: '50',
                            panelWidth: 600,
                            pagination: true,
                            nowrap: false,
                            idField: 'cust_id',
                            textField: 'cust_nama',
                            editable: true,
                            url: url,
                            method: 'get',
                            mode: 'remote',
                            value: self.cust_id,
                            multiSort: true,
                            fitColumns: false,
                            required: true,
                            columns: [[
                                {field: 'cust_id', hidden: true},
                                {field: 'cust_nama', title: 'Nama Pelanggan', width: 390, sortable: true,},
                                {field: 'total_sert', title: 'Total<br/>Sertifikat', width: 100, sortable: true,},
                            ]],
                            onSelect: async function (index, row) {
								await self.setCustId(row.cust_id);
                            },
                        });
						
						$('#jadw_tanggal_mulai').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.jadw_tanggal_mulai,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
								await self.setTanggalMulai(data_date);
							}
						});
						
						$('#jadw_tanggal_selesai').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:this.jadw_tanggal_selesai,
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
