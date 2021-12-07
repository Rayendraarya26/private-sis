<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<form action="#">
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Data Pelanggan</label>
			<div class="col-xl-7"><a href="#">{{$dataJadwal->cust_nama}}</a></div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">No. Billing</label>
			<div class="col-xl-7"><a href="#">{{$dataJadwal->bill_nomor_billing}}</a></div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">No. Permohonan</label>
			<div class="col-xl-7"><a href="#">{{$dataJadwal->mohon_id}} ({{$dataJadwal->sert_nama}})</a></div>
		</div>
		<div class="form-group">
			<label class="col-xl-3 col-form-label text-sm-left" for="aud_thp1_tanggal_selesai">Tujuan Audit</label>
			<div class="col-xl-3">
			  <textarea class="form-control" id="aud_thp1_tujuan" v-on:keyup="tujuanSet" v-model="aud_thp1_tujuan">{{$dataJadwal->aud_thp1_tujuan}}</textarea>
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
                    cust_id: `{{$dataJadwal->cust_id}}`,
                    bill_id: `{{$dataJadwal->bill_id}}`,
                    mohon_id: `{{$dataJadwal->mohon_id}}`,
                    aud_thp1_tujuan: `{{$dataJadwal->aud_thp1_tujuan}}`,
                    aud_thp1_standart_acuan: `{{$dataJadwal->aud_thp1_standart_acuan}}`,
                    aud_thp1_tanggal_mulai: `{{$dataJadwal->aud_thp1_tanggal_mulai?->format("Y-m-d")}}`,
                    aud_thp1_tanggal_selesai: `{{$dataJadwal->aud_thp1_tanggal_selesai?->format("Y-m-d")}}`,
                },
                mounted() {
                    this.setForm();
                },
                methods: {
					tujuanSet: async function(event) {
						this.aud_thp1_tujuan = $('#aud_thp1_tujuan').val();
					},
					standartSet: async function(event) {
						this.aud_thp1_standart_acuan = $('#aud_thp1_standart_acuan').val();
					},
					async setTanggalMulai(date) {
						this.aud_thp1_tanggal_mulai = date;
					},
					async setTanggalSelesai(date) {
						this.aud_thp1_tanggal_selesai = date;
					},
                    validate() {
                        if ($("#aud_thp1_tujuan").val() == '') throw "Isi Tujuan Audit"
                        if ($("#aud_thp1_standart_acuan").val() == '') throw "Isi Stadart Acuan"
                        if ($("#aud_thp1_tanggal_mulai").val() == '') throw "Isi Tanggal Mulai"
                        if ($("#aud_thp1_tanggal_selesai").val() == '') throw "Isi Tanggal Selesai"
                    },
                    async setForm() {
						$('#aud_thp1_tujuan').val(`${this.aud_thp1_tujuan}`);
						$('#aud_thp1_standart_acuan').val(`${this.aud_thp1_standart_acuan}`);
						let self = this;
						
						$('#aud_thp1_tanggal_mulai').datebox({
							required:true,
							editable:false,
							formatter:myformatter,
							parser:myparser,
							value:self.aud_thp1_tanggal_mulai,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
								await self.setTanggalMulai(data_date);
							}
						});
						
						$('#aud_thp1_tanggal_selesai').datebox({
							required:true,
							editable:false,
							formatter:myformatter,
							parser:myparser,
							value:self.aud_thp1_tanggal_selesai,
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
