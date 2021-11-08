<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<form action="#">
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">No Jadwal</label>
			<div class="col-xl-7">
				<a href="#">{{$dataJadwal->jadw_id}}</a>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Data Pelanggan</label>
			<div class="col-xl-7">
				<a href="#">{{$dataJadwal->cust_nama}}</a>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">No. Billing</label>
			<div class="col-xl-7">
				<a href="#">{{$dataJadwal->bill_nomor_billing}}</a>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Jenis Jadwal</label>
			<div class="col-xl-9">
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="tunggal" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe1" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('tunggal')">
					<label class="custom-control-label" for="jadw_jenis_tipe1">Tunggal</label>
				  </div>
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="integrasi" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe4" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('integrasi')">
					<label class="custom-control-label" for="jadw_jenis_tipe4">Integrasi</label>
				  </div>
				  <div class="custom-control custom-radio custom-control-inline">
					<input value="gabungan" aria-describedby="jadw_jenis_tipeHelp" type="radio" id="jadw_jenis_tipe3" name="jadw_jenis_tipe" class="custom-control-input" @click="setJenisJadwal('gabungan')">
					<label class="custom-control-label" for="jadw_jenis_tipe3">Gabungan</label>
				  </div>
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
                    jadw_id: `{{$dataJadwal->jadw_id}}`,
                    cust_id: `{{$dataJadwal->cust_id}}`,
                    bill_id: `{{$dataJadwal->bill_id}}`,
                    jenis: `{{$dataJadwal->jadw_jenis}}`,
                    jadw_tanggal_mulai: `{{$dataJadwal->jadw_tanggal_mulai}}`,
                    jadw_tanggal_selesai: `{{$dataJadwal->jadw_tanggal_selesai}}`,
                },
                mounted() {
                    this.setForm();
                },
                methods: {
					async setTanggalMulai(date) {
						this.jadw_tanggal_mulai = date;
					},
					async setTanggalSelesai(date) {
						this.jadw_tanggal_selesai = date;
					},
					async setJenisJadwal(dt) {
						this.jenis = dt;
					},
                    validate() {
                        if (this.jenis == null) throw "Pilih jenis jadwal"
                        if ($("#jadw_tanggal_mulai").val() == '') throw "Isi Tanggal Mulai"
                        if ($("#jadw_tanggal_selesai").val() == '') throw "Isi Tanggal Selesai"
                    },
                    async setForm() {
                        // jadwal_data_itms
						var $radios = $('input:radio[name=jadw_jenis_tipe]');
						if($radios.is(':checked') === false) {
							$radios.filter('[value='+ this.jenis +']').prop('checked', true);
						}
						let self = this;
						$('#jadw_tanggal_mulai').datebox({
							required:true,
							editable:false,
							formatter:myformatter,
							parser:myparser,
							value:self.jadw_tanggal_mulai,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
								await self.setTanggalMulai(data_date);
							}
						});
						
						$('#jadw_tanggal_selesai').datebox({
							required:true,
							editable:false,
							formatter:myformatter,
							parser:myparser,
							value:self.jadw_tanggal_selesai,
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
