@extends("layouts.layout_app")

@section('title', 'Persetujuan Temuan')

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
                            {!! session('message') !!}
                        </div>
                    @endif


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
                            let dom                = `dropdownMenu_${row.jadw_id}`;
                            let btnDetail          = `<div data-options="iconCls:'fad fa-info-circle'" onclick="location.href = '{{url("$url/detail")}}/${row.jadw_id}'">Detail & Approve</div>`;
                            let btnCetakLks        = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/lks')">LKS</div>`;
                            let btnCetakLapRingkas = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/lap-ringkas')">Laporan Ringkas</div>`;
                            let btnCetakDafHadir   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/daftar-hadir')">Daftar Hadir</div>`;
                            let btnCetakNotulen    = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url/cetak")}}/${row.jadw_id}/notulen')">Notulen</div>`;

                            return `
                            <div>
                            <button class="btn-action btn-info" data-index="${row.jadw_id}" title="Aksi">
                                <i class="fa fa-setting"></i> Aksi
                            </button>
                            <div id="${dom}" style="width:150px; display: none;">
                                @if(authorized("{$module}@detail")) ${btnDetail} @endif
                            <div class="menu-sep"></div>
                            @if(authorized("{$module}@cetak")) ${btnCetakNotulen} @endif
                            @if(authorized("{$module}@cetak")) ${btnCetakLapRingkas} @endif
                            @if(authorized("{$module}@cetak")) ${btnCetakDafHadir} @endif
                            @if(authorized("{$module}@cetak")) ${btnCetakLks} @endif
                            </div>
                        </div>`;
                        }
                    }
                ]],
                columns: [[
                    {
                        field: 'jadw_setujui_temuan', title: 'Status Persetujuan', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'diajukan':
                                    return 'Diajukan';
                                case 'setuju':
                                    return 'Setuju';
                                case 'revisi':
                                    return 'Revisi';
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
                        field: 'jadw_setujui_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'diajukan', text: 'Diajukan'},
                                {value: 'setuju', text: 'Setuju'},
                                {value: 'revisi', text: 'Revisi'},
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
