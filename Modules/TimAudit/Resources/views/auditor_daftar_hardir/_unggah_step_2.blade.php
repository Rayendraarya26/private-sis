<div class="row" id="vueStepTwo">
	<div class="col-xl-12">
		<div class="">
			<div class=" tabs-container">
				<ul class="nav nav-tabs" role="tablist">
				  <li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tb2" role="tab" aria-controls="tb2"
					   aria-selected="true">Detail Pelaksanaan</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tb3" role="tab" aria-controls="tb2"
					   aria-selected="true">Log Persetujuan Temuan</a>
				  </li>
				</ul>
				<div class="tab-content">								  
				  <div id="tb2" class="tab-pane active">
					<div class="">
						<div class="dt-card__body">
							<div class="form-group row">
								<label class="col-form-label col-sm-2">
									Laporan Ringkas
								</label>
								<div class="col-sm-10">
									<a href="{{ url("$url/detail?tipe=lap-ringkas&jadw_id=$data->jadw_id") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-2">
									LKS
								</label>
								<div class="col-sm-10">
									<a href="{{ url("$url/detail?tipe=lks&jadw_id=$data->jadw_id") }}" target="_blank"><i class="fad fa-download"></i> Download</a>
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
				  
				  <div id="tb3" class="tab-pane">
					<div class="">
					  <div class="dt-card__body">
						@foreach($SisJadwalLog as $dps)
						<!-- Card -->
						<div class="card shadow-none horizontal rounded-0 pb-8 border-bottom">
						  <!-- Card Stacked -->
						  <div class="card-stacked">

							<!-- Card Body -->
							<div class="card-body py-sm-0 px-0 px-sm-6 px-md-8">

							  <!-- Badges -->
							  <span class="badge bg-teal text-white text-uppercase mb-2">Revisi</span>
							  <!-- /badges -->

							  <!-- Card Title-->
							  <h3 class="card-title font-weight-normal text-truncate mb-2">{{$dps->jlog_judul}}</h3>
							  <!-- Card Title-->

							  <div class="card-text text-light-gray">{!! $dps->jlog_pesan !!}</div>

							</div>
							<!-- /card body -->

							<!-- Card Footer -->
							<div class="card-footer d-flex flex-column justify-content-between p-0 text-sm-right">
							  <!-- Pricing -->
							  <a href="javascript:void(0)" class="display-5 mb-6">
								<i class="icon icon-calendar icon-fw mr-2"></i><span class="align-middle" style="font-size:12px;">{{$dps->created_at?->format("Y-m-d H:i:s")}}</span> </a>
							  <!-- /pricing -->
							</div>
							<!-- /card footer -->

						  </div>
						  <!-- /card stacked -->

						</div>
						<!-- /card -->
						@endforeach
					  </div>
					</div>
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
