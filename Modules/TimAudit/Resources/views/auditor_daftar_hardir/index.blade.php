@extends('layouts.layout_app')

@section('title', 'Daftar Hadir')

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
                            <h3 class="dt-card__title">Daftar Hadir & Notulen</h3>
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
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 130,
                        align: 'center',
                        formatter: function (val, row) {
                            if (row.is_uploaded) {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-warning"><i class="fas fa-upload"></i> Unggah Ulang</a>`
                            } else {
                                return `<a href="{{url("$url/unggah")}}/${row.jadw_id}" class="btn btn-xs btn-success"><i class="fas fa-upload"></i> Unggah Berkas</a>`
                            }
                        },
                    },
                ]],
                columns: [[
                    {field: 'cust_nama', title: 'Nama pelanggan', width: 200, sortable: true},
                    {field: 'jadw_jenis', title: 'Jenis Jadwal', width: 150, sortable: true},
                    {
                        field: 'total_jadwal', title: 'Jadwal', width: 80, sortable: true,
                        formatter: function (val) {
                            return val + " Jadwal";
                        },
                    },
                    {field: 'sert_nama', title: 'Sertifikasi', width: 250, sortable: true},
                    {field: 'jadw_tanggal_mulai', title: 'Tanggal<br/>Mulai', width: 100, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tanggal<br/>Selesai', width: 100, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'total_jadwal', type: 'label'},
                    {field: 'jadw_audit_jenis', type: 'label'},
                ]);
        });
    </script>
@endpush
