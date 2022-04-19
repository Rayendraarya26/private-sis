@extends("layouts.layout_app")

@section('title', 'Detail Permohonan')


@push("javascript")
    <script>		
		function confirmStatus(id_reg) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-danger mb-2',
                cancelButtonClass: 'btn btn-success mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Ajukan Permohonan ?`,
                text: `Ajukan ke tahap invoice permohonan dengan no pengajuan "{{$data_permohonan['id_reg']}}", fitur aksi ini bersifat permanen dan tidak dapat di kembalikan?`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ajukan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
				if (result.value) {
					$.messager.progress();
                    $.ajax({
                        url: `{{url("$url/update")}}`,
                        type: 'POST',
                        dataType: 'json',
                        data: {id_reg: id_reg},
                        success: function (response) {
							$.messager.progress('close');
                            $.messager.alert({
								title: 'Informasi',
								msg: response.message,
								fn: function(){
									window.location.href = `{{url("$url")}}`;
								}
							});
                        },
                        error: function (xhr) {
							$.messager.progress('close');
                            if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
                            else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
                        }
                    });
                }
            });
        }
    </script>
@endpush

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
				@if(authorized("{$module}@update"))
                <a class="btn btn-sm btn-info" href="javascript:void(0)" style="margin-bottom: 20px" onclick="confirmStatus({{$data_permohonan['id_reg']}})"> <i class="far fa-comment-alt-edit"></i> Update Status => Ajuan</a>
				@endif
			</div>
		</div>
		<hr/>
        <div class="row">
			<div class="profile">
			  <div class="profile__banner">
				<div class="profile__banner-top">
				  <div class="dt-avatar-wrapper">
					<div class="dt-avatar-info">
					  <span class="dt-avatar-name display-4 mb-2 font-weight-light">Permohonan dari "{{$data_permohonan['nama_pu']}}"</span>
					</div>
				  </div>
				  
				  <div class="ml-sm-auto">
					<ul class="dt-list dt-list-bordered dt-list-one-third">
					  <li class="dt-list__item text-center">
						<h4 class="font-weight-medium mb-4 text-white">Ajuan</h4>
						<span class="d-inline-block f-12">Status</span>
					  </li>
					</ul>
				  </div>
				</div>
			  </div>
			  
			  <div class="profile-content">
				<div class="row">
				  <div class="col-xl-4 order-xl-2">
					<div class="row">
					  <div class="col-xl-12 col-md-6 col-12 order-xl-1">
						<div class="dt-card dt-card__full-height">
						  <div class="dt-card__header">
							<div class="dt-card__heading">
							  <h3 class="dt-card__title">Kontak</h3>
							</div>
						  </div>
						  <div class="dt-card__body">
							<div class="media mb-5">
							  <i class="icon icon-company icon-xl mr-5"></i>
							  <!-- Media Body -->
							  <div class="media-body">
								<span class="d-block text-light-gray f-12 mb-1">Alamat</span>
								<a href="javascript:void(0)">{{$data_permohonan['alamat_pu']}}, {{$data_permohonan['kota_pu']}}, {{$data_permohonan['prov_pu']}}, {{$data_permohonan['negara_pu']}}, Kodepos {{$data_permohonan['kode_pos_pu']}}</a>
							  </div>
							  <!-- /media body -->
							</div>
							<div class="media mb-5">
							  <i class="icon icon-email icon-xl mr-5"></i>
							  <div class="media-body">
								<span class="d-block text-light-gray f-12 mb-1">Mail</span>
								<a href="javascript:void(0)">{{$data_permohonan['email']}}</a>
							  </div>
							</div>
							<div class="media">
							  <i class="icon icon-phone icon-xl mr-5"></i>
							  <div class="media-body">
								<span class="d-block text-light-gray f-12 mb-1">Telp</span>
								<span class="h5">{{$data_permohonan['no_telp']}}</span>
							  </div>
							</div>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  
				  <div class="col-xl-8 order-xl-1">
					<div class="card">
						<div class="card-body pb-1">
							<ul class="card-header-links nav nav-underline" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#paneOverview" role="tab" aria-controls="paneOverview" aria-selected="true">Overview</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#paneDokumen" role="tab" aria-controls="paneDokumen" aria-selected="true">Dokumen</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#paneProduk" role="tab" aria-controls="paneProduk" aria-selected="true">Produk</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#panePabrik" role="tab" aria-controls="panePabrik" aria-selected="true">Pabrik</a>
								</li>
							</ul>
							<br/>
							<br/>
							<!-- Tab Content-->
							<div class="tab-content mt-5">	
								<div id="paneOverview" class="tab-pane active">
									<div class="table-responsive">
										<table class="table mb-0">
											<tbody>
												<tr>
												  <td class="text-uppercase" scope="col">No. Pendaftaran</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['no_daftar']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Tanggal Daftar</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['tgl_daftar']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Jenis Usaha</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['jenis_usaha']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Jenis Daftar</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['jenis_daftar']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Skala Usaha</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['skala_usaha']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Nama PJ</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['nama_pj']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Telp PJ</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['no_kontak_pj']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Email PJ</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['email_pj']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Jenis Produk</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['jenis_produk']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Merk Dagang</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['merek_dagang']}}</a></td>
												</tr>
												<tr>
												  <td class="text-uppercase" scope="col">Area Pemasaran</td>
												  <td class="text-uppercase" scope="col">:</td>
												  <td class="text-uppercase" scope="col"><a href="javascript:void(0)" class="btn-link">{{$data_permohonan['area_pemasaran']}}</a></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div id="paneDokumen" class="tab-pane">
									<div class="dt-card__body" id="panel-dokumen">
										<div class="table-responsive col-xl-12 col-md-12 col-12">
											<table class="table table-hover mb-0">
												<thead>
													<tr>
													  <th class="text-uppercase" scope="col">Tipe Dokumen</th>
													  <th class="text-uppercase" scope="col">Keterangan</th>
													  <th class="text-uppercase" scope="col">Download</th>
													  <!-- <th class="text-uppercase" scope="col">Cek Status</th> -->
													</tr>
												</thead>
												<tbody>
													@foreach($data_permohonan['documents'] as $dpd)
													<tr>
													  <td>{{$dpd['tipe_dok']}}</td>
													  <td>{{$dpd['ket_lainnya']}}</td>
													  <td><a href="{{config("app.sihalal_folder_dokumen_url")}}{{$dpd['file_dok']}}" target="_blank" class="btn btn-xs btn-primary">Download</a></td>
													  <!-- <td>{{$dpd['ck_list']}}</td> -->
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div id="paneProduk" class="tab-pane">
									<div class="dt-card__body" id="panel-dokumen">
										<div class="table-responsive col-xl-12 col-md-12 col-12">
											<table class="table table-hover mb-0">
												<thead>
													<tr>
													  <th class="text-uppercase" scope="col">Nama Produk</th>
													  <!-- <th class="text-uppercase" scope="col">Publish Status</th> -->
													  <th class="text-uppercase" scope="col">Foto Produk</th>
													</tr>
												</thead>
												<tbody>
													@foreach($data_permohonan['products'] as $dpp)
													<tr>
													  <td>{{$dpp['reg_prod_name']}}</td>
													  <!-- <td>{{$dpp['reg_publish']}}</td> -->
													  <td>@if($dpp['foto_produk'] != '')<a href="https://ptsp.halal.go.id/file/{{$dpd['foto_produk']}}" target="_blank" class="btn btn-xs btn-primary">Download</a>@endif</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div id="panePabrik" class="tab-pane">
									<div class="table-responsive col-xl-12 col-md-12 col-12">
											<table class="table table-hover mb-0">
												<thead>
													<tr>
													  <th class="text-uppercase" scope="col">Nama pabrik</th>
													  <th class="text-uppercase" scope="col">Alamat</th>
													  <th class="text-uppercase" scope="col">Kode Pos</th>
													  <th class="text-uppercase" scope="col">Status Milik</th>
													  <th class="text-uppercase" scope="col">FASIL_ID</th>
													</tr>
												</thead>
												<tbody>
													@foreach($data_permohonan['factories'] as $dppab)
													<tr>
													  <td>{{$dppab['nama']}}</td>
													  <td>{{$dppab['alamat']}}, {{$dppab['kab_kota']}}, {{$dppab['provinsi']}}, {{$dppab['negara']}}</td>
													  <td>{{$dppab['kode_pos']}}</td>
													  <td>{{$dppab['status_milik']}}</td>
													  <td>{{$dppab['fasil_id']}}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
								</div>
							</div>
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</div>
        </div>
    </div>
@endsection