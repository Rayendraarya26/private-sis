@extends('layouts.layout_app')

@section('title', 'Persetujuan Audit')

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
                            <h3 class="dt-card__title">Data Jadwal dan Persetujuan Audit</h3>
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
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                pagination: false,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "",
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnEdit = ``;			
							btnEdit += `<a class="btn btn-info btn-block btn-xs" href="{{ url("$url/edit") }}?tipe=kesanggupan-tim&jadw_id=${row.jadw_id}&jenis=${row.jadw_status}"><i class="fas fa-handshake"></i> Persetujuan</a>`;
							
                            return `@if(authorized("{$module}@edit")) ${btnEdit} @endif`
                        }
                    }
                ]],
                columns: [[
					{field: 'jadw_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',},
                    {field: 'jadw_tim_kesanggupan', title: 'Status<br>Kesanggupan?', width: 150, sortable: true},
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 150, sortable: true},
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'jadw_audit_jenis', type: 'label'},
                    {field: 'jadw_tim_kesanggupan', type: 'label'},
                    {field: 'jadw_tanggal_mulai', type: 'label'},
                    {field: 'jadw_tanggal_selesai', type: 'label'},
                ]);
        });
    </script>
@endpush
