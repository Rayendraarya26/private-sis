@extends("layouts.layout_app")

@section('title', 'Persetujuan Temuan Tahap 1')

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
                            let dom        = `dropdownMenu_${row.aud_thp1_id}`;
                            let btnRevisi  = `<div data-options="iconCls:'fad fa-pencil'" onclick="location.href = '{{url("$url/revisi")}}/${row.enc_aud_thp1_id}'">Revisi</div>`;
                            let btnCetakLap   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url2/cetak")}}/${row.aud_thp1_id}/laporan', '_blank')">Laporan</div>`;
                            let btnCetakTinjauan   = `<div data-options="iconCls:'fad fa-print'" onclick="window.open('{{url("$url2/cetak")}}/${row.aud_thp1_id}/tinjauan', '_blank')">Hasil Tinjauan</div>`;

                            return `
                        <div>
                        <button class="btn-action btn-info" data-index="${row.aud_thp1_id}" title="Aksi">
                            <i class="fa fa-setting"></i> Aksi
                        </button>
                            <div id="${dom}" style="width:150px; display: none;">
                            ${btnRevisi}
                            ${btnCetakLap}
                            ${btnCetakTinjauan}
                            </div>`;
                        }
                    }
                ]],
                columns: [[
                    {field: 'total_temuan', title: 'Total Temuan', width: 150, sortable: true},
                    {
                        field: 'sert_tahap1_jenis', title: 'Jenis', width: 200, sortable: true,
                        formatter: function (val) {
                            switch (val) {
                                case 'pusat':
                                    return 'Pusat';
                                case 'sni':
                                    return 'SNI';
                            }
                        }
                    },
                    {field: 'tanggal', title: 'Tanggal Pelaksanaan', width: 200, sortable: true},
                    {
                        field: 'tims', title: 'Tim Auditor', width: 200, sortable: true,
                        formatter: function (val) {
                            let htmls = ""
                            if (val.length > 0) {
                                htmls += `<ul>`
                                val.map(e => {
                                    htmls += `
                                    <li>
                                        <b>${e.posisi}</b> <br> ${e.nama} (${e.kode})
                                    </li>`
                                })
                                htmls += `</ul>`
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
                            menu: '#dropdownMenu_' + data.rows[idx].aud_thp1_id
                        });
                    });
                },
            });
            dg.datagrid(
                'enableFilter', [
                    {field: 'action', type: 'label'},
                    {field: 'tanggal', type: 'label'},
                    {
                        field: 'sert_tahap1_jenis',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'pusat', text: 'Pusat'},
                                {value: 'sni', text: 'SNI'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'sert_tahap1_jenis',
                                    op: 'equal',
                                    value: value
                                });

                                dg.datagrid('doFilter');
                            }
                        }
                    },
                    {
                        field: 'aud_thp1_status_temuan',
                        type: 'combobox',
                        options: {
                            panelHeight: 'auto',
                            data: [
                                {value: '', text: 'Semua'},
                                {value: 'proses', text: 'Pusat'},
                                {value: 'diajukan', text: 'Diajukan'},
                                {value: 'revisi', text: 'Revisi'},
                                {value: 'setuju', text: 'Setuju'},
                            ],
                            onChange: function (value) {
                                dg.datagrid('addFilterRule', {
                                    field: 'aud_thp1_status_temuan',
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
