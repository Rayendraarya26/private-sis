@extends('layouts.layout_app')

@section('title', 'Upload Surat Tagihan Biaya')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
				@if(session('message'))
					<div class="alert alert-primary alert-dismissible fade show" role="alert">
						{!! session('message') !!}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
				@endif
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Permohonan Sertifikasi</h3>
                        </div>
                    </div>
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid-permohonan") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                toolbar: '#toolbar',
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 90,
                        align: 'center',
                        formatter: function (val, row) {
							let btnDetail = '';
							if(row.mohon_tagihan_biaya_status == 'proses'){
								if(row.status_step == 're-upload'){
									btnDetail = `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=detail-permohonan" class="btn btn-warning btn-xs btn-block"><i class="fad fa-upload"></i> Upload Ulang</a>`;
								}
								else{
									btnDetail = `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=detail-permohonan" class="btn btn-primary btn-xs btn-block"><i class="fad fa-upload"></i> Upload Baru</a>`;
								}
							}
							else if(row.mohon_tagihan_biaya_status == 'tidak'){
								btnDetail = `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=detail-permohonan" class="btn btn-success btn-xs btn-block"><i class="fad fa-table"></i> Detail Permohonan</a>`;
							}
							else if(row.mohon_tagihan_biaya_status == 'setuju'){
								btnDetail = `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=detail-permohonan" class="btn btn-success btn-xs btn-block"><i class="fad fa-table"></i> Detail Permohonan</a>`;
							}
							
                            return `@if(authorized("{$module}@detail")) ${btnDetail} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'mohon_id', title: 'No.<br/>Permohonan', width: 120, sortable: true},
                    {field: 'created_at', title: 'Tgl Pengajuan', width: 150, sortable: true},
                    {field: 'mohon_cust_nama', title: 'Nama Perusahaan', width: 320, sortable: true},
                    {field: 'mohon_tagihan_biaya_status', title: 'Status<br/>Surat', width: 100, sortable: true, align: 'center',
						formatter: function (val, row) {
							let statusLabel = '';
							
							if(row.mohon_tagihan_biaya_status == 'proses'){
								statusLabel = `Proses`;
							}
							else if(row.mohon_tagihan_biaya_status == 'tidak'){
								statusLabel = `Ditolak`;
							}
							else if(row.mohon_tagihan_biaya_status == 'setuju'){
								statusLabel = `Setuju`;
							}
							
							return statusLabel;
						}
					},
                    {field: 'sert_nama', title: 'Pengajuan Sertifikasi', width: 320, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
					{
						field:'mohon_tagihan_biaya_status',
						type:'combobox',
						options:{
							panelHeight:'auto',
							data:[{value:'',text:'Semua'},{value:'proses',text:'Proses'},{value:'setuju',text:'Setuju'},{value:'tidak',text:'Ditolak'}],
							onChange:function(value){
								if (value == ''){
									dg.datagrid('removeFilterRule', 'mohon_tagihan_biaya_status');
								} else {
									dg.datagrid('addFilterRule', {
										field: 'mohon_tagihan_biaya_status',
										op: 'equal',
										value: value
									});
								}
								dg.datagrid('doFilter');
							}
						}
					}
                ]);
        });
    </script>
@endpush
