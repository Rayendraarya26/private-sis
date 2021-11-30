@extends('layouts.layout_app')

@section('title', 'Pengajuan Komite')

@push('css')
    <style>
        .borderless tr td, .borderless th {
            border: none;
        }
    </style>
@endpush

@section('content')
    <div class="dt-content" id="laporanPage">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">
                                Informasi Audit Jadwal No #{{ $data->jadw_id }}
                            </h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <table class="table borderless">
									<tr>
										<td>Nama Perusahaan</td>
										<td>: {{$data->sis_pelanggan->cust_nama}}
										</td>
									</tr>
									<tr>
										<td>Tanggal Pelaksanaan</td>
										<td>
											: {{ $data->jadw_tanggal_mulai->isoFormat("LL") }}
											s/d {{ $data->jadw_tanggal_selesai->isoFormat("LL") }}
											@if($data->jadw_file_jadwal != '')<br/><a href="{{ url($data->jadw_file_jadwal) }}" target="_blank">Download Jadwal</a>@endif
										</td>
									</tr>
									<tr>
										<td>Ruang Lingkup <i>(Nace Code)</i></td>
										<td>:
											@if($data->sis_jadwal_audits->count() > 1)
												<ol>
													@foreach($data->sis_jadwal_audits as $audit)
														<li>{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}</li>
													@endforeach
												</ol>
											@else
												@foreach($data->sis_jadwal_audits as $audit)
													{{$audit->jadw_audit_ruang_lingkup . ' - ' . $audit->jadw_audit_kode_nace . (!$loop->last ? ' ; ' : '.')}}
												@endforeach
											@endif
										</td>
									</tr>

									<tr>
										<td>Komoditas</td>
										<td>:
											@foreach($data->sis_jadwal_audits as $audit)
												@if($audit->master_komoditi->komodt_nama != "")
													{{$audit->master_komoditi->komodt_nama . (!$loop->last ? ' ; ' : '.')}}
												@endif
											@endforeach
										</td>
									</tr>

									<tr>
										<td>Jenis Audit</td>
										<td>:
											@foreach($data->sis_jadwal_audits()->groupBy('jadw_audit_jenis')->get() as $audit)
												Audit {{ucwords($audit->jadw_audit_jenis) . (!$loop->last ? ' ; ' : '.')}}
											@endforeach
										</td>
									</tr>
								</table>
                            </div>


                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    Susunan TIM Audit
                                </label>
                                <div class="col-sm-9">
                                    <ol>
                                        @foreach($data->sis_jadwal_tims as $tim)
                                            <li>
                                                {{ucwords($tim->jadw_tim_posisi)}}:
                                                {{$tim->master_pegawai->peg_nama}}
                                            </li>
                                        @endforeach
										@if($data->jadw_file_kehadiran != '')
											<br/><a href="{{ url($data->jadw_file_kehadiran) }}" target="_blank">Download Daftar Hadir</a>
										@else
											<br/><span class="badge badge-danger mb-3">*Daftar Hadir Belum Diupload</span>
										@endif
										
										@if($data->jadw_file_notulen_rapat != '')
											<br/><a href="{{ url($data->jadw_file_notulen_rapat) }}" target="_blank">Download Notulen</a>
										@else
											<span class="badge badge-danger mb-3">*Notulen Belum Diupload</span>
										@endif
                                    </ol>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">
                                    Jumlah Temuan LKS
                                </label>
                                <div class="col-sm-9">
                                    <ul>
                                        <li>Kritis: {{$dataLKS['jumlah']['kritis']}}</li>
                                        <li>Mayor: {{$dataLKS['jumlah']['mayor']}}</li>
                                        <li>Minor: {{$dataLKS['jumlah']['minor']}}</li>
                                        <br>
                                        <li>Total: {{$dataLKS['jumlah']['total']}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-xl-12">	
				<div class="dt-card">
					<div class="dt-card__header">
						<div class="dt-card__heading">
                            <h3 class="dt-card__title" style="text-align: center">Detail Audit</h3>
						</div>
					</div>
					<div class="dt-card__body">
						<div class="form-group row">
							<label class="col-form-label col-sm-2">
								Laporan Ringkas
							</label>
							<div class="col-sm-10">
								<a href="{{ url("$url/detail?tipe=lap-ringkas&jadw_id=$data->jadw_id") }}" target="_blank">Lihat laporan ringkas</a>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-sm-2">
								Laporan Lengkap
							</label>
							<div class="col-sm-10">
								<a href="{{ url("$url/detail?tipe=lap-lengkap&jadw_id=$data->jadw_id") }}" target="_blank">Lihat laporan lengkap</a>
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
										<td>@if($tim->dftr_periksa_file != '')<a href="{{ url($tim->dftr_periksa_file) }}" target="_blank">Download</a>@endif</td>
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
										<td>@if($tim->logbook_filepath != '')<a href="{{ url($tim->logbook_filepath) }}" target="_blank">Download</a>@endif</td>
									</tr>
									@endforeach
								</table>
							</div>
						</div>
						@if(!empty($data->sis_audit_ppcs))
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
									@foreach($data->sis_audit_ppcs as $ppc)
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
										<td>@if($ppc->audit_ppc_filepath != '')<a href="{{ url($ppc->audit_ppc_filepath) }}" target="_blank">Download</a>@endif</td>
									</tr>
									@endforeach
								</table>
							</div>
						</div>
						@endif
					</div>
					<div id="vuePengajuan">
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
									<i class="fas fa-paper-plane"></i> Ajukan ke Komite
								</button>
							</template>
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
            window.vuePengajuan = new Vue({
                el: "#vuePengajuan",
                data: {
                    agreement: true,
                    loading_submit: false,
                },
                methods: {
                    submitPermohonan() {
                        swalWithBootstrapButtons({
                            title: `Ajukan ke Komite ?`,
                            text: `Proses akan berjalan beberapa saat, mohon bersabar untuk menunggu`,
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then(async (result) => {
                            if (result.value) {
								let formData = new FormData();
									formData.append("jadw_id", `{{$data->jadw_id}}`);									
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
											setTimeout(() => location.href = "{{url("$url")}}", 500)
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

