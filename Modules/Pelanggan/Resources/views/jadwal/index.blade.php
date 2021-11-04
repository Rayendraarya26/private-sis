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
                    let htmls = '<ol>';
                    row.logs.map(e => {
                        htmls += `
                            <li>${e.judul}<br><pre>${e.pesan}</pre></li>
                        `;
                    })

                    htmls += '</ol>'

                    return htmls;
                },
                frozenColumns: [[
                    {
                        field: 'action',
                        title: "Aksi",
                        width: 150,
                        align: 'center',
                        formatter: function (val, row) {
                            let btnApproveTgl = "";
                            let btnApproveTim = "";
                            if (row.jadw_tanggal_status == "on-going" || row.jadw_tanggal_status == 'fixed') {
                                btnApproveTgl = `<a href="{{url("$url/approve/tanggal")}}/${row.jadw_id}" class="btn btn-xs btn-primary btn-block"><i class="fad fa-check"></i> Approve Tanggal</a>`
                            } else if (row.jadw_tanggal_status == 'accepted' && (row.jadw_team_status == "on-going" || row.jadw_team_status == "fixed") && row.enable_approval_tim) {
                                btnApproveTim = `<a href="{{url("$url/approve/tim")}}/${row.jadw_id}" class="btn btn-xs btn-primary btn-block"><i class="fad fa-check"></i> Approve Tim</a>`
                            }
                            return btnApproveTgl + btnApproveTim;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_tanggal_status',
                        title: 'Status Jadwal',
                        width: 200,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on-going':
                                    return "Menunggu Persetujuan";
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
                        field: 'jadw_team_status',
                        title: 'Status Tim',
                        width: 200,
                        sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'on-going':
                                    return "Menunggu Persetujuan";
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
                        field: 'jadw_jenis', title: 'Jenis Kegiatan', width: 150, sortable: true,
                        formatter: function (val) {
                            return val.toUpperCase()
                        }
                    },
                    {field: 'jadw_tanggal_mulai', title: 'Tgl Mulai', width: 150, sortable: true},
                    {field: 'jadw_tanggal_selesai', title: 'Tgl Selesai', width: 150, sortable: true},
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
                                {value: 'on-going', text: 'Menunggu Persetujuan'},
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
                        field: 'jadw_team_status',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'on-going', text: 'Menunggu Persetujuan'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'fixed', text: 'Perbaikan Revisi'},
                                {value: 'accepted', text: 'Diterima'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'jadw_team_status',
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
