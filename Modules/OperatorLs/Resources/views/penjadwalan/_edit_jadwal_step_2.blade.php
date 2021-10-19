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
				<div id="form-tambah" style="display:none;">
					<form action="#" id="form_id">
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cust_id">Pilih Tipe Jadwal</label>
							<div class="col-xl-9">
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="survailan" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe4" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('survailan')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe4">Survailent</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="tahap-1" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe1" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('tahap-1')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe1">Tahap I</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe2" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe2">Sertifikasi</label>
								  </div>
								  <div class="custom-control custom-radio custom-control-inline">
									<input value="re-sertifikasi" aria-describedby="jadw_audit_jenis_tipeHelp" type="radio" id="jadw_audit_jenis_tipe3" name="jadw_audit_jenis_tipe" class="custom-control-input" @click="setJenisAudit('re-sertifikasi')">
									<label class="custom-control-label" for="jadw_audit_jenis_tipe3">Re-Sertifikasi</label>
								  </div>
								<small id="jadw_audit_jenis_tipeHelp" class="form-text">Note: Silahkan pilih tipe audit items.</small>
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" for="cb_data_id" >Data Permohonan/Sertifikat</label>
							<div class="col-xl-8">
								<input type="text" class="form-control" id="cb_data_id">
								<small class="form-text">Note: Data tahap-I, re-sertifikasi dan sertifikasi untuk data permohonan; Data survailan untuk data sertifikat.</small>
								
								<input type="hidden"  id="jadw_audit_id" value="">
								<input type="hidden"  id="mohon_id" value="">
								<input type="hidden" id="sert_id" value="">
								<input type="hidden" id="cust_sert_id" value="">
								<input type="hidden" id="nomor_sertifikat" value="">
								<input type="hidden" id="nomor_referensi" value="">
								<input type="hidden" id="komodt_id" value="">
								<input type="hidden" id="komodt_nama" value="">
								<input type="hidden" id="tipe" value="">
								<input type="hidden" id="merk" value="">
								<input type="hidden" id="sni" value="">
								<input type="hidden" id="ukuran" value="">
						
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Komoditi</label>
							<div class="col-xl-8">
								<input class="form-control" id="cb_komoditi" value="">
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode NACE</label>
							<div class="col-xl-8">
								<select id="kode_nace" name="kode_nace" style="max-width:300px;">
								</select>
							</div>
						</div>
						
						<div class="form-group form-row" id="sertifikasi_komoditi">
							<label class="col-xl-3 col-form-label text-sm-left" >Kode EA</label>
							<div class="col-xl-8">
								<select id="kode_ea" name="kode_ea" style="max-width:300px;">
								</select>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Standart Acuan</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="standart_acuan"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Ruang Lingkup</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="ruang_lingkup"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Kegiatan Audit</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="kegiatan"></textarea>
							</div>
						</div>
						
						<div class="form-group form-row">
							<label class="col-xl-3 col-form-label text-sm-left" >Tujuan Audit</label>
							<div class="col-xl-8">
								<textarea class="form-control" id="tujuan_audit" value=""></textarea>
							</div>
						</div>
							
						<div class="col-md-12 komoditi-button">
							<a href="#" class="btn btn-sm btn-primary" onClick="simpanAction">
								<i class="fas fa-save"></i> Ubah
							</a>
							<a href="#" class="btn btn-sm btn-danger" onClick="cancelAction">
								<i class="fas fa-close"></i> Batal
							</a>
						</div>
						<hr/>
				</div>
			</div>
            <div class="col-md-12">
				
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
							@click="submitJadwal">
						<i class="fad fa-disk"></i> Simpan jadwal
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
                    status_submit: true,
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
					async submitJadwal() {
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
								// Step 1
								formData.append("tipe", 'edit-jadwal')
								formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`)
								formData.append("jadw_tanggal_status", `{{$dataJadwal->jadw_tanggal_status}}`)
								formData.append("jadw_tanggal_mulai", window.vueStepOne.jadw_tanggal_mulai)
								formData.append("jadw_tanggal_selesai", window.vueStepOne.jadw_tanggal_selesai)
								formData.append("jadw_jenis", window.vueStepOne.jenis)
								formData.append("cust_id", window.vueStepOne.cust_id)

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
            })
        })
    </script>
@endpush
