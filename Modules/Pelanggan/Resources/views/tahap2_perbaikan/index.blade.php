@extends("layouts.layout_app")

@section('title', 'Perbaikan Temuan')

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
    <script>
        $(function () {
            let dg = $('#ttData').datagrid({
                method: 'get',
                height: document.documentElement.scrollHeight - 300,
                url: `{{ url("$url/ajax?action=datagrid") }}`,
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
                        width: 120,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnTemuan = `<a href="{{url("$url/temuan-lks")}}/${row.jadw_id}" class="btn btn-warning btn-block btn-xs">${row.total_temuan} Temuan LKS</a>`;
                            let btnDetail = `<a href="{{url("$url/temuan-lks")}}/${row.jadw_id}/detail" class="btn btn-primary btn-block btn-xs"><i class="fas fa-info"></i> Detail LKS</a>`;
                            return btnDetail + btnTemuan
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_jenis', title: 'Status Audit', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'tunggal':
                                    return 'Tunggal';
                                case 'gabungan':
                                    return 'Gabungan';
                                case 'integrasi':
                                    return 'Intergrasi';
                            }
                        }
                    },
                    {field: 'tanggal', title: 'Tanggal Pelaksanaan', width: 200, sortable: true},
                    {
                        field: 'audits', title: 'Agenda', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ol>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.jadw_audit_jenis}</b> <br> No. Sert: ${e.jadw_audit_nomor_sertifikat} <br> No. Ref: ${e.jadw_audit_nomor_referensi}
                                    </li>`
                                })
                                htmls += `</ol>`
                            }

                            return htmls
                        }
                    },
                    {
                        field: 'tims', title: 'Tim Auditor', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ol>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.tim_posisi}</b> <br> ${e.tim_nama} (${e.tim_kode})
                                    </li>`
                                })
                                htmls += `</ol>`
                            }

                            return htmls
                        }
                    },
                ]],
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'tanggal', type: 'label'},
                    {
                        field: 'jadw_jenis',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'tunggal', text: 'Tunggal'},
                                {value: 'gabungan', text: 'Gabungan'},
                                {value: 'integrasi', text: 'Intergrasi'},
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

                    // {
                    //     field: 'lks_status',
                    //     type: 'combobox',
                    //     options: {
                    //         panelHeight: 'auto',
                    //         data: [
                    //             {value: '', text: 'Semua'},
                    //             {value: 'memadai', text: 'Memadai'},
                    //             {value: 'tidak-memadai', text: 'Tidak Memadai'},
                    //             {value: 'revisi', text: 'Revisi'},
                    //         ],
                    //         onChange: function (value) {
                    //             dg.datagrid('addFilterRule', {
                    //                 field: 'lks_status',
                    //                 op: 'equal',
                    //                 value: value
                    //             });
                    //
                    //             dg.datagrid('doFilter');
                    //         }
                    //     }
                    // },
                    // {
                    //     field: 'lks_kategori_ketidaksesuaian',
                    //     type: 'combobox',
                    //     options: {
                    //         panelHeight: 'auto',
                    //         data: [
                    //             {value: '', text: 'Semua'},
                    //             {value: 'observasi', text: 'Observasi'},
                    //             {value: 'minor', text: 'Minor'},
                    //             {value: 'mayor', text: 'Mayor'},
                    //             {value: 'kritis', text: 'Kritis'},
                    //         ],
                    //         onChange: function (value) {
                    //             dg.datagrid('addFilterRule', {
                    //                 field: 'lks_status',
                    //                 op: 'equal',
                    //                 value: value
                    //             });
                    //
                    //             dg.datagrid('doFilter');
                    //         }
                    //     }
                    // },
                ]);
        });
    </script>
@endpush
