@extends('layouts.layout_app')

@section('title', 'Upload Sertifikat Uji')

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
                            <h3 class="dt-card__title">Data Jadwal Audit dan File Upload Sertifikat Uji</h3>
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
                pagination: false,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "<br/><br/><br/>",
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
							let btnEdit = '';
							if(row.status_upload == 're-upload'){
								btnEdit = `<a href="{{ url("$url/edit") }}?tipe=upload-hasil-uji&jadw_id=${row.jadw_id}" class="btn btn-warning btn-xs btn-block"><i class="fas fa-cloud-upload"></i> Re-Upload</a>`;
							}
							else{
								btnEdit = `<a href="{{ url("$url/edit") }}?tipe=upload-hasil-uji&jadw_id=${row.jadw_id}" class="btn btn-primary btn-xs btn-block"><i class="fas fa-cloud-upload"></i> Upload</a>`;
							}
                            return `@if(authorized("{$module}@edit")) ${btnEdit} @endif`;
                        }
                    }
                ]],
                columns: [[
					{field: 'jadw_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                    {field: 'total_hasil_uji', title: 'Total<br/>File', width: 100, sortable: true, align: 'center'},
                ]],
				onBeforeLoad: function () {
                    
                },
                onLoadSuccess: function (data) {
                    
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'total_hasil_uji', type: 'label'},
                ]);
        });
    </script>
@endpush
