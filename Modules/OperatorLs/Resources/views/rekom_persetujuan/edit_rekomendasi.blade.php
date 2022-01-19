@extends("layouts.layout_app")

@section('title', 'Rekomendasi untuk Persetujuan')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="dt-card">
					  <div class="dt-card__header">
						<div class="dt-card__heading"><h3 class="dt-card__title">Informasi Data Jadwal No. #{{$dataJadwal->jadw_id}}</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div class="accordion" id="accordion-example">
							<div class="card">
								<div class="card-header" id="headingOne"><h5 class="mb-0"><button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-one" aria-expanded="true" aria-controls="collapse-one">1. Diajukan untuk</button></h5></div>
								<div id="collapse-one" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion-example">
								  <div class="card-body">
									<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table mb-0">
											<tbody>
												<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
												<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr>
												<tr><td>Type</td><td>: {{$dataJadwal->jadw_audit_tipe}}</td></tr>
												<tr><td>SM/SNI yang diacu</td><td>: {{$dataJadwal->jadw_audit_sni}}</td></tr>
												<tr><td>Alamat</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
											</tbody>
										</table>
									</div>
								  </div>
							    </div>
								
								<div class="card-header" id="headingTwo">
								  <h5 class="mb-0"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-two" aria-expanded="false" aria-controls="collapse-two">2. Kronologis kegiatan *)</button></h5>
								</div>
								<div id="collapse-two" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-example">
								  <div class="card-body">
									<table class="table mb-0">
										<tbody>
											<tr>
												<td>
												- Audit dilaksanakan pada {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}
												<br/>
												- 
												</td>
											</tr>
											<tr>
												<td>
												Permohonan sertifikasi dari pemohon :
												<ul>
													@foreach($dataMohon as $dp)
													<li>Surat pemohon No {{$dp->mohon_id}} tanggal {{ $dp->created_at?->format("d M Y") }}</li>
													@endforeach
												</ul>
												
												</td>
											</tr>
											
											@foreach($dataThp1 as $thp1)
											<tr>
												<td>
													<div class="col-xl-12">
														<div class="row">
															<div class="col-md-8">
															Pelaksanaan Audit Tahap I
															<br/>
															Susunan Tim :<br/>{!! $thp1->tim_list !!}
															<br/>
															Jumlah Temuan : {{$thp1->total_temuan * $thp1->total_det/ $thp1->total_data}}
															</div>
															<div class="col-md-4">
																Tanggal {{ date('d M Y', strtotime($thp1->aud_thp1_tanggal_mulai)) }}
															</div>
														</div>
													</div>
												</td>
											</tr>
											@endforeach
											
											@foreach($dataAudit as $aud)
											<tr>
												<td>
													<div class="col-xl-12">
														<div class="row">
															<div class="col-md-8">
															Pelaksanaan Audit {{$aud->jenis_jadwal}}
															<br/>
															Susunan Tim :<br/>{!! $aud->tim_list !!}
															</div>
															<div class="col-md-4">
																Tanggal {{ date('d M Y', strtotime($aud->jadw_tanggal_mulai)) }} s/d {{ date('d M Y', strtotime($aud->jadw_tanggal_selesai)) }}
															</div>
															<div class="col-md-12">
															<div class="table-responsive">
																  <table class="table table-bordered mb-0 p-0 no-margin">
																	<thead>
																	<tr>
																	  <th scope="col">Status LKS :</th>
																	  <th class="text-uppercase" scope="col">Kritis</th>
																	  <th class="text-uppercase" scope="col">Mayor</th>
																	  <th class="text-uppercase" scope="col">Minor</th>
																	  <th class="text-uppercase" scope="col">Observasi</th>
																	  <th class="text-uppercase" scope="col">Total</th>
																	</tr>
																	</thead>
																	<tbody>
																	<tr>
																		<td>LKS yang ditutup</td>
																		<td>{{$aud->total_kritis * $aud->lks_total/ $aud->total_data}}</td>
																		<td>{{$aud->total_mayor * $aud->lks_total/ $aud->total_data}}</td>
																		<td>{{$aud->total_minor * $aud->lks_total/ $aud->total_data}}</td>
																		<td>{{$aud->total_observasi * $aud->lks_total/ $aud->total_data}}</td>
																		<td>{{ ($aud->total_kritis * $aud->lks_total/ $aud->total_data) + ($aud->total_mayor * $aud->lks_total/ $aud->total_data) + ($aud->total_minor * $aud->lks_total/ $aud->total_data) + ($aud->total_observasi * $aud->lks_total/ $aud->total_data) }}</td>
																	</tr>
																	<tr>
																	  <td>LKS yang tetap ada/baru</td>
																	  <td>....</td>
																	  <td>....</td>
																	  <td>....</td>
																	  <td>....</td>
																	  <td>....</td>
																	</tr>
																	</tbody>
																  </table>
																</div>
															</div>
														</div>
													</div>
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								  </div>
								</div>
								
								<div class="card-header" id="heading3">
								  <h5 class="mb-0"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-tree" aria-expanded="false" aria-controls="collapse-two">3. LKS ditutup tanggal @if($dataJadwal->lks_expired_date_perbaikan != '') {{ date('d M Y', strtotime($dataJadwal->lks_expired_date_perbaikan)) }} @endif</button></h5>
								</div>
								<div id="collapse-tree" class="collapse" aria-labelledby="heading3" data-parent="#accordion-example">
								  <div class="card-body">
									@foreach($dataPPC as $ppc)
										Pengambilan Contoh*) untuk SPPT SNI
										<hr/>
										Petugas Pengambil Contoh : {{$ppc->peg_nama}}
									@endforeach
										<hr/>
									Sertifikat No :
									<?php
									foreach($dataSertifikat as $sert){
										$path = (isset($sert->prod_sert_filepath)) ? url($sert->prod_sert_filepath) : '#';
										echo '<a href="'.$path.'" target="_blank">'. $sert->prod_sert_nomor .'</a>, ';
									}
									?>
								  </div>
								</div>
								
								<div class="card-header" id="heading4">
								  <h5 class="mb-0"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-four" aria-expanded="false" aria-controls="collapse-two">4. Data File</button></h5>
								</div>
								<div id="collapse-four" class="collapse" aria-labelledby="heading4" data-parent="#accordion-example">
								  <div class="card-body">
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Laporan Ringkas
										</label>
										<div class="col-sm-10">
											<a href="{{ url("$dataJadwal->jadw_file_laporan_ringkas") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											LKS
										</label>
										<div class="col-sm-10">
											<a href="{{ url("$dataJadwal->jadw_file_lks") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Daftar Kehadiran Rapat Akhir
										</label>
										<div class="col-sm-10">
											<a href="{{ url("$dataJadwal->jadw_file_kehadiran") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Notulen
										</label>
										<div class="col-sm-10">
										{!! $dataJadwal->jadw_notulen_rapat !!}
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Daftar Periksa File Upload Tim
										</label>
										<div class="col-sm-10">
											<table class="table table-bordered mb-0">
												<tr>
													<th>Nama</th>
													<th>Posisi</th>
													<th>File Daftar Periksa</th>
												</tr>
												@foreach($dataAuditTim as $tim)
												<tr>
													<td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
													<td>{{ucwords($tim->jadw_tim_posisi)}}</td>
													<td>@if($tim->dftr_periksa_file != '')<a href="{{ url($tim->dftr_periksa_file) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
												</tr>
												@endforeach
											</table>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Logbook Tim
										</label>
										<div class="col-sm-10">
											<table class="table table-bordered mb-0">
												<tr>
													<th>Nama</th>
													<th>Posisi</th>
													<th>File Logbook</th>
												</tr>
												@foreach($dataTimLogbook as $tim)
												<tr>
													<td>{{$tim->peg_nama}} ({{$tim->jadw_tim_kode}})</td>
													<td>{{ucwords($tim->jadw_tim_posisi)}}</td>
													<td>@if($tim->logbook_filepath != '')<a href="{{ url($tim->logbook_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
												</tr>
												@endforeach
											</table>
										</div>
									</div>
									@if(!empty($dataFilePpc))
									<div class="form-group row">
										<label class="col-form-label col-sm-2">
											Laporan PPC
										</label>
										<div class="col-sm-10">
											<table class="table table-bordered mb-0">
												<tr>
													<th>Jenis File Laporan</th>
													<th>Download File</th>
												</tr>
												@foreach($dataFilePpc as $ppc)
												<tr>
													<td>
													@if($ppc->audit_ppc_jenis_file == '19')
														19. RENCANA PENGAMBILAN CONTOH
													@elseif($ppc->audit_ppc_jenis_file == '20')
														20. BERITA ACARA PENGAMBILAN CONTOH
													@elseif($ppc->audit_ppc_jenis_file == '21')
														21. LABEL CONTOH UJI
													@elseif($ppc->audit_ppc_jenis_file == '22')
														22. LAPORAN KEGIATAN PENGAMBILAN CONTOH
													@endif
													</td>
													<td>@if($ppc->audit_ppc_filepath != '')<a href="{{ url($ppc->audit_ppc_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
												</tr>
												@endforeach
											</table>
										</div>
									</div>
									@endif
								  </div>
								</div>
							</div>
						</div>
					  </div>
					</div>
				</div>
				
				<div class="col-xl-12">
					<div class="dt-card">
					  <div class="dt-card__header">
						<div class="dt-card__heading"><h3 class="dt-card__title">Rekomendasi Persetujuan</h3></div>
					  </div>
					  <div class="dt-card__body">
						<div id="vueRekomendasi">
							<div class="form-group form-row">
								<label class="col-md-2 col-sm-3 text-sm-right mb-4 mb-sm-0">Isi Rekomendasi</label>
								<div class="col-md-10 col-sm-9">
									<textarea class="form-control" v-on:keyup="validateSertifikat" name="rekmd_komte_isi" id="rekmd_komte_isi">@if(isset($dataJadwal->rekmd_komte_isi)) {{$dataJadwal->rekmd_komte_isi}} @endif</textarea>
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-md-2 col-sm-3 text-sm-right mb-4 mb-sm-0">Simpan Draft ?</label>
								<div class="col-md-10 col-sm-9">
								  <div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="rekmd_komte_status" id="aud_thp1_status1" value="ditutup" @click="setTutup('ditutup')">
									<label class="form-check-label" for="aud_thp1_status1">Tidak</label>
								  </div>
								  <div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="rekmd_komte_status" id="aud_thp1_status2" value="on-going" @click="setTutup('on-going')" >
									<label class="form-check-label" for="aud_thp1_status2">Ya</label>
								  </div>
									<small class="form-text">Note: Jika "tidak" maka akan muncul pada menu penilaian komite, jika "ya" maka sebaliknya, dan masih bisa diedit.</small>
								</div>
							</div>
							
							<div style="padding-top: 20px">
								<template v-if="loading_submit">
									<div class="fa-3x" style="text-align: center">
										<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
									</div>
								</template>
								<template v-else>
									<button :disabled="!status_submit" :class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}" @click="submitRekomendasi">
										<i class="fas fa-save"></i> Simpan Rekomendasi
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
	<script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
    <script>
	const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
			
        $(document).ready(function () {
            window.vueRekomendasi = new Vue({
                el: "#vueRekomendasi",
                data: {
                    rekmd_komte_status: null,
                    rekmd_komte_isi: null,
                    jadw_audit_sertifikat_filepath: null,
                    status_submit: false,
                    loading_submit: false,
                },
				mounted: function () {
					this.$nextTick(function () {
						$('textarea#rekmd_komte_isi').tinymce({
								height: 200,
								plugins: 'autosave link image code lists',
								relative_urls: false,
								placeholder: '',
								images_reuse_filename: true,
								automatic_uploads: true,
								images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
								images_upload_credentials: true,
								toolbar: [{name: 'history',items: ['undo', 'redo']}, {name: 'styles',items: ['styleselect']}, {name: 'formatting',items: ['bold', 'italic']}, {name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']}, {name: 'list',items: ['bullist', 'numlist']}, {name: 'indentation',items: ['outdent', 'indent']}, {name: 'link',items: ['link', 'image']}, {name: 'restore',items: ['restoredraft']},
								],
							  });
					})
				},
                methods: {
					validateSertifikat() {
						tinyMCE.triggerSave();
                        this.rekmd_komte_isi = tinyMCE.get('rekmd_komte_isi').getContent();
                    },
					setTutup(val){
						this.rekmd_komte_status = `${val}`;
						this.status_submit = true;
                    },
                    submitRekomendasi() {
						tinyMCE.triggerSave();
						if(tinyMCE.get('rekmd_komte_isi').getContent() === ''){
							toastCenter({
										type: 'warning',
										title: "Silahkan Isi Rekomendasi"
									});
						}
						else{
							swalWithBootstrapButtons({
								title: `Submit Rekomendasi ?`,
								text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
								type: 'info',
								showCancelButton: true,
								confirmButtonText: 'Simpan',
								cancelButtonText: 'Batal',
								reverseButtons: true
							}).then(async (result) => {
								if (result.value) {
									// Submit Permohonan
									let formData = new FormData();
									formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
									formData.append("tipe", `rekomendasi`)
									formData.append("rekmd_komte_isi", tinyMCE.get('rekmd_komte_isi').getContent())
									formData.append("rekmd_komte_status", this.rekmd_komte_status)
									
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
