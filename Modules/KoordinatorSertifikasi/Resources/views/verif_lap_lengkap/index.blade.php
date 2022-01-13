@extends('layouts.layout_app')

@section('title', 'Verifikasi Laporan Lengkap')

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
                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror
                <div class="dt-card">
                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Data Pengajuan Laporan Lengkap</h3>
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
                        title: "Aksi",
                        width: 100,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnPreview   = `<a target="blank_" href="{{url("$url/cetak")}}/${row.jadw_id}/lap-lengkap" class="btn btn-xs btn-primary btn-block"><i class="fas file-pdf"></i> Preview</a>`
                            let btnDet = ``
                            btnDet     = `<a href="{{url("$url/detail")}}/${row.jadw_id}" class="btn btn-xs btn-danger btn-block"><i class="fas fa-check-square-o"></i> Verifikasi</a>`
                            return `@if(authorized("{$module}@cetak")) ${btnPreview} @endif  @if(authorized("{$module}@detail")) ${btnDet} @endif`;
                        },
                    },
                ]],
                columns: [[
					{field: 'jadw_id', title: 'No.<br>Jadwal', width: 150, sortable: true, align: 'left',},
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
                    {field: 'sudah_mengisi', type: 'label'},
                    {field: 'logbook_filepath', type: 'label'},
                ]);
        });
    </script>
@endpush
