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
                singleSelect: true,
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
                            let btnEdit   = '';
                            let btnDetail = '';
                            if (!row.is_approve) {
                                btnEdit += `<a class="btn btn-success btn-block btn-xs" href="{{ url("$url/edit") }}?tipe=kesanggupan-tim&jadw_id=${row.jadw_id}&jenis=${row.jadw_status}"><i class="fas fa-handshake"></i> Persetujuan</a>`;
                            }
                            btnDetail += `<a class="btn btn-info btn-block btn-xs" href="{{ url("$url/detail") }}?tipe=kesanggupan-tim&jadw_id=${row.jadw_id}&jenis=${row.jadw_status}"><i class="fas fa-book"></i> Detail Data</a>`;

                            return `
                            @if(authorized("{$module}@edit")) ${btnEdit} @endif
							@if(authorized("{$module}@detail")) ${btnDetail} @endif
                            `
                        }
                    }
                ]],
                columns: [[
                    {field: 'jadw_id', title: 'No.<br>Jadwal', width: 80, sortable: true, align: 'left',},
                    {
                        field: 'file_jadwal',
                        title: 'File Jadwal',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            if (val != null) {
                                return `<a href="${val}" target="_blank"><i class="fad fa-download"></i> Unduh</a>`
                            } else {
                                return `Belum tersedia`
                            }
                        }
                    },
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
                    {field: 'file_jadwal', type: 'label'},
                    {field: 'jadw_audit_jenis', type: 'label'},
                    {field: 'jadw_tim_kesanggupan', type: 'label'},
                    {field: 'jadw_tanggal_mulai', type: 'label'},
                    {field: 'jadw_tanggal_selesai', type: 'label'},
                ]);
        });
    </script>
@endpush
