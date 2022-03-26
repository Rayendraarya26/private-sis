@extends('layouts.layout_app')

@section('title', 'Set Pelunasan Billing')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-xl-12">
				<!--
					ada 3 cara:
					action(): mengarah ke controller
					url(): mengarah ke lokasi url
					route(): mengarah ke nama route
				-->
				<form method="POST" enctype="multipart/form-data" action="{{action("$module@update")}}" id="theForm">
                <a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
				<a class="btn btn-sm btn-success" href="javascript:void(0)" onClick="confirmLunas()" style="margin-bottom: 20px"> <i class="fas fa-save"></i> Set Lunas ?</a>
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


								<!-- Security CSRF TOKEN -->
								@csrf
								<input type="hidden" name="tipe" value="pelunasan">
								<input type="hidden" name="bil_id" value="{{old('bil_id') ?? $data_billing->bill_id}}">
								<input type="hidden" name="cust_id" value="{{old('cust_id') ?? $data_billing->cust_id}}">
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
												<td>Nama Pelanggan</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->cust_nama}}</a></td>
											</tr>
											<tr>
												<td>Nomor Billing</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_nomor_billing}}</a></td>
											</tr>
											<tr>
												<td>Tanggal Billing</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_billing_date?->format('Y-m-d')}}</a></td>
											</tr>
											<tr>
												<td>Jatuh Tempo</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_due_date?->format('Y-m-d')}}</a></td>
											</tr>
											<tr>
												<td>Total Billing(Rp.)</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{ number_format($data_billing->itms_bil_total, 2, ',', '.') }}</a></td>
											</tr>

											<tr>
												<td>File Invoice</th>
												<td>:</td>
												<td><a href="{{url($data_billing->bill_invoice_file)}}"  target="_blank"><i class="fas fa-download"></i> Download File</a></td>
											</tr>
										</tbody>
									</table>
								</div>

								<div class="form-group form-row">
									<div id="ttData" style="width:100%; min-width: 310px"></div>
								</div>


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
												<td>Informasi Pembayaran</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_payment_note}}</a></td>
											</tr>
											<tr>
												<td>Tanggal Pembayaran</th>
												<td>:</td>
												<td><a href="javascript:void(0)" class="btn-link">{{$data_billing->bill_payment_date?->format('Y-m-d')}}</a></td>
											</tr>
											<tr>
												<td>File Bukti Pembayaran</th>
												<td>:</td>
												<td>
													@if ($data_billing->bill_payment_file != '')
														<a href="{{url($data_billing->bill_payment_file)}}"  target="_blank"><i class="fas fa-download"></i> Download File</a></h5>
													@endif
												</td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
				</form>
            </div>
        </div>
    </div>

@endsection
@push('javascript')
    <script>
		function confirmLunas() {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success mb-2',
                cancelButtonClass: 'btn btn-warning mr-2 mb-2',
                buttonsStyling: false,
            });

            swalWithBootstrapButtons({
                title: `Set Lunas Data ?`,
                text: "Apakah anda yakin ingin men-set data billing ini menjadi lunas?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
					document.getElementById('theForm').submit();
                }
            });
        }

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
                    {field: 'itms_bil_desc', title: 'Deskripsi', width: 520, sortable: true},
                    // {field: 'itms_bil_total', title: 'Total(Rp.)', width: 100, sortable: true, align:'right',},
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

