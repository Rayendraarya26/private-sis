@extends("layouts.layout_app")

@section('title', 'Audit')

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
                            <h3 class="dt-card__title">Proses Audit</h3>
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
    <script src="{{asset('assets/plugins/easyui/datagrid-detailview.js')}}"></script>
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                view: detailview,
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
                detailFormatter: function (index, row) {

                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 150,
                        align: 'center',
                        formatter: function (val, row) {
                            return ``;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_audit_status', title: 'Status Audit', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on-going':
                                    return 'Proses Audit';
                                case 'berhak-memperoleh':
                                    return 'Berhak Memperoleh';
                                case 'berhak-memperoleh-kembali':
                                    return 'Berhak Memperoleh Kembali';
                                case 'tetap-dapat-menggunakan':
                                    return 'Tetap Dapat Menggunakan';
                                case 'tidak-berhak-menggunakan':
                                    return 'Tidak Berhak Menggunakan';
                            }
                        }
                    },
                    {
                        field: 'jadw_audit_jenis', title: 'Jenis Audit', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'surveilans':
                                    return 'Surverilans';
                                case 'sertifikasi':
                                    return 'Sertifikasi';
                                case 're-sertifikasi':
                                    return 'Re-Sertifikasi';
                            }
                        }
                    },
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
                        field: 'jadw_audit_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on-going', text: 'Proses Audit'},
                                {value: 'berhak-memperoleh', text: 'Berhak Memperoleh'},
                                {value: 'berhak-memperoleh-kembali', text: 'Berhak Memperoleh Kembali'},
                                {value: 'tetap-dapat-menggunakan', text: 'Tetap Dapat Menggunakan'},
                                {value: 'tidak-berhak-menggunakan', text: 'Tidak Berhak Menggunakan'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_audit_status',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'jadw_audit_jenis',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'surveilans', text: 'Surverilans'},
                                {value: 'sertifikasi', text: 'Sertifikasi'},
                                {value: 're-sertifikasi', text: 'Re-Sertifikasi'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_audit_jenis',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
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
                ]);
        });
    </script>
@endpush
