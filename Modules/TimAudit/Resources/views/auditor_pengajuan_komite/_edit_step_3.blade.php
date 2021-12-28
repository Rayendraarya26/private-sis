<div class="row" id="vueStepThree">
	<div class="col-md-12">
		<div class="form-group row">
			<label class="col-form-label col-sm-3" for="jadw_file_laporan_ringkas">
				File Laporan Ringkas *
				<br>
				<small>(pdf) yang sudah ditanda tangani basah</small>
			</label>
			<div class="col-sm-8">
				<input type="hidden" id="cust_nama" name="cust_nama" value="{{$data->sis_pelanggan->cust_nama}}">
				<input type="hidden" id="cust_email" name="cust_email" value="{{$data->sis_pelanggan->cust_email}}">
				<input type="hidden" id="cust_id" name="cust_id" value="{{$data->sis_pelanggan->cust_id}}">
				<input type="hidden" id="user_id" name="user_id" value="{{$data->sis_pelanggan->user_id}}">
				<input type="hidden" id="jadw_id" name="jadw_id" value="{{$data->jadw_id}}">
				
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
				<i class="fad fa-disk"></i> Simpan
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
								const jadw_file_laporan_ringkas = document.querySelector("#jadw_file_laporan_ringkas").files[0];
								const jadw_file_lks = document.querySelector("#jadw_file_lks").files[0];
								if (jadw_file_laporan_ringkas === '') {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Lap. Ringkas"
											})
								}
								else if (jadw_file_lks === '') {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File LKS"
											})
								}
								else{
									let formData = new FormData();									
									formData.append("jadw_file_laporan_ringkas", jadw_file_laporan_ringkas)
									formData.append("jadw_file_lks", jadw_file_lks)
									formData.append("cust_nama", $('#cust_nama').val())
									formData.append("cust_email", $('#cust_email').val())
									formData.append("cust_id", $('#cust_id').val())
									formData.append("user_id", $('#user_id').val())
									formData.append("jadw_id", $('#jadw_id').val())
									
									// Submit Permohonan
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
					validateUploadLks(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#jadw_file_lks").val("");
							
							this.status_submit = false;
                        }
						else{
							this.status_submit = true;
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

                            $("#jadw_file_laporan_ringkas").val("");
							this.status_submit = false;
                        }
						else{
							this.status_submit = true;
						}
                    },
					async start() {
						setTimeout(async () => {
								$(".tab-content").height("100%");
							}, 1000);					
                    },
                }
            })
        })
    </script>
@endpush
