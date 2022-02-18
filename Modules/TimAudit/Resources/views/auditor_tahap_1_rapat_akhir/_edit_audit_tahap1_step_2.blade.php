@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
        }
		
		.label-form {
            font-weight:normal;
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
				@if(!$dataRevisi->isEmpty())
				<div class="form-group row">
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
				</div>
				@endif
			
				<div class="form-group row">
					<div class="table-responsive">
						<table class="table table-bordered mb-0">
							<thead class="thead-light">
								<tr>
								  <th scope="col">Nama Tim</th>
								  <th scope="col">Posisi</th>
								  <th scope="col">Dokumen Logbook</th>
								</tr>
							</thead>
							<tbody>
								@foreach($dataLogbook as $dlb)
								<tr>
								  <td>{{$dlb->peg_nama}} ( {{$dlb->thp1_tim_kode}} )</td>
								  <td>{{$dlb->thp1_tim_posisi}}</td>
								  <td>@if($dlb->thp1_logbook_filepath != '')<a href="{{ url($dlb->thp1_logbook_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a> @else - @endif</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<br>
				<br>
				<div class="form-group">
					<label class="label-form">Notulen Rapat*</label>
					<textarea class="form-control" name="aud_thp1_notulen" id="aud_thp1_notulen">@if(isset($dataJadwal->aud_thp1_kolom_xii)) {{$dataJadwal->aud_thp1_kolom_xii}} @endif</textarea>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="aud_thp1_file_notulen">
						Kehadiran*
						<br>
						<small>(pdf/excel)</small>
					</label>
					<div class="col-sm-8">
						<input type="file" name="aud_thp1_file_daftar_hadir" id="aud_thp1_file_daftar_hadir" @change="validateUploadKehadiran" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
						@if(!empty($dataJadwal->aud_thp1_file_daftar_hadir))
							<hr/>
								<a href="{{asset($dataJadwal->aud_thp1_file_daftar_hadir)}}" target="_blank">
									<i class="fad fa-download"></i> Download Kehadiran
								</a>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label class="label-form">Hasil Akhir Audit Tahap I?</label>
					<div class="col-md-12">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="aud_thp1_status" id="aud_thp1_status1" value="memenuhi" @click="setStatusAudit('memenuhi')" @if(isset($dataJadwal->aud_thp1_status)) @if($dataJadwal->aud_thp1_status == 'memenuhi') checked @endif @endif>
                        <label class="form-check-label" for="aud_thp1_status1">Sudah Memenuhi Kecukupan Minimal</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="aud_thp1_status" id="aud_thp1_status2" value="tidak-memenuhi" @click="setStatusAudit('tidak-memenuhi')" @if(isset($dataJadwal->aud_thp1_status)) @if($dataJadwal->aud_thp1_status == 'tidak-memenuhi') checked @endif @endif>
                        <label class="form-check-label" for="aud_thp1_status2">Belum Memenuhi Kecukupan Minimal</label>
                      </div>
                    </div>
				</div>
			</div>
			<div class="col-md-12">
				<hr/>
				<template v-if="loading_submit">
					<div class="fa-3x" style="text-align: center">
						<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
					</div>
				</template>
				<template v-else>
					<button :disabled="!status_submit"
							:class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}"
							@click="submitAudit">
						<i class="fad fa-disk"></i> Simpan Audit Tahap 1 dan Ajukan
					</button>
				</template>
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
                    status_audit: @if(isset($dataJadwal->aud_thp1_status)) @if($dataJadwal->aud_thp1_status != '') `{{$dataJadwal->aud_thp1_status}}` @else `` @endif @endif,
                    aud_thp1_file_daftar_hadir: null,
                    status_submit: false,
                    loading_submit: false,
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
					setStatusAudit(event) {
                        this.status_submit = true;
                        this.status_audit = `${event}`;
                    },
					validateUploadKehadiran(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#aud_thp1_file_daftar_hadir").val("")
                        }
                    },
					async submitAudit() {
						tinyMCE.triggerSave();
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
								if (@if($dataJadwal->aud_thp1_file_daftar_hadir == '') $.trim($("#aud_thp1_file_daftar_hadir").val()) === "" @else status_from == false @endif) {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Daftar Hadir"
											})
								}
								else{
									formData.append("cust_id", '{{$dataJadwal->cust_id}}');
									formData.append("aud_thp1_id", '{{$dataJadwal->aud_thp1_id}}');
									formData.append("sert_id", '{{$dataJadwal->sert_id}}');
									formData.append("mohon_id", '{{$dataJadwal->mohon_id}}');
									formData.append("jenis", '{{$dataJadwal->sert_tahap1_jenis}}');
									formData.append("aud_thp1_notulen", tinyMCE.get('aud_thp1_notulen').getContent());
									formData.append("status_audit", this.status_audit);
									const file_daftar = document.querySelector("#aud_thp1_file_daftar_hadir").files[0];
									formData.append("aud_thp1_file_daftar_hadir", file_daftar);
									
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
                            }
                        });
                    },
                    async start() {
                        setTimeout(async () => {
							$(".tab-content").height("100%");
							this.tynimceForm();
							this.status_submit = @if(isset($dataJadwal->aud_thp1_status)) @if($dataJadwal->aud_thp1_status != '') true @else false @endif @endif;  
						}, 1000);					
                    },
					async tynimceForm() {
						$('textarea#aud_thp1_notulen').tinymce({
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
					},
                }
            });
			
			
        });		
    </script>
@endpush
