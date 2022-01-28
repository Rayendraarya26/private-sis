@extends('layouts.layout_app')

@section('title', 'Kompetensi Auditor')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                @if(session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @error('message')
                <div class="alert alert-danger">
                    {{$message}}
                </div>
                @enderror

                <div class="dt-card">
                    <div class="dt-card__body">
                        <div id="ttData" style="width:100%;min-width: 310px"></div>
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
                height: document.documentElement.scrollHeight - 250,
                url: `{{ url("$url/ajax?action=datagrid-by-pegawai") }}`,
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
                    {field: 'ck', checkbox: true, sortable: false},
                    {
                        field: 'action', title: 'Aksi', sortable: false, width: 80, align: 'center',
                        formatter: function (value, row) {
                            @if(authorized("{$module}@editByPegawai"))
                                return `<a href="{{url("$url/edit/by/pegawai")}}/${row.peg_id}" class="btn btn-primary btn-xs btn-block"><i class="fas fa-pencil-alt"></i> Edit</a>`
                            @endif
                        }
                    },
                ]],
                columns: [[
                    {field: 'peg_kode', title: 'Kode', width: 60, sortable: true},
                    {field: 'peg_nama', title: 'Nama', width: 120, sortable: true},
                    {field: 'kompetensi', title: 'Kompetensi', width: 500, sortable: true},
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'ck', type: 'label'},
                    {field: 'action', type: 'label'},
                    {field: 'peg_kode', type: 'label'},
                ]);
        });
    </script>
@endpush
