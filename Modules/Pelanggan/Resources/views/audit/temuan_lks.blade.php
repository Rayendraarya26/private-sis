@extends("layouts.layout_app")

@section('title', 'Temuan LKS')

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


                    <div class="dt-card__body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <b>Note</b>
                                    <br>
                                    LKS: LAPORAN KETIDAKSESUAIAN
                                </div>
                            </div>
                        </div>

                        <div id="ttData" style="width:100%; min-width: 310px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("javascript")
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                view: detailview,
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{!!  url("$url/ajax?action=datagrid_lks&jadwal_id=" . $data->jadw_id)  !!}`,
                rownumbers: true,
                nowrap: false,
                singleSelect: false,
                remoteFilter: true,
                multiSort: true,
                // fitColumns: true,
                pagination: true,
                pageSize: 50,
                clientPaging: false,
                detailFormatter: function (index, row) {
                    return `
                    <div style="padding: 20px 0 20px 0">
                        <h4>Detail Perbaikan</h4>
                        <ul>
                            <li><b>Analisa</b>: ${row.lks_perbaikan_analisa || '-'}</li>
                            <li><b>Koreksi</b>: ${row.lks_perbaikan_koreksi || '-'}</li>
                            <li><b>Tindakan</b>: ${row.lks_perbaikan_tindakan || '-'}</li>
                        </ul>
                    </div>
                    `
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnDetail    = `<a href="{{url("$url/temuan-lks/" . $data->jadw_id . "/detail")}}/${row.lks_id}" class="btn btn-primary btn-block btn-xs"><i class="fas fa-info"></i> Detail</a>`;
                            let btnPerbaikan = `<a href="{{url("$url/temuan-lks/" . $data->jadw_id ."/perbaikan")}}/${row.lks_id}" class="btn btn-primary btn-block btn-xs"><i class="fas fa-tools"></i> Perbaikan</a>`;
                            if (row.lks_status == "tidak-memadai") {
                                return btnPerbaikan
                            } else {
                                return btnDetail
                            }
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'lks_status', title: 'Status LKS', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'memadai':
                                    return 'Memadai';
                                case 'tidak-memadai':
                                    return 'Tidak Memadai';
                                case 'revisi':
                                    return 'Revisi';
                            }
                        },
                        styler: function (val) {
                            switch (val) {
                                case 'memadai':
                                    return 'background-color:#388e3c;color:white;';
                                case 'tidak-memadai':
                                    return 'background-color:#d32f2f;color:white;';
                                case 'revisi':
                                    return 'background-color:#ffc046;color:#000;';
                            }
                        }
                    },
                    {
                        field: 'lks_kategori_ketidaksesuaian', title: 'Kategori LKS', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'kritis':
                                    return 'Kritis';
                                case 'mayor':
                                    return 'Mayor';
                                case 'minor':
                                    return 'Minor';
                                case 'observasi':
                                    return 'Observasi';
                            }
                        }
                    },
                    {field: 'lks_expired_date_perbaikan', title: 'Tgl Maks Revisi', width: 200, sortable: true},

                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {
                        field: 'lks_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'memadai', text: 'Memadai'},
                                {value: 'tidak-memadai', text: 'Tidak Memadai'},
                                {value: 'revisi', text: 'Revisi'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'lks_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'lks_kategori_ketidaksesuaian',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'observasi', text: 'Observasi'},
                                {value: 'minor', text: 'Minor'},
                                {value: 'mayor', text: 'Mayor'},
                                {value: 'kritis', text: 'Kritis'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'lks_status',
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
