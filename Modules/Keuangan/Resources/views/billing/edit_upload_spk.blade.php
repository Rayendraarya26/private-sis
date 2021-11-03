@extends('layouts.layout_app')

@section('title', 'Upload SPK')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
                <a class="btn btn-sm btn-default"
                   href="{{url("$url")}}"
                   style="margin-bottom: 20px">
                    <i class="fad fa-arrow-left"></i> Kembali
                </a>
                <div class="dt-card">
                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        {!! implode('', $errors->all('<li>:message</li>')) !!}
                                    </div>
                                @endif
                                @if(session('message'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('message') }}
                                    </div>
								@endif

                            <!--
                                    ada 3 cara:
                                    action(): mengarah ke controller
                                    url(): mengarah ke lokasi url
                                    route(): mengarah ke nama route
                                -->
                                <form method="POST" enctype="multipart/form-data" action="{{action("$module@update")}}">
                                    <!-- Security CSRF TOKEN -->
                                    @csrf
                                    <input type="hidden" name="tipe" value="upload-spk">
                                    <input type="hidden" name="bil_id" value="{{old('bil_id') ?? $data_billing->bill_id}}">
                                    <input type="hidden" name="bill_nomor_billing" value="{{old('bill_nomor_billing') ?? $data_billing->bill_nomor_billing}}">
									<div class="table-responsive">
										<table class="table table-hover mb-0">
											<thead>
												<tr>
												  <th scope="col">#</th>
												  <th class="text-uppercase" scope="col"></th>
												  <th class="text-uppercase" scope="col"></th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<th scope="row">Nama Pelanggan</th>
													<td>:</td>
													<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->cust_nama}}</a></td>
												</tr>
												<tr>
													<th scope="row">Nomor Billing</th>
													<td>:</td>
													<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_nomor_billing}}</a></td>
												</tr>
												<tr>
													<th scope="row">Tanggal Billing</th>
													<td>:</td>
													<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_billing_date?->format('Y-m-d')}}</a></td>
												</tr>
												<tr>
													<th scope="row">Jatuh Tempo</th>
													<td>:</td>
													<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_due_date?->format('Y-m-d')}}</a></td>
												</tr>
												<tr>
													<th scope="row">File Invoice</th>
													<td>:</td>
													<td><a href="{{url($data_billing->bill_invoice_file)}}" class="btn-link">Download File</a></td>
												</tr>
											</tbody>
										</table>
                                    </div>
									
									<div class="form-group form-row">
										<div id="ttData" style="width:100%; min-width: 310px"></div>
									</div>
									
									
									<div class="form-group form-row">
										<label class="col-xl-3 col-form-label text-sm-left" for="bill_file_spk" >SPK File</label>
										<div class="col-xl-8">
											<input type="file" class="form-control" aria-label="File Invoice" accept="application/pdf" name="bill_file_spk" id="bill_file_spk">
											@if ($data_billing->bill_file_spk != '')
												<h5>File Lama <a href="{{url($data_billing->bill_file_spk)}}" target="_blank">Download</a></h5>
											@endif
											<small><span>Upload file harus berjenis PDF, silahkan isikan kosong jika tidak ingin mengganti</span></small>
										</div>
									</div>
									
									
                                    <div class="form-buttons-w">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('javascript')
    <script>
        $(document).ready(function () {			
			let dg = $('#ttData').datagrid({
                method: 'get',
                height: 250,
                url: `{{ url("$url/ajax?action=datagrid-billing-items") }}&bill_id={{$data_billing->bill_id}}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: false,
				onError: function (index, row) {
					$.messager.alert('Informasi', 'Terjadi kesalahan load data.', 'warning');
					$('#ttData').datagrid('reload');
				},
                frozenColumns: [[
					{field: 'is_new', hidden: true},
					{field: 'bill_id', title: '', hidden: true},
					{field: 'itms_bil_id', title: '', hidden: true},
                    
                ]],
                columns: [[
                    {field: 'itms_bil_tipe', title: 'Tipe', width: 100, sortable: true,},
                    {field: 'itms_bil_desc', title: 'Deskripsi', width: 320, sortable: true},
                    {field: 'itms_bil_total', title: 'Total(Rp.)', width: 100, sortable: true, align:'right',},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'itms_bil_total', type: 'label'},
                    {field: 'mohon_id', type: 'label'},
                    {field: 'cust_sert_id', type: 'label'},
                ]);
        });
    </script>
@endpush

