@extends("layouts.layout_app")

@section('title', 'Jadwal')

@section('content')
    <div class="dt-content">
        <div class="row">
            <div class="col-md-12">
                <div class="dt-card">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {!! implode('', $errors->all('<li>:message</li>')) !!}
                        </div>
                    @endif
                    @if(session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="dt-card__header">
                        <div class="dt-card__heading">
                            <h3 class="dt-card__title">Jadwal Audit</h3>
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
    {{--    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>--}}
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                // view: detailview,
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid") }}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
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
                            return ``;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_tanggal_status',
                        title: 'Status <br> Pembayaran',
                        width: 120,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on-going':
                                    return "Sedang Berjalan";
                                case 'revisi':
                                    return "Revisi";
                                case 'fixed':
                                    return "Perbaikan Revisi";
                                case 'accepted':
                                    return "Diterima";
                            }
                        }
                    },
                    {
                        field: 'jadw_jenis', title: 'Jenis Kegiatan', width: 100, sortable: true,
                        formatter: function (val) {
                            return val.toUpperCase()
                        }
                    },
                    {field: 'jadw_tanggal_mulai', title: 'Tgl Mulai', width: 220, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tgl Selesai', width: 220, sortable: true},
                    {
                        field: 'jadw_file_jadwal', title: 'File Jadwal', width: 400, sortable: false,
                        formatter: function (val) {
                            if (val != "") {
                                return `<a href="${val}" target="_blank"><i class="fad fa-download"></i> Jadwal</a>`
                            }
                        }
                    },
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {
                        field: 'jadw_tanggal_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on-going', text: 'Sedang Berjalan'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'fixed', text: 'Perbaikan Revisi'},
                                {value: 'accepted', text: 'Diterima'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_tanggal_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'jadw_jenis',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'tunggal', text: 'Tunggal'},
                                {value: 'kombinasi', text: 'Kombinasi'},
                                {value: 'gabungan', text: 'Gabungan'},
                                {value: 'integrasi', text: 'Integrasi'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_jenis',
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
