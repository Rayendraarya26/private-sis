@extends("layouts.layout_app")

@section('title', 'Input Tinjauan Audit Tahap 1')

@push("css")
    <!-- HTML -->
    <link rel="stylesheet" href="{{asset("assets/plugins/smartwizard/css/smart_wizard_all.min.css")}}">
    <style>
        .step1_image {
            width: 100%;
            max-width: 400px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endpush

@section('content')
    <div class="dt-content">
        <div class="row">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__body">
<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<div class="table-responsive">
			<table class="table table-bordered mb-0">
				<thead class="thead-light">
					<tr>
					  <th rowspan="2" scope="col">Klausul</th>
					  <th rowspan="2" scope="col">Persyaratan</th>
					  <th colspan="2" scope="col">Dokumen {{$dataJadwal->cust_nama}}</th>
					  <th rowspan="2" scope="col">Hasil Tinjauan(OK / NO)</th>
					  <th rowspan="2" scope="col">Keterangan</th>
					</tr>
					<tr>
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
						<th scope="col">Kode Dokumen </th>
					    <th scope="col">Judul Dokumen</th>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
						<th scope="col">Nilai </th>
					    <th scope="col">Satuan</th>
					  @endif 
					</tr>
				</thead>
				<tbody>
					@foreach($dataAuditKlausul as $dpk)
					<tr>
					  <th scope="row">{{$dpk->aud_thp1_det_thp1_nomor}}</th>
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
					  <td>{{$dpk->aud_thp1_det_peryataan}}</td>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
					  <td>{{$dpk->aud_thp1_det_persyaratan}}</td>
					  @endif 
					  
					  @if($dataJadwal->sert_tahap1_jenis == 'sni')
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="kode_dok[{{$dpk->aud_thp1_det_id}}]" id="kode_dok" placeholder="Kode Dokumen" value="{{$dpk->aud_thp1_det_kode_dok}}">
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="judul_dok[{{$dpk->aud_thp1_det_id}}]" id="judul_dok" placeholder="Judul Dokumen" value="{{$dpk->aud_thp1_det_judul_dok}}">
						@endif 
					  </td>
					  @elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="nilai[{{$dpk->aud_thp1_det_id}}]" id="nilai" placeholder="" value="{{$dpk->aud_thp1_det_nilai}}">
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<input type="text" class="form-control" name="satuan[{{$dpk->aud_thp1_det_id}}]" id="satuan" placeholder="Satuan" value="{{$dpk->aud_thp1_det_satuan}}">
						@endif 
					  </td>
					  @endif 
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							@if($dpk->aud_thp1_det_hasil_tinjauan == 'ok')
								<div class="col-md-12 col-sm-12">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok" checked>
									<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no">
									<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
								</div>
								</div>
							@elseif($dpk->aud_thp1_det_hasil_tinjauan == 'no')
							<div class="col-md-12 col-sm-12">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok">
									<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no" checked>
									<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
								</div>
								</div>
							@else
								<div class="col-md-12 col-sm-12">
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}" value="ok" checked>
										<label class="form-check-label" for="hasil_tinjauan_ok{{$dpk->aud_thp1_det_id}}">OK</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}" id="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}" value="no">
										<label class="form-check-label" for="hasil_tinjauan_no{{$dpk->aud_thp1_det_id}}">NO</label>
									</div>
								</div>
							@endif 
						@endif 
					  </td>
					  <td>
						@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
							<textarea type="text" class="form-control" name="keterangan_{{$dpk->aud_thp1_det_id}}" id="keterangan" placeholder="Keterangan">{{$dpk->aud_thp1_det_keterangan}}</textarea>
						@endif 
					  </td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@if(!$dataRevisi->isEmpty())
		<br/>
		<div class="table-responsive">
			<table class="table table-bordered mb-0">
				<thead class="thead-light">
					<tr>
						<th scope="col">Tanggal Revisi</th>
						<th scope="col">Catatan Revisi Dari Pelanggan</th>
					</tr>
				</thead>
				<tbody>
				@foreach($dataRevisi as $drv)
				<tr>
					<td>{{date('d mm Y', strtotime($drv->created_at))}}</td>
					<td>{!! $drv->aud_thp1_perseujuan_revisi_catatan !!}</td>
				</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		@endif 
		<br/>
		<template v-if="loading_submit">
			<div class="fa-3x" style="text-align: center">
				<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
			</div>
		</template>
		<template v-else>
			<button :disabled="!status_submit"
					:class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}"
					@click="submitAudit">
				<i class="fad fa-disk"></i> Simpan Audit Tahap 1 
			</button>
		</template>
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
        const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });

        $(document).ready(function () {
			@if($statusEntry == false)
				swalWithBootstrapButtons({
					title: `Informasi Audit Tahap 1`,
					text: `Data LKS/Klausul dan Laporan belum pernah ter-entry, apakah anda ingin men-generate data dari master?`,
					type: 'info',
					showCancelButton: true,
					allowOutsideClick: false,
					confirmButtonText: 'Generate',
					cancelButtonText: 'Batal',
					reverseButtons: true
				}).then(async (result) => {
					if (result.value) {
						$.messager.progress();
						let formData = new FormData();
						formData.append("tipe", 'update-generate-tahap1')
						formData.append("sert_tahap1_jenis", '{{$dataJadwal->sert_tahap1_jenis}}')
						formData.append("aud_thp1_id", '{{$dataJadwal->aud_thp1_id}}')
						formData.append("sert_id", '{{$dataJadwal->sert_id}}')
						formData.append("mohon_id", '{{$dataJadwal->mohon_id}}')
						$.ajax({
							url: `{{action("$module@update")}}`,
							type: 'post',
							processData: false,
							contentType: false,
							data: formData,
							success: async function (res) {
								$.messager.progress('close');
								toastCenter({
									type: 'success',
									title: res.message
								})
								setTimeout(() => location.href = "{{url("$url")}}/edit?tipe=audit-tahap1&aud_thp1_id={{$dataJadwal->aud_thp1_id}}", 1000)
							},
							error: function (xhr) {
								$.messager.progress('close');
								self.loading_submit = false;
								if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
								else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
							}
						});
					}
					else{
						setTimeout(() => location.href = "{{url("$url")}}", 500);
					}
				});
			@endif
           
		   window.vueStepOne = new Vue({
                el: "#vueStepOne",
                data: {
                    aud_thp1_id: `{{$dataJadwal->aud_thp1_id}}`,
                    aud_thp1_id: `{{$dataJadwal->aud_thp1_id}}`,
                    status_validasi: true,
                    status_submit: true,
                    loading_submit: false,
                },
                mounted() {
                    this.setForm();
                },
                methods: {
                    validate() {
						this.status_validasi = true;
						@foreach($dataAuditKlausul as $dpk)
							@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
								if($('input[name="hasil_tinjauan_{{$dpk->aud_thp1_det_id}}').is(':checked')) { 
					
								}
								else{
									toastCenter({
												type: 'warning',
												title: "Pilih Hasil Tinjauan untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}}"
											})
										this.status_validasi = false;
								}
								@if($dataJadwal->sert_tahap1_jenis == 'sni')
									if ($('input[name="kode_dok[{{$dpk->aud_thp1_det_id}}]').val() == '') {
										toastCenter({
												type: 'warning',
												title: "Kode Dokumen untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
											})
										this.status_validasi = false;
									}

									
									if ($('input[name="judul_dok[{{$dpk->aud_thp1_det_id}}]').val() == '') {
										toastCenter({
												type: 'warning',
												title: "Judul Dokumen untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
											})
										this.status_validasi = false;
									}
								@elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
									if ($('input[name="nilai[{{$dpk->aud_thp1_det_id}}]').val() == '') {
										toastCenter({
												type: 'warning',
												title: "Satuan untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
											})
										this.status_validasi = false;
									}
									
									if ($('input[name="satuan[{{$dpk->aud_thp1_det_id}}]').val() == '') {
										toastCenter({
												type: 'warning',
												title: "Satuan untuk Klausul {{$dpk->aud_thp1_det_thp1_nomor}} masih kosong" 
											});
										this.status_validasi = false;
									} 
								@endif
							@endif
						@endforeach
						
                    },
                    async setForm() {
                        
                    },
					async submitAudit() {
						this.validate();
						if(this.status_validasi == true){
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
									let status_from = true;
									formData.append("tipe", 'update-audit-tahap1');
									formData.append("cust_id", '{{$dataJadwal->cust_id}}');
									formData.append("aud_thp1_id", '{{$dataJadwal->aud_thp1_id}}');
									formData.append("sert_id", '{{$dataJadwal->sert_id}}');
									formData.append("mohon_id", '{{$dataJadwal->mohon_id}}');
									formData.append("jenis", '{{$dataJadwal->sert_tahap1_jenis}}');

									@foreach($dataAuditKlausul as $dpk)
										@if($dpk->aud_thp1_det_is_tinjauan == 'ya')
											var myRadio = $("input[name=hasil_tinjauan_{{$dpk->aud_thp1_det_id}}]");
											var checkedValue = myRadio.filter(":checked").val();
											formData.append("detail_hasil_tinjauan[{{$dpk->aud_thp1_det_id}}]", checkedValue);
											@if($dataJadwal->sert_tahap1_jenis == 'sni')
											formData.append("detail_kode_dok[{{$dpk->aud_thp1_det_id}}]", $('input[name="kode_dok[{{$dpk->aud_thp1_det_id}}]').val());
											formData.append("detail_judul_dok[{{$dpk->aud_thp1_det_id}}]", $('input[name="judul_dok[{{$dpk->aud_thp1_det_id}}]').val());
											@elseif($dataJadwal->sert_tahap1_jenis == 'pusat')
											formData.append("detail_nilai[{{$dpk->aud_thp1_det_id}}]", $('input[name="nilai[{{$dpk->aud_thp1_det_id}}]').val());
											formData.append("detail_satuan[{{$dpk->aud_thp1_det_id}}]", $('input[name="satuan[{{$dpk->aud_thp1_det_id}}]').val());
											@endif
											formData.append("detail_keterangan[{{$dpk->aud_thp1_det_id}}]", $('textarea[name="keterangan_{{$dpk->aud_thp1_det_id}}').val());
										@endif
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
											// self.loading_submit = false;
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
            });
			
        });
    </script>
@endpush
