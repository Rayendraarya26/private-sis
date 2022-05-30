@extends("layouts.layout_app")

@section('title', 'Persetujuan Pembatalan Permohonan')

@section('content')
<style>
	.datagrid-btable, .datagrid-header-inner, .datagrid-htable {
		width : 100%;
	}
</style>
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"> <i class="fad fa-arrow-left"></i> Kembali</a>
				@if(authorized("{$module}@procesCancel"))
                <a class="btn btn-sm btn-danger" href="#" onClick="confirmVerif()" style="margin-bottom: 20px"> <i class="fas fa-badge-check"></i> Setujui Pembatalan Permohonan?</a>
				@endif
			</div>
		</div>
		<hr/>
        <div class="row">
			<!-- Profile -->
        <div class="profile">

          <!-- Profile Banner -->
          <div class="profile__banner">

            <!-- Profile Banner Top -->
            <div class="profile__banner-top">
              <!-- Avatar Wrapper -->
              <div class="dt-avatar-wrapper">
                <!-- Info -->
                <div class="dt-avatar-info">
                  <span class="dt-avatar-name display-4 mb-2 font-weight-light">Permohonan dari "{{$dataPermohon->mohon_cust_nama}}"</span>

                </div>
                <!-- /info -->
              </div>
              <!-- /avatar wrapper -->

			  <div class="ml-sm-auto">
                <!-- List -->
                <ul class="dt-list dt-list-bordered dt-list-one-third">
                  <!-- List Item -->
                  <li class="dt-list__item text-center">
                    <h4 class="font-weight-medium mb-4 text-white">#{{$dataPermohon->mohon_id}}</h4>
                    <span class="d-inline-block f-12">No. Pengajuan</span>
                  </li>
                  <!-- /list item -->
                </ul>
                <!-- /list -->
              </div>
            </div>
            <!-- /profile banner top -->
          </div>
          <!-- /profile banner -->

          <!-- Profile Content -->
          <div class="profile-content">

            <!-- Grid -->
            <div class="row">
			  <!-- Grid Item -->
              <div class="col-xl-8 order-xl-1">
                <!-- Card -->
                <div class="card">
                  <!-- Card Header -->
                  <div class="card-header card-nav bg-transparent d-flex justify-content-between">
                    <h2 class="mb--20">Detail Pengajuan</h2>
                  </div>
                  <!-- /card header -->

                  <!-- Card Body -->
                  <div class="card-body pb-1">
					<ul class="card-header-links nav nav-underline" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#pane1" role="tab" aria-controls="pane1" aria-selected="true">Overview</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#pane2" role="tab" aria-controls="pane3" aria-selected="true">Dokumen Pengajuan</a>
                      </li>
					  <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#pane3" role="tab" aria-controls="pane4" aria-selected="true">Riwayat Pengajuan</a>
                      </li>
                    </ul>
                    <!-- Tab Content-->
                    <div class="tab-content mt-5">
                      <!-- Tab panel -->
                      <div id="pane1" class="tab-pane active">
						<table class="table table-hover mb-0">
							<thead>
								<tr>
								  <th class="" scope="col">File Permohonan Pembatalan</th>
								  <th class="" scope="col">:</th>
								  <th class="" scope="col">@if($dataPermohon->mohon_cancel_file != '') <a href="{{url($dataPermohon->mohon_cancel_file)}}" target="_blank">Download</a> @endif </th>
								</tr>
								<tr>
								  <th class="" scope="col">Alasan Pembatalan</th>
								  <th class="" scope="col">:</th>
								  <th class="" scope="col">@if($dataPermohon->mohon_cancel_reason != '') <p>{{$dataPermohon->mohon_cancel_reason}}</p> @endif </th>
								</tr>
								<tr>
								  <th class="" scope="col">Tanggal Pembatalan</th>
								  <th class="" scope="col">:</th>
								  <th class="" scope="col">@if($dataPermohon->mohon_cancel_at != '') <p>{{$dataPermohon->mohon_cancel_at?->format("Y-m-d H:i:s")}}</p> @endif </th>
								</tr>
							</thead>
						</table>
						  
                        <!-- List -->
						<div class="table-responsive">
						  <table class="table table-hover mb-0">
							<thead>
								<tr>
								  <th scope="col">#</th>
								  <th class="text-uppercase" scope="col"></th>
								  <th class="text-uppercase" scope="col"></th>
								  <th class="text-uppercase" scope="col"></th>
								</tr>
							</thead>
							<tbody>
								<tr><th scope="row">1</th><td>Mengajukan permohonan</td><td>:</td><td></td></tr>
								<tr>
									<td colspan="4">
										<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table table-bordered  mb-0">
											<thead>
												<tr>
												  <th class="text-uppercase" scope="col">Jenis</th>
												  <th class="text-uppercase" scope="col">Sertifikasi</th>
												</tr>
											</thead>
											<tbody>
												@foreach($dataPermohonSertifikasi as $dpser)
												<tr>
												  <td>@if($dpser->mohon_det_jenis_status == 'baru') Baru @else Perpanjang @endif</td>
												  <td>{{$dpser->sert_nama}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>
										</div>
									</td>
								</tr>
								<tr><th scope="row">2</th><td>Komoditi Yang Diajukan</td><td>:</td><td></td></tr>
								<tr>
									<td colspan="4">
										<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table table-bordered  mb-0">
											<thead>
												<tr>
												  <th class="text-uppercase" scope="col">Komoditi</th>
												  <th class="text-uppercase" scope="col">SNI</th>
												  <th class="text-uppercase" scope="col">Merk</th>
												  <th class="text-uppercase" scope="col">Type</th>
												  <th class="text-uppercase" scope="col">Ukuran</th>
												  <th class="text-uppercase" scope="col">Kapasitas Produksi/tahun</th>
												</tr>
											</thead>
											<tbody>
												@foreach($dataPermohonKomoditi as $dpk)
												<tr>
												  <td>{{$dpk->komodt_nama}}</td>
												  <td>@if($dpk->sert_is_product == 'ya') {{$dpk->mohon_kmditi_sni}} @else {{$dpk->sert_sni}} @endif</td>
												  <td>{{$dpk->mohon_kmditi_merk}}</td>
												  <td>{{$dpk->mohon_kmditi_tipe}}</td>
												  <td>{{$dpk->mohon_kmditi_ukuran}}</td>
												  <td>{{$dpk->mohon_kmditi_kapasitas_produksi_tahunan}} {{$dpk->mohon_kmditi_kapasitas_produksi_tahunan_satuan}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>
										</div>
									</td>
								</tr>
								<tr><th scope="row">3</th><td>Nama Klien</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_nama}}</a></td></tr>
								<tr><th scope="row">4</th><td>Akta Pendirian</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_nomor_akta_pendirian}}</a></td></tr>
								<tr><th scope="row">5</th><td>Nama Pemilik</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_nama_pemilik}}</a></td></tr>
								<tr><th scope="row">6</th><td>Nama Pimpinan</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_nama_pimpinan}}</a></td></tr>
								<tr><th scope="row">7</th><td>Nama Wakil Manajemen</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_nama_wakil_manajemen}}</a></td></tr>
								
								<tr><th scope="row">8</th><td>Setiap hari kerja, perusahaan bekerja dalam </td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_shif_kerja}} Shift</a></td></tr>
								<tr><th scope="row"></th><td>- Jumlah Manajemen</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_manajemen}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>- Jumlah Administrasi</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_administrasi}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>- Jumlah Bagian</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_bagian}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>- Jumlah Part-time</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_part_time}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>- Jumlah Operasional</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_operasional}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>&nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;Jumlah Shift 1</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_shift_1}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>&nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;Jumlah Shift 2</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_shift_2}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>&nbsp;&nbsp;&nbsp;&nbsp;>&nbsp;Jumlah Shift 3</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_shift_3}} Orang</a></td></tr>
								<tr><th scope="row"></th><td>- Non permanen di bawah kendali langsung perusahaan</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_jumlah_non_permanen}}</a></td></tr>
								<tr><th scope="row">9</th><td>Status perusahaan/klien</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->jenis_perusahaan_nama}}</a></td></tr>
								<tr><th scope="row">10</th><td>Luas Bangunan</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_luas_bangunan}}</a></td></tr>
								<tr><th scope="row">11</th><td>Luas Tanah</td><td>:</td><td><a href="javascript:void(0)" class="btn-link">{{$dataPermohon->mohon_cust_luas_tanah}}</a></td></tr>
								<tr>
									<td colspan="4">
									@foreach($dataPermohonPabrik as $dpp)
										<div class="table-responsive col-xl-12 col-md-12 col-12">
										<table class="table mb-0">
											<thead>
												<tr>
												  <th class="text-uppercase" scope="col">Pabrik</th>
												  <th class="text-uppercase" scope="col"></th>
												  <th class="text-uppercase" scope="col"></th>
												</tr>
											</thead>
											<tbody>
												<tr>
												  <td>(Kota, Kode Pos, Telp, Fax)</td>
												  <td>:</td>
												  <td>{{$dpp->mohon_pabrik_nama}} {{$dpp->mohon_pabrik_alamat}}, {{$dpp->kec_nama}}, {{$dpp->kab_nama}}, {{$dpp->prov_nama}} ;<hr/>
												   Kode Pos : {{$dpp->mohon_pabrik_kode_pos}};<hr/> Fax : {{$dpp->mohon_pabrik_nomor_fax}};<hr/> Telp : {{$dpp->mohon_pabrik_nomor_telp}};<hr/> Hp : {{$dpp->mohon_pabrik_nomor_hp}}
												  </td>
												</tr>
												  <td>Kegiatan Utama</td>
												  <td>:</td>
												  <td>{{$dpp->mohon_pabrik_kegiatan_utama}}</td>
												</tr>
												<tr>
												  <td>Jumlah Karyawan</td>
												  <td>:</td>
												  <td>{{$dpp->mohon_pabrik_jumlah_karyawan}} Orang</td>
												</tr>
												<tr>
												  <td>Luas Tanah</td>
												  <td>:</td>
												  <td>{{$dpp->mohon_pabrik_luas_tanah}}</td>
												</tr>
												<tr>
												  <td>Luas Bangunan</td>
												  <td>:</td>
												  <td>{{$dpp->mohon_pabrik_luas_bangunan}}</td>
												</tr>
											</tbody>
										</table>
										</div>
									@endforeach
									</td>
								</tr>
								<tr><th scope="row">12</th><td>Formulir Kelengkapan Permohonan</td><td>:</td><td><a href="javascript:void(0)" class="btn-link"><a href="{{url($dataPermohon->mohon_pertanyaan_filepath)}}" target="_blank" class="btn btn-xs btn-primary">Download</a></a></td></tr>
							</tbody>
						  </table>
						</div>
                      </div>
                      <!-- /tab panel -->

                      <!-- Tab panel -->
                      <div id="pane2" class="tab-pane">
						<!-- Card Body -->
						  <div class="dt-card__body" id="panel-dokumen">
							<div class="table-responsive col-xl-12 col-md-12 col-12">
								<table class="table table-hover mb-0">
									<thead>
										<tr>
										  <th class="text-uppercase" scope="col">Nama Dokumen</th>
										  <th class="text-uppercase" scope="col">Keterangan</th>
										  <th class="text-uppercase" scope="col">Download</th>
										</tr>
									</thead>
									<tbody>
										@foreach($dataPermohonanDokumen as $dpd)
										<tr>
										  <td>{{$dpd->jenis_dok_perusahaan_text}}</td>
										  <td>{{$dpd->mohon_dok_deskripsi}}</td>
										  <td><a href="{{url($dpd->mohon_dok_filepath)}}" target="_blank" class="btn btn-xs btn-primary">Download</a></td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						  </div>
						  <!-- /card body -->
                      </div>
                      <!-- /tab panel -->

                      <!-- Tab panel -->
                      <div id="pane3" class="tab-pane">
						<!-- Card Body -->
						  <div class="dt-card__body">
							@foreach($dataPermohonanStatus as $dps)
							<!-- Card -->
							<div class="card shadow-none horizontal rounded-0 pb-8 border-bottom">
							  <!-- Card Stacked -->
							  <div class="card-stacked">

								<!-- Card Body -->
								<div class="card-body py-sm-0 px-0 px-sm-6 px-md-8">

								  <!-- Badges -->
								  <span class="badge bg-teal text-white text-uppercase mb-2">{{$dps->status_tipe}}</span>
								  <!-- /badges -->

								  <!-- Card Title-->
								  <h3 class="card-title font-weight-normal text-truncate mb-2">{{$dps->status_judul}}</h3>
								  <!-- Card Title-->

								  <div class="card-text text-light-gray">{!! $dps->status_pesan !!}</div>

								</div>
								<!-- /card body -->

								<!-- Card Footer -->
								<div class="card-footer d-flex flex-column justify-content-between p-0 text-sm-right">
								  <!-- Pricing -->
								  <a href="javascript:void(0)" class="display-5  mb-6">
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
						<!-- /card body -->
                      </div>
                      <!-- /tab panel -->

                    </div>
                    <!-- /tab content-->
                  </div>
                  <!-- /card body -->

                </div>
                <!-- /card -->
              </div>
              <!-- /grid item -->

              <!-- Grid Item -->
              <div class="col-xl-4 order-xl-1">
                <!-- Grid -->
                <div class="row">
				  <!-- Grid Item -->
                  <div class="col-xl-12 col-md-12 col-12 order-xl-1">
                    <!-- Card -->
                    <div class="dt-card dt-card__full-height">
                      <!-- Card Header -->
                      <div class="dt-card__header">
                        <!-- Card Heading -->
                        <div class="dt-card__heading">
                          <h3 class="dt-card__title">Informasi Kontak</h3>
                        </div>
                        <!-- /card heading -->
                      </div>
                      <!-- /card header -->
                      <!-- Card Body -->
                      <div class="dt-card__body">
						<!-- Media -->
                        <div class="media mb-5">
						  <i class="icon icon-company icon-xl mr-5"></i>
                          <!-- Media Body -->
                          <div class="media-body">
                            <span class="d-block text-light-gray f-12 mb-1">Alamat</span>
                            <a href="javascript:void(0)">{{$dataPermohon->mohon_cust_alamat}}
							@if($dataPermohon->cust_asing == 'ya')
								{{$dataPermohon->negara_nama}}
							@else
								, {{$dataPermohon->kec_nama}}, {{$dataPermohon->kab_nama}}, {{$dataPermohon->prov_nama}}
							@endif</a>
                          </div>
                          <!-- /media body -->
                        </div>
                        <!-- /media -->
                        <!-- Media -->
                        <div class="media mb-5">
                          <i class="icon icon-email icon-xl mr-5"></i>
                          <!-- Media Body -->
                          <div class="media-body">
                            <span class="d-block text-light-gray f-12 mb-1">Mail</span>
                            <a href="javascript:void(0)">{{$dataPermohon->mohon_cust_email}}</a>
                          </div>
                          <!-- /media body -->
                        </div>
                        <!-- /media -->

                        <!-- Media -->
                        <div class="media">
                          <i class="icon icon-phone icon-xl mr-5"></i>
                          <!-- Media Body -->
                          <div class="media-body">
                            <span class="d-block text-light-gray f-12 mb-1">Telp</span>
                            <span class="h5">1. Telp : {{$dataPermohon->mohon_cust_nomor_telp}}</span><br/>
                            <span class="h5">2. Hp : {{$dataPermohon->mohon_cust_nomor_hp}}</span><br/>
                            <span class="h5">3. Fax : {{$dataPermohon->mohon_cust_nomor_fax}}</span>
                          </div>
                          <!-- /media body -->
                        </div>
                        <!-- /media -->
                      </div>
                      <!-- /card body -->
                    </div>
                    <!-- /card -->
                  </div>
                  <!-- /grid item -->
                </div>
                <!-- /grid -->
              </div>
              <!-- /grid item -->
            </div>
            <!-- /grid -->

          </div>
          <!-- /profile content -->

        </div>
        <!-- /profile -->
        </div>
    </div>
@endsection
@push("javascript")
    <script>
		function confirmVerif() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Detail Permohonan?`,
                text: `Apakah anda ingin men-setujui pembatalan permohonan untuk permohonan ini? (NB: Mengubah status menjadi 'Setuju Pembatalan' bersifat permanen dan tidak dapat di kembalikan)`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setujui?',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					window.location.href = `{{url("$url")}}/proses_cancel/{{$dataPermohon->mohon_id}}`;
                }
            });
        }
    </script>
@endpush

