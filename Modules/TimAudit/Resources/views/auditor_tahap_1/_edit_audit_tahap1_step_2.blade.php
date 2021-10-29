@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
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
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="status_pesan">V. Audit kecukupan informasi terdokumentasi: *</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Pesan revisi..." name="kolom_v" id="textarea">{{old('status_pesan')}}</textarea>
					</div>
				</div>



VI. Kondisi Lapangan





VII. Status dan pemahaman persyaratan standar




VIII. Informasi yang diperlukan yang berkenaan dengan (lingkup sistem manajemen K3, proses dan lokasi perusahaan, identifikasi bahaya dan risiko dan perundang-undangan/peraturan K3, dari operasi perusahaan dan risiko) tersedia.





IX. Sumber daya yang tersedia




X. Konfirmasi program audit sertifikasi tahap 2



XI. Informasi pelaksanaan audit internal dan kaji ulang manajemen



XII. Kesimpulan




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
						<i class="fad fa-disk"></i> Simpan jadwal
					</button>
				</template>
			</div>
		</div>
    </div>
</div>

@push('javascript')

    <script>
		tinymce.init({
			selector: '#textarea',
			plugins: 'autosave link image code lists',
			relative_urls: false,
			height: 500,
			placeholder: '',
			images_reuse_filename: true,
			automatic_uploads: true,
			images_upload_url: '{{url("$url/ajax?action=tinymce-uploadimage")}}',
			images_upload_credentials: true,
			toolbar: [{
				name: 'history',
				items: ['undo', 'redo']
			},
				{name: 'styles',items: ['styleselect']},
				{name: 'formatting',items: ['bold', 'italic']},
				{name: 'alignment',items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']},
				{name: 'list',items: ['bullist', 'numlist']},
				{name: 'indentation',items: ['outdent', 'indent']},
				{name: 'link',items: ['link', 'image']},
				{name: 'restore',items: ['restoredraft']},
			],
		});
			
		$(document).ready(function () {
            window.vueStepTwo = new Vue({
                el: "#vueStepTwo",
                data: {	
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
					async submitAudit() {
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
								formData.append("tipe", 'edit-jadwal')

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
                    },
                    async start() {
                        setTimeout(async () => {
							$(".tab-content").height("100%");
						}, 1000);					
                    },
                }
            });
			
			
        });		
    </script>
@endpush
