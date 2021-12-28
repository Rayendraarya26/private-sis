<div class="row" id="vueStepTwo">
	<div class="col-xl-12">
			<div class="">
			<div class="dt-card__body">
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						LKS :
					</label>
					<div class="col-sm-9">
						<a href="{{ url("$url/detail/$data->jadw_id/lks") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan Ringkas :
					</label>
					<div class="col-sm-9">
						<a href="{{ url("$url/detail/$data->jadw_id/lap-ringkas") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan Lengkap :
					</label>
					<div class="col-sm-9">
						<a href="{{ url("$url/detail/$data->jadw_id/lap-lengkap") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Tanggal Rapat :
					</label>
					<div class="col-sm-9">
					{!! $data->jadw_tanggal_rapat_akhir?->isoFormat("LL") !!}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Hadir :
					</label>
					<div class="col-sm-9">
						@if($data->jadw_file_kehadiran != '')<a href="{{ url($data->jadw_file_kehadiran) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Notulen Rapat :
					</label>
					<div class="col-sm-9">
					{!! $data->jadw_notulen_rapat !!}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Periksa File Upload Tim :
					</label>
					<div class="col-sm-9">
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
					<label class="col-form-label col-sm-3">
						Logbook Tim :
					</label>
					<div class="col-sm-9">
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
				@if(!empty($data->sis_audit_ppcs))
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Laporan PPC :
					</label>
					<div class="col-sm-9">
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
								<td>@if($ppc->audit_ppc_filepath != '')<a href="{{ url($ppc->audit_ppc_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
				@endif
				
				<div class="form-group row">
					<label class="col-form-label col-sm-3">
						Daftar Uji Sertifikat :
					</label>
					<div class="col-sm-9">
						<table class="table table-bordered mb-0">
							<tr>
								<th>Sertifikasi</th>
								<th>Nomor</th>
								<th>File</th>
							</tr>
							@foreach($dataSertifikat as $ser)
							<tr>
								<td>{{ucwords($ser->sert_nama)}}</td>
								<td>{{ucwords($ser->jadw_audit_sertifikat_nomor)}}</td>
								<td>@if($tim->jadw_audit_sertifikat_filepath != '')<a href="{{ url($tim->jadw_audit_sertifikat_filepath) }}" target="_blank"><i class="fad fa-download"></i> Download</a>@endif</td>
							</tr>
							@endforeach
						</table>
					</div>
				</div>
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
                    validate() {
						
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
