@extends("layouts.layout_app")

@section('title', 'Penyusunan Komite')

@section('content')
    <div class="dt-content">
		<div class="col-xl-12">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
					  <div class="card-body p-0">									
						<table class="table">
							<tbody>
								<tr><td>Jenis Jadwal</td><td>: {{$dataJadwal->jadw_jenis}}</td></tr>
								<tr><td>Tanggal Jadwal</td><td>: {{$dataJadwal->jadw_tanggal_mulai?->format("d M Y")}} s/d {{$dataJadwal->jadw_tanggal_selesai?->format("d M Y")}}</td></tr>
								<tr><td>Nama Perusahaan</td><td>: {{$dataJadwal->cust_nama}}</td></tr>
								<tr><td>Alamat Perusahaan</td><td>: {{$dataJadwal->cust_alamat}}</td></tr>
								<tr><td>No. Referensi</td><td>: {{$dataJadwal->jadw_audit_nomor_referensi}}</td></tr>
								<tr><td>Kode NACE</td><td>: {{$dataJadwal->jadw_audit_kode_nace}}</td></tr>
								<tr><td>EA Code</td><td>: {{$dataJadwal->jadw_audit_kode_ea}}</td></tr>
								<tr><td>Komoditas</td><td>: {{$dataJadwal->komodt_nama}}</td></tr><tr><td>Ruang Lingkup</td><td>: {{$dataJadwal->jadw_audit_ruang_lingkup}}</td></tr>
								<tr><td>Standar Acuan</td><td>: {{$dataJadwal->jadw_audit_standart_acuan}}</td></tr>
								<tr><td>Kegiatan</td><td>: {{$dataJadwal->jadw_audit_kegiatan}}</td></tr>
								<tr><td>Tujuan Audit</td><td>: {{$dataJadwal->jadw_audit_tujuan_audit}}</td></tr>
							</tbody>
						</table>
					  </div>
					</div>
				</div>
				<div class="col-xl-12">	
					<div class="dt-card">
						<div class="dt-card__header">
							<div class="dt-card__heading">
								<h3 class="dt-card__title">Penunjukan dan Kesanggupan Tim</h3>
							</div>
						</div>
						<div class="dt-card__body">
							<div id="vueKesanggupan">
							@if($jenis == 'komite')
								<p>
								Kepada Yth:
								<br/>
								1. {{$dataJadwal->peg_nama}} (<b>{{$dataJadwal->jadw_tim_posisi}}</b>)
								<br/><br/>
								Mohon untuk bisa dilakukan review dan evaluasi terhadap kecukupan dokumen sertifikasi guna Penetapan :
								<br/>
								{{$dataJadwal->sert_nama}}
								<br/>
								atas nama :
								<br/>
								<h4 style="text-align:center;">{{$dataJadwal->cust_nama}}</h4>
								
								<div class="form-group custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ck_agreement" id="ck_agreement" aria-label="sanggup" v-model="agreement">
									<label class="custom-control-label" for="ck_agreement">Saya menyatakan  sanggup *).</label>
								</div>
								</p>
							@else
								<p>
								Kami menunjuk Saudara menjadi <b>{{$dataJadwal->jadw_tim_posisi}}</b> *)<br/>
								<div class="form-group custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ck_agreement" id="ck_agreement" aria-label="sanggup" v-model="agreement">
									<label class="custom-control-label" for="ck_agreement">Saya menyatakan  sanggup *) ditunjuk sebagai Tim Audit/PPC/TAT *)<br/>Saya menyatakan pernah melakukan/tidak pernah melakukan *) layanan konsultansi sistem manajemen terhadap perusahaan ini dalam 2 (dua) tahun terakhir..</label>
								</div>
								</p>
							@endif
							
								
								<div style="padding-top: 20px">
									<template v-if="loading_submit">
										<div class="fa-3x" style="text-align: center">
											<i class="fas fa-spinner fa-spin" style="color: #0390DE"></i>
										</div>
									</template>
									<template v-else>
										<button :disabled="!agreement"
												:class="{'btn': true, 'btn-primary':agreement, 'btn-outline-primary':!agreement,'btn-block':true}"
												@click="submitPermohonan"
										>
											<i class="fad fa-paper-plane"></i> Kirim
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
	const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });
		
        $(document).ready(function () {
            window.vueKesanggupan = new Vue({
                el: "#vueKesanggupan",
                data: {
                    agreement: false,
                    loading_submit: false,
                },
                methods: {
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Kirim Persetujuan ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								// Submit Permohonan
								let formData = new FormData();
                                formData.append("jadw_id", `{{$dataJadwal->jadw_id}}`);
                                formData.append("tipe", `kesanggupan-tim`);
                                formData.append("peg_id", `{{$dataJadwal->peg_id}}`);
                                formData.append("jenis", `{{$jenis}}`);
								
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
                }
            })
        });
    </script>
@endpush
