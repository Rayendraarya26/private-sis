@extends('layouts.layout_app')

@section('title', 'Upload Log Book Auditor Tahap I')

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
                            <h3 class="dt-card__title">Data Jadwal Audit dan File Upload Log Book Auditor</h3>
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
                url: `{{ url("$url/ajax?action=datagrid-jadwal-audit") }}`,
                rownumbers: false,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
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
							let btnEdit = '';
							if(row.status_upload == 're-upload'){
								btnEdit = `<a href="{{ url("$url/upload") }}?aud_thp1_id=${row.aud_thp1_id}" class="btn btn-warning btn-xs btn-block"><i class="fas fa-cloud-upload"></i> Re-Upload</a>`;
							}
							else{
								btnEdit = `<a href="{{ url("$url/upload") }}?aud_thp1_id=${row.aud_thp1_id}" class="btn btn-primary btn-xs btn-block"><i class="fas fa-cloud-upload"></i> Upload</a>`;
							}
                            return `@if(authorized("{$module}@upload")) ${btnEdit} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'thp1_logbook_filepath', title: 'File', width: 100, sortable: false,
						formatter: function (val, row) {
                            let btnDownload = ``;		
							if(row.thp1_logbook_filepath != ''){
								btnDownload += `${row.thp1_logbook_filepath}`;
							}
							
                            return `${btnDownload}`
                        }
					},
					{field: 'aud_thp1_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left'},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'aud_thp1_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'aud_thp1_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
				onBeforeLoad: function () {

                },
                onLoadSuccess: function (data) {

                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'thp1_logbook_filepath', type: 'label'},
                ]);
        });
    </script>
@endpush
