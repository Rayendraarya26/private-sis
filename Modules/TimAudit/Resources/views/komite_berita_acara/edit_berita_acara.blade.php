@extends("layouts.layout_app")

@section('title', 'Berita Acara Komite Sertifikasi')

@push("css")
    <!-- HTML -->
	<style>
		#label-form{
			font-weight:normal;
			/* color:#5BB6EA; */
		}
	</style>
@endpush

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">				
				<div id="vueLembarPeriksa">
					<div class="col-xl-12">
						<div class="dt-card">
							<div class="dt-card__header"><div class="dt-card__heading"><h3 class="dt-card__title">1. Detail Berita Acara No. #{{$dataJadwal->jadw_id}}</h3></div></div>
							<div class="dt-card__body">
								<div class="form-group form-row">
									<label class="col-xl-3 col-form-label text-sm-left" id="label-form">Rekomendasi Komite</label>
									<div class="col-xl-5">
										<a href="{{ url("$url/detail") }}?tipe=detail-rekomendasi&jadw_id={{$dataJadwal->jadw_id}}" target="blank">Lihat Data</a>
									</div>
								</div>
								<div class="form-group form-row">
									<label class="col-xl-3 col-form-label text-sm-left" id="label-form">Lembar Periksa</label>
									<div class="col-xl-5">
										<a href="{{ url("$url/detail") }}?tipe=detail-periksa&jadw_id={{$dataJadwal->jadw_id}}" target="blank">Lihat Data</a>
									</div>
								</div>
								<div class="form-group form-row">
									<label class="col-xl-3 col-form-label text-sm-left" id="label-form">Nomor Berita Acara</label>
									<div class="col-xl-5">
										<input type="text" class="form-control" name="jadw_berita_acara_nomor" id="jadw_berita_acara_nomor" value="@if(isset($dataJadwal->jadw_berita_acara_nomor)) {{$dataJadwal->jadw_berita_acara_nomor}} @endif">
									</div>
								</div>
								<div class="form-group form-row">
									<label class="col-xl-3 col-form-label text-sm-left" id="label-form">Tanggal Berita Acara</label>
									<div class="col-xl-8">
										<input type="text"  name="jadw_berita_acara_tanggal" id="jadw_berita_acara_tanggal">
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-xl-12">
						<div class="card">
							<div class="dt-card__header"><div class="dt-card__heading"><h3 class="dt-card__title">2. Detail Sertifikasi</h3></div></div>
							<div class="card-body pt-0">
								<div class="table-responsive col-xl-12 col-md-12 col-12">
									<table class="table table-bordered mb-0">
										<thead>
											<tr>
											  <th class="text-uppercase" scope="col">Nama & Alamat Perusahaan (Ruang Lingkup, Nace Code)</th>
											  <th class="text-uppercase" scope="col">Komoditas(Type, merk)</th>
											  <th class="text-uppercase" scope="col">SM/SNI yang diterapkan</th>
											  <th scope="col">Disetujui/Tidak disetujui Tanggal Efektif Sertifikat</th>
											</tr>
										</thead>
										<tbody>
										@foreach($dataAudit as $dau)
											<tr>
											  <td>
												<b>{{$dau->cust_nama}}</b><br/>
												<p>{{$dau->cust_alamat}}</p><br/><br/>
												<p>Ruang Lingkup : </p><br>
												<p>{{$dau->jadw_audit_ruang_lingkup}}</p><br>
												<p>EA Code : {{$dau->jadw_audit_kode_ea}}</p><br>
												<p>NACE Code : {{$dau->jadw_audit_kode_nace}}</p><br>
											  </td>
											  <td>
												{{$dau->komodt_nama}}<br/>
												- Type : {{$dau->jadw_audit_tipe}}<br/>
												- Merk : {{$dau->jadw_audit_merk}}
											  </td>		
											  <td>{{$dau->jadw_audit_sni}}</td>									  
											  <td>
												@if($dau->jadw_audit_jenis == 'sertifikasi')
													Tanggal Terbit<br/>
													<input type="text" class="form-control" name="tanggal_terbit[{{$dau->jadw_audit_id}}]" id="tanggal_terbit{{$dau->jadw_audit_id}}" style="max-width:120px;"><br/><br/>
													Tanggal Berakhir<br/>
													<input type="text" class="form-control" name="tanggal_berakhir[{{$dau->jadw_audit_id}}]" id="tanggal_berakhir{{$dau->jadw_audit_id}}" style="max-width:120px;">
													
													<input type="text" class="form-control" name="tanggal_perubahan[{{$dau->jadw_audit_id}}]" id="tanggal_perubahan{{$dau->jadw_audit_id}}" style="max-width:120px;display:none;">
													
												@elseif($dau->jadw_audit_jenis == 're-sertifikasi')
													Tanggal Terbit<br/>
													<input type="text" class="form-control" name="tanggal_terbit[{{$dau->jadw_audit_id}}]" id="tanggal_terbit{{$dau->jadw_audit_id}}" style="max-width:120px;"><br/><br/>
													Tanggal Perubahan<br/>
													<input type="text" class="form-control" name="tanggal_perubahan[{{$dau->jadw_audit_id}}]" id="tanggal_perubahan{{$dau->jadw_audit_id}}" style="max-width:120px;"><br/><br/>
													Tanggal Berakhir<br/>
													<input type="text" class="form-control" name="tanggal_berakhir[{{$dau->jadw_audit_id}}]" id="tanggal_berakhir{{$dau->jadw_audit_id}}" style="max-width:120px;">
													
												@elseif($dau->jadw_audit_jenis == 'pengaktifan')
													Tanggal Terbit<br/>
													<input type="text" class="form-control" name="tanggal_terbit[{{$dau->jadw_audit_id}}]" id="tanggal_terbit{{$dau->jadw_audit_id}}" style="max-width:120px;"><br/><br/>
													Tanggal Perubahan<br/>
													<input type="text" class="form-control" name="tanggal_perubahan[{{$dau->jadw_audit_id}}]" id="tanggal_perubahan{{$dau->jadw_audit_id}}" style="max-width:120px;"><br/><br/>
													Tanggal Berakhir<br/>
													<input type="text" class="form-control" name="tanggal_berakhir[{{$dau->jadw_audit_id}}]" id="tanggal_berakhir{{$dau->jadw_audit_id}}" style="max-width:120px;">
													
												@elseif($dau->jadw_audit_jenis == 'pencabutan')
													Tanggal Berakhir<br/>
													<?=date('d M Y')?>
													<input type="text" class="form-control" name="tanggal_terbit[{{$dau->jadw_audit_id}}]" id="tanggal_terbit{{$dau->jadw_audit_id}}" style="max-width:120px;display:none;">
													<input type="text" class="form-control" name="tanggal_perubahan[{{$dau->jadw_audit_id}}]" id="tanggal_perubahan{{$dau->jadw_audit_id}}" style="max-width:120px;display:none;">
													<input type="text" class="form-control" name="tanggal_berakhir[{{$dau->jadw_audit_id}}]" id="tanggal_berakhir{{$dau->jadw_audit_id}}" style="max-width:120px;display:none;">
												@endif
													
											  </td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
								
								<div class="form-group form-row" style="margin-top: 50px">
									<label class="col-md-2 col-sm-3 text-sm-right mb-4 mb-sm-0">Simpan Draft ?</label>
									<div class="col-md-10 col-sm-9">
									  <div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="jadw_is_tutup" id="status1" value="tidak" @click="setTutup('tidak')">
										<label class="form-check-label" for="status1">Ya</label>
									  </div>
									  <div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="jadw_is_tutup" id="status2" value="ya" checked @click="setTutup('ya')">
										<label class="form-check-label" for="status2">Tidak</label>
									  </div>
										<small class="form-text">Note: Jika "ya" maka akan muncul di daftar berita acara komite dan masih bisa diedit, jika "tidak" maka sebaliknya.</small>
									</div>
								</div>
								
								<div style="padding-top: 20px">
									<template v-if="loading_submit">
										<div class="fa-3x" style="text-align: center">
											<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
										</div>
									</template>
									<template v-else>
										<button :disabled="!status_submit" :class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}" @click="submitData">
											<i class="fas fa-save"></i> Simpan Berita Acara
										</button>
									</template>
								</div>
							</div>
						</div>
					</div>
					
					
				</div>
			</div>
		</div>
	</div>
@endsection


@push("javascript")
    <script>
		$.extend($.fn.textbox.methods, {
			show: function(jq){
				return jq.each(function(){
					$(this).next().show();
				})
			},
			hide: function(jq){
				return jq.each(function(){
					$(this).next().hide();
				})
			}
		})

		const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
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
            window.vueLembarPeriksa = new Vue({
                el: "#vueLembarPeriksa",
                data: {
                    tutup_berita: `ya`,
                    status_submit: true,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						$('#jadw_berita_acara_tanggal').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:`@if(isset($dataJadwal->jadw_berita_acara_tanggal)) {{$dataJadwal->jadw_berita_acara_tanggal}} @endif`,
						});
						
						@foreach($dataAudit as $dau)
						$('#tanggal_terbit{{$dau->jadw_audit_id}}').datebox({
							required:false,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:`<?php echo date('Y-m-d'); ?>`,
						});
						
						$('#tanggal_perubahan{{$dau->jadw_audit_id}}').datebox({
							required:false,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:``,
						});
						
						$('#tanggal_berakhir{{$dau->jadw_audit_id}}').datebox({
							required:false,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:`<?php 
							if($dau->jadw_audit_jenis != 'pencabutan')
								echo date('Y-m-d', strtotime('+'.$dau->sert_expired.' year'));
							else 
								echo date('Y-m-d');
							?>`,
						});
						
						@if($dau->jadw_audit_jenis == 'sertifikasi')
							$('#tanggal_perubahan{{$dau->jadw_audit_id}}').datebox('hide');
						@elseif($dau->jadw_audit_jenis == 're-sertifikasi')
							
						@elseif($dau->jadw_audit_jenis == 'pengaktifan')
							
						@elseif($dau->jadw_audit_jenis == 'pencabutan')
							$('#tanggal_terbit{{$dau->jadw_audit_id}}').datebox('hide');
							$('#tanggal_perubahan{{$dau->jadw_audit_id}}').datebox('hide');
							$('#tanggal_berakhir{{$dau->jadw_audit_id}}').datebox('hide');
						@endif
						
						@endforeach
					})
				},
                methods: {
					async setTutup(dt){
						this.tutup_berita = dt;
					},
                    submitData() {
						if ($("#jadw_berita_acara_nomor").val() === '') {
							toastCenter({type: 'warning',title: "Silahkan Isi Nomor Berita Acara"});
						}
						else if ($("#jadw_berita_acara_tanggal").val() === '') {
							toastCenter({type: 'warning',title: "Silahkan Isi Tanggal Berita Acara"});
						}
						@foreach($dataAudit as $dau)
							@if($dau->jadw_audit_jenis == 'sertifikasi')
								else if ($("#tanggal_terbit{{$dau->jadw_audit_id}}").val() === '') {
								   toastCenter({type: 'warning',title: "Tanggal Terbit'{{$dau->sert_nama}}'"});
								}
								else if ($("#tanggal_berakhir{{$dau->jadw_audit_id}}").val() === '') {
								   toastCenter({type: 'warning',title: "Tanggal Berakhir'{{$dau->sert_nama}}'"});
								}
								
							@elseif($dau->jadw_audit_jenis == 're-sertifikasi')
								else if ($("#tanggal_berakhir{{$dau->jadw_audit_id}}").val() === '') {
								   toastCenter({type: 'warning',title: "Tanggal Berakhir'{{$dau->sert_nama}}'"});
								}
								
							@elseif($dau->jadw_audit_jenis == 'pengaktifan')
								else if ($("#tanggal_berakhir{{$dau->jadw_audit_id}}").val() === '') {
								   toastCenter({type: 'warning',title: "Tanggal Berakhir'{{$dau->sert_nama}}'"});
								}
								
							@endif
						@endforeach
						else{
							swalWithBootstrapButtons({
								title: `Submit Berita Acara ?`,
								text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
								type: 'info',
								showCancelButton: true,
								confirmButtonText: 'Simpan',
								cancelButtonText: 'Batal',
								reverseButtons: true
							}).then(async (result) => {
								if (result.value) {
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("cust_id", `{{$dataJadwal->cust_id}}`);
									formData.append("tipe", `berita-acara`);
									formData.append('jadw_berita_acara_nomor', $("#jadw_berita_acara_nomor").val());
									formData.append('jadw_berita_acara_tanggal', $("#jadw_berita_acara_tanggal").val());
									formData.append('jadw_is_tutup', this.tutup_berita);
									@foreach($dataAudit as $dau)
									formData.append('tanggal_terbit[{{$dau->jadw_audit_id}}]', $("input[name='tanggal_terbit[{{$dau->jadw_audit_id}}]']").val());
									formData.append('tanggal_berakhir[{{$dau->jadw_audit_id}}]', $("input[name='tanggal_berakhir[{{$dau->jadw_audit_id}}]']").val());
									
									@endforeach
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
						}
                    },
                }
            })
        });
    </script>
@endpush
