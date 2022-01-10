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
                        width: 80,
                        align: 'center',
                        formatter: function (val, row) {
                            let dom         = `dropdownMenu_${row.jadw_id}`;
                            let btnTemuan   = `<div data-options="iconCls:'fad fa-warning'" onclick="location.href = '{{url("$url/temuan-lks")}}/${row.jadw_id}'">${row.total_temuan} Temuan LKS</div>`;
                            let btnDetail   = `<div data-options="iconCls:'fad fa-info-circle'" onclick="location.href = '{{url("$url/temuan-lks")}}/${row.jadw_id}/detail'">Detail</div>`;
                            let btnCetakLap = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/lks', '_blank')">Cetak LKS</div>`;

                            return `
                            <div>
                            <button class="btn-action btn-info" data-index="${row.jadw_id}" title="Aksi">
                                <i class="fa fa-setting"></i> Aksi
                            </button>
                            <div id="${dom}" style="width:150px; display: none;">
                            @if(authorized("{$module}@detailAllLKS")) ${btnDetail} @endif
                            @if(authorized("{$module}@temuanLKS")) ${btnTemuan} @endif
                            <div class="menu-sep"></div>
                            @if(authorized("{$module}@temuanLKS")) ${btnCetakLap} @endif
                            </div>`;
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
                onBeforeLoad: function () {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        try {
                            $(this).menubutton('destroy');
                        } catch (e) {
                            console.log('failed destroy');
                        }
                    });
                },
                onLoadSuccess: function (data) {
                    $(this).datagrid('getPanel').find('.btn-action').each(function (idx, row) {
                        $(this).menubutton({
                            menu: '#dropdownMenu_' + data.rows[idx].jadw_id
                        });
                    });
                },
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
