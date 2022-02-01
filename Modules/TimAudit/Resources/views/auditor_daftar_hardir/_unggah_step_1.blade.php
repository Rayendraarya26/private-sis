<div class="row" id="vueStepOne">
	<div class="col-xl-12">
		<div class="">
			<div class="dt-card__header">
				<div class="dt-card__heading">
					<h3 class="dt-card__title" style="text-align: center">
						JADWAL AUDIT
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
									: {{ $data->jadw_tanggal_mulai }}
									s/d {{ $data->jadw_tanggal_selesai }}
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
</div>

@push('javascript')
    <script>
        $(document).ready(function () {
            window.vueStepOne = new Vue({
                el: "#vueStepOne",
                data: {

                },
                mounted() {
					setTimeout(() => {
                        const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                        if (currentStep === 0) {
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
