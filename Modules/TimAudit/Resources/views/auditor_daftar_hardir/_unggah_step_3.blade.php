<div class="row" id="vueStepThree">
	<div class="col-md-12">
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_tanggal_rapat_akhir">
				Tanggal Rapat*
			</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="jadw_tanggal_rapat_akhir" name="jadw_tanggal_rapat_akhir" style="max-width:300px;">
				<input type="hidden" id="cust_nama" name="cust_nama" value="{{$data->sis_pelanggan->cust_nama}}">
				<input type="hidden" id="cust_email" name="cust_email" value="{{$data->sis_pelanggan->cust_email}}">
				<input type="hidden" id="cust_id" name="cust_id" value="{{$data->sis_pelanggan->cust_id}}">
				<input type="hidden" id="user_id" name="user_id" value="{{$data->sis_pelanggan->user_id}}">
				<input type="hidden" id="jadw_id" name="jadw_id" value="{{$data->jadw_id}}">
			</div>
		</div>
		<!--
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_file_laporan_ringkas">
				File Laporan Ringkas *
				<br>
				<small>(pdf) yang sudah ditanda tangani basah</small>
			</label>
			<div class="col-sm-8">
				<input type="file" name="jadw_file_laporan_ringkas" id="jadw_file_laporan_ringkas" accept="application/pdf,application" @change="validateUploadRingkas">
				@if(!empty($data->jadw_file_laporan_ringkas))
					<small>
						<a href="{{asset($data->jadw_file_laporan_ringkas)}}" target="_blank">
							<i class="fad fa-download"></i> Download File Lama
						</a>
					</small>
				@endif
			</div>
		</div>
		
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_file_lks">
				File LKS *
				<br>
				<small>(pdf) yang sudah ditanda tangani basah</small>
			</label>
			<div class="col-sm-8">
				<input type="file" name="jadw_file_lks" id="jadw_file_lks" accept="application/pdf,application" @change="validateUploadLks">
				@if(!empty($data->jadw_file_lks))
					<small>
						<a href="{{asset($data->jadw_file_lks)}}" target="_blank">
							<i class="fad fa-download"></i> Download File Lama
						</a>
					</small>
				@endif
			</div>
		</div>
		-->
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_file_kehadiran">
				File Kehadiran *
				<br>
				<small>(pdf/excel)</small>
			</label>
			<div class="col-sm-8">
				<input type="file" name="jadw_file_kehadiran" id="jadw_file_kehadiran" accept="application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" @change="validateUploadKehadiran">
				@if(!empty($data->jadw_file_kehadiran))
					<small>
						<a href="{{asset($data->jadw_file_kehadiran)}}" target="_blank">
							<i class="fad fa-download"></i> Download File Lama
						</a>
					</small>
				@endif
			</div>
		</div>
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_notulen_rapat">
				Notulen *
			</label>
			<div class="col-sm-8">
				<textarea class="form-control" name="jadw_notulen_rapat" id="jadw_notulen_rapat">@if(isset($data->jadw_notulen_rapat)) {{$data->jadw_notulen_rapat}} @endif</textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="label-form">Simpan sebagai Draft?</label>
			<div class="col-md-12">
			  <div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="jadw_setujui_temuan" id="draft1" value="diajukan" @click="setPengajuan('diajukan')" @if(isset($data->jadw_setujui_temuan)) @if($data->jadw_setujui_temuan == 'diajukan') checked @endif @endif>
				<label class="form-check-label" for="draft1">Tidak</label>
			  </div>
			  <div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="jadw_setujui_temuan" id="draft2" value="none" @click="setPengajuan('none')" @if(isset($data->jadw_setujui_temuan)) @if($data->jadw_setujui_temuan != 'none') checked @endif @endif>
				<label class="form-check-label" for="draft2">Ya</label>
			  </div>
				<br>
				<small>Jika diisi ya, maka masih bisa diedit, jika tidak maka akan diajukan ke pelanggan untuk disetujui atau direvisi.
				</small>
			</div>
		</div>
		
		<hr/>
		<template v-if="loading_submit">
			<div class="fa-3x" style="text-align: center">
				<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
			</div>
		</template>
		<template v-else>
			<button :disabled="!status_submit"
					:class="{'btn': true, 'btn-primary':status_submit, 'btn-outline-primary':!status_submit,'btn-block':true}"
					@click="submitRapat">
				<i class="fad fa-disk"></i> Simpan jadwal
			</button>
		</template>
	</div>
</div>

@push('javascript')
    <script>		
        $(document).ready(function () {
            window.vueStepThree = new Vue({
                el: "#vueStepThree",
                data: {
                    status_submit: false,
                    loading_submit: false,
                    simpan_draft: @if(isset($data->jadw_setujui_temuan)) @if($data->jadw_setujui_temuan != '') `{{$data->jadw_setujui_temuan}}` @else `none` @endif @endif,
                },
                mounted() {
					setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 2) {
                            this.start();
                        }
                    }, 400)
                },
                methods: {
					setPengajuan(event) {
                        this.status_submit = true;
                        this.simpan_draft = `${event}`;
                    },
					async submitRapat() {
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
								let status_from = true;
								const jadw_notulen_rapat = tinyMCE.get('jadw_notulen_rapat').getContent();
								const jadw_tanggal_rapat_akhir =  $('#jadw_tanggal_rapat_akhir').datebox('getValue');
								// const jadw_file_laporan_ringkas = document.querySelector("#jadw_file_laporan_ringkas").files[0];
								// const jadw_file_lks = document.querySelector("#jadw_file_lks").files[0];
								const jadw_file_kehadiran = document.querySelector("#jadw_file_kehadiran").files[0];
								if (jadw_file_kehadiran == '') {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Kehadiran"
											})
								}
								else if (jadw_notulen_rapat == '') {
									toastCenter({
												type: 'warning',
												title: "Isikan Notulen Rapat"
											})
								}
								else if (jadw_tanggal_rapat_akhir == '') {
									toastCenter({
												type: 'warning',
												title: "Isikan Tanggal Rapat"
											})
								}
								/* else if (jadw_file_laporan_ringkas == '') {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Lap. Ringkas"
											})
								}
								else if (jadw_file_lks == '') {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File LKS"
											})
								} */
								else{
									let formData = new FormData();									
									// formData.append("jadw_file_laporan_ringkas", jadw_file_laporan_ringkas)
									// formData.append("jadw_file_lks", jadw_file_lks)
									formData.append("jadw_file_kehadiran", jadw_file_kehadiran);
									formData.append("jadw_notulen_rapat", jadw_notulen_rapat);
									formData.append("jadw_tanggal_rapat_akhir", jadw_tanggal_rapat_akhir)
									formData.append("cust_nama", $('#cust_nama').val())
									formData.append("cust_email", $('#cust_email').val())
									formData.append("cust_id", $('#cust_id').val())
									formData.append("user_id", $('#user_id').val())
									formData.append("jadw_id", $('#jadw_id').val())
									formData.append("jadw_setujui_temuan", this.simpan_draft)
									
									// Submit Permohonan
									this.loading_submit = true;
									let self = this;
									$.ajax({
										url: `{{action("$module@storeUnggah", $data->jadw_id)}}`,
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
                    validateUploadKehadiran(event) {
                        
                    },
					validateUploadLks(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#jadw_file_lks").val("")
                        }
                    },
					validateUploadRingkas(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#jadw_file_laporan_ringkas").val("")
                        }
                    },
					async start() {
						$('#jadw_tanggal_rapat_akhir').datebox({
							required:true,
							editable: false,
							formatter:myformatter,
							parser:myparser,
							value:`@if(!empty($data->jadw_tanggal_rapat_akhir)) {{$data->jadw_tanggal_rapat_akhir}} @endif`,
							onSelect: async function(date){
								var data_date = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
							}
						});
						
						$('textarea#jadw_notulen_rapat').tinymce({
							invalid_elements: "script",
							plugins: 'autosave link image lists',
							relative_urls: false,
							height: 300,
							placeholder: '',
							images_reuse_filename: true,
							automatic_uploads: true,
							images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
							images_upload_credentials: true,
							toolbar: [
								{name: 'history', items: ['undo', 'redo']},
								{name: 'styles', items: ['styleselect']},
								{name: 'formatting', items: ['bold', 'italic']},
								{name: 'alignment', items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']},
								{name: 'list', items: ['bullist', 'numlist']},
								{name: 'indentation', items: ['outdent', 'indent']},
								{name: 'link', items: ['link', 'image']},
								{name: 'restore', items: ['restoredraft']},
							],
						});
						
                        setTimeout(async () => {
								$(".tab-content").height("100%");
							}, 1000);					
                    },
                }
            })
        })
    </script>
@endpush
