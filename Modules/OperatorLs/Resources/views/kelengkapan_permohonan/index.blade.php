@extends('layouts.layout_app')

@section('title', 'Kelengkapan Pernyataan Persetujuan')

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
                rownumbers: true,
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
                        title: "",
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
							let btnDetail = '';
							if(row.status_step == 're-upload'){
								btnDetail += `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=upload_file" class="btn btn-warning btn-xs btn-block"><i class="fad fa-upload"></i> Upload Ulang</a>`;
							}
							else if(row.status_step == 'upload'){
								btnDetail += `<a href="{{url("$url/detail")}}/${row.mohon_id}?action=upload_file" class="btn btn-primary btn-xs btn-block"><i class="fad fa-upload"></i> Upload Baru</a>`;
							}
							
							if(row.status_step != 'upload'){
								btnDetail += `<a class="btn btn-info btn-xs btn-block" target="_blank" href="${row.mohon_pernyataan_persetujuan_file}"><span class="fad fa-download"></span> Download</a>`;
							}
							
							return `@if(authorized("{$module}@detail")) ${btnDetail} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'status_pernyataan', title: 'Status<br/>Data', width: 100, sortable: true},
                    {field: 'mohon_id', title: 'No.<br/>Permohonan', width: 120, sortable: true},
                    {field: 'created_at', title: 'Tgl Pengajuan', width: 150, sortable: true},
                    {field: 'mohon_cust_nama', title: 'Nama Perusahaan', width: 320, sortable: true},
                    {field: 'sert_nama', title: 'Nama Sertifikasi', width: 320, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'sert_nama', type: 'textbox'},
					{
						field:'status_pernyataan',
						type:'combobox',
						options:{
							panelHeight:'auto',
							data:[{value:'',text:'Semua'},{value:'proses',text:'Proses'},{value:'ya',text:'Ter-Upload'}],
							onChange:function(value){
								if (value == ''){
									dg.datagrid('removeFilterRule', 'status_pernyataan');
								} else {
									dg.datagrid('addFilterRule', {
										field: 'status_pernyataan',
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
