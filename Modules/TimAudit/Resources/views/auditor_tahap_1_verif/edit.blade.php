@extends("layouts.layout_app")

@section('title', 'Verifikasi Audit Tahap 1')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
				<a href="{{url($url)}}" class="btn btn-default"><i class="fad fa-arrow-left"></i>Kembali</a>
                <div class="dt-card">
                    <div class="dt-card__body table-responsive">
                        <div class="pb-3">
                            <span class="bg-orange">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Revisi
                            <br>
                            <span class="bg-light-green">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Perbaikan
                            Dikirim ke Auditor
                        </div>
						<table id="tinjauan" class="table table-striped table-bordered">
							<thead>
							<tr>
								<th rowspan="2">Klausul</th>
								<th rowspan="2">Persyaratan</th>
								<th colspan="2" class="text-center">
									Dokumen PT. {{strtoupper($data->sis_permohonan->mohon_cust_nama)}}
								</th>

								<th rowspan="2" class="text-center">Hasil Tinjauan <br>(OK / NO)</th>
								<th colspan="3" class="text-center">Perbaikan</th>
								<th rowspan="2"></th>
							</tr>
							<tr>
								<th>Kode Dokumen</th>
								<th>Judul Dokumen</th>
								<th>Ket Revisi</th>
								<th>Info Perbaikan</th>
								<th>File Upload</th>
							</tr>
							</thead>

							<tbody>
							@php
								$isClosed = 0;
							@endphp
							@foreach($data->sis_audit_tahap1_details as $detail)
								@php
									
									$isFixed = false;
									$dataRevisi = $detail->sis_audit_tahap1_revisis->whereIn("thp1_revisi_status", ["open", "fixed"])->sortByDesc("created_at")->first();
									if(empty($dataRevisi)){
										$isFixed = true;
										$dataRevisi = $detail->sis_audit_tahap1_revisis->sortByDesc("created_at")->first();
									}
									else{
										$isClosed++;
									}
								@endphp

								<tr class="{!! (($detail->sis_audit_tahap1_revisis->count() == 0 || $dataRevisi?->thp1_revisi_status == "closed") ? '' : (!$isFixed ? 'bg-orange' : 'bg-light-green')) !!}">
									<td style="padding-left: 10px">{{$detail->aud_thp1_det_thp1_nomor}}</td>
									<td>{{$detail->aud_thp1_det_peryataan}}</td>
									<td>{{$detail->aud_thp1_det_kode_dok}}</td>
									<td>{{$detail->aud_thp1_det_judul_dok}}</td>
									<td class="text-center">{{ucwords($detail->aud_thp1_det_hasil_tinjauan)}}</td>
									{{--<td>{{$detail->aud_thp1_det_keterangan}}</td>--}}

									{{--Field Revisi--}}
									@if($detail->sis_audit_tahap1_revisis->count() > 0)
										<td>{{$dataRevisi->thp1_revisi_catatan}}</td>
										<td>{{$dataRevisi->thp1_revisi_perbaikan}}</td>
										<td>
											@if($dataRevisi->sis_audit_tahap1_revisi_files->count() > 0)
												<ul>
													@foreach($dataRevisi->sis_audit_tahap1_revisi_files as $revisiFile)
														<li>
															<a href="{!! asset($revisiFile->thp1_revisi_file_path) !!}"
															   target="_blank">
																Berkas {{$loop->iteration}}
															</a>
														</li>
													@endforeach
												</ul>
											@endif
										</td>
										@if($dataRevisi->thp1_revisi_status == 'fixed')
										<td>
											<button class="btn btn-primary btn-xs btn-block"
														onClick="processVerifikasi('{{$detail->aud_thp1_det_id}}')">
													<i class="fas fa-check"></i> Close
												</button>

												<button class="btn btn-warning btn-xs btn-block"
														onClick="propmtRevisi('{{$detail->aud_thp1_det_id}}')">
													<i class="fas fa-edit"></i> Revisi
												</button>
										</td>
										@else
											<td></td>
										@endif
									@else
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									@endif
								</tr>
							@endforeach
							</tbody>
						</table>
						@if($isClosed == 0 && $isKetua)
							@if($data->aud_thp1_lap_verifikasi_status == 'ya')
							<div style="float: right">
								<button class="btn btn-primary" type="button" id="tutupTahap1" onclick="promptAgree({{$data->jadw_id}})">
									<i class="fad fa-save"></i> Tutup Tahap I
								</button>
							</div>
							@else
								<p style="color:red;text-align:center;">Silahkan isikan data-data seperti logbook file dan tulis laporan akhir tahap 1 sampai terverifikasi terlebih dahulu!!!</p>
							@endif
						@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="modal fade" id="modalBerkas" tabindex="-1" role="dialog" aria-labelledby="modalBerkas" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<!-- Modal Content -->
			<div class="modal-content">
			@csrf
			<!-- Modal Header -->
				<div class="modal-header">
					<h3 class="modal-title" id="modalBerkasTitle">
						Unggah Berkas
					</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<!-- /modal header -->

				<!-- Modal Body -->
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-1"></div>
						<div class="col-sm-10">
							<div class="form-group">
								<label for="berkas_ket">*Unggah <b>Scan Verifikasi Tahap 1</b> yang sudah diberi TTD</label>
								<input type="file" class="form-control" id="file_verifikasi" accept="application/pdf">
							</div>
							<div class="form-group">
								<label for="berkas_ket">*Unggah <b>Scan Laporan</b> yang sudah diberi TTD</label>
								<input type="file" class="form-control" id="file_laporan" accept="application/pdf">
							</div>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
				<!-- /modal body -->

				<!-- Modal Footer -->
				<div class="modal-footer">
					<button id="simpanBerkas" type="button" onclick="promptAgree({{$data->jadw_id}})"
							class="btn btn-success btn-sm">
						Simpan
					</button>
				</div>
				<!-- /modal footer -->
			</div>
			<!-- /modal content -->
		</div>
	</div>
@endsection

@push('javascript')
    <script>
		function showModalBerkas() {
            $("#modalBerkas").modal('show')
        }
		
		function submitApproval() {
            try {
                $("#tutupTahap1").attr("disabled", true)
                let formData = new FormData();
                formData.append('aud_thp1_id', {{$data->aud_thp1_id}})
                formData.append('aud_thp1_ditutup', 'ya')
                formData.append('tipe', 'tutup-tahap1')
				formData.append('user_id', `{{$data->sis_permohonan->user_id}}`);
				formData.append('cust_nama', `{{strtoupper($data->sis_permohonan->mohon_cust_nama)}}`);
				formData.append('cust_email', `{{strtoupper($data->sis_permohonan->mohon_cust_email)}}`);
/* 
                let fileLKS = document.querySelector("#file_verifikasi").files[0];
                    validateBerkas(fileLKS);
                    formData.append('file_verifikasi', fileLKS)

                    let fileLapRing = document.querySelector("#file_laporan").files[0];
                    validateBerkas(fileLapRing);
                    formData.append('file_laporan', fileLapRing)
 */
				$.ajax({
                    url: `{{url("$url/update")}}`,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: async function (res) {
                        toastCenter({
                            type: 'success',
                            title: res.message
                        })

                        location.href = "/{{$url}}"
                    },
                    error: function (xhr) {
                        if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                        else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
						$("#tutupTahap1").attr("disabled", false);
                    }
                });
            } catch (error) {
                toastCenter({type: 'error', 'title': error})
            }

        }

        function validateBerkas(berkas) {
            if (berkas == null) throw `Berkas tidak dapat kosong`
            if (berkas.type != "application/pdf") {
                throw `File ${berkas.name} harus berformat PDF`
            }
        }
		
		function promptAgree() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-danger mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: 'Tutup Tahap 1 ?',
                html: `Keputusan ini bersifat permanen dan tidak dapat dikembalikan<br><br> tekan ESC untuk batal`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                closeOnConfirm: false,
                closeOnCancel: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    submitApproval()
                }
            });
        }
		
		function processVerifikasi(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-primary mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Closed Temuan ?`,
                text: "Apakah anda yakin ingin menutup temuan ini?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Tutup',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    let formData = new FormData();
					formData.append('tipe', 'tutup-temuan');
					formData.append('aud_thp1_det_id', `${id}`);
					
					$.ajax({
						url: `{{url("$url/update")}}`,
						type: 'post',
						processData: false,
						contentType: false,
						data: formData,
						success: async function (res) {
							setTimeout(() => location.href = "{{url("$url/edit?aud_thp1_id=$data->aud_thp1_id")}}", 100);
						},
						error: function (xhr) {
							self.loading_submit = false;
							if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
							else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
						}
					});
                }
            });
        }
				
        function propmtRevisi(id) {
			Swal.fire({
				title: 'Silahkan isikan catatan revisi',
				input: 'textarea',
				inputAttributes: {
				autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonText: 'Revisi',
				showLoaderOnConfirm: true,
				preConfirm: (revisi) => {
					let formData = new FormData();
					formData.append('thp1_revisi_catatan', `${revisi}`);
					formData.append('tipe', 'revisi-temuan');
					formData.append('aud_thp1_det_id', `${id}`);
					formData.append('aud_thp1_id', `{{$data->aud_thp1_id}}`);
					formData.append('user_id', `{{$data->sis_permohonan->user_id}}`);
					formData.append('cust_nama', `{{strtoupper($data->sis_permohonan->mohon_cust_nama)}}`);
					formData.append('cust_email', `{{strtoupper($data->sis_permohonan->mohon_cust_email)}}`);
					if(revisi != ''){
						return fetch(`{{url("$url/update")}}`, {
							headers: {
								"X-CSRF-TOKEN": `{{ csrf_token() }}`
							  },
							  method: "POST",
							  credentials: "same-origin",
							  body: formData
						})
						.then(response => {
							if (!response.ok) {
							  throw new Error(response.statusText)
							}
							return response.json()
						})
						.catch(error => {
							  console.log(error);
							Swal.showValidationMessage(
							  `Request failed: ${error}`
							)
						})
					}
					else{
						return Swal.showValidationMessage(
							  `Request failed: Silahkan isikan text`
							);
					}
					
				},
				allowOutsideClick: () => !Swal.isLoading()
				}).then((obj) => {
					if (obj.value.results.isConfirmed) {
						setTimeout(() => location.href = "{{url("$url/edit?aud_thp1_id=$data->aud_thp1_id")}}", 100);
					}
					else{
						toastCenter({type: 'error', 'title': 'Error data tidak bisa disimpan.'});
					}
				});
		}
    </script>
@endpush


