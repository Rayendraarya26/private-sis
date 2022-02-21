@extends('layouts.layout_app')

@section('title', 'Audit Tahap 1')

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
                            <h3 class="dt-card__title">Data Jadwal Audit Tahap 1</h3>
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
                        title: "<br><br><br>",
                        width: 140,
                        align: 'center',
                        formatter: function (val, row) {
							let btnEdit = '';
							if(row.aud_thp1_status_temuan == 'proses'){
								btnEdit = `<a href="{{ url("$url/edit") }}?aud_thp1_id=${row.aud_thp1_id}" class="btn btn-primary btn-xs btn-block"><i class="fad fa-paper-plane"></i> Ajukan</a>`;
							}
							else{
								btnEdit = `<a href="{{ url("$url/edit") }}?aud_thp1_id=${row.aud_thp1_id}" class="btn btn-warning btn-xs btn-block"><i class="fad fa-paper-plane"></i> Ajukan Ulang</a>`;
							}
							
                            return `@if(authorized("{$module}@edit")) ${btnEdit} @endif`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'aud_thp1_id', title: 'No.<br>Jadwal', width: 120, sortable: true, align: 'left',},
                    {field: 'aud_thp1_status_temuan', title: 'Status<br>Pengajuan', width: 120, sortable: true, align: 'left',},
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
                    {field: 'aud_thp1_status_temuan', type: 'label'},
                ]);
        });
    </script>
@endpush
