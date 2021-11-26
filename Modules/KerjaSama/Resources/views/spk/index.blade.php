@extends('layouts.layout_app')

@section('title', 'Upload SPK')

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
		function statusStyle(value,row,index){
            if (value == 're-upload'){
                return 'background-color:blue;color:white;';
            }
			else{
				return 'background-color:#ffee00;color:red;';
			}
        }
		
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
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let btnDetail = '';
							if(row.status_step == 're-upload'){
								btnDetail = `<a href="{{url("$url/detail")}}?action=detail-permohonan&mohon_id=${row.mohon_id}" class="btn btn-warning btn-xs btn-block"><i class="fad fa-upload"></i> Upload Ulang</a>`;
							}
							else{
								btnDetail = `<a href="{{url("$url/detail")}}?action=detail-permohonan&mohon_id=${row.mohon_id}" class="btn btn-primary btn-xs btn-block"><i class="fad fa-upload"></i> Upload Baru</a>`;
							}
                            return `@if(authorized("{$module}@detail")) ${btnDetail} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'mohon_id', title: 'No.<br/>Permohonan', width: 120, sortable: true},
                    {field: 'status_step', title: 'Status<br/>SPK', width: 120, sortable: true, styler:statusStyle,
						formatter: function (val, row) {
							if(row.status_step == 're-upload'){
								return `Sudah Diupload`;
							}
							else{
								return `Belum Diupload`;
							}
                        }
					},
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
                        field: 'status_step',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
							value: '',
                            data: [
                                {value: 'belum', text: 'Belum Diupload'},
                                {value: 'sudah', text: 'Sudah Diupload'},
                                {value: '', text: 'Semua'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'status_step',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                ]);
        });
    </script>
@endpush
