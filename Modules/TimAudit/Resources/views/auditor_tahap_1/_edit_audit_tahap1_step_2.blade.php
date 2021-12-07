@push('css')
    <style>
        .komoditi-button {
            padding-top: 15px;
        }
		
		.label-form {
            font-weight:bold;
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
				<div class="form-group">
					<label class="label-form">V. Audit kecukupan informasi terdokumentasi: *</label>
					<textarea class="form-control" name="kolom_v" id="kolom_v">@if(isset($dataAudit->aud_thp1_kolom_v)) {{$dataAudit->aud_thp1_kolom_v}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">VI. Kondisi Lapangan</label>
					<textarea class="form-control" name="kolom_vi" id="kolom_vi">@if(isset($dataAudit->aud_thp1_kolom_vi)) {{$dataAudit->aud_thp1_kolom_vi}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">VII. Status dan pemahaman persyaratan standar</label>
					<textarea class="form-control" name="kolom_vii" id="kolom_vii">@if(isset($dataAudit->aud_thp1_kolom_vii)) {{$dataAudit->aud_thp1_kolom_vii}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">VIII. Informasi yang diperlukan yang berkenaan dengan (lingkup sistem manajemen K3, proses dan lokasi perusahaan, identifikasi bahaya dan risiko dan perundang-undangan/peraturan K3, dari operasi perusahaan dan risiko) tersedia.</label>
					<textarea class="form-control" name="kolom_viii" id="kolom_viii">@if(isset($dataAudit->aud_thp1_kolom_viii)) {{$dataAudit->aud_thp1_kolom_viii}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">IX. Sumber daya yang tersedia</label>
					<textarea class="form-control" name="kolom_ix" id="kolom_ix">@if(isset($dataAudit->aud_thp1_kolom_ix)) {{$dataAudit->aud_thp1_kolom_ix}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">X. Konfirmasi program audit sertifikasi tahap 2</label>
					<textarea class="form-control" name="kolom_x" id="kolom_x">@if(isset($dataAudit->aud_thp1_kolom_x)) {{$dataAudit->aud_thp1_kolom_x}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">XI. Informasi pelaksanaan audit internal dan kaji ulang manajemen</label>
					<textarea class="form-control" name="kolom_xi" id="kolom_xi">@if(isset($dataAudit->aud_thp1_kolom_xi)) {{$dataAudit->aud_thp1_kolom_xi}} @endif</textarea>
				</div>
				<div class="form-group">
					<label class="label-form">XII. Kesimpulan</label>
					<textarea class="form-control" name="kolom_xii" id="kolom_xii">@if(isset($dataAudit->aud_thp1_kolom_xii)) {{$dataAudit->aud_thp1_kolom_xii}} @endif</textarea>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="aud_thp1_file_notulen">
						Kehadiran*
						<br>
						<small>(pdf/excel)</small>
					</label>
					<div class="col-sm-8">
						<input type="file" name="aud_thp1_file_notulen" id="aud_thp1_file_notulen" @change="validateUploadNotulen" accept="application/pdf">
						@if(!empty($dataAudit->aud_thp1_file_notulen))
							<hr/>
								<a href="{{asset($dataAudit->aud_thp1_file_notulen)}}" target="_blank">
									<i class="fad fa-download"></i> Download Notulen
								</a>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="aud_thp1_file_notulen">
						Kehadiran*
						<br>
						<small>(pdf/excel)</small>
					</label>
					<div class="col-sm-8">
						<input type="file" name="aud_thp1_file_daftar_hadir" id="aud_thp1_file_daftar_hadir" @change="validateUploadKehadiran" accept="application/pdf">
						@if(!empty($dataAudit->aud_thp1_file_daftar_hadir))
							<hr/>
								<a href="{{asset($dataAudit->aud_thp1_file_daftar_hadir)}}" target="_blank">
									<i class="fad fa-download"></i> Download Kehadiran
								</a>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label class="label-form">Hasil Akhir Audit Tahap I?</label>
					<div class="col-md-12">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="aud_thp1_status" id="aud_thp1_status1" value="memenuhi" @click="setStatusAudit('memenuhi')" @if(isset($dataAudit->aud_thp1_status)) @if($dataAudit->aud_thp1_status == 'memenuhi') checked @endif @endif>
                        <label class="form-check-label" for="aud_thp1_status1">Sudah Memenuhi Kecukupan Minimal</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="aud_thp1_status" id="aud_thp1_status2" value="tidak-memenuhi" @click="setStatusAudit('tidak-memenuhi')" @if(isset($dataAudit->aud_thp1_status)) @if($dataAudit->aud_thp1_status == 'tidak-memenuhi') checked @endif @endif>
                        <label class="form-check-label" for="aud_thp1_status2">Belum Memenuhi Kecukupan Minimal</label>
                      </div>
                    </div>
				</div>
				<div class="form-group">
					<label class="label-form">Simpan sebagai Draft?</label>
					<div class="col-md-12">
					  <div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="aud_thp1_ditutup" id="aud_thp1_ditutup1" value="ya" @click="setTutupAudit('ya')" @if(isset($dataAudit->aud_thp1_ditutup)) @if($dataAudit->aud_thp1_ditutup == 'ya') checked @endif @endif>
						<label class="form-check-label" for="aud_thp1_ditutup1">Tidak</label>
					  </div>
					  <div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="aud_thp1_ditutup" id="aud_thp1_ditutup2" value="tidak" @click="setTutupAudit('tidak')" @if(isset($dataAudit->aud_thp1_ditutup)) @if($dataAudit->aud_thp1_ditutup == 'tidak') checked @endif @endif>
						<label class="form-check-label" for="aud_thp1_ditutup2">Ya</label>
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
                    status_audit: @if(isset($dataAudit->aud_thp1_status)) @if($dataAudit->aud_thp1_status != '') `{{$dataAudit->aud_thp1_status}}` @else `` @endif @endif,
                    tutup_audit: @if(isset($dataAudit->aud_thp1_ditutup)) @if($dataAudit->aud_thp1_ditutup != '') `{{$dataAudit->aud_thp1_ditutup}}` @else `` @endif @endif,
                    aud_thp1_file_notulen: null,
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
					setTutupAudit(event) {
                        this.status_submit = true;
                        this.tutup_audit = `${event}`;
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
					validateUploadNotulen(event) {
                        let uploaded = event.target.files[0];
                        if (uploaded.type !== "application/pdf") {
                            swalWithBootstrapButtons({
                                title: `Validasi`,
                                text: "File harus bertipe PDF",
                                type: 'warning',
                            })

                            $("#aud_thp1_file_notulen").val("")
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
								if (@if($dataAudit->aud_thp1_file_notulen == '') $.trim($("#aud_thp1_file_notulen").val()) === "" @else status_from == false @endif) {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Notulen"
											})
								}
								
								else if (@if($dataAudit->aud_thp1_file_daftar_hadir == '') $.trim($("#aud_thp1_file_daftar_hadir").val()) === "" @else status_from == false @endif) {
									toastCenter({
												type: 'warning',
												title: "Silahkan Unggah File Daftar Hadir"
											})
								}
								else{
									formData.append("tipe", 'update-audit-tahap1');
									formData.append("cust_id", '{{$dataJadwal->cust_id}}');
									formData.append("aud_thp1_id", '{{$dataJadwal->aud_thp1_id}}');
									formData.append("sert_id", '{{$dataJadwal->sert_id}}');
									formData.append("mohon_id", '{{$dataJadwal->mohon_id}}');
									formData.append("jenis", '{{$dataJadwal->sert_tahap1_jenis}}');
									formData.append("kolom_v", tinyMCE.get('kolom_v').getContent());
									formData.append("kolom_vi", tinyMCE.get('kolom_vi').getContent());
									formData.append("kolom_vii", tinyMCE.get('kolom_vii').getContent());
									formData.append("kolom_viii", tinyMCE.get('kolom_viii').getContent());
									formData.append("kolom_x", tinyMCE.get('kolom_x').getContent());
									formData.append("kolom_ix", tinyMCE.get('kolom_ix').getContent());
									formData.append("kolom_xi", tinyMCE.get('kolom_xi').getContent());
									formData.append("kolom_xii", tinyMCE.get('kolom_xii').getContent());
									formData.append("status_audit", this.status_audit);
									formData.append("tutup_audit", this.tutup_audit);
									const file_daftar = document.querySelector("#aud_thp1_file_daftar_hadir").files[0];
									formData.append("aud_thp1_file_daftar_hadir", file_daftar)
									const file_notulen = document.querySelector("#aud_thp1_file_notulen").files[0];
									formData.append("aud_thp1_file_notulen", file_notulen)

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
							this.status_submit = @if(isset($dataAudit->aud_thp1_status)) @if($dataAudit->aud_thp1_status != '') true @else false @endif @endif;  
						}, 1000);					
                    },
					async tynimceForm() {
						$('textarea#kolom_v').tinymce({
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
							$('textarea#kolom_vi').tinymce({
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
							$('textarea#kolom_vii').tinymce({
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
							$('textarea#kolom_viii').tinymce({
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
							$('textarea#kolom_ix').tinymce({
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
							$('textarea#kolom_x').tinymce({
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
							$('textarea#kolom_xi').tinymce({
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
							$('textarea#kolom_xii').tinymce({
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
